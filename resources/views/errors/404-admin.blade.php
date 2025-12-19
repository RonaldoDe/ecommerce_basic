@extends('layouts.admin')

@section('content')
<div class="error-page container">
    <div class="col-md-8 col-12 offset-md-2">
        <div class="text-center">
            <img class="img-error" src="{{asset('./assets/compiled/svg/error-404.svg')}}" width="50%" alt="Not Found">
            <h1 class="error-title">NO ENCONTRADO</h1>
            <p class='fs-5 text-gray-600'>La página que estás buscando puede haber sido eliminada, cambiado su nombre o está temporalmente disponible.</p>
            <a href="{{ route('admin') }}" class="btn btn-lg btn-outline-primary mt-3">Regresar al Inicio</a>
        </div>
    </div>
</div>
@endsection