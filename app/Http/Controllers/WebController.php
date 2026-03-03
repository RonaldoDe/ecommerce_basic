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

        $products = Product::where('stock', '>', 0)->paginate(8);

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
                            ->take(8)
                            ->get();

        $brands             = Brand::where('status', true)->take(12)->get();
        $featuredCategories = Category::withCount('products')->take(6)->get();

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
            'favoriteIds'   // ← esto es lo que faltaba
        ));
    }

    public function search()
    {
        $settings     = Ajuste::first();
        $search       = request('search');
        $category     = request('category');
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

        if ($search && $category_name) {
            $searchText = "{$search} en {$category_name}";
        } elseif ($search) {
            $searchText = $search;
        } elseif ($category_name) {
            $searchText = $category_name;
        } else {
            $searchText = '';
        }

        $products = $products->orderBy('name', 'asc')->paginate(8);

        return view('web.search', compact('products', 'searchText', 'settings'));
    }
}