<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('transaction_id', 'LIKE', "%{$search}%")
                  ->orWhere('tracking_number', 'LIKE', "%{$search}%")
                  ->orWhere('coupon_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', fn($q) =>
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                  );
            });
        }

        if ($request->filled('status'))         $query->where('status', $request->status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('date_from'))      $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))        $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('with_discount'))  $query->withDiscount();
        if ($request->filled('with_tracking'))  $query->withTracking();

        // Estadísticas rápidas para el header
        $stats = [
            'total'      => Order::count(),
            'pending'    => Order::where('status', 'PENDING')->count(),
            'processing' => Order::where('status', 'PROCESSING')->count(),
            'shipped'    => Order::where('status', 'SHIPPED')->count(),
        ];

        $orders   = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $settings = Ajuste::first();

        return view('admin.orders.index', compact('orders', 'settings', 'stats'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product.images',
            'items.variant',
            'statusHistory.changer',
        ])->findOrFail($id);

        $settings = Ajuste::first();
        return view('admin.orders.show', compact('order', 'settings'));
    }

    public function edit($id)
    {
        $order    = Order::with(['user', 'items.product', 'items.variant'])->findOrFail($id);
        $settings = Ajuste::first();
        return view('admin.orders.edit', compact('order', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'address'         => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_company'=> 'nullable|string|max:100',
            'admin_notes'     => 'nullable|string|max:2000',
            'customer_notes'  => 'nullable|string|max:2000',
            'discount_amount' => 'nullable|numeric|min:0',
            'coupon_code'     => 'nullable|string|max:50',
        ]);

        $order    = Order::findOrFail($id);
        $discount = $request->discount_amount ?? $order->discount_amount;
        $total    = $order->subtotal - $discount;

        $order->update([
            'address'         => $request->address,
            'tracking_number' => $request->tracking_number,
            'shipping_company'=> $request->shipping_company,
            'admin_notes'     => $request->admin_notes,
            'customer_notes'  => $request->customer_notes,
            'discount_amount' => $discount,
            'coupon_code'     => $request->coupon_code,
            'total'           => max(0, $total),
        ]);

        return redirect()->route('admin.orders.show', $order->id)
            ->with(['status' => 200, 'message' => 'Orden actualizada correctamente', 'icon' => 'success']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,PROCESSING,SHIPPED,DELIVERED,CANCELLED',
        ]);

        $order      = Order::findOrFail($id);
        $oldStatus  = $order->status;
        $newStatus  = $request->status;

        $order->update(['status' => $newStatus]);

        // Registrar historial de cambio
        $this->logStatusChange($order, 'order_status', $oldStatus, $newStatus);

        // Si se cancela, restaurar stock
        if ($newStatus === 'CANCELLED' && $oldStatus !== 'CANCELLED') {
            $this->restoreStock($order);
        }

        return redirect()->back()
            ->with(['status' => 200, 'message' => 'Estado actualizado correctamente', 'icon' => 'success']);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:PENDING,PAID,COMPLETED,FAILED,REFUNDED',
        ]);

        $order     = Order::findOrFail($id);
        $oldStatus = $order->payment_status;
        $newStatus = $request->payment_status;

        $order->update(['payment_status' => $newStatus]);

        $this->logStatusChange($order, 'payment_status', $oldStatus, $newStatus);

        return redirect()->back()
            ->with(['status' => 200, 'message' => 'Estado de pago actualizado', 'icon' => 'success']);
    }

    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'tracking_number'  => 'required|string|max:100',
            'shipping_company' => 'required|string|max:100',
        ]);

        $order     = Order::findOrFail($id);
        $oldStatus = $order->status;

        $order->update([
            'tracking_number'  => $request->tracking_number,
            'shipping_company' => $request->shipping_company,
            'status'           => 'SHIPPED',
        ]);

        if ($oldStatus !== 'SHIPPED') {
            $this->logStatusChange($order, 'order_status', $oldStatus, 'SHIPPED');
        }

        return redirect()->back()
            ->with(['status' => 200, 'message' => 'Tracking actualizado y orden marcada como enviada', 'icon' => 'success']);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('items.variant')->findOrFail($id);

            // Restaurar stock (producto y variante)
            $this->restoreStock($order);

            $order->items()->delete();
            $order->delete();

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with(['status' => 200, 'message' => 'Orden eliminada y stock restaurado', 'icon' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with(['status' => 500, 'message' => 'Error: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function statistics()
    {
        $totalOrders        = Order::count();
        $totalRevenue       = Order::whereIn('payment_status', ['PAID', 'COMPLETED'])->sum('total');
        $totalDiscount      = Order::sum('discount_amount');
        $ordersWithDiscount = Order::withDiscount()->count();
        $ordersWithTracking = Order::withTracking()->count();

        $pendingOrders    = Order::where('status', 'PENDING')->count();
        $processingOrders = Order::where('status', 'PROCESSING')->count();
        $shippedOrders    = Order::where('status', 'SHIPPED')->count();
        $deliveredOrders  = Order::where('status', 'DELIVERED')->count();
        $cancelledOrders  = Order::where('status', 'CANCELLED')->count();

        $pendingPayments  = Order::where('payment_status', 'PENDING')->count();
        $paidOrders       = Order::whereIn('payment_status', ['PAID', 'COMPLETED'])->count();
        $failedPayments   = Order::where('payment_status', 'FAILED')->count();

        // Ventas por mes (últimos 6 meses)
        $salesByMonth = Order::whereIn('payment_status', ['PAID', 'COMPLETED'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as total, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.code',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Variantes más vendidas
        $topVariants = DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name as product_name',
                'order_items.variant_attributes',
                DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->whereNotNull('order_items.variant_id')
            ->groupBy('order_items.variant_id', 'products.name', 'order_items.variant_attributes')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        $topCoupons = Order::whereNotNull('coupon_code')
            ->select('coupon_code',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(discount_amount) as total_discount'))
            ->groupBy('coupon_code')
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();

        $settings = Ajuste::first();

        return view('admin.orders.statistics', compact(
            'totalOrders', 'totalRevenue', 'totalDiscount',
            'ordersWithDiscount', 'ordersWithTracking',
            'pendingOrders', 'processingOrders', 'shippedOrders', 'deliveredOrders', 'cancelledOrders',
            'pendingPayments', 'paidOrders', 'failedPayments',
            'salesByMonth', 'recentOrders', 'topProducts', 'topVariants', 'topCoupons',
            'settings'
        ));
    }

    public function invoice($id)
    {
        $order    = Order::with(['user', 'items.product', 'items.variant'])->findOrFail($id);
        $settings = Ajuste::first();
        return view('admin.orders.invoice', compact('order', 'settings'));
    }

    // ---- Helpers privados ----

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            // Restaurar stock de variante
            if ($item->variant_id && $item->variant) {
                $item->variant->stock += $item->quantity;
                $item->variant->save();
            }

            // Restaurar stock del producto
            if ($item->product) {
                $item->product->stock += $item->quantity;
                $item->product->save();
            }
        }
    }

    private function logStatusChange(Order $order, string $type, string $from, string $to): void
    {
        // Solo si tienes la tabla order_status_histories
        if (class_exists(\App\Models\OrderStatusHistory::class)) {
            try {
                \App\Models\OrderStatusHistory::create([
                    'order_id'   => $order->id,
                    'type'       => $type,
                    'from_status'=> $from,
                    'to_status'  => $to,
                    'changed_by' => Auth::id(),
                ]);
            } catch (\Throwable) {
                // Silenciar si la tabla no existe aún
            }
        }
    }
}