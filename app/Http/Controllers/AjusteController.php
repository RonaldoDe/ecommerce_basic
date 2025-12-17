<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\HttpCache\Store;

class AjusteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setting = Ajuste::first();
        $json_data = json_decode(file_get_contents('https://api.hilariweb.com/divisas'));
        return view('admin.setting.index', compact('setting', 'json_data'));
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
        $setting = Ajuste::first();

       $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'badge' => 'required|string|max:10',
            'website' => 'nullable|url|max:255',
        ];

        if($setting){
            $rules['logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['image_login'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }else{
            $rules['logo'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['image_login'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $request->validate($rules);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'errors' => $validator->errors()
        //     ], 422);
        // }
        try {
            DB::beginTransaction();
            if(!$setting){
                $setting = new Ajuste();
            }

            $setting->name = $request->name;
            $setting->description = $request->description;
            $setting->branch = $request->branch;
            $setting->address = $request->address;
            $setting->phones = $request->phone;
            $setting->email = $request->email;
            $setting->badge = $request->badge;
            $setting->website = $request->website;

            if($request->hasFile('logo')){
                if($setting->logo && Storage::disk('public')->exists($setting->logo)){
                    Storage::disk('public')->delete($setting->logo);
                }
                $setting->logo = $request->file('logo')->store('logo', 'public');
            }

            if($request->hasFile('image_login')){
                if($setting->image_login && Storage::disk('public')->exists($setting->image_login)){
                    Storage::disk('public')->delete($setting->image_login);
                }
                $setting->image_login = $request->file('image_login')->store('image_login', 'public');
            }

            $setting->save();

        } catch (\Throwable $th) {
            DB::rollBack();
                // dd($th->getMessage());
                return redirect()->route('admin.settings.index')->with(['status' => 500, 'message' => 'Error al crear el ajuste '. $th->getMessage(), 'icon' => 'error']);
        }

        DB::commit();
        return redirect()->route('admin.settings.index')->with(['status' => 200, 'message', 'message' => 'Ajuste actualizados correctamente', 'icon' => 'success']);
        // return response()->json(['status' => true, 'message' => 'Ajuste creado correctamente'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ajuste $ajuste)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ajuste $ajuste)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ajuste $ajuste)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ajuste $ajuste)
    {
        //
    }
}
