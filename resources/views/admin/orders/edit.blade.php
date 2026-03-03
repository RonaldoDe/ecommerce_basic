@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="mb-0">Editar orden #{{ $order->id }}</h1>
            <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
        </div>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Ver orden
        </a>
    </div>
    <hr>

    <div class="row">
        {{-- Columna izquierda --}}
        <div class="col-md-6">

            {{-- Datos básicos --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información básica</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="mainForm">
                        @csrf @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">ID de Orden</label>
                                <input type="text" class="form-control" value="#{{ $order->id }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Cliente</label>
                                <input type="text" class="form-control" value="{{ $order->user->name }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Subtotal</label>
                                <input type="text" class="form-control" value="{{ $settings->badge }}{{ number_format($order->subtotal, 2) }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Total calculado</label>
                                <input type="text" class="form-control" id="totalDisplay"
                                       value="{{ $settings->badge }}{{ number_format($order->total, 2) }}" disabled>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="discount_amount">
                                Descuento <span class="text-muted small">(máx. {{ $settings->badge }}{{ number_format($order->subtotal, 2) }})</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="number" name="discount_amount" id="discount_amount"
                                       value="{{ old('discount_amount', $order->discount_amount) }}"
                                       class="form-control @error('discount_amount') is-invalid @enderror"
                                       step="0.01" min="0" max="{{ $order->subtotal }}"
                                       placeholder="0.00">
                                @error('discount_amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="coupon_code">Código de cupón</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-ticket-perforated"></i></span>
                                <input type="text" name="coupon_code" id="coupon_code"
                                       value="{{ old('coupon_code', $order->coupon_code) }}"
                                       class="form-control @error('coupon_code') is-invalid @enderror"
                                       placeholder="CODIGO">
                                @error('coupon_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="address">Dirección de envío</label>
                            <textarea name="address" id="address" rows="2"
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Dirección completa">{{ old('address', $order->address) }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>

            {{-- Notas --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-sticky"></i> Notas</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="notesForm">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label" for="admin_notes">
                                Notas internas <small class="text-muted">(solo admin)</small>
                            </label>
                            <textarea name="admin_notes" id="admin_notes" rows="3"
                                      class="form-control @error('admin_notes') is-invalid @enderror"
                                      placeholder="Notas internas del equipo">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                            @error('admin_notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="customer_notes">
                                Notas del cliente
                            </label>
                            <textarea name="customer_notes" id="customer_notes" rows="3"
                                      class="form-control @error('customer_notes') is-invalid @enderror"
                                      placeholder="Instrucciones del cliente">{{ old('customer_notes', $order->customer_notes) }}</textarea>
                            @error('customer_notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Columna derecha --}}
        <div class="col-md-6">

            {{-- Estados --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-toggles"></i> Estados</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="mb-3">
                        @csrf @method('PATCH')
                        <label class="form-label">Estado de orden</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                @foreach(['PENDING'=>'Pendiente','PROCESSING'=>'Procesando','SHIPPED'=>'Enviado','DELIVERED'=>'Entregado','CANCELLED'=>'Cancelado'] as $val => $label)
                                    <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <form action="{{ route('admin.orders.update-payment-status', $order->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <label class="form-label">Estado de pago</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                            <select name="payment_status" class="form-select" onchange="this.form.submit()">
                                @foreach(['PENDING'=>'Pendiente','PAID'=>'Pagado','COMPLETED'=>'Completado','FAILED'=>'Fallido','REFUNDED'=>'Reembolsado'] as $val => $label)
                                    <option value="{{ $val }}" {{ $order->payment_status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tracking --}}
            @if(!in_array($order->status, ['DELIVERED','CANCELLED']))
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-truck"></i> Información de envío</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.update-tracking', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Número de rastreo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                    <input type="text" name="tracking_number" class="form-control"
                                           value="{{ $order->tracking_number }}"
                                           placeholder="ABC123456789" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Empresa de envío</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="shipping_company" class="form-control"
                                           value="{{ $order->shipping_company }}"
                                           placeholder="DHL, FedEx, Servientrega..." required>
                                </div>
                            </div>
                            <button class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Guardar tracking
                                @if(!$order->hasTracking())
                                    <small class="opacity-75">(cambia estado a Enviado)</small>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Productos (solo lectura con variantes) --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box"></i> Productos de la orden</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold small">{{ $item->display_name }}</div>
                                            @if($item->variant_label)
                                                <div class="mt-1">
                                                    @foreach($item->variant_attributes as $k => $v)
                                                        <span class="badge bg-light text-dark border" style="font-size:0.7rem">
                                                            {{ $k }}: {{ $v }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <small class="text-muted">{{ $item->product_code }}</small>
                                        </td>
                                        <td class="text-center small">{{ $settings->badge }}{{ number_format($item->price, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-center small fw-bold">
                                            {{ $settings->badge }}{{ number_format($item->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end small"><strong>Subtotal:</strong></td>
                                    <td class="text-center small">{{ $settings->badge }}{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                    <tr class="text-success">
                                        <td colspan="3" class="text-end small"><strong>Descuento:</strong></td>
                                        <td class="text-center small">-{{ $settings->badge }}{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-center fw-bold">{{ $settings->badge }}{{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Botones finales --}}
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Cancelar
        </a>
        <button type="submit" form="mainForm" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Guardar cambios
        </button>
    </div>

    <script>
        // Actualizar total en tiempo real al cambiar el descuento
        const subtotal      = {{ $order->subtotal }};
        const badge         = "{{ $settings->badge }}";
        const discountInput = document.getElementById('discount_amount');
        const totalDisplay  = document.getElementById('totalDisplay');

        discountInput.addEventListener('input', function () {
            let discount = parseFloat(this.value) || 0;
            if (discount > subtotal) {
                discount = subtotal;
                this.value = subtotal.toFixed(2);
            }
            const total = Math.max(0, subtotal - discount);
            totalDisplay.value = badge + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });
    </script>
@endsection