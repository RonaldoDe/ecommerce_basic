@extends('layouts.admin')

@section('content')
    <h1>Listado de productos</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Productos Registrados 
                        <a href="{{ route('admin.products.create') }}" style="float: right" class="btn btn-primary"><i class="bi bi-plus"></i> Nuevo registro</a>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('admin.products.index') }}" method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="search" placeholder="Buscar" class="form-control" placeholder="Buscar..." value="{{ $_REQUEST['search'] ?? '' }}">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                                    @if (isset($_REQUEST['search']) && $_REQUEST['search'] != '')
                                        <a href="{{ url('/admin/products') }}" class="btn btn-secondary"><i class="bi bi-trash"></i> Limpiar</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table table-bordered table-responsive table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>Categoría</th>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Descripción corta</th>
                                <th>Precio de compra</th>
                                <th>Precio de venta</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nro = ($products->currentPage() - 1) * $products->perPage() + 1;
                            @endphp
                            @foreach ($products as $product)
                                <tr>
                                    <td style="text-align: center;">{{ $nro++ }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->short_description }}</td>
                                    <td>{{ $settings->badge.' '.$product->cost_price }}</td>
                                    <td>{{ $settings->badge.' '.$product->selling_price }}</td>
                                    <td style="text-align: center">{{ $product->stock }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Acciones">
                                            <a href="{{ route('admin.products.show', $product->id) }}"
                                            class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>

                                            <a href="{{ route('admin.products.images', $product->id) }}"
                                            class="btn btn-warning btn-sm">
                                                <i class="bi bi-card-image"></i> Imágenes
                                            </a>

                                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-success btn-sm">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>

                                            <form action="{{ route('admin.products.destroy', $product->id) }}"
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
                    @if($products->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4 px-3" style="border-b">
                            <div class="text-muted">
                                Mostrando de {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} registros
                            </div>
                            <div>
                                {{ $products->links('pagination::bootstrap-4') }}
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
