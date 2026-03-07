<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
    }

    public function payment(Request $request)
    {
        // Validación actualizada con los nuevos campos
        $request->validate([
            'total' => 'required|numeric|min:0.01',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            // 'address' => 'required|string|max:500',
            'customer_notes' => 'nullable|string|max:2000',
        ]);

        $user_id = Auth::user()->id;
        
        // Obtener datos del carrito
        $cart = Cart::where('user_id', $user_id)->with('product')->get();
        
        if ($cart->isEmpty()) {
            return redirect()->route('web.cart')
                ->with([
                    'status' => 400,
                    'message' => 'Tu carrito está vacío',
                    'icon' => 'error'
                ]);
        }

        // Calcular subtotal desde el carrito (verificación de seguridad)
        $calculatedSubtotal = $cart->sum(function($item) {
            return ($item->variant?->price ?? $item->product->selling_price) * $item->quantity;
        });

        // Obtener descuento y cupón de sesión o del request
        $discount = session('discount_amount', $request->discount ?? 0);
        $couponCode = session('coupon_code', null);
        $subtotal = $request->subtotal;
        $total = $request->total;

        $address_id = Address::where('user_id', $user_id)->where('is_default', 1)->first();
        if(!$address_id){
            return redirect()->route('web.cart')
                ->with([
                    'status' => 400,
                    'message' => 'No se encontró una dirección de envío',
                    'icon' => 'error'
                ]);
        }

        $address = $address_id->address_line_1 . ' - ' . $address_id->address_line_2;
        $customerNotes = $request->customer_notes;

        // Validar que el subtotal y total sean correctos
        $expectedTotal = $subtotal - $discount;
        if (abs($expectedTotal - $total) > 0.01) {
            return redirect()->route('web.cart')
                ->with([
                    'status' => 400,
                    'message' => 'Error en el cálculo del total. Por favor, recarga la página.',
                    'icon' => 'error'
                ]);
        }

        // Validar que el subtotal calculado coincida
        if (abs($calculatedSubtotal - $subtotal) > 0.01) {
            return redirect()->route('web.cart')
                ->with([
                    'status' => 400,
                    'message' => 'Error en el subtotal. Por favor, recarga el carrito.',
                    'icon' => 'error'
                ]);
        }

        // Guardar datos en sesión para usar en success()
        $request->session()->put('address', $address);
        $request->session()->put('customer_notes', $customerNotes);
        $request->session()->put('coupon_code', $couponCode);
        $request->session()->put('discount_amount', $discount);
        $request->session()->put('cart_subtotal', $subtotal);

        // Preparar datos para PayPal
        $data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => config('paypal.currency'),
                        "value" => number_format($total, 2, '.', ''),
                    ],
                    "description" => "Compra en " . (config('app.name') ?? 'mi tienda')
                ]
            ],
            "application_context" => [
                "return_url" => route('web.paypal.success'),
                "cancel_url" => route('web.paypal.cancel'),
            ]
        ];

        // Si hay descuento, incluir el breakdown detallado
        if ($discount > 0) {
            $data["purchase_units"][0]["amount"]["breakdown"] = [
                "item_total" => [
                    "currency_code" => config('paypal.currency'),
                    "value" => number_format($subtotal, 2, '.', '')
                ],
                "discount" => [
                    "currency_code" => config('paypal.currency'),
                    "value" => number_format($discount, 2, '.', '')
                ]
            ];

            // Agregar items del carrito
            $data["purchase_units"][0]["items"] = $cart->map(function($item) {
                return [
                    "name" => $item->product->name,
                    "description" => $item->product->short_description ?? 'Producto',
                    "quantity" => $item->quantity,
                    "unit_amount" => [
                        "currency_code" => config('paypal.currency'),
                        "value" => number_format($item->product->selling_price, 2, '.', '')
                    ]
                ];
            })->toArray();
        }

        try {
            $response = $this->provider->createOrder($data);

            if (isset($response['id']) && $response['status'] == 'CREATED') {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
                
                return redirect()->route('web.cart')
                    ->with([
                        'status' => 400,
                        'message' => 'No se pudo redirigir a PayPal',
                        'icon' => 'error'
                    ]);
            } else {
                return redirect()->route('web.cart')
                    ->with([
                        'status' => 500,
                        'message' => 'Error al crear la orden de pago',
                        'icon' => 'error'
                    ]);
            }
        } catch (Exception $e) {
            return redirect()->route('web.cart')
                ->with([
                    'status' => 500,
                    'message' => 'Error al procesar el pago: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function success(Request $request)
    {
        $user_id = Auth::user()->id;
        $token   = $request->query('token');

        try {
            $response = $this->provider->capturePaymentOrder($token);

            if (!isset($response['status']) || $response['status'] !== 'COMPLETED') {
                return redirect()->route('web.cart')
                    ->with(['status' => 500, 'message' => 'Error al capturar el pago', 'icon' => 'error']);
            }

            $data_payment    = $response['purchase_units'][0]['payments']['captures'][0];
            $total           = $data_payment['amount']['value'];
            $payment_id      = $data_payment['id'];
            $payment_status  = $data_payment['status'];
            $payment_currency= $data_payment['amount']['currency_code'];

            $coupon_code     = $request->session()->get('coupon_code', null);
            $discount_amount = $request->session()->get('discount_amount', 0);
            $customer_notes  = $request->session()->get('customer_notes', null);

            $address_id = Address::where('user_id', $user_id)->where('is_default', 1)->first();
            if (!$address_id) {
                return redirect()->route('web.cart')
                    ->with(['status' => 400, 'message' => 'No se encontró una dirección de envío', 'icon' => 'error']);
            }

            $address = $address_id->address_line_1 . ' - ' . $address_id->address_line_2;

            DB::beginTransaction();
            try {
                // ✅ Cargar carrito ANTES de procesar (con relaciones necesarias)
                $cart = Cart::where('user_id', $user_id)
                    ->with(['product.images', 'variant'])
                    ->get();

                $subtotal = $total + $discount_amount;

                $order = new Order();
                $order->user_id        = $user_id;
                $order->subtotal       = $subtotal;
                $order->discount_amount= $discount_amount;
                $order->total          = $total;
                $order->coupon_code    = $coupon_code;
                $order->badge          = $payment_currency;
                $order->transaction_id = $payment_id;
                $order->payment_status = $payment_status;
                $order->status         = 'PROCESSING';
                $order->address_id     = $address_id->id;
                $order->address        = $address;
                $order->customer_notes = $customer_notes;
                $order->save();

                // ✅ Construir sesión ANTES de eliminar el carrito
                $orderItems = [];

                foreach ($cart as $item) {
                    // ✅ Precio correcto: variante > producto
                    $price = $item->variant?->price ?? $item->product->selling_price;

                    // ✅ Atributos de variante con getAttribute() en lugar de ->attributes
                    $variantAttributes = $item->variant
                        ? $item->variant->getAttribute('attributes') ?? []
                        : [];

                    $order_item = new OrderItem();
                    $order_item->order_id          = $order->id;
                    $order_item->product_id        = $item->product_id;
                    $order_item->variant_id        = $item->variant_id;          // ✅
                    $order_item->product_name      = $item->product->name;       // snapshot
                    $order_item->product_code      = $item->product->code;       // snapshot
                    $order_item->product_sku       = $item->variant?->sku        // snapshot variante
                                                    ?? $item->product->sku;
                    $order_item->variant_attributes= $variantAttributes;         // ✅ snapshot atributos
                    $order_item->price             = $price;                     // ✅ precio correcto
                    $order_item->quantity          = $item->quantity;
                    $order_item->save();

                    // ✅ Descontar stock de variante si aplica
                    if ($item->variant_id && $item->variant) {
                        $item->variant->stock -= $item->quantity;
                        $item->variant->save();
                    }

                    // Descontar stock del producto
                    $item->product->stock -= $item->quantity;
                    $item->product->save();

                    // ✅ Guardar para sesión ANTES de delete
                    $orderItems[] = [
                        'product_name' => $item->product->name,
                        'variant_label'=> collect($variantAttributes)
                                            ->map(fn($v, $k) => "{$k}: {$v}")
                                            ->implode(' / '),
                        'quantity'     => $item->quantity,
                        'price'        => $price,
                        'image'        => $item->product->images->first()?->image,
                    ];

                    $item->delete();
                }

                // Guardar en sesión para vista de confirmación
                $request->session()->put('order_id',    $order->id);
                $request->session()->put('order_total', $order->total);
                $request->session()->put('order_items', $orderItems);
                $request->session()->put('address',     $address);

                // Incrementar uso del cupón
                $couponId = $request->session()->get('coupon_id');
                if ($couponId) {
                    $coupon = Coupon::find($couponId);
                    $coupon?->incrementUsage();
                }

                $request->session()->forget([
                    'coupon_code', 'coupon_id', 'discount_amount', 'customer_notes'
                ]);

                DB::commit();

                return redirect()->route('web.paypal.order_completed')
                    ->with([
                        'status'   => 200,
                        'message'  => 'Pago exitoso. Gracias por tu compra. Orden #' . $order->id,
                        'icon'     => 'success',
                        'order_id' => $order->id,
                    ]);

            } catch (Exception $e) {
                DB::rollBack();
                return redirect()->route('web.cart')
                    ->with(['status' => 500, 'message' => 'Error al procesar la orden: ' . $e->getMessage(), 'icon' => 'error']);
            }

        } catch (Exception $e) {
            return redirect()->route('web.cart')
                ->with(['status' => 500, 'message' => 'Error en el proceso de pago: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function orderCompleted()
    {
        $orderId = session('order_id');
        $orderTotal = session('order_total');
        $settings = \App\Models\Ajuste::first(); // o Setting::first() según tu modelo
        
        if (!$orderId) {
            return redirect()->route('web.index')
                ->with([
                    'status' => 400,
                    'message' => 'No se encontró información de la orden',
                    'icon' => 'error'
                ]);
        }
        
        // Limpiar sesión después de mostrar la confirmación
        // (opcional, puedes dejarlo para que el usuario pueda recargar la página)
        
        return view('web.order_completed', compact('settings'));
    }

    /**
 * MÉTODO PARA LIMPIAR SESIÓN (opcional)
 * Puedes llamarlo desde un enlace en la vista o automáticamente
 */
    public function clearOrderSession(Request $request)
    {
        $request->session()->forget([
            'order_id',
            'order_total',
            'order_items',
            'cart_subtotal',
            'discount_amount',
            'coupon_code',
            'customer_notes',
            'address'
        ]);
        
        return redirect()->route('web.index');
    }

    public function cancel(Request $request)
    {
        
    }


}

