@extends('layouts.admin')

@section('content')
    <h1>Listado de categorías</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Categorías Registradas 
                        <a href="{{ route('admin.categories.create') }}" style="float: right" class="btn btn-primary"><i class="bi bi-plus"></i> Nuevo registro</a>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('admin.categories.index') }}" method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="search" placeholder="Buscar" class="form-control" placeholder="Buscar..." value="{{ $_REQUEST['search'] ?? '' }}">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                                    @if (isset($_REQUEST['search']) && $_REQUEST['search'] != '')
                                        <a href="{{ url('/admin/categories') }}" class="btn btn-secondary"><i class="bi bi-trash"></i> Limpiar</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered table-responsive table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Nombre</th>
                                <th>Slug</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nro = ($categories->currentPage() - 1) * $categories->perPage() + 1;
                            @endphp
                            @foreach ($categories as $category)
                                <tr>
                                    <td style="text-align: center;">{{ $nro++ }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Acciones">
                                            <a href="{{ route('admin.categories.show', $category->id) }}"
                                            class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>

                                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="btn btn-success btn-sm">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>

                                            <form action="{{ route('admin.categories.destroy', $category->id) }}"
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
                    @if($categories->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4 px-3" style="border-b">
                            <div class="text-muted">
                                Mostrando de {{ $categories->firstItem() }} a {{ $categories->lastItem() }} de {{ $categories->total() }} registros
                            </div>
                            <div>
                                {{ $categories->links('pagination::bootstrap-4') }}
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
