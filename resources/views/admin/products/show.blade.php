@extends('layouts.admin')

@section('content')
    <h1>Datos del producto: {{ $product->name }}
        <div style="float: right">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Volver</a>
                        </div>
    </h1>   
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Datos del producto</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_id">Categoría</label>
                                <p><i class="bi bi-tag-fill"></i> {{$product->category->name}} </p>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Nombre del producto</label>
                                <p><i class="bi bi-box"></i> {{$product->name}} </p>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="code">Código</label>
                                <p><i class="bi bi-upc-scan"></i> {{$product->code}} </p>
                                
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="short_description">Descripción corta</label>
                                        <p><i class="bi bi-text-left"></i> {{$product->short_description}} </p>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cost_price">Precio de compra</label>
                                        <p><i class="bi bi-currency-dollar"></i> {{$product->cost_price}} </p>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="selling_price">Precio de venta</label>
                                        <p><i class="bi bi-currency-dollar"></i> {{$product->selling_price}} </p>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="stock">Stock</label>
                                        <p><i class="bi bi-boxes"></i> {{$product->stock}} </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="long_description">Descripción detallada</label>
                                        <p> {!! $product->long_description !!} </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Imagenes del producto</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($product->images as $image)
                            <div class="col-md-3" style="margin-bottom: 20px">
                                <div class="card">
                                    <img src="{{ asset('storage/'.$image->image) }}" style="width: 100%" alt="{{$product->name}}" class="img-fluid">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection