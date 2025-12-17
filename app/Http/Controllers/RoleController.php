<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::paginate(8);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        try {
            DB::beginTransaction();

            $role = new Role();
            $role->name = strtoupper($request->name);
            $role->guard_name = 'web';
            $role->save();

        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th->getMessage());   
            return redirect()->route('admin.roles.index')->with(['status' => 500, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        DB::commit();
        return redirect()->route('admin.roles.index')->with(['status' => 200, 'message' => 'Rol creado correctamente', 'icon' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::find($id);
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$id,
        ]);

        try {
            DB::beginTransaction();

            $role = Role::find($id);
            $role->name = strtoupper($request->name);
            $role->save();

        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th->getMessage());   
            return redirect()->route('admin.roles.index')->with(['status' => 500, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        DB::commit();
        return redirect()->route('admin.roles.index')->with(['status' => 200, 'message' => 'Rol actualizado correctamente' , 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $role = Role::find($id);
            $role->delete();

        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th->getMessage());   
            return redirect()->route('admin.roles.index')->with(['status' => 500, 'message' => $th->getMessage(), 'icon' => 'error']);
        }
        
        DB::commit();
        return redirect()->route('admin.roles.index')->with(['status' => 200, 'message' => 'Rol eliminado correctamente', 'icon' => 'success']);
    }
}
