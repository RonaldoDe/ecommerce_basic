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
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }
        
        $settings = Ajuste::first();
        return view('web.dashboard', compact('settings'));
    }

    public function cart()
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }
        $settings = Ajuste::first();
        return view('web.cart', compact('settings'));
    }

    public function login()
    {
        $settings = Ajuste::first();
        return view('web.login', compact('settings'));
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $settings = Ajuste::first();
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('web.dashboard');
        }

        return redirect()->route('web.login')->with([
                'status' => 401, 
                'icon' => 'error', 
                'message' => 'Credenciales incorrectas.',
                'settings' => $settings,
            ]);
    }

    public function register()
    {
        $settings = Ajuste::first();
        return view('web.register', compact('settings'));
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        try{
            DB::beginTransaction();
            $settings = Ajuste::first();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            $user->assignRole('CLIENTE');
            Auth::login($user);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->route('web.register')->with([
                'status' => 401, 
                'icon' => 'error', 
                'message' => 'Error al registrar usuario.'.$e->getMessage(),
                'settings' => $settings,
            ]);
        }

        return redirect()->route('web.dashboard')->with([
                'status' => 200, 
                'icon' => 'success', 
                'message' => 'Usuario registrado correctamente.',
                'settings' => $settings,
            ]);
    }
}
