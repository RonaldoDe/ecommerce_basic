@extends('layouts.admin')

@section('content')
    <h1>Detalle de orden #{{ $order->id }}</h1>   
    <hr> 

    <div class="row">
        <!-- Información principal -->
        <div class="col-md-8">
            <!-- Información de la orden -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Información de la orden</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID de Orden:</strong> #{{ $order->id }}</p>
                            <p><strong>Fecha de orden:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                            <p><strong>ID de Transacción:</strong> {{ $order->transaction_id ?? 'N/A' }}</p>
                            @if($order->hasTracking())
                                <p><strong>Número de rastreo:</strong> 
                                    <span class="badge bg-primary">{{ $order->tracking_number }}</span>
                                </p>
                                <p><strong>Empresa de envío:</strong> {{ $order->shipping_company }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Estado de orden:</strong> 
                                @if($order->status == 'PENDING')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($order->status == 'PROCESSING')
                                    <span class="badge bg-info">Procesando</span>
                                @elseif($order->status == 'SHIPPED')
                                    <span class="badge bg-primary">Enviado</span>
                                @elseif($order->status == 'DELIVERED')
                                    <span class="badge bg-success">Entregado</span>
                                @elseif($order->status == 'CANCELLED')
                                    <span class="badge bg-danger">Cancelado</span>
                                @endif
                            </p>
                            <p>
                                <strong>Estado de pago:</strong> 
                                @if(in_array($order->payment_status, ['PAID', 'COMPLETED']))
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($order->payment_status == 'PENDING')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($order->payment_status == 'FAILED')
                                    <span class="badge bg-danger">Fallido</span>
                                @elseif($order->payment_status == 'REFUNDED')
                                    <span class="badge bg-secondary">Reembolsado</span>
                                @endif
                            </p>
                            @if($order->hasDiscount())
                                <p><strong>Cupón aplicado:</strong> 
                                    <span class="badge bg-success">{{ $order->coupon_code }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del cliente -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Información del cliente</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> {{ $order->user->name }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Dirección de envío:</strong></p>
                            <p>{{ $order->address ?? 'No especificada' }}</p>
                        </div>
                    </div>
                    @if($order->customer_notes)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong><i class="bi bi-chat-left-text"></i> Notas del cliente:</strong><br>
                                    {{ $order->customer_notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Productos de la orden -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Productos de la orden</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:65px">Imagen</th>
                                    <th>Producto</th>
                                    <th>Código / SKU</th>
                                    <th style="text-align:center;">Precio</th>
                                    <th style="text-align:center;">Cantidad</th>
                                    <th style="text-align:center;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        {{-- Imagen: accessor prioriza imagen de variante --}}
                                        <td style="text-align:center;">
                                            <img src="{{ $item->image_url }}"
                                                 alt="{{ $item->display_name }}"
                                                 style="width:55px;height:55px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;">
                                        </td>

                                        {{-- Nombre desde snapshot + badges de variante --}}
                                        <td>
                                            <div class="fw-semibold">{{ $item->display_name }}</div>
                                            @if($item->variant_label)
                                                <div class="mt-1">
                                                    @foreach($item->variant_attributes as $key => $value)
                                                        <span class="badge bg-light text-dark border me-1"
                                                              style="font-size:0.75rem;font-weight:500;">
                                                            {{ $key }}: {{ $value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Código y SKU desde snapshot --}}
                                        <td>
                                            <code>{{ $item->product_code ?? $item->product?->code ?? '—' }}</code>
                                            @if($item->product_sku)
                                                <br><small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                            @endif
                                        </td>

                                        <td style="text-align:center;">{{ $settings->badge }} {{ number_format($item->price, 2) }}</td>
                                        <td style="text-align:center;">{{ $item->quantity }}</td>
                                        <td style="text-align:center;">
                                            <strong>{{ $settings->badge }} {{ number_format($item->subtotal, 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                    <td style="text-align:center;">{{ $settings->badge }} {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->hasDiscount())
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            <strong>Descuento 
                                                @if($order->coupon_code)({{ $order->coupon_code }})@endif:
                                            </strong>
                                        </td>
                                        <td style="text-align:center;" class="text-success">
                                            -{{ $settings->badge }} {{ number_format($order->discount_amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                    <td style="text-align:center;">
                                        <h5 class="mb-0">
                                            <strong>{{ $settings->badge }} {{ number_format($order->total, 2) }}</strong>
                                        </h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notas del administrador -->
            @if($order->admin_notes)
                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <h5><i class="bi bi-sticky"></i> Notas internas del administrador</h5>
                    </div>
                    <div class="card-body">
                        {{ $order->admin_notes }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar de acciones -->
        <div class="col-md-4">
            <!-- Acciones principales -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Acciones</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-pencil"></i> Editar orden
                    </a>
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-warning w-100 mb-2" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Generar factura
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100 mb-2">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                    <hr>
                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger w-100" onclick="confirmDelete(this.form)">
                            <i class="bi bi-trash"></i> Eliminar orden
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cambiar estado de orden -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Cambiar estado de orden</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-2">
                            <select name="status" class="form-control" required>
                                <option value="PENDING"    {{ $order->status == 'PENDING'    ? 'selected' : '' }}>Pendiente</option>
                                <option value="PROCESSING" {{ $order->status == 'PROCESSING' ? 'selected' : '' }}>Procesando</option>
                                <option value="SHIPPED"    {{ $order->status == 'SHIPPED'    ? 'selected' : '' }}>Enviado</option>
                                <option value="DELIVERED"  {{ $order->status == 'DELIVERED'  ? 'selected' : '' }}>Entregado</option>
                                <option value="CANCELLED"  {{ $order->status == 'CANCELLED'  ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Actualizar estado
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cambiar estado de pago -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Cambiar estado de pago</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-payment-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-2">
                            <select name="payment_status" class="form-control" required>
                                <option value="PENDING"   {{ $order->payment_status == 'PENDING'   ? 'selected' : '' }}>Pendiente</option>
                                <option value="PAID"      {{ $order->payment_status == 'PAID'      ? 'selected' : '' }}>Pagado</option>
                                <option value="COMPLETED" {{ $order->payment_status == 'COMPLETED' ? 'selected' : '' }}>Completado</option>
                                <option value="FAILED"    {{ $order->payment_status == 'FAILED'    ? 'selected' : '' }}>Fallido</option>
                                <option value="REFUNDED"  {{ $order->payment_status == 'REFUNDED'  ? 'selected' : '' }}>Reembolsado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Actualizar pago
                        </button>
                    </form>
                </div>
            </div>

            <!-- Agregar/Actualizar tracking -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4><i class="bi bi-truck"></i> Información de envío</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-tracking', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-2">
                            <label for="tracking_number">Número de rastreo</label>
                            <input type="text" name="tracking_number" id="tracking_number" 
                                   class="form-control" 
                                   value="{{ old('tracking_number', $order->tracking_number) }}" 
                                   required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="shipping_company">Empresa de envío</label>
                            <input type="text" name="shipping_company" id="shipping_company" 
                                   class="form-control" 
                                   value="{{ old('shipping_company', $order->shipping_company) }}" 
                                   required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-seam"></i> {{ $order->hasTracking() ? 'Actualizar' : 'Agregar' }} tracking
                        </button>
                    </form>
                    @if($order->hasTracking())
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Al actualizar, el estado cambiará a "Enviado"
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: '¿Desea eliminar esta orden?',
                text: 'Esta acción restaurará el stock de los productos',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
  
@endsection