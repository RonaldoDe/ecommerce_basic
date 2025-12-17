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
                    <h4>Imagenes del producto
                        <div style="float: right">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="bi bi-plus"></i> Agregar imagen
                            </button>
                        </div>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Cargar  del producto</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.products.upload_image', $product->id) }}" method="POST" enctype="multipart/form-data" accept-charset="UTF-8}}">
                                    @csrf
                                    @method('POST')
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="image" class="form-label">Imagen del producto (*)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-camera"></i>
                                                    </span>
                                                    <input type="file" name="image" onchange="showImage2(event)" id="image_login" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                                                    @error('image')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <center>
                                                <img src="" id="preview2" style="max-width: 200px; margin-top: 10px" alt="">
                                            </center>
                                            <script>
                                                const showImage2 = e =>
                                                    document.getElementById('preview2').src = URL.createObjectURL(e.target.files[0]);
                                            </script>
                                            <br>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Guardar imagen</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @foreach ($product->images as $image)
                            <div class="col-md-3" style="margin-bottom: 20px">
                                <div class="card">
                                    <img src="{{ asset('storage/'.$image->image) }}" style="width: 100%" alt="{{$product->name}}" class="img-fluid">
                                    <br>
                                    <form action="{{ route('admin.products.remove_image', $image->id) }}"
                                        method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-block"
                                                onclick="confirmDelete(this.form)">
                                            <i class="bi bi-trash"></i> Eliminar imagen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: '¿Desea eliminar la imagen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endsection