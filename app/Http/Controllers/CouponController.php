<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons.
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Búsqueda por código
        if ($request->has('search') && $request->search != '') {
            $query->where('code', 'LIKE', "%{$request->search}%");
        }

        // Filtro por tipo
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filtro por estado
        if ($request->has('status') && $request->status != '') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('is_active', $status);
        }

        // Filtro por expirados
        if ($request->has('expired') && $request->expired == '1') {
            $query->whereNotNull('expires_at')
                  ->where('expires_at', '<', now());
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón creado exitosamente');
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón actualizado exitosamente');
    }

    /**
     * Toggle coupon active status.
     */
    public function toggleStatus($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        $status = $coupon->is_active ? 'activado' : 'desactivado';
        return redirect()->back()
            ->with('success', "Cupón {$status} exitosamente");
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        
        // Verificar si el cupón ha sido usado
        if ($coupon->usage_count > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un cupón que ya ha sido usado. Desactívalo en su lugar.');
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón eliminado exitosamente');
    }

    /**
     * Reset usage count for a coupon.
     */
    public function resetUsage($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->usage_count = 0;
        $coupon->save();

        return redirect()->back()
            ->with('success', 'Contador de usos reiniciado');
    }
}