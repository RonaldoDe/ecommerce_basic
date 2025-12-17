<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();
                
        if($request->search){
            $query->where('name', 'like', '%'.$request->search.'%')
            ->orWhere('code', 'like', '%'.$request->search.'%')
            ->orWhere('long_description', 'like', '%'.$request->search.'%')
            ->orWhere('short_description', 'like', '%'.$request->search.'%')
            ->orWhere('category_id', 'like', '%'.$request->search.'%');
        }

        $settings = Ajuste::first();

        $products = $query->paginate(10);
        return view('admin.products.index', compact('products', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:products,code',
            'long_description' => 'required|string|max:255',
            'short_description' => 'required|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $product = new Product();
            $product->name = $request->name;
            $product->code = $request->code;
            $product->long_description = $request->long_description;
            $product->short_description = $request->short_description;
            $product->cost_price = $request->cost_price;
            $product->selling_price = $request->selling_price;
            $product->category_id = $request->category_id;
            $product->stock = $request->stock;

            $product->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->route('admin.products.index')->with(['status' => 500, 'message' => 'Error al crear el producto.'. $th->getMessage(), 'icon' => 'error']);
        }

        DB::commit();
        return redirect()->route('admin.products.index')->with(['status' => 200, 'message' => 'Producto creado correctamente', 'icon' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product = Product::findOrFail($product->id);
        return view('admin.products.show', compact('product'));
    }

    public function images(Product $product)
    {
        $product = Product::findOrFail($product->id);
        return view('admin.products.images', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product = Product::findOrFail($product->id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        try{
            DB::beginTransaction();
            $product = Product::find($product->id);
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->code = $request->code;
            $product->short_description = $request->short_description;
            $product->long_description = $request->long_description;
            $product->cost_price = $request->cost_price;
            $product->selling_price = $request->selling_price;
            $product->stock = $request->stock;
            $product->save();
            DB::commit();
        }catch(\Throwable $th){
            DB::rollBack();
            return redirect()->route('admin.products.index')->with(['status' => 500, 'message' => 'Error al actualizar el producto.'. $th->getMessage(), 'icon' => 'error']);
        }
        
        return redirect()->route('admin.products.index')->with(['status' => 200, 'message' => 'Producto actualizado correctamente', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try{
            DB::beginTransaction();
            $product = Product::findOrFail($product->id);

                foreach ($product->images as $image) {
                    if($image->image && Storage::disk('public')->exists($image->image)){
                        Storage::disk('public')->delete($image->image);
                    }
                    $image->delete();
                }
                $product->delete();
            DB::commit();
        }catch(\Throwable $th){
            DB::rollBack();
            return redirect()->route('admin.products.index')->with(['status' => 500, 'message' => 'Error al eliminar el producto.'. $th->getMessage(), 'icon' => 'error']);
        }

        return redirect()->route('admin.products.index')->with(['status' => 200, 'message' => 'Producto eliminado correctamente', 'icon' => 'success']);
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);

            $image_product = new ProductImage();
            $image_product->product_id = $product->id;


            $image_product->image = $request->file('image')->store('products', 'public');
            $image_product->save();

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.products.images', $product->id)->with(['status' => 500, 'message' => 'Error al cargar la imagen.'. $th->getMessage(), 'icon' => 'error']);
        }
        DB::commit();

        return redirect()->route('admin.products.images', $product->id)->with(['status' => 200, 'message' => 'Imagen cargada correctamente', 'icon' => 'success']);
    }

    public function removeImage($id)
    {
        $image = ProductImage::findOrFail($id);
        if($image->image && Storage::disk('public')->exists($image->image)){
            Storage::disk('public')->delete($image->image);
        }
        $image->delete();
        return redirect()->route('admin.products.images', $image->product_id)->with(['status' => 200, 'message' => 'Imagen eliminada correctamente', 'icon' => 'success']);
    }
}
