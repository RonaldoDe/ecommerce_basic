@extends('layouts.admin')

@section('content')
    <h1>Gestión de Reseñas</h1>
    <hr>

    <div class="row mb-3">
        <div class="col-md-12">
            <!-- Estadísticas -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h3>{{ \App\Models\Review::pending()->count() }}</h3>
                            <p class="mb-0">Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h3>{{ \App\Models\Review::approved()->count() }}</h3>
                            <p class="mb-0">Aprobadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h3>{{ \App\Models\Review::rejected()->count() }}</h3>
                            <p class="mb-0">Rechazadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h3>{{ number_format(\App\Models\Review::approved()->avg('rating'), 2) }}</h3>
                            <p class="mb-0">Rating Promedio</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Reseñas</h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form action="{{ route('admin.reviews.index') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos los estados</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                        Pendientes
                                    </option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                                        Aprobadas
                                    </option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                        Rechazadas
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="rating" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas las calificaciones</option>
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                            {{ $i }} estrella{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                        placeholder="Buscar..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary w-100">
                                    Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de Reviews -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Producto</th>
                                    <th>Usuario</th>
                                    <th width="100">Rating</th>
                                    <th>Comentario</th>
                                    <th width="120">Estado</th>
                                    <th width="100">Fecha</th>
                                    <th width="200">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>{{ $review->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $review->product_id) }}" 
                                                class="text-decoration-none">
                                                {{ $review->product->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $review->user->name }}
                                            @if($review->verified_purchase)
                                                <span class="badge bg-success" title="Compra verificada">
                                                    <i class="bi bi-check-circle"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                                                @endfor
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->title)
                                                <strong>{{ Str::limit($review->title, 30) }}</strong><br>
                                            @endif
                                            <small>{{ Str::limit($review->comment, 80) }}</small>
                                            @if($review->images->count() > 0)
                                                <br>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-image"></i> {{ $review->images->count() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($review->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">Aprobada</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Rechazada</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <small>{{ $review->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            @if($review->status === 'pending')
                                                <button onclick="approveReview({{ $review->id }})" 
                                                    class="btn btn-success btn-sm">
                                                    <i class="bi bi-check"></i> Aprobar
                                                </button>
                                                <button onclick="rejectReview({{ $review->id }})" 
                                                    class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x"></i> Rechazar
                                                </button>
                                            @endif
                                            <button onclick="viewReview({{ $review->id }})" 
                                                class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">No hay reseñas</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($reviews->hasPages())
                        <div class="mt-3">
                            {{ $reviews->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function approveReview(id) {
            Swal.fire({
                title: '¿Aprobar esta reseña?',
                text: "La reseña será visible para todos los usuarios",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/review/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Aprobada!', data.message, 'success')
                                .then(() => location.reload());
                        }
                    });
                }
            });
        }

        function rejectReview(id) {
            Swal.fire({
                title: '¿Rechazar esta reseña?',
                input: 'textarea',
                inputLabel: 'Motivo del rechazo (opcional)',
                inputPlaceholder: 'Escribe el motivo...',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/review/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            admin_note: result.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Rechazada!', data.message, 'success')
                                .then(() => location.reload());
                        }
                    });
                }
            });
        }

        function viewReview(id) {
            // Aquí podrías abrir un modal con más detalles
            window.location.href = `/admin/reviews?id=${id}`;
        }
    </script>
@endsection