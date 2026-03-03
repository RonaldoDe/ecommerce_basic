@extends('layouts.web')

@section('content')

{{-- ══ HERO ═══════════════════════════════════════════════════════════ --}}
<section id="hero" class="hero section">
  <div class="hero-container">
    <div class="hero-content">
      <div class="content-wrapper" data-aos="fade-up" data-aos-delay="100">
        <span class="hero-label"><i class="bi bi-stars me-1"></i> Colección {{ date('Y') }}</span>
        <h1 class="hero-title">Descubre los<br><span class="hero-title-grad">mejores productos</span></h1>
        <p class="hero-description">Explora nuestra colección de artículos premium diseñados para mejorar tu estilo de vida. Calidad garantizada, envío rápido.</p>
        <div class="hero-actions" data-aos="fade-up" data-aos-delay="200">
          <a href="#categories" class="ix-btn-primary">
            <i class="bi bi-grid-3x3-gap me-2"></i> Explorar categorías
          </a>
          <a href="{{ route('web.search') }}" class="ix-btn-ghost">
            <i class="bi bi-search me-2"></i> Ver todo
          </a>
        </div>
        <div class="features-list" data-aos="fade-up" data-aos-delay="300">
          <div class="feature-item"><i class="bi bi-truck"></i><span>Envío gratis</span></div>
          <div class="feature-item"><i class="bi bi-award"></i><span>Garantía de calidad</span></div>
          <div class="feature-item"><i class="bi bi-headset"></i><span>Soporte 24/7</span></div>
        </div>
      </div>
    </div>

    <div class="hero-visuals">
      <div class="product-showcase" data-aos="fade-left" data-aos-delay="200">
        <div class="product-card featured">
          <div class="featured-img-wrap">
            <img src="assets/img/product/product-2.webp" alt="Featured Product">
            <div class="product-badge">Best Seller</div>
          </div>
          <div class="product-info">
            <h4>Premium Wireless Headphones</h4>
            <div class="price">
              <span class="sale-price">$299</span>
              <span class="original-price">$399</span>
            </div>
          </div>
        </div>
        <div class="product-grid">
          <div class="product-mini" data-aos="zoom-in" data-aos-delay="400">
            <img src="assets/img/product/product-3.webp" alt="Product">
            <span class="mini-price">$89</span>
          </div>
          <div class="product-mini" data-aos="zoom-in" data-aos-delay="500">
            <img src="assets/img/product/product-5.webp" alt="Product">
            <span class="mini-price">$149</span>
          </div>
        </div>
      </div>
      <div class="floating-elements">
        <div class="floating-icon cart-float" data-aos="fade-up" data-aos-delay="600">
          <i class="bi bi-cart3"></i><span class="notification-dot">3</span>
        </div>
        <div class="floating-icon wishlist-float" data-aos="fade-up" data-aos-delay="700">
          <i class="bi bi-heart-fill"></i>
        </div>
        <div class="floating-icon star-float" data-aos="fade-up" data-aos-delay="800">
          <i class="bi bi-star-fill"></i>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══ FLASH DEALS ════════════════════════════════════════════════════ --}}
<section id="flash-deals" class="flash-deals section">
  <div class="container">
    <div class="row align-items-center gy-5">
      <div class="col-lg-3" data-aos="fade-right">
        <div class="deals-header">
          <span class="deals-badge">⚡ Flash Sale</span>
          <h2>Ofertas por<br>Tiempo Limitado</h2>
          <div class="countdown" id="countdown">
            <div class="countdown-item"><span class="countdown-value" id="days">00</span><span class="countdown-label">Días</span></div>
            <div class="countdown-item"><span class="countdown-value" id="hours">00</span><span class="countdown-label">Horas</span></div>
            <div class="countdown-item"><span class="countdown-value" id="minutes">00</span><span class="countdown-label">Min</span></div>
            <div class="countdown-item"><span class="countdown-value" id="seconds">00</span><span class="countdown-label">Seg</span></div>
          </div>
        </div>
      </div>
      <div class="col-lg-9" data-aos="fade-left" data-aos-delay="100">
        <div class="deals-slider swiper init-swiper">
          <script type="application/json" class="swiper-config">{"loop":true,"speed":600,"autoplay":{"delay":5000},"slidesPerView":1,"spaceBetween":20,"breakpoints":{"768":{"slidesPerView":2},"992":{"slidesPerView":3}}}</script>
          <div class="swiper-wrapper">
            @foreach($flashDeals as $deal)
            <div class="swiper-slide">
              <div class="deal-card">
                <div class="deal-badge">−{{ $deal->discount_percentage }}%</div>
                <div class="deal-image">
                  <img src="{{ asset('storage/' . ($deal->images->first()?->image ?? 'products/default.png')) }}" alt="{{ $deal->name }}">
                </div>
                <div class="deal-info">
                  <h5>{{ Str::limit($deal->name, 40) }}</h5>
                  <div class="deal-price">
                    <span class="old-price">{{ $settings->badge }}{{ number_format($deal->selling_price, 2) }}</span>
                    <span class="new-price">{{ $settings->badge }}{{ number_format($deal->final_price, 2) }}</span>
                  </div>
                  <div class="deal-progress">
                    @php $sp = $deal->sales_count > 0 ? min(($deal->sales_count / ($deal->sales_count + $deal->stock)) * 100, 100) : 0; @endphp
                    <div class="progress"><div class="progress-bar" style="width:{{ $sp }}%"></div></div>
                    <span>Vendido: {{ round($sp) }}%</span>
                  </div>
                  <a href="{{ route('web.product.show', $deal->id) }}" class="btn-deal">
                    <i class="bi bi-lightning-fill me-1"></i> Comprar Ahora
                  </a>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══ NUEVOS PRODUCTOS ════════════════════════════════════════════════ --}}
<section id="new-arrivals" class="new-arrivals section">
  <div class="container">
    <div class="ix-section-head" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Recién llegados</span>
        <h2>Nuevos Productos</h2>
      </div>
      <a href="{{ route('web.search') }}" class="ix-see-all">Ver todos <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
      @foreach($newProducts as $product)
        @php $isFav = in_array($product->id, $favoriteIds ?? []); @endphp
        <div class="col-xl-3 col-lg-4 col-md-6">
          <div class="product-card-new" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">

            <div class="product-img">
              <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default.png')) }}"
                   alt="{{ $product->name }}" loading="lazy">

              <span class="pn-badge-new">Nuevo</span>
              @if($product->has_variants)
                <span class="pn-badge-var"><i class="bi bi-grid-3x3-gap-fill me-1"></i>Variantes</span>
              @endif

              <div class="quick-actions">
                <button type="button"
                        class="quick-btn js-fav {{ $isFav ? 'is-fav' : '' }}"
                        data-product-id="{{ $product->id }}"
                        title="{{ $isFav ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                  <i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i>
                </button>
                <a href="{{ route('web.product.show', $product->id) }}" class="quick-btn">
                  <i class="bi bi-eye"></i>
                </a>
              </div>
            </div>

            <div class="product-details">
              <span class="pn-category">{{ $product->category->name }}</span>
              <h5><a href="{{ route('web.product.show', $product->id) }}">{{ Str::limit($product->name, 40) }}</a></h5>
              <div class="pn-rating">
                @for($i = 1; $i <= 5; $i++)
                  <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                @endfor
                <span>({{ $product->reviews_count }})</span>
              </div>
              <div class="price-cart">
                <div class="price">
                  @if($product->is_on_sale)
                    <span class="old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                  @endif
                  <span class="current">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                </div>
                @if($product->has_variants)
                  <a href="{{ route('web.product.show', $product->id) }}" class="add-cart-btn add-cart-var" title="Ver opciones">
                    <i class="bi bi-grid-3x3-gap"></i>
                  </a>
                @else
                  <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-cart-btn js-cart" title="Agregar al carrito">
                      <i class="bi bi-cart-plus"></i>
                    </button>
                  </form>
                @endif
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ══ MÁS VENDIDOS ════════════════════════════════════════════════════ --}}
<section id="best-sellers" class="best-sellers section ix-bg-soft">
  <div class="container">
    <div class="ix-section-head" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Top ventas</span>
        <h2>Los Más Vendidos</h2>
      </div>
      <a href="{{ route('web.search') }}" class="ix-see-all">Ver todos <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
      @foreach($products as $product)
        @php $isFav = in_array($product->id, $favoriteIds ?? []); @endphp
        <div class="col-xl-3 col-lg-4 col-md-6">
          <div class="product-item" style="border-radius: 20px">

            <div class="product-image">
              {{-- Badges --}}
              @if($product->is_on_sale)
                <div class="product-badge sale-badge">−{{ $product->discount_percentage }}%</div>
              @endif
              @if($product->is_new && !$product->is_on_sale)
                <div class="product-badge new-badge-sm">Nuevo</div>
              @endif
              @if($product->has_variants)
                <div class="product-badge variants-badge-card"><i class="bi bi-grid-3x3-gap-fill"></i></div>
              @endif

              <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                   alt="{{ $product->name }}" loading="lazy">

              <div class="product-actions">
                <button type="button"
                        class="action-btn js-fav {{ $isFav ? 'is-fav' : '' }}"
                        data-product-id="{{ $product->id }}"
                        title="{{ $isFav ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                  <i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i>
                </button>
                <a href="{{ route('web.product.show', $product->id) }}" class="action-btn">
                  <i class="bi bi-eye"></i>
                </a>
              </div>

              @if($product->has_variants)
                <a href="{{ route('web.product.show', $product->id) }}" class="cart-btn">
                  <i class="bi bi-grid-3x3-gap me-1"></i> Ver opciones
                </a>
              @else
                <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $product->id }}">
                  <input type="hidden" name="quantity" value="1">
                  <button type="submit" class="cart-btn js-cart">
                    <i class="bi bi-cart-plus me-1"></i> Agregar al carrito
                  </button>
                </form>
              @endif
            </div>

            <div class="product-info">
              <span class="product-category">{{ $product->category->name }}</span>
              <h4 class="product-name">
                <a href="{{ route('web.product.show', $product->id) }}">{{ Str::limit($product->name, 50) }}</a>
              </h4>
              <div class="product-meta">
                <div class="product-rating">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                  @endfor
                  <span class="rating-count">({{ $product->reviews_count }})</span>
                </div>
                @if($product->has_variants)
                  <span class="meta-chip chip-var"><i class="bi bi-grid-3x3-gap-fill me-1"></i>{{ $product->variants->count() }} variantes</span>
                @else
                  <span class="meta-chip chip-stock-{{ $product->stock > 5 ? 'ok' : ($product->stock > 0 ? 'low' : 'out') }}">
                    {{ $product->stock > 0 ? $product->stock.' disponibles' : 'Agotado' }}
                  </span>
                @endif
              </div>
              <div class="product-price">
                @if($product->is_on_sale)
                  <span class="old-price">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                @endif
                <span class="current-price">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>

    @if($products->hasPages())
      <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
        <p class="ix-pag-info">Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }} productos</p>
        <div class="ix-pag">{{ $products->links('pagination::bootstrap-4') }}</div>
      </div>
    @endif
  </div>
</section>

{{-- ══ MARCAS ══════════════════════════════════════════════════════════ --}}
<section id="brands" class="brands section">
  <div class="container">
    <div class="ix-section-head ix-section-center" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Partners</span>
        <h2>Marcas Destacadas</h2>
      </div>
    </div>
    <div class="brands-slider swiper init-swiper" data-aos="fade-up" data-aos-delay="100">
      <script type="application/json" class="swiper-config">{"loop":true,"speed":600,"autoplay":{"delay":3000},"slidesPerView":2,"spaceBetween":24,"breakpoints":{"576":{"slidesPerView":3},"768":{"slidesPerView":4},"992":{"slidesPerView":5},"1200":{"slidesPerView":6}}}</script>
      <div class="swiper-wrapper align-items-center">
        @foreach($brands as $brand)
        <div class="swiper-slide">
          <div class="brand-item">
            @if($brand->logo)
              <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}">
            @else
              <span class="brand-name">{{ $brand->name }}</span>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- ══ CATEGORÍAS ══════════════════════════════════════════════════════ --}}
<section id="categories" class="categories section ix-bg-soft">
  <div class="container">
    <div class="ix-section-head ix-section-center" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Navega</span>
        <h2>Explora por Categoría</h2>
      </div>
    </div>
    <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
      @php $catIcons = ['bag','laptop','house-door','watch','phone','camera','headphones','bicycle','flower1','brush','cup-hot','tools']; @endphp
      @foreach($featuredCategories as $category)
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
          <a href="{{ route('web.search', ['category' => $category->id]) }}"
             class="category-card" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 60 }}">
            <div class="category-icon">
              <i class="bi bi-{{ $catIcons[$loop->index % count($catIcons)] }}"></i>
            </div>
            <h5>{{ $category->name }}</h5>
            <p>{{ $category->products_count }} productos</p>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ══ ESTILOS ══════════════════════════════════════════════════════════ --}}
<style>
/* ── Design tokens (heredados del layout) ──────────────────── */
/* --ds-indigo:#6366f1 | --ds-purple:#8b5cf6 | --ds-dark:#1e1b4b */

.section { padding: 80px 0; }
.ix-bg-soft { background: #f8f9fb; }

/* Section headers */
.ix-section-head {
  display: flex; justify-content: space-between;
  align-items: flex-end; margin-bottom: 40px;
  flex-wrap: wrap; gap: 12px;
}
.ix-section-center { justify-content: center; text-align: center; }
.ix-section-label {
  display: inline-block;
  font-size: .72rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: .12em;
  color: #6366f1; background: #eef2ff;
  padding: 4px 12px; border-radius: 20px; margin-bottom: 8px;
}
.ix-section-head h2 {
  font-family: 'Sora', sans-serif;
  font-size: 1.65rem; font-weight: 800;
  color: #111827; margin: 0;
}
.ix-see-all {
  display: inline-flex; align-items: center; gap: 2px;
  font-size: .84rem; font-weight: 600; color: #6366f1;
  text-decoration: none; transition: gap .2s; white-space: nowrap;
}
.ix-see-all:hover { color: #4f46e5; gap: 6px; }

/* ─────────────────────────────────────────────────────────────
   HERO
───────────────────────────────────────────────────────────── */
.hero {
  min-height: 580px;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
  position: relative; overflow: hidden; display: flex; align-items: center;
  padding: 80px 0;
}
.hero::after {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E");
  pointer-events: none;
}

.hero-container {
  max-width: 1280px; margin: 0 auto; padding: 0 24px;
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 48px; align-items: center;
  position: relative; z-index: 1;
}

/* Hero copy */
.hero-label {
  display: inline-flex; align-items: center;
  background: rgba(255,255,255,.12) !important;
  border: 1px solid rgba(255,255,255,.2);
  color: #a5b4fc !important; font-size: .78rem; font-weight: 700;
  padding: 5px 14px; border-radius: 20px;
  letter-spacing: .06em; text-transform: uppercase;
  margin-bottom: 18px;
}
.hero-title {
  font-family: 'Sora', sans-serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800; color: #fff !important;
  line-height: 1.15; margin: 0 0 18px;
}
.hero-title-grad {
  background: linear-gradient(135deg, #a5b4fc, #c4b5fd) !important;
  -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important;
  background-clip: text !important;
}
.hero-description {
  color: rgba(255,255,255,.75) !important;
  font-size: 1rem; line-height: 1.7;
  margin: 0 0 28px; max-width: 480px;
}
.hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 32px; }

.ix-btn-primary {
  display: inline-flex; align-items: center;
  padding: 13px 26px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; border-radius: 12px;
  font-weight: 700; font-size: .9rem; text-decoration: none;
  box-shadow: 0 4px 20px rgba(99,102,241,.4);
  transition: all .25s;
}
.ix-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(99,102,241,.5); color: #fff; }

.ix-btn-ghost {
  display: inline-flex; align-items: center;
  padding: 12px 24px;
  background: rgba(255,255,255,.1);
  border: 1.5px solid rgba(255,255,255,.25);
  color: #fff; border-radius: 12px;
  font-weight: 600; font-size: .9rem; text-decoration: none;
  transition: all .25s;
}
.ix-btn-ghost:hover { background: rgba(255,255,255,.18); color: #fff; }

.features-list { display: flex; gap: 20px; flex-wrap: wrap; }
.feature-item {
  display: flex; align-items: center; gap: 7px;
  font-size: .82rem; color: rgba(255,255,255,.75) !important; font-weight: 500;
}
.feature-item span { color: rgba(255,255,255,.75) !important; }
.feature-item i { color: #a5b4fc !important; font-size: 1rem; }

/* Hero visuals */
.hero-visuals { position: relative; }
.product-showcase { display: flex; flex-direction: column; gap: 14px; }

.product-card.featured {
  background: #fff; border-radius: 20px; overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,.3); position: relative;
}
.featured-img-wrap { position: relative; }
.featured-img-wrap img { width: 100%; height: 220px; object-fit: cover; display: block; }
.product-card.featured .product-badge {
  position: absolute; top: 14px; left: 14px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-size: .72rem; font-weight: 700;
  padding: 4px 12px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: .06em;
}
.product-card.featured .product-info {
  padding: 16px 18px;
  display: flex; justify-content: space-between; align-items: center;
}
.product-card.featured .product-info h4 {
  font-size: .9rem; font-weight: 700; color: #111827; margin: 0;
}
.product-card.featured .sale-price { font-size: 1.15rem; font-weight: 800; color: #6366f1; display: block; }
.product-card.featured .original-price { font-size: .78rem; color: #9ca3af; text-decoration: line-through; }

.product-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.product-mini {
  background: #fff; border-radius: 14px; overflow: hidden;
  position: relative; box-shadow: 0 8px 24px rgba(0,0,0,.18);
  transition: transform .3s;
}
.product-mini:hover { transform: translateY(-4px); }
.product-mini img { width: 100%; height: 110px; object-fit: cover; display: block; }
.mini-price {
  position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%);
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-size: .75rem; font-weight: 800;
  padding: 3px 12px; border-radius: 12px; white-space: nowrap;
}

/* Floating icons */
.floating-elements { position: absolute; inset: 0; pointer-events: none; }
.floating-icon {
  position: absolute; width: 44px; height: 44px;
  border-radius: 12px; display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; box-shadow: 0 8px 24px rgba(0,0,0,.2);
  animation: heroFloat 3s ease-in-out infinite;
}
.cart-float     { background: #fff; color: #6366f1; bottom: 20px; left: -16px; animation-delay: 0s; }
.wishlist-float { background: linear-gradient(135deg,#f43f5e,#e11d48); color: #fff; top: 20px; right: -10px; animation-delay: .6s; }
.star-float     { background: linear-gradient(135deg,#f59e0b,#d97706); color: #fff; top: 50%; right: -26px; animation-delay: 1.2s; }
.notification-dot {
  position: absolute; top: -4px; right: -4px;
  background: #ef4444; color: #fff; width: 18px; height: 18px;
  border-radius: 50%; font-size: .6rem; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid #fff;
}
@keyframes heroFloat {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-8px); }
}

/* ─────────────────────────────────────────────────────────────
   FLASH DEALS
───────────────────────────────────────────────────────────── */
.flash-deals {
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 55%, #4338ca 100%);
  padding: 80px 0;
}

.deals-header { color: #fff; }
.deals-badge {
  display: inline-block;
  background: rgba(255,255,255,.15);
  border: 1px solid rgba(255,255,255,.2);
  color: #fbbf24; font-size: .78rem; font-weight: 700;
  padding: 5px 16px; border-radius: 20px;
  margin-bottom: 14px; text-transform: uppercase; letter-spacing: .06em;
}
.deals-header h2 {
  font-family: 'Sora', sans-serif;
  font-size: 1.7rem; font-weight: 800; color: #fff;
  margin-bottom: 24px; line-height: 1.25;
}

.countdown { display: flex; gap: 10px; }
.countdown-item {
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.15);
  border-radius: 12px; padding: 12px 14px;
  text-align: center; min-width: 64px;
}
.countdown-value {
  display: block; font-family: 'Sora', sans-serif;
  font-size: 1.6rem; font-weight: 800; color: #fff;
  line-height: 1; margin-bottom: 2px;
}
.countdown-label { font-size: .65rem; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: .08em; }

/* Deal cards */
.deal-card {
  background: #fff; border-radius: 18px; overflow: hidden;
  position: relative;
  box-shadow: 0 8px 32px rgba(0,0,0,.15);
  transition: transform .3s, box-shadow .3s;
}
.deal-card:hover { transform: translateY(-4px); box-shadow: 0 18px 50px rgba(0,0,0,.22); }

.deal-badge {
  position: absolute; top: 12px; right: 12px;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #fff; font-size: .72rem; font-weight: 800;
  padding: 4px 12px; border-radius: 20px; z-index: 2;
}
.deal-image { height: 195px; overflow: hidden; }
.deal-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
.deal-card:hover .deal-image img { transform: scale(1.06); }

.deal-info { padding: 18px; }
.deal-info h5 {
  font-size: .9rem; font-weight: 700; color: #111827;
  margin: 0 0 10px; height: 42px; overflow: hidden;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.deal-price { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.deal-price .old-price { font-size: .82rem; color: #9ca3af; text-decoration: line-through; }
.deal-price .new-price { font-size: 1.3rem; font-weight: 800; color: #6366f1; }

.deal-progress { margin-bottom: 14px; }
.deal-progress .progress {
  height: 7px; background: #f3f4f6; border-radius: 10px;
  margin-bottom: 5px; overflow: hidden;
}
.deal-progress .progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #6366f1, #8b5cf6);
  border-radius: 10px;
}
.deal-progress span { font-size: .75rem; color: #6b7280; }

.btn-deal {
  display: flex; align-items: center; justify-content: center;
  width: 100%; padding: 11px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; border-radius: 10px;
  text-decoration: none; font-weight: 700; font-size: .86rem;
  transition: all .25s;
  box-shadow: 0 3px 12px rgba(99,102,241,.25);
}
.btn-deal:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,.4); color: #fff; }

/* ─────────────────────────────────────────────────────────────
   PRODUCT CARDS — Nuevos productos
───────────────────────────────────────────────────────────── */
.product-card-new {
  background: #fff;
  border: 1px solid #eef0f3;
  border-radius: 18px; overflow: hidden;
  transition: transform .3s, box-shadow .3s, border-color .3s;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
  height: 100%; display: flex; flex-direction: column;
}
.product-card-new:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 40px rgba(99,102,241,.12);
  border-color: #c7d2fe;
}

.product-img {
  position: relative; height: 250px; overflow: hidden; background: #f9fafb;
  padding: 0; /* contenedor de clip */
}
.product-img::before {
  /* padding interior visual sin afectar el clip */
  content: '';
  position: absolute; inset: 8px;
  border-radius: 10px;
  z-index: 0; pointer-events: none;
}
.product-img img {
  width: calc(100% - 16px); height: calc(100% - 16px);
  object-fit: cover; transition: transform .4s;
  position: absolute; top: 8px; left: 8px;
  border-radius: 10px;
}
.product-card-new:hover .product-img img { transform: scale(1.06); }

.pn-badge-new {
  position: absolute; top: 12px; left: 12px; z-index: 2;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff; font-size: .66rem; font-weight: 800;
  padding: 3px 10px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: .06em;
}
.pn-badge-var {
  position: absolute; top: 12px; right: 12px; z-index: 2;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-size: .66rem; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
  display: flex; align-items: center;
}

.quick-actions {
  position: absolute; bottom: 12px; right: 12px;
  display: flex; flex-direction: column; gap: 7px;
  opacity: 0; transform: translateX(10px);
  transition: all .3s; z-index: 3;
}
.product-card-new:hover .quick-actions { opacity: 1; transform: translateX(0); }

.quick-btn {
  width: 36px; height: 36px;
  background: #fff; border: none; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,.12);
  color: #374151; font-size: .88rem;
  text-decoration: none; transition: all .2s;
}
.quick-btn:hover { background: #6366f1; color: #fff; }
.quick-btn.is-fav { color: #ef4444; }
.quick-btn.is-fav:hover { background: #ef4444; color: #fff; }

.product-details { padding: 16px 18px 18px; flex: 1; display: flex; flex-direction: column; }
.pn-category {
  font-size: .68rem; font-weight: 800; color: #6366f1;
  text-transform: uppercase; letter-spacing: .08em; margin-bottom: 5px; display: block;
}
.product-details h5 {
  font-size: .92rem; font-weight: 700; color: #111827;
  margin: 0 0 8px; flex: 1;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.product-details h5 a { color: inherit; text-decoration: none; transition: color .2s; }
.product-details h5 a:hover { color: #6366f1; }

.pn-rating { display: flex; align-items: center; gap: 2px; margin-bottom: 12px; }
.pn-rating i { color: #fbbf24; font-size: .75rem; }
.pn-rating span { font-size: .73rem; color: #9ca3af; margin-left: 4px; }

.price-cart { display: flex; justify-content: space-between; align-items: center; }
.price-cart .price { display: flex; flex-direction: column; }
.price-cart .price .old { font-size: .75rem; color: #9ca3af; text-decoration: line-through; }
.price-cart .price .current { font-size: 1.05rem; font-weight: 800; color: #6366f1; }

.add-cart-btn {
  width: 40px; height: 40px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; border: none; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; font-size: .95rem;
  text-decoration: none; transition: all .25s;
  box-shadow: 0 3px 10px rgba(99,102,241,.25); flex-shrink: 0;
}
.add-cart-btn:hover { transform: scale(1.1); box-shadow: 0 6px 18px rgba(99,102,241,.4); color: #fff; }
.add-cart-btn.add-cart-var { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.add-cart-btn.btn-loading { opacity: .6; pointer-events: none; }

/* ─────────────────────────────────────────────────────────────
   PRODUCT ITEMS — Más vendidos
───────────────────────────────────────────────────────────── */
.product-item {
  background: #fff; border: 1px solid #eef0f3;
  border-radius: 18px; overflow: hidden;
  transition: transform .3s, box-shadow .3s, border-color .3s;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.product-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 40px rgba(99,102,241,.12);
  border-color: #c7d2fe;
}

.product-image {
    position: relative;
  overflow: hidden;
  padding: 8px; /* agrega espacio interno */
}
.product-image img {
  width: calc(100% - 16px); height: calc(100% - 16px);
  object-fit: cover; transition: transform .4s;
  border-radius: 10px;
}
.product-item:hover .product-image img { transform: scale(1.06); }

/* Badges — forzar ancho automático con !important para neutralizar main.css */
.product-badge {
  position: absolute; z-index: 2;
  font-size: .66rem !important; font-weight: 800;
  padding: 3px 10px; border-radius: 20px;
  display: inline-flex !important;
  width: auto !important; max-width: fit-content !important;
  align-items: center; line-height: 1.4;
}
.sale-badge    { top: 16px; left: 16px; background: linear-gradient(135deg,#ef4444,#dc2626) !important; color: #fff !important; }
.new-badge-sm  { top: 16px; left: 16px; background: linear-gradient(135deg,#10b981,#059669) !important; color: #fff !important; }
.variants-badge-card {
  top: 16px; right: 16px; left: auto !important;
  background: linear-gradient(135deg,#6366f1,#8b5cf6) !important;
  color: #fff !important; padding: 4px 10px;
}

.product-actions {
  position: absolute; top: 12px; right: 12px;
  display: flex; flex-direction: column; gap: 7px;
  opacity: 0; transform: translateX(10px);
  transition: all .3s; z-index: 3;
}
.product-item:hover .product-actions { opacity: 1; transform: translateX(0); }

.action-btn {
  width: 36px; height: 36px;
  background: #fff; border: none; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,.12);
  color: #374151; font-size: .88rem;
  text-decoration: none; transition: all .2s;
}
.action-btn:hover { background: #6366f1; color: #fff; }
.action-btn.is-fav { color: #ef4444; }
.action-btn.is-fav:hover { background: #ef4444; color: #fff; }

/* Cart button (slide up from bottom) */
.cart-btn {
  position: absolute; bottom: 8px; left: 8px; right: 8px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-size: .84rem; font-weight: 700;
  padding: 11px; text-align: center;
  text-decoration: none; border: none; cursor: pointer;
  transform: translateY(120%); transition: transform .3s;
  box-shadow: 0 -4px 16px rgba(99,102,241,.25);
}
.product-item:hover .cart-btn { transform: translateY(0); }
.js-cart-form { margin: 0; }

/* Product info */
.product-info { padding: 16px 18px 18px; }
.product-category {
  display: block; font-size: .68rem; font-weight: 800; color: #6366f1;
  text-transform: uppercase; letter-spacing: .08em; margin-bottom: 5px;
}
.product-name {
  font-size: .92rem; font-weight: 700; color: #111827;
  margin: 0 0 10px;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  min-height: 2.6em;
}
.product-name a { color: inherit; text-decoration: none; transition: color .2s; }
.product-name a:hover { color: #6366f1; }

.product-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
.product-rating { display: flex; align-items: center; gap: 2px; }
.product-rating i { color: #fbbf24; font-size: .73rem; }
.rating-count { font-size: .72rem; color: #9ca3af; margin-left: 3px; }

.meta-chip { font-size: .68rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; display: inline-flex; align-items: center; }
.chip-var           { background: #eef2ff; color: #6366f1; border: 1px solid #c7d2fe; }
.chip-stock-ok      { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.chip-stock-low     { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.chip-stock-out     { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

.product-price { display: flex; align-items: center; gap: 8px; margin-top: 4px; }
.old-price    { font-size: .78rem; color: #9ca3af; text-decoration: line-through; }
.current-price { font-size: 1.1rem; font-weight: 800; color: #6366f1; }

/* ─────────────────────────────────────────────────────────────
   BRANDS
───────────────────────────────────────────────────────────── */
.brand-item {
  display: flex; align-items: center; justify-content: center;
  padding: 18px; height: 88px;
  background: #fff; border-radius: 14px;
  border: 1px solid #eef0f3;
  transition: all .3s;
}
.brand-item:hover { box-shadow: 0 6px 24px rgba(99,102,241,.1); transform: translateY(-3px); border-color: #c7d2fe; }
.brand-item img { max-width: 110px; max-height: 48px; object-fit: contain; filter: grayscale(1); opacity: .55; transition: all .3s; }
.brand-item:hover img { filter: grayscale(0); opacity: 1; }
.brand-name { font-family: 'Sora', sans-serif; font-weight: 800; font-size: 1rem; color: #374151; }

/* ─────────────────────────────────────────────────────────────
   CATEGORIES
───────────────────────────────────────────────────────────── */
.category-card {
  display: flex; flex-direction: column; align-items: center;
  background: #fff; border: 1.5px solid #eef0f3;
  border-radius: 18px; padding: 28px 16px 20px;
  text-align: center; text-decoration: none;
  transition: all .3s;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.category-card:hover {
  border-color: #6366f1; transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(99,102,241,.15);
}
.category-icon {
  width: 72px; height: 72px; border-radius: 18px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 14px; transition: transform .3s;
  box-shadow: 0 6px 20px rgba(99,102,241,.3);
}
.category-card:hover .category-icon { transform: scale(1.08) rotate(-4deg); }
.category-icon i { font-size: 1.8rem; color: #fff; }
.category-card h5 { font-size: .9rem; font-weight: 700; color: #111827; margin: 0 0 4px; }
.category-card p { font-size: .75rem; color: #9ca3af; margin: 0; }

/* ─────────────────────────────────────────────────────────────
   PAGINATION
───────────────────────────────────────────────────────────── */
.ix-pag-info { font-size: .84rem; color: #6b7280; margin: 0; }
.ix-pag .pagination { gap: 4px; margin: 0; }
.ix-pag .page-link {
  border-radius: 8px; border: 1px solid #e5e7eb;
  color: #374151; font-weight: 600; font-size: .84rem;
  padding: 6px 12px; transition: all .2s;
}
.ix-pag .page-link:hover { background: #eef2ff; border-color: #c7d2fe; color: #6366f1; }
.ix-pag .page-item.active .page-link {
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  border-color: transparent; color: #fff;
  box-shadow: 0 2px 8px rgba(99,102,241,.3);
}

/* ─────────────────────────────────────────────────────────────
   AJAX STATES
───────────────────────────────────────────────────────────── */
.js-fav.is-fav { color: #ef4444 !important; }
.js-fav i { transition: transform .2s cubic-bezier(.34,1.56,.64,1), color .2s; }
.js-fav.pop i { transform: scale(1.5); }
.btn-loading { opacity: .6; pointer-events: none; }
.js-cart-form { margin: 0; padding: 0; }

/* Toast */
#toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
.toast-msg { display: flex; align-items: center; gap: 12px; padding: 14px 20px; border-radius: 12px; font-size: .88rem; font-weight: 500; min-width: 240px; max-width: 320px; pointer-events: all; color: #fff; box-shadow: 0 8px 30px rgba(0,0,0,.18); animation: toastIn .35s cubic-bezier(.34,1.56,.64,1) forwards; }
.toast-msg.t-success { background: #1e1b4b; border-left: 4px solid #4ade80; }
.toast-msg.t-error   { background: #1e1b4b; border-left: 4px solid #f87171; }
.toast-msg.t-warning { background: #1e1b4b; border-left: 4px solid #fbbf24; }
.toast-msg.t-info    { background: #1e1b4b; border-left: 4px solid #60a5fa; }
.toast-msg i { font-size: 1.1rem; flex-shrink: 0; }
.toast-msg.t-success i { color: #4ade80; }
.toast-msg.t-error   i { color: #f87171; }
.toast-msg.t-warning i { color: #fbbf24; }
.toast-msg.t-info    i { color: #60a5fa; }
.toast-msg.leaving { animation: toastOut .25s ease forwards; }
@keyframes toastIn  { from{opacity:0;transform:translateY(16px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes toastOut { from{opacity:1;transform:translateY(0) scale(1)} to{opacity:0;transform:translateY(8px) scale(.95)} }

/* ─────────────────────────────────────────────────────────────
   RESPONSIVE
───────────────────────────────────────────────────────────── */
@media (max-width: 991.98px) {
  .hero-container { grid-template-columns: 1fr; }
  .hero-visuals { display: none; }
  .hero { min-height: auto; padding: 60px 0; }
  .hero-title { font-size: 2rem; }
}
@media (max-width: 575.98px) {
  .hero-actions { flex-direction: column; }
  .ix-btn-primary, .ix-btn-ghost { justify-content: center; }
  .countdown { gap: 6px; }
  .countdown-item { min-width: 52px; padding: 10px 8px; }
  .countdown-value { font-size: 1.2rem; }
}
</style>

{{-- ══ JAVASCRIPT ══════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    var FAV_URL = '{{ route("web.favorites.store") }}';
    var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};

    /* ── Toast ── */
    var wrap = document.createElement('div');
    wrap.id = 'toast-container';
    document.body.appendChild(wrap);
    var ICONS = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };

    function showToast(msg, type, ms) {
        var el = document.createElement('div');
        el.className = 'toast-msg t-' + (type || 'success');
        el.innerHTML = '<i class="bi ' + (ICONS[type] || ICONS.success) + '"></i><span>' + msg + '</span>';
        wrap.appendChild(el);
        setTimeout(function () {
            el.classList.add('leaving');
            setTimeout(function () { if (el.parentNode) el.remove(); }, 300);
        }, ms || 3000);
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]'); if (m) return m.content;
        var i = document.querySelector('input[name="_token"]');     return i ? i.value : '';
    }
    function badge(id, val) { if (val == null) return; var el = document.getElementById(id); if (el) el.textContent = val; }

    /* ── Favoritos ── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-fav'); if (!btn) return;
        if (!IS_AUTH) { showToast('Inicia sesión para guardar favoritos', 'warning'); return; }
        var pid = btn.getAttribute('data-product-id');
        if (!pid || btn.classList.contains('btn-loading')) return;
        btn.classList.add('btn-loading');
        fetch(FAV_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrf(), 'Accept':'application/json' },
            body: JSON.stringify({ product_id: pid })
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            btn.classList.remove('btn-loading');
            var icon  = btn.querySelector('i');
            var added = (data.status === 'added' || data.added === true);
            if (added) {
                btn.classList.add('is-fav');
                if (icon) { icon.classList.remove('bi-heart'); icon.classList.add('bi-heart-fill'); }
                btn.title = 'Quitar de favoritos';
                showToast('Añadido a favoritos ❤️', 'success');
            } else {
                btn.classList.remove('is-fav');
                if (icon) { icon.classList.remove('bi-heart-fill'); icon.classList.add('bi-heart'); }
                btn.title = 'Agregar a favoritos';
                showToast('Eliminado de favoritos', 'info');
            }
            btn.classList.add('pop');
            setTimeout(function(){ btn.classList.remove('pop'); }, 350);
            badge('fav-badge', data.count);
        })
        .catch(function(){ btn.classList.remove('btn-loading'); showToast('Error al actualizar favoritos', 'error'); });
    });

    /* ── Carrito ── */
    document.addEventListener('submit', function (e) {
        var cartBtn = e.target.querySelector('.js-cart'); if (!cartBtn) return;
        e.preventDefault();
        if (!IS_AUTH) { showToast('Inicia sesión para agregar al carrito', 'warning'); return; }
        var form = e.target, orig = cartBtn.innerHTML;
        cartBtn.classList.add('btn-loading');
        cartBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN':csrf(), 'Accept':'application/json' },
            body: new FormData(form)
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            cartBtn.classList.remove('btn-loading');
            if (data.status === 200 || data.success) {
                cartBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
                showToast(data.message || 'Agregado al carrito 🛒', 'success');
                badge('cart-badge', data.count);
                setTimeout(function(){ cartBtn.innerHTML = orig; }, 1400);
            } else {
                cartBtn.innerHTML = orig;
                showToast(data.message || 'No se pudo agregar', 'error');
            }
        })
        .catch(function(){ cartBtn.classList.remove('btn-loading'); cartBtn.innerHTML = orig; showToast('Error de conexión', 'error'); });
    });

    /* ── Countdown ── */
    var endTime = Date.now() + 86400000;
    function pad(n){ return String(Math.floor(n)).padStart(2,'0'); }
    function tick(){
        var d = endTime - Date.now(); if (d < 0) return;
        var el;
        el = document.getElementById('days');    if(el) el.textContent = pad(d/86400000);
        el = document.getElementById('hours');   if(el) el.textContent = pad((d%86400000)/3600000);
        el = document.getElementById('minutes'); if(el) el.textContent = pad((d%3600000)/60000);
        el = document.getElementById('seconds'); if(el) el.textContent = pad((d%60000)/1000);
        setTimeout(tick, 1000);
    }
    tick();

})();
</script>

@endsection