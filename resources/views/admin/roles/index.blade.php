@extends('layouts.admin')

@section('content')
    <h1>Listado de roles</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Roles Registrados 
                        <a href="{{ route('admin.roles.create') }}" style="float: right" class="btn btn-primary"><i class="bi bi-plus"></i> Nuevo registro</a>
                    </h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Nombre</th>
                                <th>Slug</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nro = ($roles->currentPage() - 1) * $roles->perPage() + 1;
                            @endphp
                            @foreach ($roles as $role)
                                <tr>
                                    <td style="text-align: center;">{{ $nro++ }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->guard_name }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Acciones">
                                            <a href="{{ route('admin.roles.show', $role->id) }}"
                                            class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>

                                            <a href="{{ route('admin.roles.edit', $role->id) }}"
                                            class="btn btn-success btn-sm">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>

                                            <form action="{{ route('admin.roles.destroy', $role->id) }}"
                                                method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete(this.form)">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($roles->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4 px-3" style="border-b">
                            <div class="text-muted">
                                Mostrando de {{ $roles->firstItem() }} a {{ $roles->lastItem() }} de {{ $roles->total() }} registros
                            </div>
                            <div>
                                {{ $roles->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: '¿Desea eliminar el registro?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
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
