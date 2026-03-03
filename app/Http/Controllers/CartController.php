<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ajuste;
use App\Models\Coupon;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return redirect()->route('web.login')->with([
                'status'  => 401,
                'message' => 'Debes iniciar sesión para ver tus productos en el carrito',
                'icon'    => 'warning',
            ]);
        }
        $settings = Ajuste::first();
        $cart     = Cart::where('user_id', Auth::user()->id)->with('product.images')->get();
        return view('web.cart', compact('cart', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|numeric|min:1',
        ]);

        $product   = Product::findOrFail($request->product_id);
        $variantId = $request->variant_id ?: null;

        // Verificar stock
        if ($variantId) {
            $variant         = ProductVariant::findOrFail($variantId);
            $stockDisponible = $variant->stock;
        } else {
            $stockDisponible = $product->stock;
        }

        if ($request->quantity > $stockDisponible) {
            return $this->respond(422, 'No hay suficiente stock disponible.', 'warning');
        }

        try {
            DB::beginTransaction();

            $cart = Cart::where('user_id', Auth::id())
                        ->where('product_id', $request->product_id)
                        ->where('variant_id', $variantId)
                        ->first();

            if ($cart) {
                if (($cart->quantity + $request->quantity) > $stockDisponible) {
                    DB::rollBack();
                    return $this->respond(
                        422,
                        'Ya tienes ' . $cart->quantity . ' en el carrito y no hay suficiente stock.',
                        'warning'
                    );
                }
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                $cart             = new Cart();
                $cart->user_id    = Auth::id();
                $cart->product_id = $request->product_id;
                $cart->variant_id = $variantId;
                $cart->quantity   = $request->quantity;
                $cart->save();
            }

            DB::commit();

            // Contar items totales en el carrito para actualizar el badge
            $cartCount = Cart::where('user_id', Auth::id())->count();

            return $this->respond(200, 'Producto agregado al carrito', 'success', [
                'count'   => $cartCount,
                'success' => true,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(500, 'Error al agregar el producto al carrito.', 'error');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $cart = Cart::find($id);
            if (!$cart) {
                DB::rollBack();
                return $this->respond(404, 'Producto no encontrado', 'error');
            }

            // Verificar stock (variante o producto)
            $stock = $cart->variant
                ? $cart->variant->stock
                : $cart->product->stock;

            if ($stock < $request->quantity) {
                DB::rollBack();
                return $this->respond(400, 'No hay suficiente stock', 'error');
            }

            $cart->quantity = $request->quantity;
            $cart->save();

            DB::commit();
            return $this->respond(200, 'Cantidad actualizada', 'success');

        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(500, 'Error al actualizar la cantidad. ' . $e->getMessage(), 'error');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $cart = Cart::find($id);
            if (!$cart) {
                return $this->respond(404, 'Producto no encontrado en el carrito', 'error');
            }
            $cart->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(500, 'Error al eliminar el producto del carrito. ' . $e->getMessage(), 'error');
        }

        return $this->respond(200, 'Producto eliminado del carrito', 'success');
    }

    public function clear()
    {
        try {
            DB::beginTransaction();
            Cart::where('user_id', Auth::user()->id)->delete();
            DB::commit();
            return $this->respond(200, 'Carrito limpio', 'success');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(500, 'Error al limpiar el carrito. ' . $e->getMessage(), 'error');
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $couponCode = strtoupper(trim($request->coupon_code));
        $coupon     = Coupon::where('code', $couponCode)->valid()->first();

        if (!$coupon || !$coupon->isValid()) {
            return redirect()->back()->withErrors([
                'coupon_code' => 'Cupón inválido, expirado o sin usos disponibles',
            ])->withInput();
        }

        $cart     = Cart::where('user_id', Auth::id())->with('product')->get();
        $subtotal = $cart->sum(fn($item) => $item->product->selling_price * $item->quantity);
        $discount = $coupon->calculateDiscount($subtotal);

        if ($discount == 0) {
            $minPurchase = $coupon->min_purchase ? '$' . number_format($coupon->min_purchase, 2) : '';
            return redirect()->back()->withErrors([
                'coupon_code' => "Este cupón requiere una compra mínima de {$minPurchase}",
            ])->withInput();
        }

        session([
            'coupon_code'     => $couponCode,
            'coupon_id'       => $coupon->id,
            'discount_amount' => $discount,
        ]);

        return redirect()->back()->with([
            'status'  => 200,
            'message' => 'Cupón aplicado. Descuento: $' . number_format($discount, 2),
            'icon'    => 'success',
        ]);
    }

    public function removeCoupon()
    {
        session()->forget(['coupon_code', 'coupon_id', 'discount_amount']);

        return redirect()->back()->with([
            'status'  => 200,
            'message' => 'Cupón eliminado',
            'icon'    => 'info',
        ]);
    }

    // ─── Helper: responde JSON si es AJAX, redirect si no ───────────────────────

    private function respond(int $status, string $message, string $icon, array $extra = [])
    {
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(array_merge([
                'status'  => $status,
                'success' => $status === 200,
                'message' => $message,
                'icon'    => $icon,
            ], $extra), $status === 200 ? 200 : $status);
        }

        $with = ['status' => $status, 'message' => $message, 'icon' => $icon];

        return $status === 200
            ? redirect()->back()->with($with)
            : redirect()->back()->with($with);
    }
}