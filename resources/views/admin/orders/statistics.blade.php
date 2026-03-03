@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Estadísticas de órdenes</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
    <hr>

    {{-- Tarjetas resumen --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Total órdenes</div>
                            <div class="fw-bold fs-2">{{ $totalOrders }}</div>
                            <div class="text-muted small">Históricas</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-cart-check text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Ingresos</div>
                            <div class="fw-bold fs-2">{{ $settings->badge }}{{ number_format($totalRevenue, 0) }}</div>
                            <div class="text-success small">Órdenes pagadas</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-cash-stack text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Descuentos</div>
                            <div class="fw-bold fs-2">{{ $settings->badge }}{{ number_format($totalDiscount, 0) }}</div>
                            <div class="text-muted small">{{ $ordersWithDiscount }} órdenes</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-tag-fill text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Con tracking</div>
                            <div class="fw-bold fs-2">{{ $ordersWithTracking }}</div>
                            <div class="text-muted small">
                                {{ $totalOrders > 0 ? round($ordersWithTracking/$totalOrders*100) : 0 }}% del total
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-truck text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estados --}}
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Estado de órdenes</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Estado</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">%</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $orderStates = [
                                    ['label'=>'Pendiente',   'color'=>'warning', 'count'=>$pendingOrders],
                                    ['label'=>'Procesando',  'color'=>'info',    'count'=>$processingOrders],
                                    ['label'=>'Enviado',     'color'=>'primary', 'count'=>$shippedOrders],
                                    ['label'=>'Entregado',   'color'=>'success', 'count'=>$deliveredOrders],
                                    ['label'=>'Cancelado',   'color'=>'danger',  'count'=>$cancelledOrders],
                                ];
                            @endphp
                            @foreach($orderStates as $state)
                                @php $pct = $totalOrders > 0 ? ($state['count']/$totalOrders)*100 : 0; @endphp
                                <tr>
                                    <td><span class="badge bg-{{ $state['color'] }}">{{ $state['label'] }}</span></td>
                                    <td class="text-center fw-bold">{{ $state['count'] }}</td>
                                    <td class="text-center">{{ number_format($pct, 1) }}%</td>
                                    <td style="width:120px">
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar bg-{{ $state['color'] }}"
                                                 style="width:{{ $pct }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card"></i> Estado de pagos</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Estado</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">%</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $payStates = [
                                    ['label'=>'Pendiente',    'color'=>'warning', 'count'=>$pendingPayments],
                                    ['label'=>'Pagado',       'color'=>'success', 'count'=>$paidOrders],
                                    ['label'=>'Fallido',      'color'=>'danger',  'count'=>$failedPayments],
                                ];
                            @endphp
                            @foreach($payStates as $state)
                                @php $pct = $totalOrders > 0 ? ($state['count']/$totalOrders)*100 : 0; @endphp
                                <tr>
                                    <td><span class="badge bg-{{ $state['color'] }}">{{ $state['label'] }}</span></td>
                                    <td class="text-center fw-bold">{{ $state['count'] }}</td>
                                    <td class="text-center">{{ number_format($pct, 1) }}%</td>
                                    <td style="width:120px">
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar bg-{{ $state['color'] }}"
                                                 style="width:{{ $pct }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Productos + Variantes más vendidos --}}
    <div class="row mb-4 g-3">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Productos más vendidos (Top 10)</h5>
                </div>
                <div class="card-body p-0">
                    @if($topProducts->count())
                        @php $maxQty = $topProducts->max('total_quantity'); @endphp
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Uds.</th>
                                    <th class="text-center">Ingresos</th>
                                    <th style="width:100px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $i => $item)
                                    <tr>
                                        <td>
                                            @if($i===0) 🥇 @elseif($i===1) 🥈 @elseif($i===2) 🥉 @else <span class="text-muted">{{ $i+1 }}</span> @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold small">{{ $item->name }}</div>
                                            <code class="small">{{ $item->code }}</code>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $item->total_quantity }}</span>
                                        </td>
                                        <td class="text-center text-success small fw-bold">
                                            {{ $settings->badge }}{{ number_format($item->total_revenue, 2) }}
                                        </td>
                                        <td>
                                            <div class="progress" style="height:8px">
                                                <div class="progress-bar bg-primary"
                                                     style="width:{{ $maxQty>0 ? ($item->total_quantity/$maxQty)*100 : 0 }}%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4 text-muted">Sin datos</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-grid-3x3-gap"></i> Variantes más vendidas</h5>
                </div>
                <div class="card-body p-0">
                    @if($topVariants->count())
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto / Variante</th>
                                    <th class="text-center">Uds.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topVariants as $i => $v)
                                    @php
                                        $attrs = is_string($v->variant_attributes)
                                            ? json_decode($v->variant_attributes, true)
                                            : (array)$v->variant_attributes;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold small">{{ $v->product_name }}</div>
                                            <div class="mt-1">
                                                @foreach($attrs as $k => $val)
                                                    <span class="badge bg-light text-dark border me-1" style="font-size:0.7rem">
                                                        {{ $k }}: {{ $val }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-dark">{{ $v->total_quantity }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-grid-3x3-gap d-block fs-2 mb-2"></i>
                            Sin ventas de variantes aún
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Cupones --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Cupones más usados</h5>
        </div>
        <div class="card-body p-0">
            @if($topCoupons->count())
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cupón</th>
                            <th class="text-center">Usos</th>
                            <th class="text-center">Descuento total</th>
                            <th class="text-center">Promedio por uso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCoupons as $i => $coupon)
                            <tr>
                                <td>
                                    @if($i===0) 🥇 @elseif($i===1) 🥈 @elseif($i===2) 🥉 @else {{ $i+1 }} @endif
                                </td>
                                <td><strong>{{ $coupon->coupon_code }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">{{ $coupon->usage_count }}</span>
                                </td>
                                <td class="text-center text-success fw-bold">
                                    {{ $settings->badge }}{{ number_format($coupon->total_discount, 2) }}
                                </td>
                                <td class="text-center">
                                    {{ $settings->badge }}{{ number_format($coupon->total_discount / $coupon->usage_count, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total descuentos:</td>
                            <td class="text-center text-success">
                                {{ $settings->badge }}{{ number_format($topCoupons->sum('total_discount'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="text-center py-4 text-muted">Sin cupones usados aún</div>
            @endif
        </div>
    </div>

    {{-- Órdenes recientes --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Órdenes recientes</h5>
        </div>
        <div class="card-body p-0">
            @if($recentOrders->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Descuento</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Pago</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>
                                        <div class="fw-semibold small">{{ $order->user->name }}</div>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    </td>
                                    <td>
                                        <div class="small">{{ $order->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ $settings->badge }}{{ number_format($order->total, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($order->discount_amount > 0)
                                            <span class="badge bg-success">
                                                -{{ $settings->badge }}{{ number_format($order->discount_amount, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php $sm = ['PENDING'=>['warning','Pendiente'],'PROCESSING'=>['info','Procesando'],'SHIPPED'=>['primary','Enviado'],'DELIVERED'=>['success','Entregado'],'CANCELLED'=>['danger','Cancelado']]; [$sc,$sl] = $sm[$order->status] ?? ['secondary',$order->status]; @endphp
                                        <span class="badge bg-{{ $sc }}">{{ $sl }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php $pm = ['PENDING'=>['warning text-dark','Pendiente'],'PAID'=>['success','Pagado'],'COMPLETED'=>['success','Pagado'],'FAILED'=>['danger','Fallido'],'REFUNDED'=>['secondary','Reembolsado']]; [$pc,$pl] = $pm[$order->payment_status] ?? ['secondary',$order->payment_status]; @endphp
                                        <span class="badge bg-{{ $pc }}">{{ $pl }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                           class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">Sin órdenes recientes</div>
            @endif
        </div>
    </div>
@endsection