@extends('layouts.admin')

@section('content')
    <h1>Crear un nuevo producto</h1>
    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Datos del producto</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" id="productForm">
                        @csrf

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
                                                <input type="text" name="name" id="name" value="{{ old('name') }}"
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
                                                <input type="text" name="code" id="code" value="{{ old('code') }}"
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
                                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                                                    class="form-control @error('sku') is-invalid @enderror" placeholder="Auto-generado">
                                                @error('sku')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                            <small class="text-muted">Dejar vacío para generar automáticamente</small>
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
                                                    <option value="" selected disabled>Seleccione una categoría</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                                                    value="{{ old('short_description') }}"
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
                                                class="form-control @error('long_description') is-invalid @enderror">{{ old('long_description') }}</textarea>
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
                                                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}"
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
                                                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}"
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
                                                    value="{{ old('discount_percentage', 0) }}"
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
                                                value="{{ old('discount_start_date') }}"
                                                class="form-control @error('discount_start_date') is-invalid @enderror">
                                            @error('discount_start_date')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="discount_end_date">Fecha fin descuento</label>
                                            <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                                value="{{ old('discount_end_date') }}"
                                                class="form-control @error('discount_end_date') is-invalid @enderror">
                                            @error('discount_end_date')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- INVENTARIO -->
                            <div class="tab-pane fade" id="inventory" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="stock">Stock <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}"
                                                    class="form-control @error('stock') is-invalid @enderror"
                                                    placeholder="0" step="1" min="0">
                                                @error('stock')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="stock_alert">Alerta de stock bajo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                                <input type="number" name="stock_alert" id="stock_alert" value="{{ old('stock_alert', 10) }}"
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
                                                <option value="in_stock" {{ old('stock_status') == 'in_stock' ? 'selected' : '' }}>En stock</option>
                                                <option value="out_of_stock" {{ old('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Sin stock</option>
                                                <option value="on_backorder" {{ old('stock_status') == 'on_backorder' ? 'selected' : '' }}>En pedido pendiente</option>
                                            </select>
                                            @error('stock_status')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="manage_stock"
                                                id="manage_stock" value="1" {{ old('manage_stock', true) ? 'checked' : '' }}>
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
                                            <input type="number" name="weight" id="weight" value="{{ old('weight') }}"
                                                class="form-control @error('weight') is-invalid @enderror"
                                                placeholder="0.00" step="0.01" min="0">
                                            @error('weight')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="length">Largo (cm)</label>
                                            <input type="number" name="length" id="length" value="{{ old('length') }}"
                                                class="form-control @error('length') is-invalid @enderror"
                                                placeholder="0.00" step="0.01" min="0">
                                            @error('length')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="width">Ancho (cm)</label>
                                            <input type="number" name="width" id="width" value="{{ old('width') }}"
                                                class="form-control @error('width') is-invalid @enderror"
                                                placeholder="0.00" step="0.01" min="0">
                                            @error('width')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="height">Alto (cm)</label>
                                            <input type="number" name="height" id="height" value="{{ old('height') }}"
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
                                                id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status"><strong>Producto activo</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="featured"
                                                id="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featured"><strong>Producto destacado</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_new"
                                                id="is_new" value="1" {{ old('is_new') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_new"><strong>Producto nuevo</strong></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="visibility">Visibilidad</label>
                                            <select class="form-control @error('visibility') is-invalid @enderror" name="visibility" id="visibility">
                                                <option value="public" {{ old('visibility', 'public') == 'public' ? 'selected' : '' }}>Público (visible para todos)</option>
                                                <option value="catalog" {{ old('visibility') == 'catalog' ? 'selected' : '' }}>Solo catálogo (sin búsqueda)</option>
                                                <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Privado (oculto)</option>
                                            </select>
                                            @error('visibility')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="published_at">Fecha de publicación</label>
                                            <input type="datetime-local" name="published_at" id="published_at"
                                                value="{{ old('published_at') }}"
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
                                            <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
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
                                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
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
                                                placeholder="Descripción para motores de búsqueda" maxlength="160">{{ old('meta_description') }}</textarea>
                                            @error('meta_description')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Máximo 160 caracteres</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="meta_keywords">Meta palabras clave</label>
                                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}"
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
                                                placeholder="Información sobre la garantía del producto">{{ old('warranty') }}</textarea>
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
                                                placeholder="Política de devolución del producto">{{ old('return_policy') }}</textarea>
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
                                                placeholder="Información sobre el envío">{{ old('shipping_info') }}</textarea>
                                            @error('shipping_info')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- VARIANTES -->
                            <div class="tab-pane fade" id="variants" role="tabpanel">
                                <div class="alert alert-info d-flex align-items-center gap-3">
                                    <i class="bi bi-info-circle-fill fs-4"></i>
                                    <div>
                                        <strong>Las variantes se configuran después de crear el producto.</strong><br>
                                        Guarda el producto primero y luego edítalo para agregar variantes (tallas, colores, etc.).
                                    </div>
                                </div>
                            </div>

                        </div>{{-- /tab-content --}}

                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Registrar Producto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>{{-- ✅ FIN del form principal --}}

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

            const sellingPrice = document.getElementById('selling_price');
            const discountPct  = document.getElementById('discount_percentage');
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
    </style>
@endsection