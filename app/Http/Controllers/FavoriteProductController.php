<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\FavoriteProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class FavoriteProductController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return redirect()->route('web.login')->with([
                'status'  => 401,
                'message' => 'Debes iniciar sesión para ver tus favoritos',
                'icon'    => 'warning',
            ]);
        }

        $settings         = Ajuste::first();
        $favoriteProducts = FavoriteProduct::where('user_id', Auth::user()->id)
                                           ->with('product.images')
                                           ->get();

        return view('web.favorite', compact('favoriteProducts', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        if (Auth::guest()) {
            return $this->respond(401, 'Debes iniciar sesión', 'warning', [
                'added' => false,
            ]);
        }

        $userId    = Auth::id();
        $productId = $request->product_id;

        try {
            DB::beginTransaction();

            $existing = FavoriteProduct::where('user_id', $userId)
                                       ->where('product_id', $productId)
                                       ->first();

            if ($existing) {
                // Toggle: quitar de favoritos
                $existing->delete();
                DB::commit();

                $count = FavoriteProduct::where('user_id', $userId)->count();

                return $this->respond(200, 'Producto eliminado de favoritos', 'info', [
                    'status' => 'removed',
                    'added'  => false,
                    'count'  => $count,
                ]);
            }

            // Toggle: agregar a favoritos
            FavoriteProduct::create([
                'product_id' => $productId,
                'user_id'    => $userId,
            ]);

            DB::commit();

            $count = FavoriteProduct::where('user_id', $userId)->count();

            return $this->respond(200, 'Producto agregado a favoritos', 'success', [
                'status' => 'added',
                'added'  => true,
                'count'  => $count,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(500, 'Error al actualizar favoritos.', 'error', [
                'added' => false,
            ]);
        }
    }

    public function destroy(FavoriteProduct $favoriteProduct)
    {
        try {
            DB::beginTransaction();
            $favoriteProduct->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(500, 'Error al eliminar el producto de favoritos.', 'error');
        }

        return $this->respond(200, 'Producto eliminado de favoritos', 'success');
    }

    // ─── Helper: responde JSON si es AJAX, redirect si no ───────────────────────

    private function respond(int $status, string $message, string $icon, array $extra = [])
    {
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(array_merge([
                'status'  => $status,
                'success' => $status === 200,
                'message' => $message,
                'icon'    => $icon,
            ], $extra), $status === 200 ? 200 : $status);
        }

        return redirect()->back()->with([
            'status'  => $status,
            'message' => $message,
            'icon'    => $icon,
        ]);
    }
}