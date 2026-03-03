<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Ajuste;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images']);
        
        // Búsqueda
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%')
                  ->orWhere('tags', 'like', '%' . $request->search . '%');
            });
        }

        // Filtros adicionales
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('brand') && $request->brand != '') {
            $query->where('brand_id', $request->brand);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('stock_status') && $request->stock_status != '') {
            $query->where('stock_status', $request->stock_status);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $settings = Ajuste::first();
        $categories = Category::all();
        $brands = Brand::all();
        $products = $query->paginate(10);


        return view('admin.products.index', compact('products', 'settings', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Campos básicos
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:products,code',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            
            // Precios
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
            
            // Inventario
            'stock' => 'required|integer|min:0',
            'stock_alert' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'stock_status' => 'nullable|in:in_stock,out_of_stock,on_backorder',
            
            // Dimensiones
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            
            // Estado
            'status' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'visibility' => 'nullable|in:public,private,catalog',
            
            // SEO
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            
            // Adicional
            'tags' => 'nullable|string',
            'warranty' => 'nullable|string',
            'return_policy' => 'nullable|string',
            'shipping_info' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $product = new Product();
            
            // Básicos
            $product->name = $request->name;
            $product->code = $request->code;
            $product->sku = $request->sku; // Se auto-genera en el modelo si está vacío
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->short_description = $request->short_description;
            $product->long_description = $request->long_description;
            
            // Precios
            $product->cost_price = $request->cost_price;
            $product->selling_price = $request->selling_price;
            $product->discount_percentage = $request->discount_percentage ?? 0;
            $product->discount_start_date = $request->discount_start_date;
            $product->discount_end_date = $request->discount_end_date;
            
            // Calcular precio con descuento
            if ($product->discount_percentage > 0) {
                $product->discount_price = $product->selling_price * (1 - $product->discount_percentage / 100);
            }
            
            // Inventario
            $product->stock = $request->stock ?? 0;
            $product->stock_alert = $request->stock_alert ?? 10;
            $product->manage_stock = $request->has('manage_stock') ? true : false;
            $product->stock_status = $request->stock_status ?? ($product->stock > 0 ? 'in_stock' : 'out_of_stock');
            
            // Dimensiones
            if ($request->length || $request->width || $request->height) {
                $product->dimensions = [
                    'length' => $request->length ?? 0,
                    'width' => $request->width ?? 0,
                    'height' => $request->height ?? 0,
                ];
            }
            $product->weight = $request->weight;
            
            // Estado
            $product->status = $request->has('status') ? true : false;
            $product->featured = $request->has('featured') ? true : false;
            $product->is_new = $request->has('is_new') ? true : false;
            $product->visibility = $request->visibility ?? 'public';
            
            // SEO
            $product->meta_title = $request->meta_title ?? $product->name;
            $product->meta_description = $request->meta_description ?? $product->short_description;
            $product->meta_keywords = $request->meta_keywords;
            
            // Adicional
            $product->tags = $request->tags;
            $product->warranty = $request->warranty;
            $product->return_policy = $request->return_policy;
            $product->shipping_info = $request->shipping_info;
            
            // Publicación
            $product->published_at = $request->published_at ?? now();
            
            $product->save();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with([
                    'status' => 200, 
                    'message' => 'Producto creado correctamente', 
                    'icon' => 'success'
                ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.products.index')
                ->with([
                    'status' => 500, 
                    'message' => 'Error al crear el producto: ' . $th->getMessage(), 
                    'icon' => 'error'
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $settings = Ajuste::first();
        $product->load(['category', 'brand', 'images', 'reviews', 'variants']);
        return view('admin.products.show', compact('product', 'settings'));
    }

    /**
     * Gestión de imágenes del producto
     */
    public function images(Product $product)
    {
        $product->load('images');
        return view('admin.products.images', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        
        // Desempaquetar dimensiones si existen
        $dimensions = $product->dimensions ?? [];
        
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'dimensions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            // Campos básicos
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:products,code,' . $product->id,
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            
            // Precios
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
            
            // Inventario
            'stock' => 'required|integer|min:0',
            'stock_alert' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'stock_status' => 'nullable|in:in_stock,out_of_stock,on_backorder',
            
            // Dimensiones
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            
            // Estado
            'status' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'visibility' => 'nullable|in:public,private,catalog',
            
            // SEO
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            
            // Adicional
            'tags' => 'nullable|string',
            'warranty' => 'nullable|string',
            'return_policy' => 'nullable|string',
            'shipping_info' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Básicos
            $product->name = $request->name;
            $product->code = $request->code;
            $product->sku = $request->sku;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->short_description = $request->short_description;
            $product->long_description = $request->long_description;
            
            // Precios
            $product->cost_price = $request->cost_price;
            $product->selling_price = $request->selling_price;
            $product->discount_percentage = $request->discount_percentage ?? 0;
            $product->discount_start_date = $request->discount_start_date;
            $product->discount_end_date = $request->discount_end_date;
            
            // Calcular precio con descuento
            if ($product->discount_percentage > 0) {
                $product->discount_price = $product->selling_price * (1 - $product->discount_percentage / 100);
            } else {
                $product->discount_price = null;
            }
            
            // Inventario
            $product->stock = $request->stock ?? 0;
            $product->stock_alert = $request->stock_alert ?? 10;
            $product->manage_stock = $request->has('manage_stock') ? true : false;
            $product->stock_status = $request->stock_status ?? ($product->stock > 0 ? 'in_stock' : 'out_of_stock');
            
            // Dimensiones
            if ($request->length || $request->width || $request->height) {
                $product->dimensions = [
                    'length' => $request->length ?? 0,
                    'width' => $request->width ?? 0,
                    'height' => $request->height ?? 0,
                ];
            } else {
                $product->dimensions = null;
            }
            $product->weight = $request->weight;
            
            // Estado
            $product->status = $request->has('status') ? true : false;
            $product->featured = $request->has('featured') ? true : false;
            $product->is_new = $request->has('is_new') ? true : false;
            $product->visibility = $request->visibility ?? 'public';
            
            // SEO
            $product->meta_title = $request->meta_title ?? $product->name;
            $product->meta_description = $request->meta_description ?? $product->short_description;
            $product->meta_keywords = $request->meta_keywords;
            
            // Adicional
            $product->tags = $request->tags;
            $product->warranty = $request->warranty;
            $product->return_policy = $request->return_policy;
            $product->shipping_info = $request->shipping_info;
            
            // Publicación
            if ($request->published_at) {
                $product->published_at = $request->published_at;
            }
            
            $product->save();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with([
                    'status' => 200, 
                    'message' => 'Producto actualizado correctamente', 
                    'icon' => 'success'
                ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.products.index')
                ->with([
                    'status' => 500, 
                    'message' => 'Error al actualizar el producto: ' . $th->getMessage(), 
                    'icon' => 'error'
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Eliminar imágenes del storage
            foreach ($product->images as $image) {
                if ($image->image && Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }
                $image->delete();
            }

            // Eliminar el producto
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with([
                    'status' => 200, 
                    'message' => 'Producto eliminado correctamente', 
                    'icon' => 'success'
                ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.products.index')
                ->with([
                    'status' => 500, 
                    'message' => 'Error al eliminar el producto: ' . $th->getMessage(), 
                    'icon' => 'error'
                ]);
        }
    }

    /**
     * Subir imagen del producto
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);

            $image_product = new ProductImage();
            $image_product->product_id = $product->id;
            $image_product->image = $request->file('image')->store('products', 'public');
            $image_product->save();

            DB::commit();

            return redirect()->route('admin.products.images', $product->id)
                ->with([
                    'status' => 200, 
                    'message' => 'Imagen cargada correctamente', 
                    'icon' => 'success'
                ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->with([
                    'status' => 500, 
                    'message' => 'Error al cargar la imagen: ' . $th->getMessage(), 
                    'icon' => 'error'
                ]);
        }
    }

    /**
     * Eliminar imagen del producto
     */
    public function removeImage($id)
    {
        try {
            $image = ProductImage::findOrFail($id);
            $productId = $image->product_id;

            if ($image->image && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }

            $image->delete();

            return redirect()->route('admin.products.images', $productId)
                ->with([
                    'status' => 200, 
                    'message' => 'Imagen eliminada correctamente', 
                    'icon' => 'success'
                ]);

        } catch (\Throwable $th) {
            return redirect()->back()
                ->with([
                    'status' => 500, 
                    'message' => 'Error al eliminar la imagen: ' . $th->getMessage(), 
                    'icon' => 'error'
                ]);
        }
    }

    /**
     * Vista pública del producto
     */
    public function show_web(Product $product)
    {
        // Incrementar contador de vistas
        $product->incrementViews();

        // Cargar relaciones
        $product->load(['category', 'brand', 'images', 'reviews' => function($query) {
            $query->approved()->latest()->take(5);
        }]);

        // Productos relacionados
        $relatedProducts = Product::active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $settings = Ajuste::first();

        return view('web.product.show', compact('product', 'relatedProducts', 'settings'));
    }

    /**
     * Actualizar stock rápido (AJAX)
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        try {
            $product->stock = $request->stock;
            $product->stock_status = $request->stock > 0 ? 'in_stock' : 'out_of_stock';
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado correctamente',
                'stock' => $product->stock,
                'stock_status' => $product->stock_status,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el stock: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicar producto
     */
    public function duplicate(Product $product)
    {
        try {
            DB::beginTransaction();

            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Copia)';
            $newProduct->code = $product->code . '-COPY-' . time();
            $newProduct->sku = null; // Se generará automáticamente
            $newProduct->stock = 0;
            $newProduct->sales_count = 0;
            $newProduct->views_count = 0;
            $newProduct->wishlist_count = 0;
            $newProduct->reviews_count = 0;
            $newProduct->rating = 0;
            $newProduct->save();

            // Copiar imágenes
            foreach ($product->images as $image) {
                $newImage = new ProductImage();
                $newImage->product_id = $newProduct->id;
                $newImage->image = $image->image; // Reutilizar la misma imagen
                $newImage->save();
            }

            DB::commit();

            return redirect()->route('admin.products.edit', $newProduct)
                ->with([
                    'status' => 200, 
                    'message' => 'Producto duplicado correctamente', 
                    'icon' => 'success'
                ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->with([
                    'status' => 500, 
                    'message' => 'Error al duplicar el producto: ' . $th->getMessage(), 
                    'icon' => 'error'
                ]);
        }
    }

    /**
     * Exportar productos a CSV
     */
    public function export()
    {
        $products = Product::with(['category', 'brand'])->get();

        $filename = 'productos_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');

        ob_start();

        // Encabezados
        fputcsv($handle, [
            'ID', 'Nombre', 'Código', 'SKU', 'Categoría', 'Marca', 
            'Precio Compra', 'Precio Venta', 'Stock', 'Estado'
        ]);

        // Datos
        foreach ($products as $product) {
            fputcsv($handle, [
                $product->id,
                $product->name,
                $product->code,
                $product->sku,
                $product->category->name ?? 'N/A',
                $product->brand->name ?? 'N/A',
                $product->cost_price,
                $product->selling_price,
                $product->stock,
                $product->status ? 'Activo' : 'Inactivo',
            ]);
        }

        fclose($handle);
        $csv = ob_get_clean();

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function storeVariant(Request $request, Product $product)
    {
        $request->validate([
            'attributes'  => 'required|array',
            'sku'         => 'nullable|string|max:100|unique:product_variants,sku',
            'price'       => 'nullable|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('variants', 'public');
            }

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku'        => $request->input('sku') ?: strtoupper(\Illuminate\Support\Str::random(8)),
                'price'      => $request->input('price'),
                'cost_price' => $request->input('cost_price'),
                'stock'      => $request->input('stock'),
                'image'      => $imagePath,
                'status'     => $request->input('status'),
                'attributes' => $request->input('attributes'), // ✅ input() en lugar de ->attributes
                'order'      => ProductVariant::where('product_id', $product->id)->max('order') + 1,
            ]);

            $product->has_variants = true;
            $product->stock        = ProductVariant::totalStockForProduct($product->id);
            $product->save();

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Variante creada correctamente',
                'variant'  => $variant,
                'label'    => $variant->label,
                'imageUrl' => $variant->image_url,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant)
    {
        $request->validate([
            'attributes'  => 'required|array',
            'sku'         => 'nullable|string|max:100|unique:product_variants,sku,' . $variant->id,
            'price'       => 'nullable|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                if ($variant->image) Storage::disk('public')->delete($variant->image);
                $variant->image = $request->file('image')->store('variants', 'public');
            }

            if ($request->boolean('remove_image') && $variant->image) {
                Storage::disk('public')->delete($variant->image);
                $variant->image = null;
            }

            $variant->sku        = $request->input('sku');
            $variant->price      = $request->input('price');
            $variant->cost_price = $request->input('cost_price');
            $variant->stock      = $request->input('stock');
            $variant->status     = $request->input('status');
            $variant->attributes = $request->input('attributes'); // ✅
            $variant->save();

            $product->stock = ProductVariant::totalStockForProduct($product->id);
            $product->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variante actualizada correctamente',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function destroyVariant(Product $product, ProductVariant $variant)
    {
        try {
            DB::beginTransaction();

            if ($variant->image) Storage::disk('public')->delete($variant->image);
            $variant->delete();

            // Si ya no tiene variantes, desactivar has_variants
            if ($product->variants()->count() === 0) {
                $product->has_variants = false;
            }
            $product->stock = ProductVariant::totalStockForProduct($product->id);
            $product->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Variante eliminada correctamente']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}