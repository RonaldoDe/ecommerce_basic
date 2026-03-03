<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Módulos con sus etiquetas para mostrar en la vista
    private function getModules(): array
    {
        return [
            'roles'      => 'Roles',
            'users'      => 'Usuarios',
            'categories' => 'Categorías',
            'products'   => 'Productos',
            'orders'     => 'Órdenes',
            'coupons'    => 'Cupones',
            'reviews'    => 'Reseñas',
            'settings'   => 'Configuración',
        ];
    }

    private function getActions(): array
    {
        return [
            'index'   => 'Ver',
            'show'    => 'Detalle',
            'create'  => 'Crear',
            'edit'    => 'Editar',
            'destroy' => 'Eliminar',
        ];
    }

    public function index()
    {
        $roles = Role::withCount('permissions')->paginate(8);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $modules     = $this->getModules();
        $actions     = $this->getActions();
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);

        return view('admin.roles.create', compact('modules', 'actions', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name'       => strtoupper($request->name),
                'guard_name' => 'web',
            ]);

            if ($request->filled('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with(['status' => 200, 'message' => 'Rol creado correctamente', 'icon' => 'success']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.roles.index')
                ->with(['status' => 500, 'message' => $th->getMessage(), 'icon' => 'error']);
        }
    }

    public function show(string $id)
    {
        $role    = Role::with('permissions')->findOrFail($id);
        $modules = $this->getModules();
        $actions = $this->getActions();

        return view('admin.roles.show', compact('role', 'modules', 'actions'));
    }

    public function edit(string $id)
    {
        $role        = Role::with('permissions')->findOrFail($id);
        $modules     = $this->getModules();
        $actions     = $this->getActions();
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        $rolePerms   = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'modules', 'actions', 'permissions', 'rolePerms'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            DB::beginTransaction();

            $role       = Role::findOrFail($id);
            $role->name = strtoupper($request->name);
            $role->save();

            // Sincroniza: agrega los nuevos y quita los desmarcados
            $role->syncPermissions($request->permissions ?? []);

            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with(['status' => 200, 'message' => 'Rol actualizado correctamente', 'icon' => 'success']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.roles.index')
                ->with(['status' => 500, 'message' => $th->getMessage(), 'icon' => 'error']);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->delete();

            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with(['status' => 200, 'message' => 'Rol eliminado correctamente', 'icon' => 'success']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.roles.index')
                ->with(['status' => 500, 'message' => $th->getMessage(), 'icon' => 'error']);
        }
    }
}