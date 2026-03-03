@extends('layouts.web')

@section('content')
    <!-- ... contenido del producto ... -->

    <!-- SECCIÓN DE REVIEWS -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="bi bi-star-fill"></i> Reseñas de Clientes
                                <span class="badge bg-light text-dark ms-2">
                                    {{ $product->reviews_count }}
                                </span>
                            </h4>
                            @auth
                                @if(!Auth::user()->reviews()->where('product_id', $product->id)->exists())
                                    <a href="{{ route('review.create', $product->id) }}" 
                                        class="btn btn-light">
                                        <i class="bi bi-pencil"></i> Escribir reseña
                                    </a>
                                @else
                                    <span class="badge bg-success">Ya dejaste tu reseña</span>
                                @endif
                            @else
                                <a href="{{ route('web.login') }}" class="btn btn-light">
                                    <i class="bi bi-pencil"></i> Escribir reseña
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Resumen de Ratings -->
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="rating-summary">
                                    <h1 class="display-3">{{ number_format($product->rating, 1) }}</h1>
                                    <div class="stars mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill text-warning' : '' }}" 
                                                style="font-size: 1.5rem;"></i>
                                        @endfor
                                    </div>
                                    <p class="text-muted">{{ $product->reviews_count }} reseñas</p>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <!-- Distribución de ratings -->
                                @php
                                    $totalReviews = $product->reviews()->approved()->count();
                                    $ratingStats = [
                                        5 => $product->reviews()->approved()->where('rating', 5)->count(),
                                        4 => $product->reviews()->approved()->where('rating', 4)->count(),
                                        3 => $product->reviews()->approved()->where('rating', 3)->count(),
                                        2 => $product->reviews()->approved()->where('rating', 2)->count(),
                                        1 => $product->reviews()->approved()->where('rating', 1)->count(),
                                    ];
                                @endphp

                                @foreach([5, 4, 3, 2, 1] as $stars)
                                    <div class="d-flex align-items-center mb-2">
                                        <span style="width: 80px;">{{ $stars }} estrellas</span>
                                        <div class="progress flex-grow-1 mx-2" style="height: 20px;">
                                            @php
                                                $percentage = $totalReviews > 0 ? ($ratingStats[$stars] / $totalReviews) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-warning" 
                                                style="width: {{ $percentage }}%">
                                            </div>
                                        </div>
                                        <span style="width: 50px;">{{ $ratingStats[$stars] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Filtros -->
                        <div class="mb-3">
                            <div class="btn-group" role="group">
                                <a href="{{ route('product.reviews', ['product' => $product->id, 'sort' => 'recent']) }}" 
                                    class="btn btn-outline-primary {{ request('sort', 'recent') === 'recent' ? 'active' : '' }}">
                                    Más recientes
                                </a>
                                <a href="{{ route('product.reviews', ['product' => $product->id, 'sort' => 'helpful']) }}" 
                                    class="btn btn-outline-primary {{ request('sort') === 'helpful' ? 'active' : '' }}">
                                    Más útiles
                                </a>
                                <a href="{{ route('product.reviews', ['product' => $product->id, 'sort' => 'rating_high']) }}" 
                                    class="btn btn-outline-primary {{ request('sort') === 'rating_high' ? 'active' : '' }}">
                                    Mayor calificación
                                </a>
                            </div>
                        </div>

                        <!-- Lista de Reviews -->
                        @forelse($product->reviews()->approved()->latest()->take(5)->get() as $review)
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-2">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $review->user->name }}</strong>
                                                @if($review->verified_purchase)
                                                    <span class="badge bg-success ms-2">
                                                        <i class="bi bi-check-circle"></i> Compra verificada
                                                    </span>
                                                @endif
                                                <br>
                                                <div class="stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($review->title)
                                            <h6>{{ $review->title }}</h6>
                                        @endif
                                        <p class="mb-2">{{ $review->comment }}</p>

                                        <!-- Imágenes de la review -->
                                        @if($review->images->count() > 0)
                                            <div class="review-images mb-2">
                                                @foreach($review->images as $image)
                                                    <img src="{{ asset('storage/' . $image->image) }}" 
                                                        alt="Review image"
                                                        class="img-thumbnail me-2"
                                                        style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                                        onclick="showImageModal('{{ asset('storage/' . $image->image) }}')">
                                                @endforeach
                                            </div>
                                        @endif

                                        <small class="text-muted">
                                            {{ $review->created_at->diffForHumans() }}
                                        </small>

                                        <!-- Botones de útil -->
                                        <div class="mt-2">
                                            @auth
                                                <button onclick="markHelpful({{ $review->id }})" 
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-hand-thumbs-up"></i> 
                                                    Útil ({{ $review->helpful_count }})
                                                </button>
                                                <button onclick="markNotHelpful({{ $review->id }})" 
                                                    class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-hand-thumbs-down"></i>
                                                    ({{ $review->not_helpful_count }})
                                                </button>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bi bi-hand-thumbs-up"></i> {{ $review->helpful_count }} personas encontraron esto útil
                                                </span>
                                            @endauth
                                        </div>

                                        <!-- Respuesta del vendedor -->
                                        @if($review->seller_response)
                                            <div class="alert alert-info mt-3">
                                                <strong><i class="bi bi-shop"></i> Respuesta del vendedor:</strong><br>
                                                {{ $review->seller_response }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $review->responded_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-chat-quote" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">Aún no hay reseñas para este producto</p>
                                <p class="text-muted">¡Sé el primero en dejar una reseña!</p>
                            </div>
                        @endforelse

                        @if($product->reviews()->approved()->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('product.reviews', $product->id) }}" class="btn btn-primary">
                                    Ver todas las reseñas ({{ $product->reviews()->approved()->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver imágenes grandes -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        function markHelpful(reviewId) {
            fetch(`/review/${reviewId}/helpful`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }

        function markNotHelpful(reviewId) {
            fetch(`/review/${reviewId}/not-helpful`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    </script>
@endsection