@extends('layouts.admin')

@section('content')
    <h1>Crear nuevo cupón</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Datos del cupón</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Columna izquierda -->
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información básica</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Código del cupón -->
                                        <div class="form-group mb-3">
                                            <label for="code">Código del cupón <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="code" 
                                                   id="code" 
                                                   class="form-control @error('code') is-invalid @enderror" 
                                                   value="{{ old('code') }}" 
                                                   required 
                                                   placeholder="Ej: DESC20, VERANO2024"
                                                   style="text-transform: uppercase;">
                                            @error('code')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="text-muted">
                                                El código se convertirá automáticamente a mayúsculas
                                            </small>
                                        </div>

                                        <!-- Tipo de cupón -->
                                        <div class="form-group mb-3">
                                            <label for="type">Tipo de descuento <span class="text-danger">*</span></label>
                                            <select name="type" 
                                                    id="type" 
                                                    class="form-control @error('type') is-invalid @enderror" 
                                                    required
                                                    onchange="toggleDiscountFields()">
                                                <option value="">Seleccione...</option>
                                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                                    Porcentaje (%)
                                                </option>
                                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>
                                                    Monto fijo ($)
                                                </option>
                                            </select>
                                            @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Valor del descuento -->
                                        <div class="form-group mb-3">
                                            <label for="value">Valor del descuento <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="value-prefix">
                                                    <i class="bi bi-percent"></i>
                                                </span>
                                                <input type="number" 
                                                       name="value" 
                                                       id="value" 
                                                       class="form-control @error('value') is-invalid @enderror" 
                                                       value="{{ old('value') }}" 
                                                       required 
                                                       step="0.01" 
                                                       min="0"
                                                       placeholder="0.00">
                                                @error('value')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <small class="text-muted" id="value-help">
                                                Seleccione primero el tipo de descuento
                                            </small>
                                        </div>

                                        <!-- Descuento máximo (solo para porcentaje) -->
                                        <div class="form-group mb-3" id="max-discount-group" style="display: none;">
                                            <label for="max_discount">Descuento máximo (opcional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" 
                                                       name="max_discount" 
                                                       id="max_discount" 
                                                       class="form-control @error('max_discount') is-invalid @enderror" 
                                                       value="{{ old('max_discount') }}" 
                                                       step="0.01" 
                                                       min="0"
                                                       placeholder="0.00">
                                                @error('max_discount')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <small class="text-muted">
                                                Límite máximo de descuento para cupones de porcentaje
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna derecha -->
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="bi bi-gear"></i> Configuración</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Compra mínima -->
                                        <div class="form-group mb-3">
                                            <label for="min_purchase">Compra mínima requerida (opcional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" 
                                                       name="min_purchase" 
                                                       id="min_purchase" 
                                                       class="form-control @error('min_purchase') is-invalid @enderror" 
                                                       value="{{ old('min_purchase') }}" 
                                                       step="0.01" 
                                                       min="0"
                                                       placeholder="0.00">
                                                @error('min_purchase')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <small class="text-muted">
                                                Monto mínimo de compra para usar el cupón
                                            </small>
                                        </div>

                                        <!-- Límite de usos -->
                                        <div class="form-group mb-3">
                                            <label for="usage_limit">Límite de usos (opcional)</label>
                                            <input type="number" 
                                                   name="usage_limit" 
                                                   id="usage_limit" 
                                                   class="form-control @error('usage_limit') is-invalid @enderror" 
                                                   value="{{ old('usage_limit') }}" 
                                                   min="1"
                                                   placeholder="Ilimitado">
                                            @error('usage_limit')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="text-muted">
                                                Cantidad total de veces que se puede usar el cupón
                                            </small>
                                        </div>

                                        <!-- Fecha de expiración -->
                                        <div class="form-group mb-3">
                                            <label for="expires_at">Fecha de expiración (opcional)</label>
                                            <input type="datetime-local" 
                                                   name="expires_at" 
                                                   id="expires_at" 
                                                   class="form-control @error('expires_at') is-invalid @enderror" 
                                                   value="{{ old('expires_at') }}">
                                            @error('expires_at')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="text-muted">
                                                Dejar vacío para que no expire
                                            </small>
                                        </div>

                                        <!-- Estado activo -->
                                        <div class="form-group mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="is_active" 
                                                       id="is_active" 
                                                       value="1" 
                                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    <strong>Cupón activo</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Solo los cupones activos pueden ser utilizados
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview del cupón -->
                                <div class="card mb-3 bg-light">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="bi bi-eye"></i> Vista previa</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="coupon-preview p-4 border rounded bg-white shadow-sm">
                                            <h3 class="text-primary mb-2" id="preview-code">CODIGO</h3>
                                            <h4 class="text-success mb-2" id="preview-value">--</h4>
                                            <p class="text-muted mb-0" id="preview-conditions">Sin condiciones</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Crear cupón
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Actualizar campos según el tipo de descuento
        function toggleDiscountFields() {
            const type = document.getElementById('type').value;
            const valuePrefix = document.getElementById('value-prefix');
            const valueHelp = document.getElementById('value-help');
            const maxDiscountGroup = document.getElementById('max-discount-group');

            if (type === 'percentage') {
                valuePrefix.innerHTML = '<i class="bi bi-percent"></i>';
                valueHelp.textContent = 'Porcentaje de descuento (ej: 15 para 15%)';
                maxDiscountGroup.style.display = 'block';
            } else if (type === 'fixed') {
                valuePrefix.innerHTML = '$';
                valueHelp.textContent = 'Monto fijo de descuento';
                maxDiscountGroup.style.display = 'none';
            }

            updatePreview();
        }

        // Actualizar vista previa
        function updatePreview() {
            const code = document.getElementById('code').value.toUpperCase() || 'CODIGO';
            const type = document.getElementById('type').value;
            const value = document.getElementById('value').value;
            const minPurchase = document.getElementById('min_purchase').value;
            const usageLimit = document.getElementById('usage_limit').value;
            const maxDiscount = document.getElementById('max_discount').value;

            // Actualizar código
            document.getElementById('preview-code').textContent = code;

            // Actualizar valor
            let valueText = '--';
            if (value) {
                if (type === 'percentage') {
                    valueText = value + '% de descuento';
                    if (maxDiscount) {
                        valueText += ' (máx $' + maxDiscount + ')';
                    }
                } else if (type === 'fixed') {
                    valueText = '$' + value + ' de descuento';
                }
            }
            document.getElementById('preview-value').textContent = valueText;

            // Actualizar condiciones
            let conditions = [];
            if (minPurchase) {
                conditions.push('Compra mínima: $' + minPurchase);
            }
            if (usageLimit) {
                conditions.push('Usos limitados: ' + usageLimit);
            }
            
            const conditionsText = conditions.length > 0 ? conditions.join(' | ') : 'Sin condiciones';
            document.getElementById('preview-conditions').textContent = conditionsText;
        }

        // Event listeners para actualizar preview
        document.getElementById('code').addEventListener('input', updatePreview);
        document.getElementById('type').addEventListener('change', updatePreview);
        document.getElementById('value').addEventListener('input', updatePreview);
        document.getElementById('min_purchase').addEventListener('input', updatePreview);
        document.getElementById('usage_limit').addEventListener('input', updatePreview);
        document.getElementById('max_discount').addEventListener('input', updatePreview);

        // Convertir código a mayúsculas automáticamente
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>

    <style>
        .coupon-preview {
            transition: all 0.3s ease;
        }

        .coupon-preview:hover {
            transform: scale(1.05);
        }
    </style>
@endsection