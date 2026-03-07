@extends('layouts.admin')

@section('content')
    <h1>Listado de productos</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Productos Registrados 
                        <a href="{{ route('admin.products.create') }}" style="float: right" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Nuevo producto
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Búsqueda y Filtros -->
                    <form action="{{ route('admin.products.index') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                        placeholder="Buscar por nombre, código, SKU..." 
                                        value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-x-circle"></i> Limpiar
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-2">
                                <select name="category" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="brand" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas las marcas</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" 
                                            {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos los estados</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="stock_status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Estado de stock</option>
                                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>
                                        En stock
                                    </option>
                                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>
                                        Sin stock
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Acciones rápidas -->
                    <div class="mb-3">
                        <a href="{{ route('admin.products.export') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel"></i> Exportar CSV
                        </a>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">Nro</th>
                                    <th width="80">Imagen</th>
                                    <th>Producto</th>
                                    <th width="100">Código/SKU</th>
                                    <th width="120">Categoría</th>
                                    <th width="100">Marca</th>
                                    <th width="100">Precio</th>
                                    <th width="80">Stock</th>
                                    <th width="80">Estado</th>
                                    <th width="250">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $nro = ($products->currentPage() - 1) * $products->perPage() + 1;
                                @endphp
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="text-center">{{ $nro++ }}</td>
                                        <td>
                                            <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="img-thumbnail" 
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                                            <br>
                                            @if($product->featured)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-star-fill"></i> Destacado
                                                </span>
                                            @endif
                                            @if($product->is_new)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-sparkle"></i> Nuevo
                                                </span>
                                            @endif
                                            @if($product->is_on_sale)
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-tag-fill"></i> {{ $product->discount_percentage }}% OFF
                                                </span>
                                            @endif

                                            @if($product->best_seller)
                                                <span class="badge text-dark" style="background-color: #FFD700;">
                                                    <i class="bi bi-trophy-fill"></i>
                                                    {{ $product->best_seller == 1 ? 'Best Seller #1' : 'Best Seller #' . $product->best_seller }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                <strong>Código:</strong> {{ $product->code }}<br>
                                                <strong>SKU:</strong> {{ $product->sku ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($product->is_on_sale)
                                                <span class="text-decoration-line-through text-muted">
                                                    {{ $settings->badge }}{{ number_format($product->selling_price, 2) }}
                                                </span><br>
                                                <strong class="text-danger">
                                                    {{ $settings->badge }}{{ number_format($product->final_price, 2) }}
                                                </strong>
                                            @else
                                                <strong>{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</strong>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($product->stock > $product->stock_alert)
                                                <span class="badge bg-success">{{ $product->stock }}</span>
                                            @elseif($product->stock > 0)
                                                <span class="badge bg-warning">{{ $product->stock }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $product->stock }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($product->status)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Activo
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle"></i> Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical" role="group" style="width: 100%;">
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>

                                                <a href="{{ route('admin.products.images', $product->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi bi-card-image"></i> Imágenes ({{ $product->images->count() }})
                                                </a>

                                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                                    class="btn btn-success btn-sm">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>

                                                <form action="{{ route('admin.products.duplicate', $product->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-secondary btn-sm w-100">
                                                        <i class="bi bi-files"></i> Duplicar
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm w-100"
                                                            onclick="confirmDelete(this.form)">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="py-5">
                                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                                <p class="text-muted mt-3">No se encontraron productos</p>
                                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                                    <i class="bi bi-plus"></i> Crear primer producto
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($products->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Mostrando de <strong>{{ $products->firstItem() }}</strong> 
                                a <strong>{{ $products->lastItem() }}</strong> 
                                de <strong>{{ $products->total() }}</strong> registros
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
                title: '¿Desea eliminar este producto?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    <style>
        .table td {
            vertical-align: middle;
        }
        .btn-group-vertical .btn {
            margin-bottom: 2px;
        }
    </style>
@endsection