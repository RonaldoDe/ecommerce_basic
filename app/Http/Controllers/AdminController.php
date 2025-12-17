<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Product;

class AdminController extends Controller
{
    public function index()
    {
        $roles = Role::count();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'SUPER ADMINISTRADOR');
        })->count();
        $products = Product::count();
        $categories = Category::count();
        return view('admin.index', compact('roles', 'users', 'products', 'categories'));
    }
}
