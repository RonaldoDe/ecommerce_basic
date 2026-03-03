@extends('layouts.admin')

@section('content')
    <h1>Editar producto: {{ $product->name }}</h1>
    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Datos del producto</h4>
                </div>
                <div class="card-body">
                    {{-- ✅ FORM PRINCIPAL — el modal NO está dentro de aquí --}}
                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" id="productForm">
                        @csrf
                        @method('PUT')

                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                    <i class="bi bi-info-circle"></i> Información Básica
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">
                                    <i class="bi bi-currency-dollar"></i> Precios y Descuentos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
                                    <i class="bi bi-boxes"></i> Inventario
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                                    <i class="bi bi-file-text"></i> Detalles
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                                    <i class="bi bi-search"></i> SEO
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="additional-tab" data-bs-toggle="tab" data-bs-target="#additional" type="button" role="tab">
                                    <i class="bi bi-gear"></i> Adicional
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="variants-tab" data-bs-toggle="tab" data-bs-target="#variants" type="button" role="tab">
                                    <i class="bi bi-grid-3x3-gap"></i> Variantes
                                    @if($product->has_variants)
                                        <span class="badge bg-primary ms-1">{{ $product->variants->count() }}</span>
                                    @endif
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content p-3" id="productTabsContent">

                            <!-- INFORMACIÓN BÁSICA -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name">Nombre del producto <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-box"></i></span>
                                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    placeholder="Ej: Laptop HP Elite">
                                                @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="code">Código <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                                <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}"
                                                    class="form-control @error('code') is-invalid @enderror" placeholder="PROD001">
                                                @error('code')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="sku">SKU</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                                                    class="form-control @error('sku') is-invalid @enderror" placeholder="Auto-generado">
                                                @error('sku')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="category_id">Categoría <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                                                <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="category_id">
                                                    <option value="" disabled>Seleccione una categoría</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="brand_id">Marca</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-award"></i></span>
                                                <select class="form-control @error('brand_id') is-invalid @enderror" name="brand_id" id="brand_id">
                                                    <option value="">Sin marca</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                            {{ $brand->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('brand_id')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="short_description">Descripción corta <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-text-left"></i></span>
                                                <input type="text" name="short_description" id="short_description"
                                                    value="{{ old('short_description', $product->short_description) }}"
                                                    class="form-control @error('short_description') is-invalid @enderror"
                                                    placeholder="Descripción breve del producto" maxlength="255">
                                                @error('short_description')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="long_description">Descripción detallada <span class="text-danger">*</span></label>
                                            <textarea name="long_description" id="long_description"
                                                class="form-control @error('long_description') is-invalid @enderror">{{ old('long_description', $product->long_description) }}</textarea>
                                            @error('long_description')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PRECIOS Y DESCUENTOS -->
                            <div class="tab-pane fade" id="pricing" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="cost_price">Precio de compra <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                                <input type="number" name="cost_price" id="cost_price"
                                                    value="{{ old('cost_price', $product->cost_price) }}"
                                                    class="form-control @error('cost_price') is-invalid @enderror"
                                                    placeholder="0.00" step="0.01" min="0">
                                                @error('cost_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="selling_price">Precio de venta <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                                <input type="number" name="selling_price" id="selling_price"
                                                    value="{{ old('selling_price', $product->selling_price) }}"
                                                    class="form-control @error('selling_price') is-invalid @enderror"
                                                    placeholder="0.00" step="0.01" min="0">
                                                @error('selling_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mt-3">Descuento</h5>
                                <hr>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="discount_percentage">Porcentaje de descuento (%)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                                <input type="number" name="discount_percentage" id="discount_percentage"
                                                    value="{{ old('discount_percentage', $product->discount_percentage) }}"
                                                    class="form-control @error('discount_percentage') is-invalid @enderror"
                                                    placeholder="0" step="0.01" min="0" max="100">
                                                @error('discount_percentage')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="discount_start_date">Fecha inicio descuento</label>
                                            <input type="datetime-local" name="discount_start_date" id="discount_start_date"
                                                value="{{ old('discount_start_date', $product->discount_start_date ? $product->discount_start_date->format('Y-m-d\TH:i') : '') }}"
                                                class="form-control @error('discount_start_date') is-invalid @enderror">
                                            @error('discount_start_date')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="discount_end_date">Fecha fin descuento</label>
                                            <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                                value="{{ old('discount_end_date', $product->discount_end_date ? $product->discount_end_date->format('Y-m-d\TH:i') : '') }}"
                                                class="form-control @error('discount_end_date') is-invalid @enderror">
                                            @error('discount_end_date')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                @if($product->is_on_sale)
                                    <div class="alert alert-info">
                                        <strong><i class="bi bi-info-circle"></i> Descuento activo</strong><br>
                                        Precio final: <strong>${{ number_format($product->final_price, 2) }}</strong>
                                    </div>
                                @endif
                            </div>

                            <!-- INVENTARIO -->
                            <div class="tab-pane fade" id="inventory" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="stock">Stock <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                                <input type="number" name="stock" id="stock"
                                                    value="{{ old('stock', $product->stock) }}"
                                                    class="form-control @error('stock') is-invalid @enderror"
                                                    placeholder="0" step="1" min="0"
                                                    {{ $product->has_variants ? 'readonly' : '' }}>
                                                @error('stock')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                            @if($product->has_variants)
                                                <small class="text-muted">Calculado desde variantes</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="stock_alert">Alerta de stock bajo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                                <input type="number" name="stock_alert" id="stock_alert"
                                                    value="{{ old('stock_alert', $product->stock_alert) }}"
                                                    class="form-control @error('stock_alert') is-invalid @enderror"
                                                    placeholder="10" step="1" min="0">
                                                @error('stock_alert')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                            <small class="text-muted">Cantidad mínima antes de recibir alerta</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="stock_status">Estado del stock</label>
                                            <select class="form-control @error('stock_status') is-invalid @enderror" name="stock_status" id="stock_status">
                                                <option value="in_stock" {{ old('stock_status', $product->stock_status) == 'in_stock' ? 'selected' : '' }}>En stock</option>
                                                <option value="out_of_stock" {{ old('stock_status', $product->stock_status) == 'out_of_stock' ? 'selected' : '' }}>Sin stock</option>
                                                <option value="on_backorder" {{ old('stock_status', $product->stock_status) == 'on_backorder' ? 'selected' : '' }}>En pedido pendiente</option>
                                            </select>
                                            @error('stock_status')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="manage_stock"
                                                id="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manage_stock">Gestionar inventario automáticamente</label>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mt-3">Dimensiones y Peso</h5>
                                <hr>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="weight">Peso (kg)</label>
                                            <input type="number" name="weight" id="weight"
                                                value="{{ old('weight', $product->weight) }}"
                                                class="form-control @error('weight') is-invalid @enderror"
                                                placeholder="0.00" step="0.01" min="0">
                                            @error('weight')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="length">Largo (cm)</label>
                                            <input type="number" name="length" id="length"
                                                value="{{ old('length', $dimensions['length'] ?? '') }}"
                                                class="form-control @error('length') is-invalid @enderror"
                                                placeholder="0.00" step="0.01" min="0">
                                            @error('length')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="width">Ancho (cm)</label>
                                            <input type="number" name="width" id="width"
                                                value="{{ old('width', $dimensions['width'] ?? '') }}"
                                                class="form-control @error('width') is-invalid @enderror"
                                                placeholder="0.00" step="0.01" min="0">
                                            @error('width')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="height">Alto (cm)</label>
                                            <input type="number" name="height" id="height"
                                                value="{{ old('height', $dimensions['height'] ?? '') }}"
                                                class="form-control @error('height') is-invalid @enderror"
                                                placeholder="0.00" step="0.01" min="0">
                                            @error('height')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DETALLES -->
                            <div class="tab-pane fade" id="details" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="status"
                                                id="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status"><strong>Producto activo</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="featured"
                                                id="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featured"><strong>Producto destacado</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_new"
                                                id="is_new" value="1" {{ old('is_new', $product->is_new) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_new"><strong>Producto nuevo</strong></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="visibility">Visibilidad</label>
                                            <select class="form-control @error('visibility') is-invalid @enderror" name="visibility" id="visibility">
                                                <option value="public" {{ old('visibility', $product->visibility) == 'public' ? 'selected' : '' }}>Público (visible para todos)</option>
                                                <option value="catalog" {{ old('visibility', $product->visibility) == 'catalog' ? 'selected' : '' }}>Solo catálogo (sin búsqueda)</option>
                                                <option value="private" {{ old('visibility', $product->visibility) == 'private' ? 'selected' : '' }}>Privado (oculto)</option>
                                            </select>
                                            @error('visibility')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="published_at">Fecha de publicación</label>
                                            <input type="datetime-local" name="published_at" id="published_at"
                                                value="{{ old('published_at', $product->published_at ? $product->published_at->format('Y-m-d\TH:i') : '') }}"
                                                class="form-control @error('published_at') is-invalid @enderror">
                                            @error('published_at')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Dejar vacío para publicar inmediatamente</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="tags">Tags (separados por comas)</label>
                                            <input type="text" name="tags" id="tags"
                                                value="{{ old('tags', $product->tags) }}"
                                                class="form-control @error('tags') is-invalid @enderror"
                                                placeholder="nuevo, oferta, premium">
                                            @error('tags')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Ej: nuevo, oferta, premium, electrónico</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="meta_title">Meta título</label>
                                            <input type="text" name="meta_title" id="meta_title"
                                                value="{{ old('meta_title', $product->meta_title) }}"
                                                class="form-control @error('meta_title') is-invalid @enderror"
                                                placeholder="Título para motores de búsqueda" maxlength="60">
                                            @error('meta_title')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Máximo 60 caracteres</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="meta_description">Meta descripción</label>
                                            <textarea name="meta_description" id="meta_description" rows="3"
                                                class="form-control @error('meta_description') is-invalid @enderror"
                                                placeholder="Descripción para motores de búsqueda" maxlength="160">{{ old('meta_description', $product->meta_description) }}</textarea>
                                            @error('meta_description')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Máximo 160 caracteres</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="meta_keywords">Meta palabras clave</label>
                                            <input type="text" name="meta_keywords" id="meta_keywords"
                                                value="{{ old('meta_keywords', $product->meta_keywords) }}"
                                                class="form-control @error('meta_keywords') is-invalid @enderror"
                                                placeholder="laptop, computadora, tecnología">
                                            @error('meta_keywords')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Separadas por comas</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ADICIONAL -->
                            <div class="tab-pane fade" id="additional" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="warranty">Garantía</label>
                                            <textarea name="warranty" id="warranty" rows="2"
                                                class="form-control @error('warranty') is-invalid @enderror"
                                                placeholder="Información sobre la garantía del producto">{{ old('warranty', $product->warranty) }}</textarea>
                                            @error('warranty')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="return_policy">Política de devolución</label>
                                            <textarea name="return_policy" id="return_policy" rows="2"
                                                class="form-control @error('return_policy') is-invalid @enderror"
                                                placeholder="Política de devolución del producto">{{ old('return_policy', $product->return_policy) }}</textarea>
                                            @error('return_policy')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="shipping_info">Información de envío</label>
                                            <textarea name="shipping_info" id="shipping_info" rows="2"
                                                class="form-control @error('shipping_info') is-invalid @enderror"
                                                placeholder="Información sobre el envío">{{ old('shipping_info', $product->shipping_info) }}</textarea>
                                            @error('shipping_info')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VARIANTES -->
                            <div class="tab-pane fade" id="variants" role="tabpanel">
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="has_variants"
                                            id="has_variants" value="1"
                                            {{ $product->has_variants ? 'checked' : '' }}
                                            onchange="toggleVariants(this.checked)">
                                        <label class="form-check-label fw-bold" for="has_variants">
                                            Este producto tiene variantes (tallas, colores, etc.)
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Al activar variantes, el stock se calculará automáticamente desde la suma de las variantes.
                                    </small>
                                </div>

                                <div id="variantsSection" style="{{ $product->has_variants ? '' : 'display:none' }}">

                                    {{-- Configurar atributos --}}
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="bi bi-sliders"></i> Atributos de variantes</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small mb-3">
                                                Define los nombres de los atributos (ej: Color, Talla). Cada variante tendrá un valor para cada atributo.
                                            </p>
                                            <div id="attributeNames"></div>
                                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addAttributeName()">
                                                <i class="bi bi-plus"></i> Agregar atributo
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Listado de variantes --}}
                                    <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="bi bi-list-ul"></i> Variantes registradas
                                                <span class="badge bg-primary ms-1" id="variantCount">{{ $product->variants->count() }}</span>
                                            </h6>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="openVariantModal()">
                                                <i class="bi bi-plus"></i> Nueva variante
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Imagen</th>
                                                            <th>Variante</th>
                                                            <th>SKU</th>
                                                            <th class="text-center">Precio</th>
                                                            <th class="text-center">Stock</th>
                                                            <th class="text-center">Estado</th>
                                                            <th class="text-center">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="variantsBody">
                                                        @forelse($product->variants as $variant)
                                                            <tr id="variant-row-{{ $variant->id }}">
                                                                <td>
                                                                    <img src="{{ $variant->image_url }}" alt="{{ $variant->label }}"
                                                                        class="rounded" style="width:45px;height:45px;object-fit:cover;">
                                                                </td>
                                                                <td class="fw-semibold">{{ $variant->label }}</td>
                                                                <td><code>{{ $variant->sku ?? '—' }}</code></td>
                                                                <td class="text-center">
                                                                    @if($variant->price)
                                                                        ${{ number_format($variant->price, 2) }}
                                                                    @else
                                                                        <span class="text-muted small">Del producto</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge {{ $variant->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ $variant->stock }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge {{ $variant->status ? 'bg-success' : 'bg-secondary' }}">
                                                                        {{ $variant->status ? 'Activa' : 'Inactiva' }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-success btn-sm"
                                                                            onclick="editVariant({{ $variant->id }}, {{ json_encode($variant) }})">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                            onclick="deleteVariant({{ $variant->id }})">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr id="noVariantsRow">
                                                                <td colspan="7" class="text-center text-muted py-4">
                                                                    No hay variantes. Agrega la primera.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @if($product->has_variants)
                                            <div class="card-footer text-muted small">
                                                Stock total: <strong>{{ $product->variants->sum('stock') }} unidades</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>{{-- /tab-content --}}

                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info">
                                        <i class="bi bi-eye"></i> Ver Producto
                                    </a>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save"></i> Actualizar Producto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>{{-- ✅ FIN del form principal --}}

                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Modal FUERA del form principal — aquí es donde debe estar --}}
    <div class="modal fade" id="variantModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="variantModalTitle">
                        <i class="bi bi-grid-3x3-gap"></i> Nueva variante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- ✅ Este form es independiente, no está anidado --}}
                    <form id="variantForm" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="fw-bold">Atributos <span class="text-danger">*</span></label>
                            <div id="variantAttributeFields" class="row g-2 mt-1">
                                {{-- Llenado por JS --}}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">SKU</label>
                                <input type="text" id="v_sku" name="sku" class="form-control" placeholder="Auto-generado">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Precio (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="v_price" name="price" class="form-control" placeholder="Del producto" step="0.01" min="0">
                                </div>
                                <small class="text-muted">Vacío = usa precio del producto</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Precio costo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="v_cost_price" name="cost_price" class="form-control" placeholder="0.00" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Stock <span class="text-danger">*</span></label>
                                <input type="number" id="v_stock" name="stock" class="form-control" value="0" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <select id="v_status" name="status" class="form-select">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Imagen</label>
                                <input type="file" id="v_image" name="image" class="form-control"
                                    accept="image/*" onchange="previewVariantImage(this)">
                            </div>
                        </div>

                        <div id="v_imagePreview" class="mb-3 d-none">
                            <img id="v_previewImg" src="" alt="Preview" class="rounded border" style="max-height:100px;">
                            <div id="v_removeImageCheck" class="form-check mt-1 d-none">
                                <input class="form-check-input" type="checkbox" id="v_remove_image" name="remove_image" value="1">
                                <label class="form-check-label text-danger" for="v_remove_image">Eliminar imagen actual</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveVariant()">
                        <i class="bi bi-save"></i> Guardar variante
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del producto -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Estadísticas del Producto</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="bi bi-eye text-primary" style="font-size: 2rem;"></i>
                                <h3>{{ number_format($product->views_count) }}</h3>
                                <p class="text-muted">Vistas</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="bi bi-cart text-success" style="font-size: 2rem;"></i>
                                <h3>{{ number_format($product->sales_count) }}</h3>
                                <p class="text-muted">Ventas</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="bi bi-heart text-danger" style="font-size: 2rem;"></i>
                                <h3>{{ number_format($product->wishlist_count) }}</h3>
                                <p class="text-muted">Favoritos</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="bi bi-star-fill text-warning" style="font-size: 2rem;"></i>
                                <h3>{{ number_format($product->rating, 2) }}</h3>
                                <p class="text-muted">Rating ({{ $product->reviews_count }} reseñas)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ClassicEditor.create(document.querySelector('#long_description'), {
                toolbar: {
                    items: ['heading','|','bold','italic','underline','|','link','bulletedList','numberedList','|','blockQuote','insertTable','|','undo','redo'],
                    shouldNotGroupWhenFull: true
                },
                language: 'es',
            }).catch(console.error);

            const sellingPrice      = document.getElementById('selling_price');
            const discountPct       = document.getElementById('discount_percentage');
            function calcDiscount() {
                const price    = parseFloat(sellingPrice.value) || 0;
                const discount = parseFloat(discountPct.value) || 0;
                if (price > 0 && discount > 0) {
                    console.log('Precio final:', (price - price * discount / 100).toFixed(2));
                }
            }
            sellingPrice.addEventListener('input', calcDiscount);
            discountPct.addEventListener('input', calcDiscount);
        });
    </script>

    <style>
        .nav-tabs .nav-link { color: #6c757d; }
        .nav-tabs .nav-link.active { color: #0d6efd; font-weight: 600; }
        .stat-box { padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; transition: all 0.3s; }
        .stat-box:hover { box-shadow: 0 4px 15px rgba(0,0,0,.1); transform: translateY(-5px); }
        .stat-box h3 { margin: 10px 0 5px; font-weight: bold; }
    </style>

@push('scripts')
<script>
// ================================================================
// VARIANTES
// ================================================================
const PRODUCT_ID   = {{ $product->id }};
const VARIANTS_URL = `/admin/products/${PRODUCT_ID}/variants`;
let editingVariantId = null;
let attributeNames   = @json(
    $product->variants->count()
        ? $product->variants->flatMap(fn($v) => array_keys($v->attributes ?? []))->unique()->values()
        : []
);

// ---- Toggle sección variantes ----
function toggleVariants(checked) {
    document.getElementById('variantsSection').style.display = checked ? '' : 'none';
    if (checked && attributeNames.length === 0) addAttributeName();
    renderAttributeNames();
}

// ---- Nombres de atributos ----
function renderAttributeNames() {
    const container = document.getElementById('attributeNames');
    if (!container) return;
    container.innerHTML = attributeNames.map((name, i) => `
        <div class="input-group mb-2" style="max-width:300px">
            <span class="input-group-text"><i class="bi bi-tag"></i></span>
            <input type="text" class="form-control attr-name" value="${name}"
                   placeholder="Ej: Color, Talla..."
                   oninput="attributeNames[${i}] = this.value">
            <button type="button" class="btn btn-outline-danger" onclick="removeAttributeName(${i})">
                <i class="bi bi-x"></i>
            </button>
        </div>`).join('');
}

function addAttributeName() {
    attributeNames.push('');
    renderAttributeNames();
    setTimeout(() => {
        const inputs = document.querySelectorAll('.attr-name');
        if (inputs.length) inputs[inputs.length - 1].focus();
    }, 50);
}

function removeAttributeName(i) {
    attributeNames.splice(i, 1);
    renderAttributeNames();
}

// ---- Modal de variante ----
function openVariantModal(variantData = null) {
    editingVariantId = variantData ? variantData.id : null;

    document.getElementById('variantModalTitle').innerHTML = variantData
        ? '<i class="bi bi-pencil"></i> Editar variante'
        : '<i class="bi bi-plus"></i> Nueva variante';

    document.getElementById('variantForm').reset();
    document.getElementById('v_imagePreview').classList.add('d-none');
    document.getElementById('v_removeImageCheck').classList.add('d-none');

    // Actualizar atributos desde los inputs actuales antes de abrir
    attributeNames = Array.from(document.querySelectorAll('.attr-name'))
                         .map(i => i.value).filter(v => v.trim() !== '');

    renderVariantAttributeFields(variantData?.attributes ?? {});

    if (variantData) {
        document.getElementById('v_sku').value        = variantData.sku ?? '';
        document.getElementById('v_price').value      = variantData.price ?? '';
        document.getElementById('v_cost_price').value = variantData.cost_price ?? '';
        document.getElementById('v_stock').value      = variantData.stock;
        document.getElementById('v_status').value     = variantData.status ? '1' : '0';
        if (variantData.image) {
            document.getElementById('v_previewImg').src = `/storage/${variantData.image}`;
            document.getElementById('v_imagePreview').classList.remove('d-none');
            document.getElementById('v_removeImageCheck').classList.remove('d-none');
        }
    }

    new bootstrap.Modal(document.getElementById('variantModal')).show();
}

function editVariant(id, variantData) {
    openVariantModal(variantData);
}

function renderVariantAttributeFields(currentValues = {}) {
    const names     = attributeNames.filter(n => n.trim() !== '');
    const container = document.getElementById('variantAttributeFields');
    if (!container) return;

    if (names.length === 0) {
        container.innerHTML = '<p class="text-muted small col-12">Define los atributos arriba primero.</p>';
        return;
    }

    container.innerHTML = names.map(name => `
        <div class="col-md-4">
            <label class="form-label">${name} <span class="text-danger">*</span></label>
            <input type="text" name="attributes[${name}]" class="form-control"
                   value="${currentValues[name] ?? ''}"
                   placeholder="Ej: Rojo, XL..." required>
        </div>`).join('');
}

function previewVariantImage(input) {
    if (input.files && input.files[0]) {
        document.getElementById('v_previewImg').src = URL.createObjectURL(input.files[0]);
        document.getElementById('v_imagePreview').classList.remove('d-none');
    }
}

async function saveVariant() {
    const form     = document.getElementById('variantForm');
    const formData = new FormData(form);
    if (editingVariantId) formData.append('_method', 'PUT');

    const url = editingVariantId
        ? `${VARIANTS_URL}/${editingVariantId}`
        : VARIANTS_URL;

    try {
        const res  = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire({ icon: 'success', title: data.message, timer: 1500, showConfirmButton: false });
            bootstrap.Modal.getInstance(document.getElementById('variantModal')).hide();
            setTimeout(() => location.reload(), 1600);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.message });
    }
}

async function deleteVariant(id) {
    const result = await Swal.fire({
        title: '¿Eliminar variante?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });
    if (!result.isConfirmed) return;

    const res  = await fetch(`${VARIANTS_URL}/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({ _method: 'DELETE' }),
    });
    const data = await res.json();

    if (data.success) {
        document.getElementById(`variant-row-${id}`)?.remove();
        Swal.fire({ icon: 'success', title: data.message, timer: 1500, showConfirmButton: false });
    } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (attributeNames.length > 0) renderAttributeNames();
});
</script>
<script>
    // Activar tab por URL param (?tab=variants)
document.addEventListener('DOMContentLoaded', function () {
    const params  = new URLSearchParams(window.location.search);
    const tabName = params.get('tab');

    if (tabName) {
        const tabEl = document.querySelector(`#${tabName}-tab`);
        if (tabEl) {
            // Desactivar el tab activo actual
            document.querySelectorAll('.nav-link.active').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane.show.active').forEach(p => {
                p.classList.remove('show', 'active');
            });

            // Activar el tab solicitado
            tabEl.classList.add('active');
            const pane = document.querySelector(tabEl.getAttribute('data-bs-target'));
            if (pane) pane.classList.add('show', 'active');
        }
    }
});
</script>
@endpush
@endsection