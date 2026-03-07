<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FavoriteProduct;
use Illuminate\Http\Request;
use App\Models\Product;

class WebController extends Controller
{
    public function index()
    {
        $settings = Ajuste::first();

        $products = Product::where('stock', '>', 0)->paginate(30);

        $flashDeals = Product::where('discount_percentage', '>', 0)
                            ->where('status', true)
                            ->whereNotNull('discount_end_date')
                            ->where('discount_end_date', '>', now())
                            ->with('images')
                            ->take(6)
                            ->get();

        $newProducts = Product::where('is_new', true)
                            ->where('status', true)
                            ->with(['images', 'category'])
                            ->latest()
                            ->take(30)
                            ->get();

        $brands             = Brand::where('status', true)->take(12)->get();
        $featuredCategories = Category::withCount('products')->take(6)->get();

        // Trending — más vistas recientes
        $trendingProducts = Product::where('status', true)
            ->where('stock', '>', 0)
            ->with(['images', 'category'])
            ->orderBy('views_count', 'desc')
            ->take(3)
            ->get();

        // Top sellers para la columna curada
        $topSellers = Product::where('status', true)
            ->where('stock', '>', 0)
            ->with(['images', 'category'])
            ->orderBy('sales_count', 'desc')
            ->take(3)
            ->get();

        // Destacados
        $featuredProducts = Product::where('status', true)
            ->where('featured', true)
            ->where('stock', '>', 0)
            ->with(['images', 'category'])
            ->latest()
            ->take(3)
            ->get();

        

        // ✅ IDs de favoritos del usuario — array vacío si no está logueado
        $favoriteIds = auth()->check()
            ? FavoriteProduct::where('user_id', auth()->id())
                             ->pluck('product_id')
                             ->toArray()
            : [];

        return view('web.index', compact(
            'settings',
            'products',
            'flashDeals',
            'newProducts',
            'brands',
            'featuredCategories',
            'favoriteIds',
            'trendingProducts',
            'topSellers',
            'featuredProducts'
        ));
    }

    public function categories(Request $request)
    {
        $settings = Ajuste::first();

        $query = Product::where('status', true)
            ->with(['images', 'category', 'brand', 'variants']);

        // Búsqueda
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }

        // Categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Precio
        if ($request->filled('price_min')) {
            $query->where('selling_price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('selling_price', '<=', (float) $request->price_max);
        }

        // Marca
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Stock
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Ordenamiento
        match ($request->sort ?? '') {
            'price_asc'  => $query->orderBy('selling_price', 'asc'),
            'price_desc' => $query->orderBy('selling_price', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            'popular'    => $query->orderBy('sales_count', 'desc'),
            'latest'     => $query->latest(),
            default      => $query->orderBy('featured', 'desc')->latest(),
        };

        $perPage  = in_array($request->per_page, [12, 24, 48]) ? (int) $request->per_page : 12;
        $products = $query->paginate($perPage)->appends($request->query());

        // Sidebar data
        $allCategories = Category::where('status', true)
            ->whereNull('parent_id')           // solo padres
            ->withCount('products')
            ->with(['children' => function($q) {
                $q->where('status', true)->withCount('products');
            }])
            ->orderBy('name')
            ->get();

        $allBrands = Brand::whereHas('products', fn($q) =>
            $q->where('status', true)
        )->withCount(['products as active_count' => fn($q) =>
            $q->where('status', true)
        ])->orderByDesc('active_count')->get();

        $priceMin = (int) (Product::where('status', true)->min('selling_price') ?? 0);
        $priceMax = (int) (Product::where('status', true)->max('selling_price') ?? 1000);

        $favoriteIds = auth()->check()
            ? FavoriteProduct::where('user_id', auth()->id())->pluck('product_id')->toArray()
            : [];

        return view('web.categories', compact(
            'settings', 'products', 'allCategories',
            'allBrands', 'favoriteIds', 'priceMin', 'priceMax'
        ));
    }
    
    public function category($id)
    {
        $settings = Ajuste::first();
        $category = Category::find($id);
        
        if (!$category) {
            abort(404, 'Categoría no encontrada');
        }
        
        $products = Product::where('status', 1)
            ->where('stock', '>', 0)
            ->where('category_id', $id)
            ->paginate(12);
        
        return view('web.category', compact('settings', 'category', 'products'));
    }

    public function search()
    {
        $settings     = Ajuste::first();
        $search       = request('search');
        $category     = request('category');
        $newProducts  = request('newProducts');
        $bestSellers  = request('bestSellers');
        $flashDeals   = request('flashDeals');

        $category_name = Category::find($category)?->name;

        $products = Product::where('status', 1)
            ->where('stock', '>', 0)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });

        if ($category) {
            $products->where('category_id', $category);
        }

        if($newProducts){
            $products->where('is_new', 1);
        }

        if($bestSellers){
            $products->orderBy('sales_count', 'desc');
        }

        if($flashDeals){
            $products->onSale();
        }

        if($category_name){
            $products->where('category_id', $category);
        }

        if ($search && $category_name) {
            $searchText = "{$search} en {$category_name}";
        } elseif ($search) {
            $searchText = $search;
        } elseif ($category_name) {
            $searchText = $category_name;
        } else {
            $searchText = '';
        }

        $products = $products->paginate(8);

        return view('web.search', compact('products', 'searchText', 'settings'));
    }
}