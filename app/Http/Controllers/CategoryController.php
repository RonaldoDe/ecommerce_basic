<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
                
        if($request->search){
            $query->where('name', 'like', '%'.$request->search.'%')->orWhere('description', 'like', '%'.$request->search.'%');
        }

        $categories = $query->paginate(8);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => $request->slug,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.categories.index')->with([
                'status' => 500, 
                'icon' => 'error', 
                'message' => 
                'Error al crear la categoría.'. $th->getMessage()]);
        }

        DB::commit();
        return redirect()->route('admin.categories.index')->with([
            'status' => 200, 
            'icon' => 
            'success', 
            'message' => 'Categoría creada correctamente.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category = Category::findOrFail($category->id);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category = Category::findOrFail($category->id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $category = Category::findOrFail($category->id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->slug = $request->slug;
            $category->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.categories.index')->with([
                'status' => 500, 
                'icon' => 'error', 
                'message' => 
                'Error al actualizar la categoría.'. $th->getMessage()]);
        }

        DB::commit();
        return redirect()->route('admin.categories.index')->with([
            'status' => 200, 
            'icon' => 
            'success', 
            'message' => 'Categoría actualizada correctamente.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();
             $category = Category::findOrFail($category->id);
             $category->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.categories.index')->with([
                'status' => 500, 
                'icon' => 'error', 
                'message' => 
                'Error al eliminar la categoría.'. $th->getMessage()]);
        }
       DB::commit();
        return redirect()->route('admin.categories.index')->with([
            'status' => 200, 
            'icon' => 
            'success', 
            'message' => 'Categoría eliminada correctamente.']);
    }
}
