@extends('layouts.admin')

@section('content')
    <h1>Datos de la categoría: {{ $category->name }}</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Datos de la categoría</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nombre de la categoría</label>
                                <p><i class="bi bi-tag"></i> {{$category->name}} </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="slug">Slug</label>
                                <p><i class="bi bi-link-45deg"></i> {{$category->slug}} </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <p><i class="bi bi-text-paragraph"></i> {{$category->description}} </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fecha">Fecha y hora de Registro</label>
                                <p><i class="bi bi-calendar3"></i> {{$category->created_at}} </p>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Volver</a></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection