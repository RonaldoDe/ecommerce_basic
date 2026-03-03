@extends('layouts.admin')

@section('content')
    <h1>Rol: {{ $role->name }}</h1>
    <hr>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detalle del rol</h4>
            <span class="badge bg-primary fs-6">{{ $role->permissions->count() }} permisos</span>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-muted small">Nombre</label>
                    <p class="fw-semibold"><i class="bi bi-person-badge-fill me-1"></i>{{ $role->name }}</p>
                </div>
                <div class="col-md-4">
                    <label class="text-muted small">Creado</label>
                    <p class="fw-semibold"><i class="bi bi-calendar3 me-1"></i>{{ $role->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <hr>
            <label class="fw-bold mb-3">Permisos asignados</label>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:180px">Módulo</th>
                            @foreach($actions as $action => $label)
                                <th class="text-center">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module => $moduleLabel)
                            <tr>
                                <td class="fw-semibold">{{ $moduleLabel }}</td>
                                @foreach($actions as $action => $label)
                                    @php $has = $role->permissions->contains('name', "{$module}.{$action}"); @endphp
                                    <td class="text-center">
                                        @if($has)
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Volver</a>
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-success">Editar</a>
            </div>
        </div>
    </div>
@endsection