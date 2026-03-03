@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Órdenes</h1>
        <a href="{{ route('admin.orders.statistics') }}" class="btn btn-info">
            <i class="bi bi-graph-up"></i> Estadísticas
        </a>
    </div>
    <hr>

    {{-- Tarjetas de resumen rápido --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-cart-check text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total órdenes</div>
                        <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pendientes</div>
                        <div class="fw-bold fs-4">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="bi bi-gear text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">En proceso</div>
                        <div class="fw-bold fs-4">{{ $stats['processing'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-truck text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Enviados</div>
                        <div class="fw-bold fs-4">{{ $stats['shipped'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Listado de órdenes</h5>
        </div>
        <div class="card-body">

            {{-- Filtros --}}
            <form action="{{ route('admin.orders.index') }}" method="GET" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                               placeholder="ID, tracking, cupón o cliente..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Estado orden</option>
                            @foreach(['PENDING'=>'Pendiente','PROCESSING'=>'Procesando','SHIPPED'=>'Enviado','DELIVERED'=>'Entregado','CANCELLED'=>'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="payment_status" class="form-select">
                            <option value="">Estado pago</option>
                            @foreach(['PENDING'=>'Pendiente','PAID'=>'Pagado','COMPLETED'=>'Completado','FAILED'=>'Fallido','REFUNDED'=>'Reembolsado'] as $val => $label)
                                <option value="{{ $val }}" {{ request('payment_status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Desde">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Hasta">
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        @if(request()->hasAny(['search','status','payment_status','date_from','date_to','with_discount']))
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="mt-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="with_discount" value="1"
                               id="with_discount" {{ request('with_discount') ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <label class="form-check-label small" for="with_discount">Con descuento</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="with_tracking" value="1"
                               id="with_tracking" {{ request('with_tracking') ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <label class="form-check-label small" for="with_tracking">Con tracking</label>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Fecha</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Pago</th>
                            <th class="text-center">Tracking</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>
                                    <div class="fw-semibold">{{ $order->user->name }}</div>
                                    <small class="text-muted">{{ $order->user->email }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $order->items->count() }} producto(s)
                                        @if($order->items->whereNotNull('variant_id')->count())
                                            <span class="badge bg-info text-dark ms-1">
                                                con variantes
                                            </span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $settings->badge }}{{ number_format($order->total, 2) }}</strong>
                                    @if($order->hasDiscount())
                                        <br><small class="text-success">
                                            -{{ $settings->badge }}{{ number_format($order->discount_amount, 2) }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'PENDING'    => ['warning', 'Pendiente'],
                                            'PROCESSING' => ['info',    'Procesando'],
                                            'SHIPPED'    => ['primary', 'Enviado'],
                                            'DELIVERED'  => ['success', 'Entregado'],
                                            'CANCELLED'  => ['danger',  'Cancelado'],
                                        ];
                                        [$color, $label] = $statusMap[$order->status] ?? ['secondary', $order->status];
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $label }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $payMap = [
                                            'PENDING'   => ['warning text-dark', 'Pendiente'],
                                            'PAID'      => ['success', 'Pagado'],
                                            'COMPLETED' => ['success', 'Pagado'],
                                            'FAILED'    => ['danger',  'Fallido'],
                                            'REFUNDED'  => ['secondary','Reembolsado'],
                                        ];
                                        [$pColor, $pLabel] = $payMap[$order->payment_status] ?? ['secondary', $order->payment_status];
                                    @endphp
                                    <span class="badge bg-{{ $pColor }}">{{ $pLabel }}</span>
                                </td>
                                <td class="text-center">
                                    @if($order->hasTracking())
                                        <i class="bi bi-check-circle-fill text-success" title="{{ $order->tracking_number }}"></i>
                                    @else
                                        <i class="bi bi-x-circle text-muted"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                           class="btn btn-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                                           class="btn btn-success" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}"
                                           class="btn btn-warning" target="_blank" title="Factura">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                              method="POST" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-danger"
                                                    onclick="confirmDelete(this.form)" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No hay órdenes que coincidan con los filtros
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Mostrando {{ $orders->firstItem() }} - {{ $orders->lastItem() }}
                        de {{ $orders->total() }} órdenes
                    </div>
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: '¿Eliminar esta orden?',
                text: 'Se restaurará el stock de los productos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then(result => { if (result.isConfirmed) form.submit(); });
        }
    </script>
@endsection