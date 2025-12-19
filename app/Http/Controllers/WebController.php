<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use Illuminate\Http\Request;
use App\Models\Product;

class WebController extends Controller
{
    public function index()
    {
        $settings = Ajuste::first();
        $products = Product::paginate(8);
        return view('web.index', compact('settings', 'products'));
    }

    public function search() {

        $settings = Ajuste::first();
        $search = request('search');
        $products = Product::where('name', 'like', '%' . $search . '%')
        ->orWhere('short_description', 'like', '%' . $search . '%')
        ->orderBy('name', 'asc')
        ->paginate(8);

        return view('web.search', compact('products', 'search', 'settings'));
    }
}
