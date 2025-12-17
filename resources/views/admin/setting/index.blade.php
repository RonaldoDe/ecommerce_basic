@extends('layouts.admin')

@section('content')
    <h1>Ajustes del sistema</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Configuraciones del sistema</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" accept-charset="UTF-8">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nombre (*)</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="bi bi-building"></i>
                                                </span>
                                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nombre de la empresa" value="{{ old('name', $setting->name ?? '') }}">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Descripción (*)</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="bi bi-tag"></i>
                                                </span>
                                                <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Descripción de la empresa" value="{{ old('description', $setting->description ?? '') }}">
                                                @error('description')
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
                                        <div class="form-group">
                                            <label for="branch" class="form-label">Sucursal (*)</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="bi bi-shop"></i>
                                                </span>
                                                <input type="text" name="branch" id="branch" class="form-control @error('branch') is-invalid @enderror" placeholder="Sucursal" value="{{ old('branch', $setting->branch ?? '') }}">
                                                @error('branch')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="address" class="form-label">Dirección (*)</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="bi bi-geo-alt"></i>
                                                </span>
                                                <textarea name="address" rows="1" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Calle, Número, Barrio">{{ old('address', $setting->address ?? '') }}</textarea>
                                                @error('address')
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
                                            <label for="phone" class="form-label">Telefonos (*)</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="bi bi-telephone"></i>
                                                </span>
                                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Telefonos" value="{{ old('phone', $setting->phones ?? '') }}">
                                                @error('phone')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Correo (*)</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="bi bi-envelope"></i>
                                                </span>
                                                <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Correo" value="{{ old('email', $setting->email ?? '') }}">
                                                @error('email')
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
                                            <label for="website" class="form-label">Pagina Web</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-globe"></i>
                                                </span>
                                                <input type="text" name="website" id="website" class="form-control @error('website') is-invalid @enderror" placeholder="Pagina Web" value="{{ old('website', $setting->website ?? '') }}">
                                                @error('website')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="badge" class="form-label">Divisa (*)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-currency-dollar"></i>
                                                </span>

                                                <select name="badge" id="badge" class="form-control @error('badge') is-invalid @enderror">
                                                    <option value="" selected>Seleccione una Moneda</option>
                                                    @foreach ($json_data as $divisa)
                                                        <option value="{{$divisa->symbol}}" {{ (old('badge', $setting->badge ?? '') == $divisa->symbol) ? 'selected' : ''}}>{{$divisa->name}} ({{$divisa->symbol}})</option>
                                                    @endforeach
                                                </select>
                                                @error('badge')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="logo" class="form-label">Logo @if(!@isset($setting) || !$setting->logo) (*) @else  @endif</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-image"></i>
                                                </span>
                                                <input type="file" name="logo" onchange="showImage(event)" id="logo" class="form-control @error('logo') is-invalid @enderror">
                                                @error('logo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <center>
                                            @if (@isset($setting) && $setting->logo)
                                                <img src="{{ asset('storage/'.$setting->logo) }}" style="max-width: 200px; margin-top: 10px" alt="Logo" id="preview1">
                                            @else
                                                <img src="" id="preview1" style="max-width: 200px; margin-top: 10px" alt="">
                                            @endif
                                        </center>
                                        <script>
                                            const showImage = e =>
                                                document.getElementById('preview1').src = URL.createObjectURL(e.target.files[0]);
                                        </script>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="image_login" class="form-label">Imagen de Login @if(!@isset($setting) || !$setting->image_login) (*) @else  @endif</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-camera"></i>
                                                </span>
                                                <input type="file" name="image_login" onchange="showImage2(event)" id="image_login" class="form-control @error('image_login') is-invalid @enderror">
                                                @error('image_login')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <center>
                                            @if (@isset($setting) && $setting->image_login)
                                                <img src="{{ asset('storage/'.$setting->image_login) }}" id="preview2" style="max-width: 200px; margin-top: 10px" alt="Logo">
                                            @else
                                                <img src="" id="preview2" style="max-width: 200px; margin-top: 10px" alt="">
                                            @endif
                                        </center>
                                        <script>
                                            const showImage2 = e =>
                                                document.getElementById('preview2').src = URL.createObjectURL(e.target.files[0]);
                                        </script>
                                        <br>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i>
                                            Guardar Cambios</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection