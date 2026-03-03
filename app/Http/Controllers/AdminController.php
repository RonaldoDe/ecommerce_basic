<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Stats básicas
        $roles = Role::count();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'SUPER ADMINISTRADOR');
        })->count();
        $products = Product::count();
        $categories = Category::count();

        // Stats de órdenes
        $totalOrders     = Order::count();
        $pendingOrders   = Order::where('status', 'pending')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Ingresos
        $totalRevenue   = Order::where('status', '!=', 'cancelled')->sum('total');
        $monthRevenue   = Order::where('status', '!=', 'cancelled')
                            ->whereMonth('created_at', Carbon::now()->month)
                            ->sum('total');
        $todayRevenue   = Order::where('status', '!=', 'cancelled')
                            ->whereDate('created_at', Carbon::today())
                            ->sum('total');

        // Órdenes recientes (últimas 7)
        $recentOrders = Order::with('user')
                            ->latest()
                            ->take(7)
                            ->get();

        // Productos con stock bajo (menos de 5 unidades)
        $lowStockProducts = Product::where('stock', '<=', 5)
                                ->orderBy('stock', 'asc')
                                ->take(5)
                                ->get();

        // Datos para gráfica de ventas (últimos 7 días)
        $salesChart = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date'  => $date->format('d/m'),
                'total' => Order::where('status', '!=', 'cancelled')
                                ->whereDate('created_at', $date)
                                ->sum('total'),
            ];
        });

        return view('admin.index', compact(
            'roles', 'users', 'products', 'categories',
            'totalOrders', 'pendingOrders', 'cancelledOrders',
            'totalRevenue', 'monthRevenue', 'todayRevenue',
            'recentOrders', 'lowStockProducts', 'salesChart'
        ));
    }
}
