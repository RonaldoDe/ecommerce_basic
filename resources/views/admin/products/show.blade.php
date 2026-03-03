@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Detalle del Producto</h1>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-success">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <hr> 

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="bi bi-info-circle"></i> Información Básica</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Nombre del producto</label>
                            <h5>{{ $product->name }}</h5>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted">Código</label>
                            <p class="mb-0"><code>{{ $product->code }}</code></p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted">SKU</label>
                            <p class="mb-0"><code>{{ $product->sku }}</code></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Categoría</label>
                            <p><i class="bi bi-tag-fill text-primary"></i> {{ $product->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Marca</label>
                            <p><i class="bi bi-award text-warning"></i> {{ $product->brand->name ?? 'Sin marca' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted">Descripción corta</label>
                            <p>{{ $product->short_description }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="text-muted">Descripción detallada</label>
                            <div class="border p-3 rounded bg-light">
                                {!! $product->long_description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Precios -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-currency-dollar"></i> Precios</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted">Precio de compra</label>
                            <h5>{{ $settings->badge }}{{ number_format($product->cost_price, 2) }}</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">Precio de venta</label>
                            <h5>{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">Precio final</label>
                            @if($product->is_on_sale)
                                <h5 class="text-danger">
                                    {{ $settings->badge }}{{ number_format($product->final_price, 2) }}
                                    <span class="badge bg-danger">-{{ $product->discount_percentage }}%</span>
                                </h5>
                            @else
                                <h5>{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</h5>
                            @endif
                        </div>
                    </div>

                    @if($product->is_on_sale)
                        <hr>
                        <div class="alert alert-info mb-0">
                            <strong><i class="bi bi-tag-fill"></i> Descuento activo:</strong>
                            {{ $product->discount_percentage }}% de descuento
                            @if($product->discount_start_date && $product->discount_end_date)
                                <br>
                                <small>
                                    Válido desde {{ $product->discount_start_date->format('d/m/Y H:i') }}
                                    hasta {{ $product->discount_end_date->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Inventario -->
            <div class="card mb-3">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="bi bi-boxes"></i> Inventario</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="text-muted">Stock actual</label>
                            @if($product->stock > $product->stock_alert)
                                <h4><span class="badge bg-success">{{ $product->stock }}</span></h4>
                            @elseif($product->stock > 0)
                                <h4><span class="badge bg-warning">{{ $product->stock }}</span></h4>
                            @else
                                <h4><span class="badge bg-danger">{{ $product->stock }}</span></h4>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted">Alerta de stock</label>
                            <p>{{ $product->stock_alert }} unidades</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted">Estado</label>
                            <p>
                                @switch($product->stock_status)
                                    @case('in_stock')
                                        <span class="badge bg-success">En stock</span>
                                        @break
                                    @case('out_of_stock')
                                        <span class="badge bg-danger">Sin stock</span>
                                        @break
                                    @case('on_backorder')
                                        <span class="badge bg-warning">En pedido</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted">Gestión de stock</label>
                            <p>
                                @if($product->manage_stock)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activa</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactiva</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($product->needsRestock())
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Atención:</strong> El stock está por debajo del nivel de alerta.
                        </div>
                    @endif

                    @if($product->dimensions || $product->weight)
                        <hr>
                        <h6 class="text-muted">Dimensiones y Peso</h6>
                        <div class="row">
                            @if($product->weight)
                                <div class="col-md-3">
                                    <label class="text-muted">Peso</label>
                                    <p>{{ $product->weight }} kg</p>
                                </div>
                            @endif
                            @if($product->dimensions)
                                <div class="col-md-3">
                                    <label class="text-muted">Largo</label>
                                    <p>{{ $product->dimensions['length'] ?? 'N/A' }} cm</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted">Ancho</label>
                                    <p>{{ $product->dimensions['width'] ?? 'N/A' }} cm</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted">Alto</label>
                                    <p>{{ $product->dimensions['height'] ?? 'N/A' }} cm</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información Adicional -->
            @if($product->warranty || $product->return_policy || $product->shipping_info)
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="bi bi-info-square"></i> Información Adicional</h4>
                    </div>
                    <div class="card-body">
                        @if($product->warranty)
                            <div class="mb-3">
                                <label class="text-muted"><i class="bi bi-shield-check"></i> Garantía</label>
                                <p>{{ $product->warranty }}</p>
                            </div>
                        @endif

                        @if($product->return_policy)
                            <div class="mb-3">
                                <label class="text-muted"><i class="bi bi-arrow-return-left"></i> Política de devolución</label>
                                <p>{{ $product->return_policy }}</p>
                            </div>
                        @endif

                        @if($product->shipping_info)
                            <div class="mb-0">
                                <label class="text-muted"><i class="bi bi-truck"></i> Información de envío</label>
                                <p>{{ $product->shipping_info }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Estado -->
            <div class="card mb-3">
                <div class="card-header bg-light text-white">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Estado del Producto</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Estado general</label>
                        <p>
                            @if($product->status)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactivo</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Visibilidad</label>
                        <p>
                            @switch($product->visibility)
                                @case('public')
                                    <span class="badge bg-success">Público</span>
                                    @break
                                @case('catalog')
                                    <span class="badge bg-warning">Solo catálogo</span>
                                    @break
                                @case('private')
                                    <span class="badge bg-danger">Privado</span>
                                    @break
                            @endswitch
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Características</label>
                        <div>
                            @if($product->featured)
                                <span class="badge bg-warning text-dark mb-1">
                                    <i class="bi bi-star-fill"></i> Destacado
                                </span>
                            @endif
                            @if($product->is_new)
                                <span class="badge bg-success mb-1">
                                    <i class="bi bi-sparkle"></i> Nuevo
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($product->published_at)
                        <div class="mb-0">
                            <label class="text-muted">Fecha de publicación</label>
                            <p>{{ $product->published_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card mb-3">
                <div class="card-header bg-light text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="bi bi-eye"></i> Vistas: <strong>{{ number_format($product->views_count) }}</strong>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-cart"></i> Ventas: <strong>{{ number_format($product->sales_count) }}</strong>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-heart"></i> En listas de deseos: <strong>{{ number_format($product->wishlist_count) }}</strong>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-star-fill text-warning"></i> Rating: 
                        <strong>{{ number_format($product->rating, 2) }}</strong>
                        ({{ $product->reviews_count }} reseñas)
                    </div>
                </div>
            </div>

            <!-- SEO -->
            @if($product->meta_title || $product->meta_description || $product->meta_keywords)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-search"></i> SEO</h5>
                    </div>
                    <div class="card-body">
                        @if($product->meta_title)
                            <div class="mb-2">
                                <label class="text-muted">Meta título</label>
                                <p class="small">{{ $product->meta_title }}</p>
                            </div>
                        @endif

                        @if($product->meta_description)
                            <div class="mb-2">
                                <label class="text-muted">Meta descripción</label>
                                <p class="small">{{ $product->meta_description }}</p>
                            </div>
                        @endif

                        @if($product->meta_keywords)
                            <div class="mb-0">
                                <label class="text-muted">Palabras clave</label>
                                <p class="small">{{ $product->meta_keywords }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($product->tags)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-tags"></i> Tags</h5>
                    </div>
                    <div class="card-body">
                        @foreach(explode(',', $product->tags) as $tag)
                            <span class="badge bg-primary mb-1">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Fechas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Registro</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Creado:</strong><br>
                        {{ $product->created_at->format('d/m/Y H:i') }}<br>
                        <strong>Actualizado:</strong><br>
                        {{ $product->updated_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Imágenes del producto -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-images"></i> Imágenes del producto ({{ $product->images->count() }})
                        <a href="{{ route('admin.products.images', $product->id) }}" class="btn btn-light btn-sm float-end">
                            <i class="bi bi-pencil"></i> Gestionar imágenes
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    @if($product->images->count() > 0)
                        <div class="row">
                            @foreach ($product->images as $image)
                                <div class="col-md-2 mb-3">
                                    <div class="card">
                                        <img src="{{ asset('storage/'.$image->image) }}" 
                                            class="card-img-top" 
                                            alt="{{ $product->name }}"
                                            style="width: 100%; object-fit: cover;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No hay imágenes cargadas</p>
                            <a href="{{ route('admin.products.images', $product->id) }}" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Subir imágenes
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Variantes --}}
    @if($product->has_variants && $product->variants->count())
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-grid-3x3-gap"></i> Variantes
                        <span class="badge bg-secondary ms-2">{{ $product->variants->count() }}</span>
                    </h4>
                    <div>
                        <span class="me-3 small">
                            Stock total:
                            <strong class="badge bg-success fs-6">{{ $product->variants->sum('stock') }} uds.</strong>
                        </span>
                        <a href="{{ route('admin.products.edit', $product->id) }}?tab=variants"
                        class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Gestionar variantes
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">Imagen</th>
                                    <th>Variante</th>
                                    <th>SKU</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Costo</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr class="{{ !$variant->status ? 'table-secondary opacity-75' : '' }}">
                                        <td>
                                            <img src="{{ $variant->image_url }}"
                                                alt="{{ $variant->label }}"
                                                class="rounded border"
                                                style="width:45px;height:45px;object-fit:cover;">
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $variant->label }}</span>
                                            {{-- Mostrar atributos como badges --}}
                                            @if($variant->getAttribute('attributes'))
                                                <div class="mt-1">
                                                    @foreach($variant->getAttribute('attributes') as $key => $value)
                                                        <span class="badge bg-light text-dark border me-1">
                                                            <small class="text-muted">{{ $key }}:</small> {{ $value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td><code>{{ $variant->sku ?? '—' }}</code></td>
                                        <td class="text-center">
                                            @if($variant->price)
                                                <strong>{{ $settings->badge }}{{ number_format($variant->price, 2) }}</strong>
                                            @else
                                                <span class="text-muted small">
                                                    {{ $settings->badge }}{{ number_format($product->selling_price, 2) }}
                                                    <br><small class="text-muted">(del producto)</small>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($variant->cost_price)
                                                {{ $settings->badge }}{{ number_format($variant->cost_price, 2) }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($variant->stock > ($product->stock_alert ?? 0))
                                                <span class="badge bg-success fs-6">{{ $variant->stock }}</span>
                                            @elseif($variant->stock > 0)
                                                <span class="badge bg-warning fs-6">{{ $variant->stock }}</span>
                                            @else
                                                <span class="badge bg-danger fs-6">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($variant->status)
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-secondary">Inactiva</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">Stock total:</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6">
                                            {{ $product->variants->where('status', true)->sum('stock') }}
                                        </span>
                                        <br><small class="text-muted fw-normal">(activas)</small>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($product->has_variants && $product->variants->count() === 0)
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Este producto tiene variantes activadas pero <strong>no hay variantes registradas</strong>.
                <a href="{{ route('admin.products.edit', $product->id) }}" class="alert-link ms-1">
                    Ir a editar para agregar variantes
                </a>
            </div>
        </div>
    </div>
    @endif
@endsection