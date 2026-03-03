@extends('layouts.admin')

@section('content')
<h1>Listado de categorías</h1>
<hr>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Categorías Registradas</h4>
        @can('categories.create')
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Nueva categoría
            </a>
        @endcan
    </div>
    <div class="card-body">

        {{-- Filtros --}}
        <form action="{{ route('admin.categories.index') }}" method="GET" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Buscar por nombre..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="parent" class="form-select">
                        <option value="">Todas las categorías</option>
                        <option value="root" {{ request('parent') === 'root' ? 'selected' : '' }}>
                            Solo principales
                        </option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ request('parent') == $parent->id ? 'selected' : '' }}>
                                Sub: {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'status', 'parent']))
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
                        <th style="width:60px">Orden</th>
                        <th style="width:70px">Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría padre</th>
                        <th>Subcategorías</th>
                        <th>Slug</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $category->order }}</td>
                            <td class="text-center">
                                <img src="{{ $category->image_url }}"
                                     alt="{{ $category->name }}"
                                     class="rounded"
                                     style="width:45px;height:45px;object-fit:cover;">
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $category->name }}</span>
                                @if($category->description)
                                    <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($category->parent)
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-diagram-2"></i> {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">
                                    {{ $category->children_count }}
                                </span>
                            </td>
                            <td>
                                <code>{{ $category->slug }}</code>
                            </td>
                            <td class="text-center">
                                @if($category->status)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-danger">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    @can('categories.show')
                                        <a href="{{ route('admin.categories.show', $category->id) }}"
                                           class="btn btn-info btn-sm" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endcan
                                    @can('categories.edit')
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                           class="btn btn-success btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="openParentModal({{ $category->id }}, '{{ $category->name }}', {{ $category->parent_id ?? 'null' }})"
                                                title="Asignar padre">
                                            <i class="bi bi-diagram-2"></i>
                                        </button>
                                    @endcan
                                    @can('categories.destroy')
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                              method="POST" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete(this.form)" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No se encontraron categorías
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <p class="text-muted mb-0">
                    Mostrando {{ $categories->firstItem() }} - {{ $categories->lastItem() }}
                    de {{ $categories->total() }} registros
                </p>
                {{ $categories->links('pagination::bootstrap-4') }}
            </div>
        @endif

        {{-- Modal (fuera del foreach, al final del card-body) --}}
        <div class="modal fade" id="parentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-diagram-2 me-1"></i> Asignar categoría padre
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="parentForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="text-muted mb-3">
                                Categoría: <strong id="modalCategoryName"></strong>
                            </p>
                            <label class="form-label">Selecciona el padre</label>
                            <select name="parent_id" class="form-select" id="parentSelect">
                                <option value="">— Ninguna (convertir en categoría principal) —</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block">
                                Solo se muestran categorías principales como opciones de padre.
                            </small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(form) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            text: 'Las subcategorías quedarán sin padre.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(r => { if (r.isConfirmed) form.submit(); });
    }
</script>
<script>
    function openParentModal(categoryId, categoryName, currentParentId) {
        document.getElementById('modalCategoryName').textContent = categoryName;
        document.getElementById('parentForm').action = `/admin/categories/${categoryId}/set-parent`;

        // Pre-seleccionar el padre actual
        const select = document.getElementById('parentSelect');
        select.value = currentParentId ?? '';

        new bootstrap.Modal(document.getElementById('parentModal')).show();
    }
</script>
@endsection