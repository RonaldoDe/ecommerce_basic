<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent')
                         ->withCount('children');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('parent')) {
            $request->parent === 'root'
                ? $query->whereNull('parent_id')
                : $query->where('parent_id', $request->parent);
        }

        $query->orderBy('order')->orderBy('name');

        $categories = $query->paginate(10)->withQueryString();
        $parents    = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'parents'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->active()->orderBy('name')->get();
        $maxOrder = Category::max('order') ?? 0;
        return view('admin.categories.create', compact('parents', 'maxOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'order'       => 'required|integer|min:0',
            'status'      => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
            }

            Category::create([
                'parent_id'   => $request->parent_id ?: null,
                'name'        => $request->name,
                'slug'        => $request->slug,
                'description' => $request->description,
                'image'       => $imagePath,
                'order'       => $request->order,
                'status'      => $request->status,
            ]);

            DB::commit();
            return redirect()->route('admin.categories.index')
                ->with(['status' => 200, 'icon' => 'success', 'message' => 'Categoría creada correctamente.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.categories.index')
                ->with(['status' => 500, 'icon' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
        }
    }

    public function show(Category $category)
    {
        $category->load(['parent', 'children' => fn($q) => $q->withCount('products')->orderBy('order')]);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        // Excluir la categoría actual y sus hijos como posibles padres
        $childIds = $category->children->pluck('id')->toArray();
        $parents  = Category::whereNull('parent_id')
                            ->where('id', '!=', $category->id)
                            ->whereNotIn('id', $childIds)
                            ->orderBy('name')
                            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'order'       => 'required|integer|min:0',
            'status'      => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Manejar imagen
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $category->image = $request->file('image')->store('categories', 'public');
            }

            // Eliminar imagen si se marcó
            if ($request->boolean('remove_image') && $category->image) {
                Storage::disk('public')->delete($category->image);
                $category->image = null;
            }

            $category->parent_id   = $request->parent_id ?: null;
            $category->name        = $request->name;
            $category->slug        = $request->slug;
            $category->description = $request->description;
            $category->order       = $request->order;
            $category->status      = $request->status;
            $category->save();

            DB::commit();
            return redirect()->route('admin.categories.index')
                ->with(['status' => 200, 'icon' => 'success', 'message' => 'Categoría actualizada correctamente.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.categories.index')
                ->with(['status' => 500, 'icon' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
        }
    }

    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            // Reasignar subcategorías al padre del padre (o null)
            Category::where('parent_id', $category->id)
                    ->update(['parent_id' => $category->parent_id]);

            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            DB::commit();
            return redirect()->route('admin.categories.index')
                ->with(['status' => 200, 'icon' => 'success', 'message' => 'Categoría eliminada correctamente.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.categories.index')
                ->with(['status' => 500, 'icon' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
        }
    }

    public function setParent(Request $request, Category $category)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // Evitar que se asigne a sí misma o a sus hijos
        if ($request->parent_id == $category->id) {
            return back()->with(['status' => 400, 'icon' => 'error', 'message' => 'Una categoría no puede ser su propio padre.']);
        }

        $childIds = $category->children->pluck('id')->toArray();
        if (in_array($request->parent_id, $childIds)) {
            return back()->with(['status' => 400, 'icon' => 'error', 'message' => 'No puedes asignar una subcategoría como padre.']);
        }

        $category->parent_id = $request->parent_id ?: null;
        $category->save();

        return back()->with(['status' => 200, 'icon' => 'success', 'message' => 'Categoría padre actualizada correctamente.']);
    }
}