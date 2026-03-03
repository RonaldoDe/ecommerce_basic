@extends('layouts.admin')

@section('content')
<h1>Editar usuario</h1>
<hr>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h4>Datos del usuario</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Rol --}}
                    <div class="mb-3">
                        <label class="form-label">Rol <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="">Seleccione un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}">
                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}">
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Cambio de contraseña colapsable --}}
                    <div class="mb-4">
                        <a class="btn btn-outline-secondary btn-sm mb-3" data-bs-toggle="collapse"
                           href="#passwordSection">
                            <i class="bi bi-key-fill"></i> Cambiar contraseña
                        </a>
                        <div class="collapse" id="passwordSection">
                            <div class="card card-body bg-light">
                                <p class="text-muted small mb-3">
                                    Deja vacío si no deseas cambiar la contraseña.
                                </p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                            <input type="password" name="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="Mínimo 8 caracteres">
                                            @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                            <input type="password" name="password_confirmation"
                                                   class="form-control" placeholder="Repite la contraseña">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection