<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Listar direcciones de un cliente
     */
    public function index(User $user)
    {
        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('admin.addresses.index', compact('user', 'addresses'));
    }

    /**
     * Formulario crear dirección
     */
    public function create(User $user)
    {
        return view('admin.addresses.create', compact('user'));
    }

    /**
     * Guardar nueva dirección
     */
    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $data = $request->validate([
            'label'           => 'required|string|max:50',
            'phone'           => 'nullable|string|max:30',
            'address_line_1'  => 'required|string|max:255',
            'address_line_2'  => 'nullable|string|max:255',
            'city'            => 'required|string|max:100',
            'state'           => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'country'         => 'required|string|max:2',
            'reference'       => 'nullable|string|max:255',
            'is_default'      => 'boolean',
        ]);

        DB::transaction(function () use ($user, $data) {

            // Si se marca como default, quitar el default anterior
            if (!empty($data['is_default'])) {
                Address::where('user_id', $user->id)
                    ->update(['is_default' => false]);
            }

            $data['recipient_name'] = $user->name;
            $data['user_id'] = $user->id;


            $user->addresses()->create($data);
        });

        return redirect()
            ->route('web.dashboard.addresses', $user)
            ->with([
                'status' => 200,
                'message' => 'Dirección creada correctamente',
                'icon' => 'success'
            ]);
    }

    /**
     * Formulario editar dirección
     */
    public function edit(User $user, Address $address)
    {
        $this->authorizeAddress($user, $address);

        return view('admin.addresses.edit', compact('user', 'address'));
    }

    /**
     * Actualizar dirección
     */
    public function update(Request $request, $id)
    {
        $address = Address::find($id);
        $user = User::find($address->user_id);
        
        $this->authorizeAddress($user, $address);

        $data = $request->validate([
            'label'           => 'nullable|string|max:50',
            'recipient_name'  => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:30',
            'address_line_1'  => 'required|string|max:255',
            'address_line_2'  => 'nullable|string|max:255',
            'city'            => 'required|string|max:100',
            'state'           => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'country'         => 'required|string|max:2',
            'reference'       => 'nullable|string|max:255',
            'is_default'      => 'boolean',
        ]);

        DB::transaction(function () use ($address, $user, $data) {

            if (!empty($data['is_default'])) {
                if ($data['is_default']) {

                    DB::transaction(function () use ($user, $address) {
                        Address::where('user_id', $user->id)
                            ->update(['is_default' => false]);

                        $address->update(['is_default' => true]);
                    });
                }
            }

            $address->update($data);
        });

        return redirect()
            ->route('web.dashboard.addresses', $user)
            ->with([
                'status' => 200,
                'message' => 'Dirección actualizada correctamente',
                'icon' => 'success'
            ]);
    }

    /**
     * Eliminar dirección
     */
    public function destroy(User $user, $id)
    {
        $address = Address::find($id);
        $user = User::find($address->user_id);
        $this->authorizeAddress($user, $address);

        // ⚠️ No permitir borrar si está asociada a órdenes
        if ($address->orders()->exists()) {
            return back()->with('error', 'No se puede eliminar una dirección usada en órdenes');
        }

        $address->delete();

        return back()->with([
            'status' => 200,
            'message' => 'Dirección eliminada correctamente',
            'icon' => 'success'
        ]);
    }

    /**
     * Validar que la dirección pertenezca al cliente
     */
    private function authorizeAddress(User $user, Address $address): void
    {
        abort_if($address->user_id !== $user->id, 403);
    }

    public function setDefault(Address $address)
    {
        $user = User::find(Auth::user()->id);

        abort_if($address->user_id !== $user->id, 403);

        DB::transaction(function () use ($user, $address) {

            // Quitar default a todas
            Address::where('user_id', $user->id)
                ->update(['is_default' => false]);

            // Marcar esta como default
            $address->update(['is_default' => true]);
        });

        return back()->with(['status' => 200, 'message' => 'Dirección predeterminada actualizada correctamente', 'icon' => 'success']);
    }
}
