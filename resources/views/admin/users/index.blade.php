@extends('layouts.admin')

@section('content')
<h1>Listado de Usuarios</h1>
<hr>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Usuarios Registrados</h4>
        @can('users.create')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Nuevo usuario
            </a>
        @endcan
    </div>
    <div class="card-body">

        {{-- Filtros --}}
        <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Buscar por nombre o correo..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    @if(request('search') || request('role') || request('status') !== null)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Registro</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php $nro = ($users->currentPage() - 1) * $users->perPage() + 1; @endphp
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $nro++ }}</td>
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $user->roles->first()?->name ?? 'Sin rol' }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($user->status == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    @if($user->status == 1)
                                        @can('users.show')
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                               class="btn btn-info btn-sm" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan
                                        @can('users.edit')
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                               class="btn btn-success btn-sm" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('users.destroy')
                                            <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                  method="POST" class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmAction(this.form, false)" title="Desactivar">
                                                    <i class="bi bi-person-slash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @else
                                        @can('users.show')
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                               class="btn btn-info btn-sm" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan
                                        @can('users.edit')
                                            <form action="{{ route('admin.users.restore', $user->id) }}"
                                                  method="POST" class="m-0">
                                                @csrf
                                                <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="confirmAction(this.form, true)" title="Restaurar">
                                                    <i class="bi bi-person-check"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No se encontraron usuarios
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <p class="text-muted mb-0">
                    Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }} de {{ $users->total() }}
                </p>
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

<script>
    function confirmAction(form, isRestore) {
        Swal.fire({
            title: isRestore ? '¿Restaurar usuario?' : '¿Desactivar usuario?',
            text: isRestore ? 'El usuario podrá volver a acceder.' : 'El usuario no podrá iniciar sesión.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isRestore ? '#f0ad4e' : '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then(r => { if (r.isConfirmed) form.submit(); });
    }
</script>
@endsection