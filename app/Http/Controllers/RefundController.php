<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuste;
use App\Models\Refund;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * Display a listing of refunds.
     */
    public function index(Request $request)
    {
        $query = Refund::with(['order.user', 'requester', 'processor']);

        // Filtro por estado
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Búsqueda por ID de orden o usuario
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhereHas('order', function($q) use ($search) {
                      $q->where('id', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('requester', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $refunds = $query->orderBy('id', 'desc')->paginate(10);
        $settings = Ajuste::first();

        return view('admin.refunds.index', compact('refunds', 'settings'));
    }

    /**
     * Display the specified refund.
     */
    public function show($id)
    {
        $refund = Refund::with(['order.user', 'order.items.product', 'requester', 'processor'])
            ->findOrFail($id);
        $settings = Ajuste::first();

        return view('admin.refunds.show', compact('refund', 'settings'));
    }

    /**
     * Show the form for creating a new refund.
     */
    public function create(Request $request)
    {
        $orderId = $request->get('order_id');
        $order = null;

        if ($orderId) {
            $order = Order::with('items.product')->findOrFail($orderId);
        }

        $settings = Ajuste::first();

        return view('admin.refunds.create', compact('order', 'settings'));
    }

    /**
     * Store a newly created refund in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Validar que el monto no exceda el total de la orden
        if ($request->amount > $order->total) {
            return redirect()->back()
                ->withErrors(['amount' => 'El monto del reembolso no puede exceder el total de la orden'])
                ->withInput();
        }

        Refund::create([
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'status' => 'pending',
            'requested_by' => Auth::user()->id,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.refunds.index')
            ->with('success', 'Reembolso creado correctamente');
    }

    /**
     * Update the specified refund status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $refund = Refund::findOrFail($id);

        DB::beginTransaction();
        try {
            // Actualizar el reembolso
            $refund->update([
                'status' => $request->status,
                'processed_by' => Auth::user()->id,
                'processed_at' => now(),
                'admin_notes' => $request->admin_notes ?? $refund->admin_notes,
            ]);

            // Si el reembolso fue completado, actualizar el estado de pago de la orden
            if ($request->status === 'completed') {
                $refund->order->update(['payment_status' => 'refunded']);
            }

            DB::commit();

            return redirect()->route('admin.refunds.show', $refund->id)
                ->with('success', 'Estado del reembolso actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el reembolso: ' . $e->getMessage());
        }
    }

    /**
     * Approve a refund.
     */
    public function approve($id)
    {
        $refund = Refund::findOrFail($id);

        if ($refund->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Solo se pueden aprobar reembolsos pendientes');
        }

        $refund->update([
            'status' => 'approved',
            'processed_by' => Auth::user()->id,
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.refunds.show', $refund->id)
            ->with('success', 'Reembolso aprobado correctamente');
    }

    /**
     * Reject a refund.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $refund = Refund::findOrFail($id);

        if ($refund->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Solo se pueden rechazar reembolsos pendientes');
        }

        $refund->update([
            'status' => 'rejected',
            'processed_by' => Auth::user()->id,
            'processed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.refunds.show', $refund->id)
            ->with('success', 'Reembolso rechazado');
    }

    /**
     * Complete a refund.
     */
    public function complete($id)
    {
        $refund = Refund::findOrFail($id);

        if ($refund->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Solo se pueden completar reembolsos aprobados');
        }

        DB::beginTransaction();
        try {
            $refund->update([
                'status' => 'completed',
                'processed_by' => Auth::user()->id,
                'processed_at' => now(),
            ]);

            // Actualizar el estado de pago de la orden
            $refund->order->update(['payment_status' => 'refunded']);

            DB::commit();

            return redirect()->route('admin.refunds.show', $refund->id)
                ->with('success', 'Reembolso completado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al completar el reembolso: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified refund from storage.
     */
    public function destroy($id)
    {
        $refund = Refund::findOrFail($id);

        if ($refund->status === 'completed') {
            return redirect()->back()
                ->with('error', 'No se pueden eliminar reembolsos completados');
        }

        $refund->delete();

        return redirect()->route('admin.refunds.index')
            ->with('success', 'Reembolso eliminado correctamente');
    }
}