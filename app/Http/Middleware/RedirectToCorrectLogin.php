<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class RedirectToCorrectLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $isAdminRoute = $request->is('admin') || $request->is('admin/*');
        // 1️⃣ NO autenticado
        if (!Auth::check()) {
            if ($isAdminRoute) {
                return redirect()->route('login');
            }

            return redirect()->route('web.login');
        }

        // 2️⃣ SÍ autenticado → validar rol
        $user = User::find(Auth::id());

        if (!$user) {
            Auth::logout();
            return redirect()->route('web.login');
        }


        $isAdmin = $user->roles->contains(function ($role) {
            return in_array($role->name, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
        });

        // Usuario ADMIN intentando entrar a zona CLIENTE
        if ($isAdmin && ! $isAdminRoute) {
            return redirect()->route('admin'); // redirige al panel de administración
        }

        // Usuario CLIENTE intentando entrar a zona ADMIN
        if (! $isAdmin && $isAdminRoute) {
            return redirect()->route('web.index'); // redirige a la página de inicio del cliente
        }

        return $next($request);
    }
}
