@extends('layouts.admin')

@section('content')
<h1>Usuario: {{ $user->name }}</h1>
<hr>

<div class="row">
    {{-- Datos del usuario --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Información</h5>
                @if($user->status == 1)
                    <span class="badge bg-success">Activo</span>
                @else
                    <span class="badge bg-danger">Inactivo</span>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Rol</label>
                    <p class="mb-0">
                        <i class="bi bi-shield-check me-1 text-primary"></i>
                        <span class="badge bg-primary">
                            {{ $user->roles->first()?->name ?? 'Sin rol' }}
                        </span>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Nombre</label>
                    <p class="mb-0">
                        <i class="bi bi-person-badge-fill me-1"></i> {{ $user->name }}
                    </p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Correo</label>
                    <p class="mb-0">
                        <i class="bi bi-envelope-fill me-1"></i> {{ $user->email }}
                    </p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Registro</label>
                    <p class="mb-0">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $user->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                @if($user->deleted_at)
                    <div class="mb-3">
                        <label class="text-muted small">Desactivado</label>
                        <p class="mb-0 text-danger">
                            <i class="bi bi-calendar-x me-1"></i>
                            {{ $user->deleted_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @endif

                {{-- Stats rápidas --}}
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="mb-0 text-primary">{{ $user->orders->count() }}</h5>
                        <small class="text-muted">Órdenes</small>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-success">
                            ${{ number_format($user->orders->where('status', '!=', 'cancelled')->sum('total'), 2) }}
                        </h5>
                        <small class="text-muted">Total gastado</small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                @can('users.edit')
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Historial de órdenes --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-1"></i> Historial de órdenes
                    <span class="badge bg-secondary ms-1">{{ $user->orders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @if($user->orders->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-cart-x" style="font-size:2.5rem"></i>
                        <p class="mt-2">Este usuario no tiene órdenes registradas.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Pago</th>
                                    @can('orders.show')
                                        <th class="text-center">Ver</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders as $order)
                                    @php
                                        $statusMap = [
                                            'PENDING'   => ['label' => 'Pendiente',  'color' => 'warning'],
                                            'PROCESSING'=> ['label' => 'Procesando', 'color' => 'info'],
                                            'SHIPPED'   => ['label' => 'Enviado',    'color' => 'primary'],
                                            'DELIVERED' => ['label' => 'Entregado',  'color' => 'success'],
                                            'CANCELLED' => ['label' => 'Cancelado',  'color' => 'danger'],
                                        ];
                                        $s = $statusMap[$order->status] ?? ['label' => $order->status, 'color' => 'secondary'];

                                        $payMap = [
                                            'PAID'     => ['label' => 'Pagado',     'color' => 'success'],
                                            'PENDING'  => ['label' => 'Pendiente',  'color' => 'warning'],
                                            'REFUNDED' => ['label' => 'Reembolsado','color' => 'info'],
                                            'FAILED'   => ['label' => 'Fallido',    'color' => 'danger'],
                                            'COMPLETED'   => ['label' => 'Completado',    'color' => 'success'],
                                        ];
                                        $p = $payMap[$order->payment_status] ?? ['label' => $order->payment_status, 'color' => 'secondary'];
                                    @endphp
                                    <tr>
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $s['color'] }}">{{ $s['label'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $p['color'] }}">{{ $p['label'] }}</span>
                                        </td>
                                        @can('orders.show')
                                            <td class="text-center">
                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                   class="btn btn-info btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection