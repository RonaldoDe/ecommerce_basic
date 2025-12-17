@extends('layouts.admin')

@section('content')
    <h1>Datos del usuario: {{ $user->name }}</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Datos del usuario</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Roles</label>
                                <p><i class="bi bi-shield-check"></i> {{$user->roles->first() ? $user->roles->first()->name : 'Sin rol'}} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre del usuario</label>
                                <p><i class="bi bi-person-badge-fill"></i> {{$user->name}} </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha">Correo</label>
                                <p><i class="bi bi-envelope-fill"></i> {{$user->email}} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fecha">Fecha y hora de Registro</label>
                                <p><i class="bi bi-calendar3"></i> {{$user->created_at}} </p>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Volver</a></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection