@extends('layouts.admin')

@section('content')
    <h1>Listado de Usuarios</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Usuarios Registrados 
                        <a href="{{ route('admin.users.create') }}" style="float: right" class="btn btn-primary"><i class="bi bi-plus"></i> Nuevo registro</a>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('admin.users.index') }}" method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="search" placeholder="Buscar" class="form-control" placeholder="Buscar..." value="{{ $_REQUEST['search'] ?? '' }}">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                                    @if (isset($_REQUEST['search']) && $_REQUEST['search'] != '')
                                        <a href="{{ url('/admin/users') }}" class="btn btn-secondary"><i class="bi bi-trash"></i> Limpiar</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Fecha de registro</th>
                                <th>Rol del usuario</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nro = ($users->currentPage() - 1) * $users->perPage() + 1;
                            @endphp
                            @foreach ($users as $user)
                                <tr>
                                    <td style="text-align: center;">{{ $nro++ }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>{{ $user->roles->first() ? $user->roles->first()->name : 'Sin rol' }}</td>
                                    <td>
                                        @if ($user->status == 0)
                                            <span class="badge bg-danger">Inactivo</span>
                                        @else
                                            <span class="badge bg-success">Activo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Acciones">
                                            @if ($user->status == 1)
                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                class="btn btn-info btn-sm">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>

                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="btn btn-success btn-sm">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>

                                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                    method="POST" class="m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete(this.form)">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.restore', $user->id) }}"
                                                    method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <button type="button"
                                                            class="btn btn-warning btn-sm"
                                                            onclick="confirmDelete(this.form, true)">
                                                        <i class="bi bi-arrow-clockwise"></i> Restaurar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($users->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4 px-3">
                            <div class="text-muted">
                                Mostrando de {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} registros
                            </div>
                            <div>
                                {{ $users->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(form, isRestore = false) {
        const message = isRestore
            ? '¿Desea restaurar el registro?'
            : '¿Desea eliminar el registro?';

        Swal.fire({
            title: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isRestore ? '#f0ad4e' : '#d33',
            cancelButtonColor: '#3085d6',
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
