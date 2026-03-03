@extends('layouts.admin')

@section('content')
<h1>Categoría: {{ $category->name }}</h1>
<hr>

<div class="row">
    {{-- Info principal --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Información</h5>
                @if($category->status)
                    <span class="badge bg-success">Activa</span>
                @else
                    <span class="badge bg-danger">Inactiva</span>
                @endif
            </div>
            <div class="card-body text-center">
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                     class="rounded mb-3 border"
                     style="width:120px;height:120px;object-fit:cover;">

                <div class="text-start">
                    <div class="mb-2">
                        <label class="text-muted small">Nombre</label>
                        <p class="mb-0 fw-semibold"><i class="bi bi-tag me-1"></i>{{ $category->name }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Slug</label>
                        <p class="mb-0"><code>{{ $category->slug }}</code></p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Categoría padre</label>
                        <p class="mb-0">
                            @if($category->parent)
                                <span class="badge bg-secondary">{{ $category->parent->name }}</span>
                            @else
                                <span class="text-muted">Categoría principal</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Orden</label>
                        <p class="mb-0"><i class="bi bi-sort-numeric-up me-1"></i>{{ $category->order }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Descripción</label>
                        <p class="mb-0">{{ $category->description ?? '—' }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Creada</label>
                        <p class="mb-0">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                @can('categories.edit')
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Subcategorías --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-diagram-3 me-1"></i> Subcategorías</h5>
                <span class="badge bg-info text-dark">{{ $category->children->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($category->children->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder2-open" style="font-size:2rem"></i>
                        <p class="mt-2">Esta categoría no tiene subcategorías.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Slug</th>
                                    <th class="text-center">Productos</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->children as $child)
                                    <tr>
                                        <td>
                                            <img src="{{ $child->image_url }}" alt="{{ $child->name }}"
                                                 class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                        </td>
                                        <td class="fw-semibold">{{ $child->name }}</td>
                                        <td><code>{{ $child->slug }}</code></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $child->products_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($child->status)
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-danger">Inactiva</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @can('categories.show')
                                                <a href="{{ route('admin.categories.show', $child->id) }}"
                                                   class="btn btn-info btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endcan
                                            @can('categories.edit')
                                                <a href="{{ route('admin.categories.edit', $child->id) }}"
                                                   class="btn btn-success btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @can('categories.create')
                <div class="card-footer">
                    <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Agregar subcategoría
                    </a>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection