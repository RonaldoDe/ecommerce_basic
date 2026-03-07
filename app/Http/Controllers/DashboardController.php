<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function getSettings()
    {
        return Ajuste::first();
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }
        $user = User::find(Auth::id());
        
        $orders = $user->orders()->paginate(10);

        return redirect()->route('web.dashboard.orders', ['orders' => $orders]);
    }

    public function orders()
    {
        $settings = $this->getSettings();
        $user = User::find(Auth::id());
        
        // Aquí cargarías las órdenes del usuario
        // $orders = $user->orders()->paginate(10);
        $orders = $user->orders()->orderBy('created_at', 'desc')->paginate(10);

        
        return view('web.dashboard.orders', compact('settings', 'orders', 'user'));
    }

    public function orderDetail($orderId)
    {
        $settings = $this->getSettings();
        $user = User::find(Auth::id());

        
        // Cargar la orden con sus relaciones
        $order = $user->orders()->with(['items.product', 'orderAddress', 'items.variant'])->find($orderId);
        $addresses = $order->orderAddress;
        
        if (!$order) {
            abort(404);
        }
        
        return view('web.dashboard.order-detail', compact('settings', 'order', 'user', 'addresses'));
    }

    public function wishlist()
    {
        $settings = $this->getSettings();
        $user     = User::find(Auth::id());

        // ✅ Cambiado a $favoriteProducts para coincidir con la vista
        $favoriteProducts = $user->favoriteProducts()->with('product.images')->paginate(10);

        return view('web.dashboard.wishlist', compact('settings', 'favoriteProducts', 'user'));
    }

    public function paymentMethods()
    {
        $settings = $this->getSettings();
        $user = Auth::user();
        
        // Cargar métodos de pago del usuario
        // $paymentMethods = $user->paymentMethods;
        $paymentMethods = []; // Temporal
        
        return view('web.dashboard.payment-methods', compact('settings', 'paymentMethods', 'user'));
    }

    public function reviews()
    {
        $settings = $this->getSettings();
        $user = User::find(Auth::id());
        
        // Cargar reviews del usuario
        $reviews = $user->reviews()->paginate(10);
        
        return view('web.dashboard.reviews', compact('settings', 'reviews', 'user'));
    }

    public function addresses()
    {
        $settings = $this->getSettings();
        $user = User::find(Auth::id());
        
        // Cargar direcciones del usuario
        // $addresses = $user->addresses;
        $addresses = $user->addresses()->paginate(10);
        
        return view('web.dashboard.addresses', compact('settings', 'addresses', 'user'));
    }

    public function settings()
    {
        $settings = $this->getSettings();
        $user = Auth::user();
        
        return view('web.dashboard.settings', compact('settings', 'user'));
    }

    public function cart()
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }

        $settings = $this->getSettings();

        return view('web.cart', compact('settings'));
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('web.index');
        }
        $settings = $this->getSettings();
        return view('web.login', compact('settings'));
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('web.index');
        }

        return redirect()->route('web.login')->with([
            'status' => 401, 
            'icon' => 'error', 
            'message' => 'Credenciales incorrectas.',
        ]);
    }

    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('web.index');
        }
        $settings = $this->getSettings();
        return view('web.register', compact('settings'));
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        
        try {
            DB::beginTransaction();
            
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            $user->assignRole('CLIENTE');
            Auth::login($user);
            DB::commit();
            
            return redirect()->route('web.index')->with([
                'status' => 200, 
                'icon' => 'success', 
                'message' => 'Usuario registrado correctamente.',
            ]);
            
        } catch(Exception $e) {
            DB::rollBack();
            return redirect()->route('web.register')->with([
                'status' => 401, 
                'icon' => 'error', 
                'message' => 'Error al registrar usuario: '.$e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('web.index');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return redirect()->route('web.dashboard.settings')->with([
            'status' => 200,
            'icon' => 'success',
            'message' => 'Perfil actualizado correctamente.',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('web.dashboard.settings')->with([
                'status' => 401,
                'icon' => 'error',
                'message' => 'La contraseña actual es incorrecta.',
            ]);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route('web.dashboard.settings')->with([
            'status' => 200,
            'icon' => 'success',
            'message' => 'Contraseña actualizada correctamente.',
        ]);
    }
}