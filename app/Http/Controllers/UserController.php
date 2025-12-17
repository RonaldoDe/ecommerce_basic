<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::whereDoesntHave('roles', function ($query) { $query->where('name', 'SUPER ADMINISTRADOR'); })
                ->withTrashed()
                ->where('id', '!=', Auth::id());
        if($request->search){
            $query->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%');
        }

        $users = $query->paginate(8);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole($request->role);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')->with([
                'status' => 500, 
                'icon' => 'error', 
                'message' => 
                'Error al crear el usuario.'. $th->getMessage()]);
        }

        DB::commit();
        return redirect()->route('admin.users.index')->with([
            'status' => 200, 
            'icon' => 
            'success', 
            'message' => 'Usuario creado correctamente.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);

        try {
            DB::beginTransaction();
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            $user->syncRoles($request->role);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')->with([
                'status' => 500, 
                'icon' => 'error', 
                'message' => 
                'Error al actualizar el usuario.'. $th->getMessage()]);
        }

        DB::commit();
        return redirect()->route('admin.users.index')->with([
            'status' => 200, 
            'icon' => 
            'success', 
            'message' => 'Usuario actualizado correctamente.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
             $user = User::find($id);
             $user->status = 0;
             $user->save();
             $user->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')->with([
                'status' => 500, 
                'icon' => 'error', 
                'message' => 
                'Error al eliminar el usuario.'. $th->getMessage()]);
        }
       DB::commit();
        return redirect()->route('admin.users.index')->with([
            'status' => 200, 
            'icon' => 
            'success', 
            'message' => 'Usuario eliminado correctamente.']);
    }

    public function restore(string $id)
    {
        try {
            DB::beginTransaction();
             $user = User::withTrashed()->find($id);
             $user->restore();
             $user->status = 1;
             $user->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.users.index')->with([
                'status' => 500, 
                'icon' => 'error', 
                'message' => 
                'Error al restablecer el usuario.'. $th->getMessage()]);
        }
        DB::commit();
        return redirect()->route('admin.users.index')->with([
            'status' => 200, 
            'icon' => 
            'success', 
            'message' => 'Usuario restaurado correctamente.']);
    }
}
