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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guest()) {
            return redirect()->route('web.login')->with(['status' => 401, 'message' => 'Debes iniciar sesión para ver tus favoritos', 'icon' => 'warning']);
        }
        $settings = Ajuste::first();
        $favoriteProducts = FavoriteProduct::where('user_id', Auth::user()->id)->with('product.images')->get();
        return view('web.favorite', compact('favoriteProducts', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);


        $favoriteProduct = FavoriteProduct::where('product_id', $request->product_id)->where('user_id', Auth::user()->id)->first();
        try{
            DB::beginTransaction();
            if($favoriteProduct){
                $favoriteProduct->delete();
                DB::commit();
                return redirect()->back()->with(['status' => 200, 'message' => 'Producto eliminado de favoritos', 'icon' => 'success']);
            }
            $favoriteProduct = new FavoriteProduct();
            $favoriteProduct->product_id = $request->product_id;
            $favoriteProduct->user_id = Auth::user()->id;
            $favoriteProduct->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['status' => 500, 'message' => 'Error al agregar el producto a favoritos. ' . $e->getMessage(), 'icon' => 'error']);
        }

        return redirect()->back()->with(['status' => 200, 'message' => 'Producto agregado a favoritos', 'icon' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(FavoriteProduct $favoriteProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FavoriteProduct $favoriteProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FavoriteProduct $favoriteProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FavoriteProduct $favoriteProduct)
    {
        try{
            DB::beginTransaction();
            $favoriteProduct->delete();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['status' => 500, 'message' => 'Error al eliminar el producto de favoritos. ' . $e->getMessage(), 'icon' => 'error']);
        }
        return redirect()->back()->with(['status' => 200, 'message' => 'Producto eliminado de favoritos', 'icon' => 'success']);

    }
}
