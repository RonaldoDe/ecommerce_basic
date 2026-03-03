@extends('layouts.admin')

@section('content')

{{-- Header --}}
<div class="row mb-3">
    <div class="col-12 col-md-6 order-md-1 order-last">
        <h2>Bienvenido, {{ Auth::user()->name }}</h2>
        <p class="text-muted">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
    </div>
    <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-primary">Rol del usuario</li>
                <li class="breadcrumb-item active">{{ Auth::user()->roles->first()->name }}</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Fila 1: Stats de ingresos --}}
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body px-4 py-4-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Ingresos totales</h6>
                        <h4 class="font-extrabold mb-0">${{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                    <i class="bi bi-cash-stack fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body px-4 py-4-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Ingresos del mes</h6>
                        <h4 class="font-extrabold mb-0">${{ number_format($monthRevenue, 2) }}</h4>
                    </div>
                    <i class="bi bi-calendar-check fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body px-4 py-4-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Ingresos hoy</h6>
                        <h4 class="font-extrabold mb-0">${{ number_format($todayRevenue, 2) }}</h4>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Fila 2: Stats de conteos --}}
<div class="row mb-3">
    <div class="col-md-3">
        <a href="{{ route('admin.roles.index') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i><i class="bi bi-shield-check"></i></i>
                            </div>
                        </div>
                        <div class="col-xxl-7">
                            <h6 class="text-muted font-semibold">Roles registrados</h6>
                            <h6 class="font-extrabold mb-0">{{ $roles }} Roles</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i><i class="bi bi-person"></i></i>
                            </div>
                        </div>
                        <div class="col-xxl-7">
                            <h6 class="text-muted font-semibold">Usuarios registrados</h6>
                            <h6 class="font-extrabold mb-0">{{ $users }} Usuarios</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2">
                                <i><i class="bi bi-tags"></i></i>
                            </div>
                        </div>
                        <div class="col-xxl-7">
                            <h6 class="text-muted font-semibold">Categorías registradas</h6>
                            <h6 class="font-extrabold mb-0">{{ $categories }} Categorías</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon red mb-2">
                                <i><i class="bi bi-box-seam"></i></i>
                            </div>
                        </div>
                        <div class="col-xxl-7">
                            <h6 class="text-muted font-semibold">Productos registrados</h6>
                            <h6 class="font-extrabold mb-0">{{ $products }} Productos</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Fila 3: Stats de órdenes --}}
<div class="row mb-3">
    <div class="col-md-4">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted font-semibold">Total de órdenes</h6>
                            <h5 class="font-extrabold mb-0">{{ $totalOrders }}</h5>
                        </div>
                        <div class="stats-icon blue">
                            <i><i class="bi bi-cart-check"></i></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted font-semibold">Órdenes pendientes</h6>
                            <h5 class="font-extrabold mb-0">{{ $pendingOrders }}</h5>
                        </div>
                        <div class="stats-icon yellow">
                            <i><i class="bi bi-hourglass-split"></i></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted font-semibold">Órdenes canceladas</h6>
                            <h5 class="font-extrabold mb-0">{{ $cancelledOrders }}</h5>
                        </div>
                        <div class="stats-icon red">
                            <i><i class="bi bi-cart-x"></i></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Fila 4: Gráfica + Stock bajo --}}
<div class="row mb-3">

    {{-- Gráfica de ventas últimos 7 días --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ventas últimos 7 días</h5>
            </div>
            <div class="card-body">
                <div id="my-sales-chart" style="min-height:250px;"></div>
            </div>
        </div>
    </div>

    {{-- Productos con stock bajo --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Stock bajo</h5>
                <span class="badge bg-danger">{{ $lowStockProducts->count() }} alertas</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($lowStockProducts as $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0 fw-semibold" style="font-size:0.85rem">{{ Str::limit($product->name, 25) }}</p>
                            </div>
                            <span class="badge {{ $product->stock == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ $product->stock }} uds.
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-3">
                            <i class="bi bi-check-circle text-success"></i> Todo en orden
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

</div>

{{-- Fila 5: Órdenes recientes --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Órdenes recientes</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Ver todas</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>
                                        <p class="mb-0">{{ $order->user->name ?? 'N/A' }}</p>
                                        <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending'   => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                'shipped'   => 'info',
                                            ];
                                            $color = $statusColors[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($order->payment_status ?? 'Pendiente') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No hay órdenes registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            var chartEl = document.querySelector('#my-sales-chart');
            if (!chartEl || typeof ApexCharts === 'undefined') return;

            chartEl.innerHTML = '';

            new ApexCharts(chartEl, {
                series: [{
                    name: 'Ventas',
                    data: {!! json_encode($salesChart->pluck('total')->map(fn($v) => (float) $v)->values()) !!}
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    animations: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 }
                },
                colors: ['#435ebe'],
                xaxis: {
                    categories: {!! json_encode($salesChart->pluck('date')->values()) !!}
                },
                yaxis: {
                    labels: {
                        formatter: val => '$' + parseFloat(val).toFixed(2)
                    }
                },
                tooltip: {
                    y: { formatter: val => '$' + parseFloat(val).toFixed(2) }
                },
                grid: { borderColor: '#f1f1f1' },
                noData: { text: 'Sin ventas en este período' }
            }).render();

        }, 500);
    });
</script>
@endpush