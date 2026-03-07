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

    @php
        $bestSellers = \App\Models\Product::with('images')
            ->bestSellers()
            ->get()
            ->keyBy('best_seller');

        $main  = $bestSellers->get(1);
        $mini1 = $bestSellers->get(2);
        $mini2 = $bestSellers->get(3);
    @endphp

    <div class="hero-visuals">
      <div class="product-showcase" data-aos="fade-left" data-aos-delay="200">

          {{-- Tarjeta principal --}}
          @if($main)
              <a href="{{ route('web.product.show', $main) }}" class="text-decoration-none">
                  <div class="product-card featured">
                      <div class="featured-img-wrap">
                          <img
                              src="{{ $main->images->first()
                                  ? Storage::url($main->images->first()->image)
                                  : asset('assets/img/product/default.webp') }}"
                              alt="{{ $main->name }}">
                          <div class="product-badge">Best Seller</div>
                      </div>
                      <div class="product-info">
                          <h4>{{ $main->name }}</h4>
                          <div class="price">
                              @if($main->is_on_sale)
                                  <span class="sale-price">
                                      ${{ number_format($main->final_price, 2) }}
                                  </span>
                                  <span class="original-price">
                                      ${{ number_format($main->selling_price, 2) }}
                                  </span>
                              @else
                                  <span class="sale-price">
                                      ${{ number_format($main->selling_price, 2) }}
                                  </span>
                              @endif
                          </div>
                      </div>
                  </div>
              </a>
          @endif

          {{-- Mini cards --}}
          <div class="product-grid">
              @foreach([$mini1, $mini2] as $i => $mini)
                  @if($mini)
                      <a href="{{ route('web.product.show', $mini) }}" class="text-decoration-none">
                          <div class="product-mini"
                              data-aos="zoom-in"
                              data-aos-delay="{{ 400 + ($i * 100) }}">
                              <img
                                  src="{{ $mini->images->first()
                                      ? Storage::url($mini->images->first()->image)
                                      : asset('assets/img/product/default.webp') }}"
                                  alt="{{ $mini->name }}">
                              <span class="mini-price">
                                  ${{ number_format($mini->final_price, 2) }}
                              </span>
                          </div>
                      </a>
                  @endif
              @endforeach
          </div>

      </div>

      {{-- Floating elements — sin cambios --}}
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
        <div class="deals-slider-wrap" style="position:relative; padding: 0 48px;">

          <button class="bs-nav-btn bs-prev" id="dealsPrev" aria-label="Anterior">
            <i class="bi bi-chevron-left"></i>
          </button>

          <div class="deals-slider swiper init-swiper" id="dealsSwiper">
            <script type="application/json" class="swiper-config">
              {
                "loop": true,
                "speed": 600,
                "autoplay": { "delay": 5000, "disableOnInteraction": false },
                "slidesPerView": 1,
                "spaceBetween": 20,
                "navigation": {
                  "nextEl": "#dealsNext",
                  "prevEl": "#dealsPrev"
                },
                "breakpoints": {
                  "768": { "slidesPerView": 2 },
                  "992": { "slidesPerView": 3 }
                }
              }
            </script>
            <div class="swiper-wrapper">
              @foreach($flashDeals as $deal)
                <div class="swiper-slide">
                  <div class="deal-card">
                    <div class="deal-badge">−{{ $deal->discount_percentage }}%</div>
                    <div class="deal-image">
                      <img src="{{ asset('storage/' . ($deal->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                          alt="{{ $deal->name }}">
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

          <button class="bs-nav-btn bs-next" id="dealsNext" aria-label="Siguiente">
            <i class="bi bi-chevron-right"></i>
          </button>

        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══ PROMO CARDS ════════════════════════════════════════════════════ --}}
<section id="promo-cards" class="promo-cards section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">

      {{-- Card grande — primera categoría --}}
      @php $featuredCat = $featuredCategories->first(); @endphp
      @if($featuredCat)
        <div class="col-lg-6">
          <a href="{{ route('web.search', ['category' => $featuredCat->id]) }}"
             class="promo-featured" data-aos="fade-right" data-aos-delay="200">
            <div class="promo-featured-img">
              @if($featuredCat->image)
                <img src="{{ asset('storage/' . $featuredCat->image) }}"
                     alt="{{ $featuredCat->name }}" class="img-fluid">
              @else
                <div class="promo-img-placeholder">
                  <i class="bi bi-grid-3x3-gap"></i>
                </div>
              @endif
              <div class="promo-featured-overlay"></div>
            </div>
            <div class="promo-featured-content">
              <span class="promo-tag">Destacado</span>
              <h2>{{ $featuredCat->name }}</h2>
              <p>Explora nuestra selección de productos en esta categoría. Calidad garantizada en cada artículo.</p>
              <span class="promo-btn">
                Ver colección <i class="bi bi-arrow-right ms-1"></i>
              </span>
            </div>
          </a>
        </div>
      @endif

      {{-- 4 cards pequeñas — siguientes categorías --}}
      <div class="col-lg-6">
        <div class="row gy-4">
          @foreach($featuredCategories->skip(1)->take(4) as $cat)
            <div class="col-xl-6" data-aos="fade-up" data-aos-delay="{{ 300 + ($loop->index * 100) }}">
              <a href="{{ route('web.search', ['category' => $cat->id]) }}"
                 class="promo-mini-card">
                <div class="promo-mini-img">
                  @if($cat->image)
                    <img src="{{ asset('storage/' . $cat->image) }}"
                         alt="{{ $cat->name }}" class="img-fluid">
                  @else
                    <div class="promo-img-placeholder">
                      <i class="bi bi-tag"></i>
                    </div>
                  @endif
                  <div class="promo-mini-overlay"></div>
                </div>
                <div class="promo-mini-content">
                  <h4>{{ $cat->name }}</h4>
                  <p>{{ $cat->products_count }} productos</p>
                  <span class="promo-mini-link">
                    Ver ahora <i class="bi bi-arrow-right ms-1"></i>
                  </span>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ══ CARDS SECTION ════════════════════════════════════════════════ --}}
<section id="cards" class="cards-section section ix-bg-soft">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="ix-section-head ix-section-center mb-5" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Selección</span>
        <h2>Productos Destacados</h2>
      </div>
    </div>

    <div class="row gy-4">

      {{-- Columna 1: Trending --}}
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="cs-column">
          <div class="cs-column-header cs-header-trending">
            <div class="cs-header-icon">
              <i class="bi bi-fire"></i>
            </div>
            <div>
              <h3>Tendencias</h3>
              <span>Lo más visto ahora</span>
            </div>
          </div>
          <div class="cs-product-list">
            @foreach($trendingProducts ?? [] as $product)
              <a href="{{ route('web.product.show', $product->id) }}" class="cs-product-card">
                <div class="cs-product-img">
                  <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                       alt="{{ $product->name }}">
                  @if($product->is_new)
                    <span class="cs-badge cs-badge-new">Nuevo</span>
                  @endif
                </div>
                <div class="cs-product-info">
                  <h4>{{ Str::limit($product->name, 35) }}</h4>
                  <div class="cs-rating">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                    @endfor
                    <span>({{ $product->reviews_count }})</span>
                  </div>
                  <div class="cs-price">
                    @if($product->is_on_sale)
                      <span class="cs-price-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                    @endif
                    <span class="cs-price-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Columna 2: Best Sellers --}}
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="cs-column">
          <div class="cs-column-header cs-header-best">
            <div class="cs-header-icon">
              <i class="bi bi-award"></i>
            </div>
            <div>
              <h3>Más Vendidos</h3>
              <span>Los favoritos del momento</span>
            </div>
          </div>
          <div class="cs-product-list">
            @foreach($topSellers ?? [] as $product)
              <a href="{{ route('web.product.show', $product->id) }}" class="cs-product-card">
                <div class="cs-product-img">
                  <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                       alt="{{ $product->name }}">
                  @if($product->is_on_sale)
                    <span class="cs-badge cs-badge-sale">-{{ round($product->discount_percentage) }}%</span>
                  @endif
                </div>
                <div class="cs-product-info">
                  <h4>{{ Str::limit($product->name, 35) }}</h4>
                  <div class="cs-rating">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                    @endfor
                    <span>({{ $product->reviews_count }})</span>
                  </div>
                  <div class="cs-price">
                    @if($product->is_on_sale)
                      <span class="cs-price-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                    @endif
                    <span class="cs-price-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Columna 3: Destacados --}}
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="cs-column">
          <div class="cs-column-header cs-header-featured">
            <div class="cs-header-icon">
              <i class="bi bi-star"></i>
            </div>
            <div>
              <h3>Destacados</h3>
              <span>Selección especial</span>
            </div>
          </div>
          <div class="cs-product-list">
            @foreach($featuredProducts ?? [] as $product)
              <a href="{{ route('web.product.show', $product->id) }}" class="cs-product-card">
                <div class="cs-product-img">
                  <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                       alt="{{ $product->name }}">
                  @if($product->stock <= $product->stock_alert && $product->stock > 0)
                    <span class="cs-badge cs-badge-hot">Últ uds.</span>
                  @endif
                </div>
                <div class="cs-product-info">
                  <h4>{{ Str::limit($product->name, 35) }}</h4>
                  <div class="cs-rating">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                    @endfor
                    <span>({{ $product->reviews_count }})</span>
                  </div>
                  <div class="cs-price">
                    @if($product->is_on_sale)
                      <span class="cs-price-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                    @endif
                    <span class="cs-price-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                  </div>
                </div>
              </a>
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
      <a href="{{ route('web.search', ['newProducts' => 1]) }}" class="ix-see-all">Ver todos <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    @php $chunksNew = $newProducts->chunk(10); @endphp

    <div class="bs-carousel-wrap" data-aos="fade-up" data-aos-delay="100">
      <button class="bs-nav-btn bs-prev" id="newPrev" aria-label="Anterior">
        <i class="bi bi-chevron-left"></i>
      </button>

      <div class="bs-swiper" id="newSwiper">
        <div class="swiper-wrapper">
          @foreach($chunksNew as $chunk)
            <div class="swiper-slide">
              <div class="bs-grid">
                @foreach($chunk as $product)
                  @php $isFav = in_array($product->id, $favoriteIds ?? []); @endphp
                  <div class="sr-card">
                    <div class="sr-card-img">
                      <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                           alt="{{ $product->name }}" loading="lazy">

                      @if($product->is_on_sale)
                        <span class="sr-badge sr-badge-sale">−{{ round($product->discount_percentage) }}%</span>
                      @elseif($product->is_new)
                        <span class="sr-badge sr-badge-new">Nuevo</span>
                      @endif
                      @if($product->has_variants)
                        <span class="sr-badge sr-badge-var">
                          <i class="bi bi-grid-3x3-gap-fill me-1"></i>Variantes
                        </span>
                      @endif

                      <div class="sr-card-overlay">
                        <button type="button"
                                class="sr-act-btn js-fav {{ $isFav ? 'is-fav' : '' }}"
                                data-product-id="{{ $product->id }}"
                                title="{{ $isFav ? 'Quitar de favoritos' : 'Guardar' }}">
                          <i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i>
                        </button>
                        <a href="{{ route('web.product.show', $product->id) }}" class="sr-act-btn" title="Ver producto">
                          <i class="bi bi-eye"></i>
                        </a>
                      </div>

                      @if($product->has_variants)
                        <a href="{{ route('web.product.show', $product->id) }}" class="sr-cart-slide">
                          <i class="bi bi-grid-3x3-gap me-1"></i> Ver opciones
                        </a>
                      @else
                        <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                          @csrf
                          <input type="hidden" name="product_id" value="{{ $product->id }}">
                          <input type="hidden" name="quantity" value="1">
                          <button type="submit" class="sr-cart-slide js-cart">
                            <i class="bi bi-cart-plus me-1"></i> Agregar al carrito
                          </button>
                        </form>
                      @endif
                    </div>

                    <div class="sr-card-body">
                      <span class="sr-card-cat">{{ $product->category->name }}</span>
                      <h5 class="sr-card-name">
                        <a href="{{ route('web.product.show', $product->id) }}">
                          {{ Str::limit($product->name, 40) }}
                        </a>
                      </h5>
                      <div class="sr-card-meta">
                        <div class="sr-stars">
                          @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                          @endfor
                          <span>({{ $product->reviews_count }})</span>
                        </div>
                        @if($product->has_variants)
                          <span class="sr-chip sr-chip-var">{{ $product->variants->count() }} variantes</span>
                        @else
                          <span class="sr-chip {{ $product->stock > 5 ? 'sr-chip-ok' : ($product->stock > 0 ? 'sr-chip-low' : 'sr-chip-out') }}">
                            {{ $product->stock > 0 ? $product->stock.' uds.' : 'Agotado' }}
                          </span>
                        @endif
                      </div>
                      <div class="sr-card-price">
                        @if($product->is_on_sale)
                          <span class="sr-p-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                        @endif
                        <span class="sr-p-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <button class="bs-nav-btn bs-next" id="newNext" aria-label="Siguiente">
        <i class="bi bi-chevron-right"></i>
      </button>
      <div class="bs-dots" id="newDots"></div>
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
      <a href="{{ route('web.search', ['bestSellers' => 1]) }}" class="ix-see-all">Ver todos <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    @php $chunksBest = collect($products->items())->chunk(10); @endphp

    <div class="bs-carousel-wrap" data-aos="fade-up" data-aos-delay="100">
      <button class="bs-nav-btn bs-prev" id="bestPrev" aria-label="Anterior">
        <i class="bi bi-chevron-left"></i>
      </button>

      <div class="bs-swiper" id="bestSwiper">
        <div class="swiper-wrapper">
          @foreach($chunksBest as $chunk)
            <div class="swiper-slide">
              <div class="bs-grid">
                @foreach($chunk as $product)
                  @php $isFav = in_array($product->id, $favoriteIds ?? []); @endphp
                  <div class="sr-card">
                    <div class="sr-card-img">
                      <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                           alt="{{ $product->name }}" loading="lazy">

                      @if($product->is_on_sale)
                        <span class="sr-badge sr-badge-sale">−{{ round($product->discount_percentage) }}%</span>
                      @elseif($product->is_new)
                        <span class="sr-badge sr-badge-new">Nuevo</span>
                      @endif
                      @if($product->has_variants)
                        <span class="sr-badge sr-badge-var">
                          <i class="bi bi-grid-3x3-gap-fill me-1"></i>Variantes
                        </span>
                      @endif

                      <div class="sr-card-overlay">
                        <button type="button"
                                class="sr-act-btn js-fav {{ $isFav ? 'is-fav' : '' }}"
                                data-product-id="{{ $product->id }}"
                                title="{{ $isFav ? 'Quitar de favoritos' : 'Guardar' }}">
                          <i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i>
                        </button>
                        <a href="{{ route('web.product.show', $product->id) }}" class="sr-act-btn" title="Ver producto">
                          <i class="bi bi-eye"></i>
                        </a>
                      </div>

                      @if($product->has_variants)
                        <a href="{{ route('web.product.show', $product->id) }}" class="sr-cart-slide">
                          <i class="bi bi-grid-3x3-gap me-1"></i> Ver opciones
                        </a>
                      @else
                        <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                          @csrf
                          <input type="hidden" name="product_id" value="{{ $product->id }}">
                          <input type="hidden" name="quantity" value="1">
                          <button type="submit" class="sr-cart-slide js-cart">
                            <i class="bi bi-cart-plus me-1"></i> Agregar al carrito
                          </button>
                        </form>
                      @endif
                    </div>

                    <div class="sr-card-body">
                      <span class="sr-card-cat">{{ $product->category->name }}</span>
                      <h5 class="sr-card-name">
                        <a href="{{ route('web.product.show', $product->id) }}">
                          {{ Str::limit($product->name, 40) }}
                        </a>
                      </h5>
                      <div class="sr-card-meta">
                        <div class="sr-stars">
                          @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                          @endfor
                          <span>({{ $product->reviews_count }})</span>
                        </div>
                        @if($product->has_variants)
                          <span class="sr-chip sr-chip-var">{{ $product->variants->count() }} variantes</span>
                        @else
                          <span class="sr-chip {{ $product->stock > 5 ? 'sr-chip-ok' : ($product->stock > 0 ? 'sr-chip-low' : 'sr-chip-out') }}">
                            {{ $product->stock > 0 ? $product->stock.' uds.' : 'Agotado' }}
                          </span>
                        @endif
                      </div>
                      <div class="sr-card-price">
                        @if($product->is_on_sale)
                          <span class="sr-p-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                        @endif
                        <span class="sr-p-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <button class="bs-nav-btn bs-next" id="bestNext" aria-label="Siguiente">
        <i class="bi bi-chevron-right"></i>
      </button>
      <div class="bs-dots" id="bestDots"></div>
    </div>

    {{-- Paginación Laravel (si quieres mantenerla) --}}
    {{-- @if($products->hasPages())
      <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
        <p class="ix-pag-info">Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }} productos</p>
        <div class="ix-pag">{{ $products->links('pagination::bootstrap-4') }}</div>
      </div>
    @endif --}}
  </div>
</section>

{{-- ══ MARCAS ══════════════════════════════════════════════════════════ --}}
{{-- <section id="brands" class="brands section">
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
</section> --}}

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
              @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}"
                    alt="{{ $category->name }}"
                    style="width:100%; height:100%; object-fit:cover; border-radius:18px;">
              @else
                <i class="bi bi-{{ $catIcons[$loop->index % count($catIcons)] }}"></i>
              @endif
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

/* ─────────────────────────────────────────────────────────────
   CAROUSEL COMPARTIDO
───────────────────────────────────────────────────────────── */
.bs-carousel-wrap {
  position: relative;
  padding: 0 52px;
  overflow: hidden;
}

/* Contenedor con overflow oculto para la animación */
.bs-swiper {
  overflow: hidden;
  border-radius: 12px;
}
.bs-swiper .swiper-wrapper {
  display: flex;
  transition: transform .45s cubic-bezier(.4,0,.2,1);
  will-change: transform;
}
.bs-swiper .swiper-slide {
  min-width: 100%;
  flex-shrink: 0;
}

.bs-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  grid-template-rows: repeat(2, auto);
  gap: 18px;
  padding: 4px 2px; /* evita que box-shadow se corte */
}

.bs-nav-btn {
  position: absolute; top: 42%; transform: translateY(-50%);
  z-index: 10; width: 42px; height: 42px; border-radius: 50%;
  border: none; background: #fff; color: #6366f1;
  font-size: 1.1rem; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(99,102,241,.2);
  cursor: pointer; transition: all .25s;
}
.bs-nav-btn:hover {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; box-shadow: 0 6px 22px rgba(99,102,241,.4);
  transform: translateY(-50%) scale(1.08);
}
.bs-nav-btn:disabled { opacity: .3; pointer-events: none; }
.bs-prev { left: 0; }
.bs-next { right: 0; }

.bs-dots {
  display: flex; justify-content: center;
  gap: 8px; margin-top: 24px;
}
.bs-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #e5e7eb; border: none; cursor: pointer;
  padding: 0; transition: all .35s cubic-bezier(.4,0,.2,1);
}
.bs-dot.active {
  width: 28px; border-radius: 4px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

@media (max-width: 1199.98px) { .bs-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 991.98px)  { .bs-grid { grid-template-columns: repeat(3, 1fr); } .bs-carousel-wrap { padding: 0 40px; } }
@media (max-width: 575.98px)  { .bs-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; } .bs-carousel-wrap { padding: 0 32px; } }

/* ─────────────────────────────────────────────────────────────
   SR-CARD (copiado de search para usar en carouseles)
───────────────────────────────────────────────────────────── */
.sr-card {
  background: #fff; border: 1px solid #eef0f3; border-radius: 18px;
  overflow: hidden; transition: transform .3s, box-shadow .3s, border-color .3s;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
  display: flex; flex-direction: column;
}
.sr-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 40px rgba(99,102,241,.12);
  border-color: #c7d2fe;
}

.sr-card-img {
  position: relative; height: 200px; overflow: hidden; background: #f9fafb;
  flex-shrink: 0;
}
.sr-card-img img {
  width: calc(100% - 16px); height: calc(100% - 16px);
  object-fit: cover; position: absolute; top: 8px; left: 8px;
  border-radius: 10px; transition: transform .4s;
}
.sr-card:hover .sr-card-img img { transform: scale(1.06); }

.sr-badge {
  position: absolute; z-index: 2;
  width: auto !important;
  font-size: .66rem; font-weight: 800; padding: 3px 10px;
  border-radius: 20px; color: #fff;
  display: inline-flex !important; align-items: center;
}
.sr-badge-sale { top: 16px; left: 16px; background: linear-gradient(135deg,#ef4444,#dc2626); }
.sr-badge-new  { top: 16px; left: 16px; background: linear-gradient(135deg,#10b981,#059669); }
.sr-badge-var  { top: 16px; right: 16px; left: auto; background: linear-gradient(135deg,#6366f1,#8b5cf6); }

.sr-card-overlay {
  position: absolute; top: 16px; right: 16px;
  display: flex; flex-direction: column; gap: 7px;
  opacity: 0; transform: translateX(10px); transition: all .3s; z-index: 3;
}
.sr-card:hover .sr-card-overlay { opacity: 1; transform: translateX(0); }

.sr-act-btn {
  width: 36px; height: 36px; background: #fff; border: none;
  border-radius: 10px; display: flex; align-items: center; justify-content: center;
  cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,.12);
  color: #374151; font-size: .88rem; text-decoration: none; transition: all .2s;
}
.sr-act-btn:hover { background: #6366f1; color: #fff; }
.sr-act-btn.is-fav { color: #ef4444; }
.sr-act-btn.is-fav:hover { background: #ef4444 !important; color: #fff !important; }

.sr-cart-slide {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; font-size: .8rem; font-weight: 700;
  padding: 10px; text-align: center; text-decoration: none;
  border: none; cursor: pointer; width: 100%;
  display: flex; align-items: center; justify-content: center;
  transform: translateY(100%); transition: transform .3s;
}
.sr-card:hover .sr-cart-slide { transform: translateY(0); }

.sr-card-body {
  padding: 12px 14px 14px; flex: 1; display: flex; flex-direction: column;
}
.sr-card-cat {
  font-size: .65rem; font-weight: 800; color: #6366f1;
  text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; display: block;
}
.sr-card-name {
  font-size: .85rem; font-weight: 700; color: #111827; margin: 0 0 8px; flex: 1;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  min-height: 2.4em;
}
.sr-card-name a { color: inherit; text-decoration: none; transition: color .2s; }
.sr-card-name a:hover { color: #6366f1; }

.sr-card-meta {
  display: flex; align-items: center; gap: 6px;
  margin-bottom: 8px; flex-wrap: wrap;
}
.sr-stars { display: flex; align-items: center; gap: 2px; }
.sr-stars i { color: #fbbf24; font-size: .7rem; }
.sr-stars span { font-size: .7rem; color: #9ca3af; margin-left: 3px; }

.sr-chip {
  font-size: .65rem; font-weight: 700;
  padding: 2px 7px; border-radius: 12px;
}
.sr-chip-var  { background: #eef2ff; color: #6366f1; border: 1px solid #c7d2fe; }
.sr-chip-ok   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.sr-chip-low  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.sr-chip-out  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

.sr-card-price { display: flex; align-items: center; gap: 7px; margin-top: auto; padding-top: 8px; }
.sr-p-old { font-size: .75rem; color: #9ca3af; text-decoration: line-through; }
.sr-p-cur { font-size: 1rem; font-weight: 800; color: #6366f1; }

/* Flash deals — botones de navegación */
#dealsPrev, #dealsNext {
  background: rgba(255,255,255,.15);
  color: #fff;
  border: 1px solid rgba(255,255,255,.2);
  box-shadow: none;
  top: 42%;
}
#dealsPrev:hover, #dealsNext:hover {
  background: rgba(255,255,255,.95);
  color: #6366f1;
  box-shadow: 0 4px 18px rgba(0,0,0,.2);
}
#dealsPrev:disabled, #dealsNext:disabled {
  opacity: .25;
}

Reemplaza solo el CSS de .promo-mini-card y .promo-featured con este nuevo diseño:
css/* ─────────────────────────────────────────────────────────────
   PROMO CARDS — rediseño con imagen a la derecha recortada
───────────────────────────────────────────────────────────── */
.promo-cards { padding: 80px 0; background: #fff; }

/* ── Card grande ── */
.promo-featured {
  display: block; text-decoration: none;
  position: relative; border-radius: 24px; overflow: hidden;
  height: 100%; min-height: 460px;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
  box-shadow: 0 8px 32px rgba(99,102,241,.15);
  transition: transform .35s, box-shadow .35s;
}
.promo-featured:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 50px rgba(99,102,241,.25);
}

/* Imagen pegada a la derecha */
.promo-featured-img {
  position: absolute;
  top: 0; right: 0;
  width: 58%; height: 100%;
}
.promo-featured-img img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: top center;
  transition: transform .6s ease;
}
.promo-featured:hover .promo-featured-img img { transform: scale(1.04); }

/* Degradado que cubre la imagen hacia la izquierda */
.promo-featured-img::before {
  content: '';
  position: absolute; inset: 0; z-index: 1;
  background: linear-gradient(
    to right,
    #1e1b4b 0%,
    #312e81 30%,
    rgba(49,46,129,.5) 60%,
    transparent 100%
  );
}

.promo-img-placeholder {
  width: 100%; height: 100%;
  background: rgba(255,255,255,.05);
  display: flex; align-items: center; justify-content: center;
}
.promo-img-placeholder i { font-size: 4rem; color: rgba(255,255,255,.15); }

/* Contenido sobre la imagen */
.promo-featured-content {
  position: relative; z-index: 2;
  padding: 40px 36px;
  max-width: 55%;
  height: 100%;
  display: flex; flex-direction: column; justify-content: flex-end;
}
.promo-tag {
  display: inline-block;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-size: .7rem; font-weight: 800;
  padding: 4px 14px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: .1em;
  margin-bottom: 14px; width: fit-content;
}
.promo-featured-content h2 {
  font-family: 'Sora', sans-serif;
  font-size: 1.9rem; font-weight: 800;
  color: #fff; margin: 0 0 12px; line-height: 1.2;
}
.promo-featured-content p {
  color: rgba(255,255,255,.7);
  font-size: .88rem; line-height: 1.65;
  margin: 0 0 22px;
}
.promo-btn {
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff; color: #6366f1;
  font-size: .84rem; font-weight: 700;
  padding: 11px 22px; border-radius: 12px;
  width: fit-content;
  transition: all .25s;
  box-shadow: 0 4px 14px rgba(0,0,0,.2);
}
.promo-featured:hover .promo-btn {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  box-shadow: 0 6px 20px rgba(99,102,241,.4);
}

/* ── Cards pequeñas ── */
.promo-mini-card {
  display: flex; align-items: stretch;
  text-decoration: none;
  position: relative; border-radius: 18px; overflow: hidden;
  height: 210px;
  box-shadow: 0 4px 18px rgba(0,0,0,.08);
  transition: transform .3s, box-shadow .3s;
  background: #f8f9ff;
  border: 1px solid #eef0f3;
}
.promo-mini-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(99,102,241,.18);
  border-color: #c7d2fe;
}

/* Contenido a la izquierda */
.promo-mini-content {
  position: relative; z-index: 2;
  padding: 22px 20px;
  flex: 1;
  display: flex; flex-direction: column; justify-content: center;
}
.promo-mini-content h4 {
  font-family: 'Sora', sans-serif;
  font-size: 1rem; font-weight: 800;
  color: #111827; margin: 0 0 4px;
  transition: color .2s;
}
.promo-mini-card:hover .promo-mini-content h4 { color: #6366f1; }
.promo-mini-content p {
  font-size: .75rem; color: #6b7280;
  margin: 0 0 12px;
}
.promo-mini-link {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: .78rem; font-weight: 700; color: #6366f1;
  transition: gap .2s;
}
.promo-mini-card:hover .promo-mini-link { gap: 9px; }

/* Imagen a la derecha recortada */
.promo-mini-img {
  width: 45%; flex-shrink: 0;
  position: relative; overflow: hidden;
}
.promo-mini-img img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: top center;
  transition: transform .5s ease;
}
.promo-mini-card:hover .promo-mini-img img { transform: scale(1.06); }

/* Degradado suave entre texto e imagen */
.promo-mini-img::before {
  content: '';
  position: absolute; inset: 0; z-index: 1;
  background: linear-gradient(
    to right,
    #f8f9ff 0%,
    transparent 35%
  );
  pointer-events: none;
}

/* Responsive */
@media (max-width: 991.98px) {
  .promo-featured { min-height: 340px; }
  .promo-featured-content { max-width: 65%; }
  .promo-featured-img { width: 50%; }
}
@media (max-width: 575.98px) {
  .promo-featured { min-height: 300px; }
  .promo-featured-content { max-width: 75%; padding: 28px 22px; }
  .promo-featured-content h2 { font-size: 1.4rem; }
  .promo-mini-card { height: 160px; }
  .promo-mini-img { width: 42%; }
}

/* ─────────────────────────────────────────────────────────────
   CARDS SECTION — estilo lista limpia
───────────────────────────────────────────────────────────── */
.cards-section { padding: 80px 0; background: #fff; }

.cs-column {
  background: #fff;
  border: 1px solid #eef0f3;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 2px 16px rgba(0,0,0,.05);
  height: 100%;
}

/* Header */
.cs-column-header {
  display: flex; align-items: center; gap: 12px;
  padding: 18px 22px 16px;
  border-bottom: 2px solid #f3f4f6;
}
.cs-column-header h3 {
  font-family: 'Sora', sans-serif;
  font-size: 1.05rem; font-weight: 800;
  color: #111827; margin: 0;
}
.cs-column-header span {
  font-size: .72rem; color: #9ca3af; display: block; margin-top: 1px;
}
.cs-header-icon {
  width: 36px; height: 36px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; flex-shrink: 0;
}
/* Iconos con color sutil en lugar de fondo oscuro */
.cs-header-trending .cs-header-icon { background: #fff7ed; color: #f59e0b; }
.cs-header-best     .cs-header-icon { background: #eef2ff; color: #6366f1; }
.cs-header-featured .cs-header-icon { background: #f0fdf4; color: #10b981; }

/* Línea de color bajo el header */
.cs-header-trending { border-bottom-color: #fde68a; }
.cs-header-best     { border-bottom-color: #c7d2fe; }
.cs-header-featured { border-bottom-color: #a7f3d0; }

/* Lista */
.cs-product-list { padding: 6px 0; }

/* Card individual */
.cs-product-card {
  display: flex; align-items: center; gap: 16px;
  padding: 14px 20px;
  text-decoration: none;
  border-bottom: 1px solid #f3f4f6;
  transition: transform .25s cubic-bezier(.34,1.56,.64,1),
              background .2s,
              box-shadow .25s;
  cursor: pointer;
}
.cs-product-card:last-child { border-bottom: none; }

/* Animación: desliza a la derecha al hacer hover */
.cs-product-card:hover {
  transform: translateX(6px);
  background: #fafbff;
  box-shadow: inset 3px 0 0 #6366f1;
}

/* Imagen más grande */
.cs-product-img {
  position: relative; flex-shrink: 0;
  width: 120px; height: 120px;
  border-radius: 14px; overflow: hidden;
  background: #f3f4f6;
  box-shadow: 0 2px 10px rgba(0,0,0,.08);
  transition: box-shadow .25s;
}
.cs-product-card:hover .cs-product-img {
  box-shadow: 0 6px 18px rgba(99,102,241,.18);
}
.cs-product-img img {
  width: 100%; height: 100%; object-fit: cover;
  transition: transform .35s ease;
}
.cs-product-card:hover .cs-product-img img { transform: scale(1.08); }

/* Badges */
.cs-badge {
  position: absolute; top: 5px; left: 5px;
  font-size: .58rem; font-weight: 800;
  padding: 2px 7px; border-radius: 8px; color: #fff;
  text-transform: uppercase; letter-spacing: .04em;
  z-index: 2;
}
.cs-badge-new  { background: #10b981; }
.cs-badge-sale { background: #ef4444; }
.cs-badge-hot  { background: #f59e0b; color: #1c1917; }

/* Info */
.cs-product-info { flex: 1; min-width: 0; }
.cs-product-info h4 {
  font-size: .92rem; font-weight: 700; color: #111827;
  margin: 0 0 5px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  transition: color .2s;
}
.cs-product-card:hover .cs-product-info h4 { color: #6366f1; }

.cs-rating {
  display: flex; align-items: center; gap: 2px;
  margin-bottom: 6px;
}
.cs-rating i { color: #fbbf24; font-size: .68rem; }
.cs-rating span { font-size: .7rem; color: #9ca3af; margin-left: 4px; }

.cs-price { display: flex; align-items: center; gap: 8px; }
.cs-price-old {
  font-size: .75rem; color: #9ca3af;
  text-decoration: line-through;
}
.cs-price-cur {
  font-size: 1rem; font-weight: 800; color: #111827;
  transition: color .2s;
}
.cs-product-card:hover .cs-price-cur { color: #6366f1; }

/* Flecha que aparece en hover */
.cs-product-card::after {
  content: '\F285'; /* bi-arrow-right */
  font-family: 'bootstrap-icons';
  font-size: .9rem;
  color: #6366f1;
  opacity: 0;
  transform: translateX(-6px);
  transition: opacity .25s, transform .25s;
  flex-shrink: 0;
}
.cs-product-card:hover::after {
  opacity: 1;
  transform: translateX(0);
}

@media (max-width: 767.98px) {
  .cs-product-img { width: 68px; height: 68px; }
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
      var i = document.querySelector('input[name="_token"]'); return i ? i.value : '';
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
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: pid })
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
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
        setTimeout(function () { btn.classList.remove('pop'); }, 350);
        badge('fav-badge', data.count);
      })
      .catch(function () { btn.classList.remove('btn-loading'); showToast('Error al actualizar favoritos', 'error'); });
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
        headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
        body: new FormData(form)
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        cartBtn.classList.remove('btn-loading');
        if (data.status === 200 || data.success) {
          cartBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
          showToast(data.message || 'Agregado al carrito 🛒', 'success');
          badge('cart-badge', data.count);
          setTimeout(function () { cartBtn.innerHTML = orig; }, 1400);
        } else {
          cartBtn.innerHTML = orig;
          showToast(data.message || 'No se pudo agregar', 'error');
        }
      })
      .catch(function () { cartBtn.classList.remove('btn-loading'); cartBtn.innerHTML = orig; showToast('Error de conexión', 'error'); });
    });

    /* ── Countdown ── */
    var endTime = Date.now() + 86400000;
    function pad(n) { return String(Math.floor(n)).padStart(2, '0'); }
    function tick() {
      var d = endTime - Date.now(); if (d < 0) return;
      var el;
      el = document.getElementById('days');    if (el) el.textContent = pad(d / 86400000);
      el = document.getElementById('hours');   if (el) el.textContent = pad((d % 86400000) / 3600000);
      el = document.getElementById('minutes'); if (el) el.textContent = pad((d % 3600000) / 60000);
      el = document.getElementById('seconds'); if (el) el.textContent = pad((d % 60000) / 1000);
      setTimeout(tick, 1000);
    }
    tick();

    /* ── Carousel genérico ── */
    function initCarousel(swiperEl, prevEl, nextEl, dotsEl) {
      if (!swiperEl) return;

      var wrapper  = swiperEl.querySelector('.swiper-wrapper');
      var slides   = swiperEl.querySelectorAll('.swiper-slide');
      var total    = slides.length;
      var current  = 0;
      var animating = false;

      // Crear dots
      slides.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.className = 'bs-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Página ' + (i + 1));
        dot.addEventListener('click', function () { goTo(i); });
        dotsEl.appendChild(dot);
      });

      function goTo(index) {
        if (animating || index === current) return;
        animating = true;

        current = index;

        // Mover el wrapper con translate
        wrapper.style.transform = 'translateX(-' + (current * 100) + '%)';

        // Actualizar dots
        dotsEl.querySelectorAll('.bs-dot').forEach(function (d, i) {
          d.classList.toggle('active', i === current);
        });

        prevEl.disabled = current === 0;
        nextEl.disabled = current === total - 1;

        // Desbloquear después de la transición
        setTimeout(function () { animating = false; }, 480);
      }

      // Posición inicial
      wrapper.style.transform = 'translateX(0%)';
      prevEl.disabled = true;
      if (total <= 1) nextEl.disabled = true;

      prevEl.addEventListener('click', function () {
        if (current > 0) goTo(current - 1);
      });
      nextEl.addEventListener('click', function () {
        if (current < total - 1) goTo(current + 1);
      });

      // Swipe táctil
      var startX = 0;
      swiperEl.addEventListener('touchstart', function (e) {
        startX = e.changedTouches[0].clientX;
      }, { passive: true });
      swiperEl.addEventListener('touchend', function (e) {
        var diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
          if (diff > 0 && current < total - 1) goTo(current + 1);
          if (diff < 0 && current > 0) goTo(current - 1);
        }
      }, { passive: true });
    }

    // Inicializar ambos carouseles
    initCarousel(
      document.getElementById('newSwiper'),
      document.getElementById('newPrev'),
      document.getElementById('newNext'),
      document.getElementById('newDots')
    );
    initCarousel(
      document.getElementById('bestSwiper'),
      document.getElementById('bestPrev'),
      document.getElementById('bestNext'),
      document.getElementById('bestDots')
    );

  })();
</script>

@endsection