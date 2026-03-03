@extends('layouts.admin')

@section('content')
    <h1>Gestión de cupones</h1>   
    <hr> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Cupones de descuento
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary" style="float: right">
                            <i class="bi bi-plus-circle"></i> Crear nuevo cupón
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Formulario de búsqueda y filtros -->
                    <form action="{{ route('admin.coupons.index') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group mb-2">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Buscar por código..." 
                                           value="{{ $_REQUEST['search'] ?? '' }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="percentage" {{ isset($_REQUEST['type']) && $_REQUEST['type'] == 'percentage' ? 'selected' : '' }}>
                                        Porcentaje
                                    </option>
                                    <option value="fixed" {{ isset($_REQUEST['type']) && $_REQUEST['type'] == 'fixed' ? 'selected' : '' }}>
                                        Monto fijo
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="active" {{ isset($_REQUEST['status']) && $_REQUEST['status'] == 'active' ? 'selected' : '' }}>
                                        Activos
                                    </option>
                                    <option value="inactive" {{ isset($_REQUEST['status']) && $_REQUEST['status'] == 'inactive' ? 'selected' : '' }}>
                                        Inactivos
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="expired" value="1" 
                                           id="expired" {{ isset($_REQUEST['expired']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="expired">
                                        Solo expirados
                                    </label>
                                </div>
                            </div>
                        </div>
                        @if (isset($_REQUEST['search']) || isset($_REQUEST['type']) || isset($_REQUEST['status']) || isset($_REQUEST['expired']))
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-trash"></i> Limpiar filtros
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th style="text-align: center;">Usos</th>
                                    <th style="text-align: center;">Límite</th>
                                    <th style="text-align: center;">Estado</th>
                                    <th>Expira</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($coupons as $coupon)
                                    <tr class="{{ !$coupon->isValid() ? 'table-secondary' : '' }}">
                                        <td style="text-align: center;"><strong>#{{ $coupon->id }}</strong></td>
                                        <td>
                                            <strong class="text-primary">{{ $coupon->code }}</strong>
                                            @if($coupon->min_purchase)
                                                <br><small class="text-muted">
                                                    Min: ${{ number_format($coupon->min_purchase, 2) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->type == 'percentage')
                                                <span class="badge bg-info">
                                                    <i class="bi bi-percent"></i> Porcentaje
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="bi bi-cash"></i> Fijo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->type == 'percentage')
                                                <strong>{{ $coupon->value }}%</strong>
                                                @if($coupon->max_discount)
                                                    <br><small class="text-muted">
                                                        Máx: ${{ number_format($coupon->max_discount, 2) }}
                                                    </small>
                                                @endif
                                            @else
                                                <strong>${{ number_format($coupon->value, 2) }}</strong>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge bg-primary">{{ $coupon->usage_count }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            @if($coupon->usage_limit)
                                                @php
                                                    $percentage = ($coupon->usage_count / $coupon->usage_limit) * 100;
                                                    $badgeClass = $percentage >= 80 ? 'bg-danger' : ($percentage >= 50 ? 'bg-warning' : 'bg-success');
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $coupon->usage_limit }}
                                                </span>
                                                <br><small class="text-muted">{{ number_format($percentage, 0) }}% usado</small>
                                            @else
                                                <span class="text-muted">Ilimitado</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($coupon->is_active)
                                                @if($coupon->isValid())
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Activo
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-exclamation-triangle"></i> Vencido/Agotado
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->expires_at)
                                                {{ $coupon->expires_at->format('d/m/Y') }}
                                                <br><small class="text-muted">{{ $coupon->expires_at->format('H:i') }}</small>
                                                @if($coupon->expires_at->isPast())
                                                    <br><span class="badge bg-danger">Expirado</span>
                                                @elseif($coupon->expires_at->diffInDays() < 7)
                                                    <br><span class="badge bg-warning text-dark">
                                                        Expira pronto
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">Sin expiración</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" 
                                                   class="btn btn-success btn-sm" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form action="{{ route('admin.coupons.toggle-status', $coupon->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="btn btn-{{ $coupon->is_active ? 'warning' : 'info' }} btn-sm"
                                                            title="{{ $coupon->is_active ? 'Desactivar' : 'Activar' }}">
                                                        <i class="bi bi-{{ $coupon->is_active ? 'pause' : 'play' }}-circle"></i>
                                                    </button>
                                                </form>

                                                @if($coupon->usage_count > 0)
                                                    <form action="{{ route('admin.coupons.reset-usage', $coupon->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="button" 
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="confirmReset(this.form)"
                                                                title="Reiniciar contador">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete(this.form)"
                                                            title="Eliminar"
                                                            {{ $coupon->usage_count > 0 ? 'disabled' : '' }}>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hay cupones registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($coupons->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4 px-3">
                            <div class="text-muted">
                                Mostrando de {{ $coupons->firstItem() }} a {{ $coupons->lastItem() }} de {{ $coupons->total() }} registros
                            </div>
                            <div>
                                {{ $coupons->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(form) {
            event.preventDefault();
            Swal.fire({
                title: '¿Desea eliminar este cupón?',
                text: 'Esta acción no se puede deshacer',
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

        function confirmReset(form) {
            event.preventDefault();
            Swal.fire({
                title: '¿Reiniciar contador de usos?',
                text: 'El contador volverá a 0',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Si, reiniciar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endsection