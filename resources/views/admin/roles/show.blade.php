@extends('layouts.admin')

@section('content')
    <h1>Datos del rol: {{ $role->name }}</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Datos del rol</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nombre del rol</label>
                                <p><i class="bi bi-person-badge-fill"></i> {{$role->name}} </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fecha">Fecha y hora de Registro</label>
                                <p><i class="bi bi-calendar3"></i> {{$role->created_at}} </p>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Volver</a></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection