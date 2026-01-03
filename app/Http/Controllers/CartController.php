<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ajuste;
use Exception;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guest()) {
            return redirect()->route('web.login')->with(['status' => 401, 'message' => 'Debes iniciar sesión para ver tus productos en el carrito', 'icon' => 'warning']);
        }
        $settings = Ajuste::first();
        $cart = Cart::where('user_id', Auth::user()->id)->with('product.images')->get();
        return view('web.cart', compact('cart', 'settings'));
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
            'quantity' => 'required|numeric|min:1',
        ]);


        $cart = Cart::where('product_id', $request->product_id)->where('user_id', Auth::user()->id)->first();
        try{
            DB::beginTransaction();
            if($cart){
                $cart->quantity += $request->quantity;
                $cart->save();
                DB::commit();

                return redirect()->back()->with(['status' => 200, 'message' => 'Producto agregado al carrito', 'icon' => 'success']);
            }
            $cart = new Cart();
            $cart->product_id = $request->product_id;
            $cart->user_id = Auth::user()->id;
            $cart->quantity = $request->quantity;
            $cart->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['status' => 500, 'message' => 'Error al agregar el producto al carrito. ' . $e->getMessage(), 'icon' => 'error']);
        }

        return redirect()->back()->with(['status' => 200, 'message' => 'Producto agregado al carrito', 'icon' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        try{
            DB::beginTransaction();
            $cart = Cart::find($id);
            if(!$cart){
                DB::rollBack();
                return redirect()->back()->with(['status' => 404, 'message' => 'Producto no encontrado', 'icon' => 'error']);
            }
            if($cart->product->stock < $request->quantity){
                DB::rollBack();
                return redirect()->back()->with(['status' => 400, 'message' => 'No hay suficiente stock', 'icon' => 'error']);
            }
            $cart->quantity = $request->quantity;
            $cart->save();
            DB::commit();
            return redirect()->back()->with(['status' => 200, 'message' => 'Cantidad actualizada', 'icon' => 'success']);
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['status' => 500, 'message' => 'Error al actualizar la cantidad del producto. ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $cart = Cart::find($id);
            $cart->delete();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['status' => 500, 'message' => 'Error al eliminar el producto del carrito. ' . $e->getMessage(), 'icon' => 'error']);
        }
        return redirect()->back()->with(['status' => 200, 'message' => 'Producto eliminado del carrito', 'icon' => 'success']);
    }

    public function clear(){
        try{
            DB::beginTransaction();
            Cart::where('user_id', Auth::user()->id)->delete();
            DB::commit();
            return redirect()->back()->with(['status' => 200, 'message' => 'Carrito limpio', 'icon' => 'success']);
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['status' => 500, 'message' => 'Error al limpiar el carrito. ' . $e->getMessage(), 'icon' => 'error']);
        }
    }
}
