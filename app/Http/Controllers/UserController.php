<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'SUPER ADMINISTRADOR');
                    })
                    ->withTrashed()
                    ->where('id', '!=', Auth::id())
                    ->with('roles');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->role) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        if ($request->status !== null && $request->status !== '') {
            $request->status == 1
                ? $query->whereNull('deleted_at')
                : $query->whereNotNull('deleted_at');
        }

        $users = $query->paginate(8)->withQueryString();
        $roles  = Role::whereNot('name', 'SUPER ADMINISTRADOR')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::whereNot('name', 'SUPER ADMINISTRADOR')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role'     => 'required|exists:roles,name',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'status'   => 1,
            ]);

            $user->assignRole($request->role);

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with(['status' => 200, 'icon' => 'success', 'message' => 'Usuario creado correctamente.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')
                ->with(['status' => 500, 'icon' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $user = User::withTrashed()
                    ->with(['roles', 'orders' => function ($q) {
                        $q->latest()->take(10);
                    }])
                    ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function edit(string $id)
    {
        $user  = User::withTrashed()->findOrFail($id);
        $roles = Role::whereNot('name', 'SUPER ADMINISTRADOR')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role'  => 'required|exists:roles,name',
            // Contraseña opcional en edición
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $user        = User::withTrashed()->findOrFail($id);
            $user->name  = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();
            $user->syncRoles($request->role);

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with(['status' => 200, 'icon' => 'success', 'message' => 'Usuario actualizado correctamente.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')
                ->with(['status' => 500, 'icon' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $user         = User::findOrFail($id);
            $user->status = 0;
            $user->save();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.users.index')
                ->with(['status' => 200, 'icon' => 'success', 'message' => 'Usuario desactivado correctamente.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')
                ->with(['status' => 500, 'icon' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
        }
    }

    public function restore(string $id)
    {
        try {
            DB::beginTransaction();
            $user         = User::withTrashed()->findOrFail($id);
            $user->status = 1;
            $user->save();
            $user->restore();
            DB::commit();
            return redirect()->route('admin.users.index')
                ->with(['status' => 200, 'icon' => 'success', 'message' => 'Usuario restaurado correctamente.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')
                ->with(['status' => 500, 'icon' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
        }
    }
}