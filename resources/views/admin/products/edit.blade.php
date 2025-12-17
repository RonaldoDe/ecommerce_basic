@extends('layouts.admin')

@section('content')
    <h1>Editar producto: </h1>{{ $product->name }}
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Datos del producto</h4>
                </div>
                <div class="card-body">
                    <form action=" {{ route('admin.products.update', $product->id) }} " method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_id">Categoría</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class=""><i class="bi bi-tag-fill"></i></i>
                                        </span>
                                        <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="">
                                            <option value="" selected disabled>Seleccione una categoría</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Nombre del producto</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class=""><i class="bi bi-box"></i></i>
                                        </span>
                                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Nombre del producto">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Código</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class=""><i class="bi bi-upc-scan"></i></i>
                                        </span>
                                        <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}" class="form-control @error('code') is-invalid @enderror" placeholder="Código">
                                        @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="short_description">Descripción corta</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class=""><i class="bi bi-text-left"></i></i>
                                                </span>
                                                <input type="text" name="short_description" id="short_description" value="{{ old('short_description', $product->short_description) }}" class="form-control @error('short_description') is-invalid @enderror" placeholder="Descripción corta" maxlength="255">
                                                @error('short_description')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cost_price">Precio de compra</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class=""><i class="bi bi-currency-dollar"></i></i>
                                                </span>
                                                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="form-control @error('cost_price') is-invalid @enderror" placeholder="0.00" step="0.01" min="0">
                                                @error('cost_price')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="selling_price">Precio de venta</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class=""><i class="bi bi-currency-dollar"></i></i>
                                                </span>
                                                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="form-control @error('selling_price') is-invalid @enderror" placeholder="0.00" step="0.01" min="0">
                                                @error('selling_price')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="stock">Stock</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class=""><i class="bi bi-boxes"></i></i>
                                                </span>
                                                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" class="form-control @error('stock') is-invalid @enderror" placeholder="0" step="1" min="0">
                                                @error('stock')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="long_description">Descripción detallada</label>
                                            <div class="input-group">
                                                {{-- <span class="input-group-text" id="basic-addon1">
                                                    <i class="bi bi-text-paragraph"></i>
                                                </span> --}}
                                                <div style="width: 100%">
                                                    <textarea name="long_description" rows="1" id="long_description" class="form-control ckeditor @error('long_description') is-invalid @enderror" placeholder="Descripción detallada">{{ old('long_description', $product->long_description) }}</textarea>
                                                </div>
                                                @error('long_description')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Editor para el contenido (más completo)
                                                ClassicEditor
                                                    .create(document.querySelector('#long_description'), {
                                                        toolbar: {
                                                            items: [
                                                                'heading', '|',
                                                                'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', '|',
                                                                'link', 'bulletedList', 'numberedList', '|',
                                                                'outdent', 'indent', '|',
                                                                'alignment', '|',
                                                                'blockQuote', 'insertTable', 'mediaEmbed', '|',
                                                                'undo', 'redo', '|',
                                                                'fontBackgroundColor', 'fontColor', 'fontSize', 'fontFamily', '|',
                                                                'code', 'codeBlock', 'htmlEmbed', '|',
                                                                'sourceEditing'
                                                            ],
                                                            shouldNotGroupWhenFull: true
                                                        },
                                                        language: 'es',
                                                    })
                                                    .catch(error => {
                                                        console.error(error);
                                                    });
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Actualizar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection