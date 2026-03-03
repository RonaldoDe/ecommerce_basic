@extends('web.dashboard.layout')

@section('dashboard-content')

<div class="section-header" data-aos="fade-up">
    <h2>Mis Reseñas</h2>
    <div class="header-actions">
        <div class="dropdown">
            <button class="ds-btn-ghost dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-funnel me-1"></i> Ordenar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Más recientes</a></li>
                <li><a class="dropdown-item" href="#">Calificación más alta</a></li>
                <li><a class="dropdown-item" href="#">Calificación más baja</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="rv-list">
    @forelse($reviews as $review)
    <div class="rv-card" data-aos="fade-up">

        <div class="rv-product">
            <div class="rv-img">
                <img src="{{ $review->product->image }}" alt="{{ $review->product->name }}" loading="lazy">
            </div>
            <div class="rv-product-info">
                <h4 class="rv-product-name">{{ $review->product->name }}</h4>
                <div class="rv-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                    @endfor
                    <span class="rv-stars-num">{{ number_format($review->rating, 1) }}</span>
                </div>
                <div class="rv-date">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $review->created_at->format('d M, Y') }}
                </div>
            </div>
            <div class="rv-rating-circle rv-rating-{{ $review->rating }}">
                {{ $review->rating }}.0
            </div>
        </div>

        <div class="rv-body">
            <p>{{ $review->comment }}</p>
        </div>

        @if($review->images && count($review->images))
        <div class="rv-imgs">
            @foreach($review->images as $img)
            <div class="rv-img-thumb">
                <img src="{{ $img }}" alt="Imagen de reseña" loading="lazy">
            </div>
            @endforeach
        </div>
        @endif

        <div class="rv-footer">
            <button type="button" class="ds-btn-ghost btn-sm"
                    data-bs-toggle="modal" data-bs-target="#editReviewModal{{ $review->id }}">
                <i class="bi bi-pencil me-1"></i> Editar
            </button>
            <button type="button" class="ds-btn-danger-ghost btn-sm">
                <i class="bi bi-trash me-1"></i> Eliminar
            </button>
        </div>
    </div>
    @empty
    <div class="ds-empty-state">
        <i class="bi bi-star"></i>
        <h3>Aún no tienes reseñas</h3>
        <p>Comparte tu experiencia sobre los productos que has comprado</p>
        <a href="{{ route('web.dashboard.orders') }}" class="ds-btn-primary">
            <i class="bi bi-box-seam me-1"></i> Ver mis Órdenes
        </a>
    </div>
    @endforelse
</div>

<style>
.rv-list { display: flex; flex-direction: column; gap: 16px; }

.rv-card {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    transition: box-shadow .25s;
}
.rv-card:hover { box-shadow: 0 6px 22px rgba(99,102,241,.1); }

/* Product row */
.rv-product {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 20px;
    background: #f8f9fb;
    border-bottom: 1px solid #eef0f3;
}

.rv-img {
    flex-shrink: 0;
    width: 64px; height: 64px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #eef0f3;
    background: #fff;
}
.rv-img img { width: 100%; height: 100%; object-fit: cover; }

.rv-product-info { flex: 1; min-width: 0; }
.rv-product-name {
    font-size: .93rem; font-weight: 700; color: #111827; margin: 0 0 5px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.rv-stars { display: flex; align-items: center; gap: 2px; margin-bottom: 4px; }
.rv-stars i { color: #fbbf24; font-size: .78rem; }
.rv-stars-num { font-size: .75rem; font-weight: 700; color: #6b7280; margin-left: 5px; }
.rv-date { font-size: .74rem; color: #9ca3af; }

/* Rating circle */
.rv-rating-circle {
    flex-shrink: 0;
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 800;
    border: 2px solid;
}
.rv-rating-5 { background: #f0fdf4; color: #15803d; border-color: #86efac; }
.rv-rating-4 { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
.rv-rating-3 { background: #fffbeb; color: #d97706; border-color: #fde68a; }
.rv-rating-2 { background: #fff7ed; color: #ea580c; border-color: #fed7aa; }
.rv-rating-1 { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }

/* Body */
.rv-body { padding: 16px 20px; }
.rv-body p { font-size: .88rem; color: #4b5563; line-height: 1.65; margin: 0; }

/* Images */
.rv-imgs { display: flex; gap: 8px; padding: 0 20px 16px; flex-wrap: wrap; }
.rv-img-thumb {
    width: 70px; height: 70px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #eef0f3;
    cursor: pointer;
    transition: transform .25s;
}
.rv-img-thumb:hover { transform: scale(1.06); }
.rv-img-thumb img { width: 100%; height: 100%; object-fit: cover; }

/* Footer */
.rv-footer {
    display: flex;
    gap: 8px;
    padding: 12px 20px;
    border-top: 1px solid #f0f0f2;
    background: #fafafa;
}

/* Ghost danger button */
.ds-btn-danger-ghost {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px;
    background: #fff;
    border: 1.5px solid #fecaca;
    border-radius: 8px;
    color: #b91c1c;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.ds-btn-danger-ghost:hover { background: #fef2f2; border-color: #f87171; }
</style>
@endsection