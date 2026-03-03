@extends('layouts.web')

@section('content')

{{-- ── Page Title ── --}}
<div class="page-title">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">{{ Str::limit($product->name, 50) }}</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="{{ route('web.index') }}">Inicio</a></li>
        <li><a href="{{ route('web.search', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a></li>
        <li class="current">{{ Str::limit($product->name, 30) }}</li>
      </ol>
    </nav>
  </div>
</div>

<section class="pd-section" style="position:relative;">
  <div class="container">
    <div class="row g-5">

      {{-- ════ GALERÍA ════ --}}
      <div class="col-lg-7" data-aos="fade-right" data-aos-delay="100" style="z-index:1;position:relative;">
        <div class="pd-gallery">

          {{-- Imagen principal --}}
          <div class="pd-main-img-wrap" id="mainImgWrap">
            <img id="mainImage"
                 src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                 alt="{{ $product->name }}" class="pd-main-img"
                 data-zoom="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}">

            {{-- Badge estado --}}
            @if($product->is_on_sale)
              <span class="pd-img-badge pd-badge-sale">−{{ round($product->discount_percentage) }}%</span>
            @elseif($product->is_new)
              <span class="pd-img-badge pd-badge-new">Nuevo</span>
            @endif

            {{-- Navegación --}}
            <button class="pd-nav-btn pd-prev" id="prevImg" type="button">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button class="pd-nav-btn pd-next" id="nextImg" type="button">
              <i class="bi bi-chevron-right"></i>
            </button>

            {{-- Zoom hint --}}
            <span class="pd-zoom-hint"><i class="bi bi-zoom-in me-1"></i>Pasa el cursor para hacer zoom</span>
          </div>

          {{-- Pane Drift — fuera del wrap para que el zoom se muestre correctamente --}}
          <div id="zoomPane" class="pd-zoom-pane"></div>

          {{-- Thumbnails --}}
          @if($product->images->count() > 1)
          <div class="pd-thumbs">
            @foreach($product->images as $img)
              <div class="pd-thumb {{ $loop->first ? 'active' : '' }}"
                   data-src="{{ asset('storage/' . ($img->image ?? 'products/default_ot_image.png')) }}">
                <img src="{{ asset('storage/' . ($img->image ?? 'products/default_ot_image.png')) }}"
                     alt="Vista {{ $loop->iteration }}">
              </div>
            @endforeach
          </div>
          @endif

        </div>
      </div>

      {{-- ════ INFO PRODUCTO ════ --}}
      <div class="col-lg-5" data-aos="fade-left" data-aos-delay="150">
        <div class="pd-info">

          {{-- Header --}}
          <div class="pd-info-header">
            <span class="pd-cat-badge">{{ $product->category->name }}</span>
            @if($product->brand)
              <span class="pd-brand-badge"><i class="bi bi-award me-1"></i>{{ $product->brand->name }}</span>
            @endif
          </div>

          <h1 class="pd-name">{{ $product->name }}</h1>

          {{-- Rating --}}
          <div class="pd-rating-row">
            <div class="pd-stars">
              @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : ($i - 0.5 <= $product->rating ? '-half' : '') }}"></i>
              @endfor
            </div>
            <span class="pd-rating-val">{{ number_format($product->rating, 1) }}</span>
            <span class="pd-rating-count">({{ $product->reviews_count }} reseñas)</span>
            <span class="pd-views"><i class="bi bi-eye me-1"></i>{{ number_format($product->views_count) }} vistas</span>
          </div>

          {{-- Precios --}}
          <div class="pd-price-block">
            <span class="pd-price-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
            @if($product->is_on_sale)
              <span class="pd-price-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
              <span class="pd-save-chip">Ahorras {{ $settings->badge }}{{ number_format($product->discount_amount, 2) }}</span>
            @endif
          </div>

          {{-- Descripción corta --}}
          <div class="pd-short-desc">
            {!! $product->short_description !!}
          </div>

          {{-- Stock badge --}}
          <div class="pd-stock-badge
            {{ $product->stock <= 0 ? 'pd-stock-out' : ($product->stock <= 5 ? 'pd-stock-low' : 'pd-stock-ok') }}"
            id="stockBadge">
            <i class="bi bi-{{ $product->stock <= 0 ? 'x-circle-fill' : ($product->stock <= 5 ? 'exclamation-triangle-fill' : 'check-circle-fill') }}"></i>
            <strong>{{ $product->stock <= 0 ? 'Sin stock' : ($product->stock <= 5 ? 'Últimas unidades' : 'Disponible') }}</strong>
            @if($product->stock > 0)
              <span>· {{ $product->stock }} unidades</span>
            @endif
          </div>

          {{-- ═══ VARIANTES ═══ --}}
          @if($product->has_variants && $product->variants->where('status', true)->count())
            @php
              $variantData = $product->variants->where('status', true)->values();
              $attrGroups  = [];
              foreach ($variantData as $v) {
                foreach ($v->getAttribute('attributes') as $key => $val) {
                  $attrGroups[$key][] = $val;
                }
              }
              foreach ($attrGroups as $key => $vals) {
                $attrGroups[$key] = array_unique($vals);
              }
              $variantsJson = $variantData->map(fn($v) => [
                'id'         => $v->id,
                'attributes' => $v->getAttribute('attributes'),
                'price'      => $v->price ?? $product->selling_price,
                'stock'      => $v->stock,
                'image'      => $v->image ? asset('storage/' . $v->image) : null,
                'sku'        => $v->sku,
              ])->values();
            @endphp

            <script>
              const VARIANTS = @json($variantsJson);
              const CURRENCY = "{{ $settings->badge }}";
            </script>

            <div class="pd-variants" id="variantSection">
              @foreach($attrGroups as $attrName => $values)
                <div class="pd-attr-group">
                  <div class="pd-attr-label">
                    <span>{{ $attrName }}</span>
                    <span class="pd-attr-selected" id="sel-{{ Str::slug($attrName) }}"></span>
                  </div>
                  <div class="pd-attr-options">
                    @foreach($values as $value)
                      @php
                        $isColor = strtolower($attrName) === 'color' || strtolower($attrName) === 'colour' || strtolower($attrName) === 'color';
                      @endphp
                      <button type="button"
                              class="pd-opt-btn {{ $isColor ? 'pd-opt-color-btn' : '' }}"
                              data-attr="{{ $attrName }}"
                              data-value="{{ $value }}"
                              onclick="selectVariantOption(this)">
                        {{ $value }}
                      </button>
                    @endforeach
                  </div>
                </div>
              @endforeach

              <div id="variantUnavailable" class="pd-variant-alert d-none">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Esta combinación no está disponible.
              </div>
            </div>
          @endif

          {{-- ═══ FORMULARIO CARRITO ═══ --}}
          <form action="{{ route('web.cart.store') }}" method="POST" id="addToCartForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="variant_id" id="selectedVariantId" value="">

            <div class="pd-purchase">
              {{-- Cantidad --}}
              <div class="pd-qty-wrap">
                <span class="pd-qty-label">Cantidad</span>
                <div class="pd-qty">
                  <button class="pd-qty-btn" type="button" id="qtyDec"><i class="bi bi-dash"></i></button>
                  <input type="number" name="quantity" id="quantityInput" class="pd-qty-input"
                         value="1" min="1"
                         max="{{ $product->has_variants ? 0 : $product->stock }}">
                  <button class="pd-qty-btn" type="button" id="qtyInc"><i class="bi bi-plus"></i></button>
                </div>
              </div>

              {{-- Botones --}}
              <div class="pd-actions">
                <button class="pd-btn-cart" type="submit" id="addToCartBtn"
                        {{ $product->has_variants ? 'disabled' : '' }}>
                  <i class="bi bi-bag-plus me-2"></i>
                  <span id="cartBtnText">{{ $product->has_variants ? 'Selecciona opciones' : 'Agregar al carrito' }}</span>
                </button>
                <button class="pd-btn-buy" type="button">
                  <i class="bi bi-lightning-fill me-2"></i> Comprar ahora
                </button>
              </div>
            </div>
          </form>

          {{-- Favorito --}}
          <form action="{{ route('web.favorites.store') }}" method="POST" class="pd-fav-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit" class="pd-fav-btn">
              <i class="bi bi-heart me-2"></i> Agregar a favoritos
            </button>
          </form>

          {{-- Beneficios --}}
          <div class="pd-benefits">
            <div class="pd-benefit"><i class="bi bi-truck-front"></i><span>Envío gratis en pedidos +$75</span></div>
            <div class="pd-benefit"><i class="bi bi-arrow-counterclockwise"></i><span>Devoluciones a 45 días</span></div>
            <div class="pd-benefit"><i class="bi bi-shield-check"></i><span>Garantía de fabricante 3 años</span></div>
            <div class="pd-benefit"><i class="bi bi-headset"></i><span>Soporte 24/7</span></div>
          </div>

          {{-- Meta info --}}
          <div class="pd-meta">
            @if($product->code)
              <span><strong>SKU:</strong> {{ $product->code }}</span>
            @endif
            @if($product->weight)
              <span><strong>Peso:</strong> {{ $product->weight }} kg</span>
            @endif
            @if($product->warranty)
              <span><strong>Garantía:</strong> {{ $product->warranty }}</span>
            @endif
          </div>

        </div>
      </div>

    </div>{{-- /row principal --}}

    {{-- ════ TABS DE INFORMACIÓN ════ --}}
    <div class="row mt-5" data-aos="fade-up" data-aos-delay="200" style="position:relative;z-index:20;">
      <div class="col-12">
        <div class="pd-tabs-wrap">

          <nav class="pd-tabs-nav">
            <button class="pd-tab-btn active" data-target="tab-desc" type="button">
              <i class="bi bi-file-text me-2"></i>Descripción
            </button>
            <button class="pd-tab-btn" data-target="tab-specs" type="button">
              <i class="bi bi-list-check me-2"></i>Especificaciones
            </button>
            <button class="pd-tab-btn" data-target="tab-reviews" type="button">
              <i class="bi bi-star me-2"></i>Reseñas ({{ $product->reviews_count }})
            </button>
            <button class="pd-tab-btn" data-target="tab-ship" type="button">
              <i class="bi bi-truck me-2"></i>Envío y devoluciones
            </button>
          </nav>

          <div class="pd-tab-content">

            {{-- TAB: DESCRIPCIÓN --}}
            <div class="pd-pane pd-pane-active" id="tab-desc">
              <div class="row g-4">
                <div class="col-lg-8">
                  <div class="pd-desc-text">
                    <h3>Descripción del producto</h3>
                    <div class="pd-long-desc">
                      {!! $product->long_description ?? '<p class="text-muted">Sin descripción disponible.</p>' !!}
                    </div>
                    @if($product->tags)
                      <div class="pd-tags">
                        @foreach(explode(',', $product->tags) as $tag)
                          <span class="pd-tag">{{ trim($tag) }}</span>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="pd-highlights-box">
                    <h4><i class="bi bi-stars me-2"></i>Características principales</h4>
                    <div class="pd-highlights-grid">
                      <div class="pd-hl-item"><i class="bi bi-check2-circle"></i><span>Calidad premium garantizada</span></div>
                      <div class="pd-hl-item"><i class="bi bi-check2-circle"></i><span>Envío rápido y seguro</span></div>
                      <div class="pd-hl-item"><i class="bi bi-check2-circle"></i><span>Devolución sin complicaciones</span></div>
                      <div class="pd-hl-item"><i class="bi bi-check2-circle"></i><span>Soporte post-venta incluido</span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- TAB: ESPECIFICACIONES --}}
            <div class="pd-pane" id="tab-specs">
              <div class="pd-specs-wrap">
                <div class="row g-4">

                  {{-- Información general --}}
                  <div class="col-md-6">
                    <div class="pd-spec-group">
                      <h4><i class="bi bi-info-circle me-2"></i>Información general</h4>
                      <div class="pd-spec-table">
                        <div class="pd-spec-row"><span>Nombre</span><span>{{ $product->name }}</span></div>
                        @if($product->code)
                          <div class="pd-spec-row"><span>Código / SKU</span><span>{{ $product->code }}</span></div>
                        @endif
                        <div class="pd-spec-row"><span>Categoría</span><span>{{ $product->category->name }}</span></div>
                        @if($product->brand)
                          <div class="pd-spec-row"><span>Marca</span><span>{{ $product->brand->name }}</span></div>
                        @endif
                        @if($product->has_variants)
                          <div class="pd-spec-row"><span>Variantes disponibles</span><span>{{ $product->variants->where('status', true)->count() }}</span></div>
                        @else
                          <div class="pd-spec-row">
                            <span>Stock</span>
                            <span class="pd-spec-stock {{ $product->stock > 5 ? 'ok' : ($product->stock > 0 ? 'low' : 'out') }}">
                              {{ $product->stock > 0 ? $product->stock.' unidades' : 'Agotado' }}
                            </span>
                          </div>
                        @endif
                        <div class="pd-spec-row"><span>Estado</span><span>{{ $product->status ? 'Activo' : 'Inactivo' }}</span></div>
                      </div>
                    </div>
                  </div>

                  {{-- Peso y dimensiones --}}
                  @if($product->weight || $product->dimensions)
                  <div class="col-md-6">
                    <div class="pd-spec-group">
                      <h4><i class="bi bi-rulers me-2"></i>Peso y dimensiones</h4>
                      <div class="pd-spec-table">
                        @if($product->weight)
                          <div class="pd-spec-row"><span>Peso</span><span>{{ $product->weight }} kg</span></div>
                        @endif
                        @if($product->dimensions)
                          <div class="pd-spec-row">
                            <span>Largo</span>
                            <span>{{ $product->dimensions['length'] ?? 'N/A' }} cm</span>
                          </div>
                          <div class="pd-spec-row">
                            <span>Ancho</span>
                            <span>{{ $product->dimensions['width'] ?? 'N/A' }} cm</span>
                          </div>
                          <div class="pd-spec-row">
                            <span>Alto</span>
                            <span>{{ $product->dimensions['height'] ?? 'N/A' }} cm</span>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  @endif

                  {{-- Especificaciones técnicas del producto --}}
                  @if($product->specifications && is_array($product->specifications) && count($product->specifications))
                  <div class="col-md-6">
                    <div class="pd-spec-group">
                      <h4><i class="bi bi-cpu me-2"></i>Especificaciones técnicas</h4>
                      <div class="pd-spec-table">
                        @foreach($product->specifications as $k => $v)
                          <div class="pd-spec-row">
                            <span>{{ ucwords(str_replace(['_','-'], ' ', $k)) }}</span>
                            <span>{{ $v }}</span>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @endif

                  {{-- Información adicional: garantía, devolución, envío --}}
                  @if($product->warranty || $product->return_policy || $product->shipping_info)
                  <div class="col-md-6">
                    <div class="pd-spec-group">
                      <h4><i class="bi bi-patch-check me-2"></i>Información adicional</h4>
                      <div class="pd-spec-table">
                        @if($product->warranty)
                          <div class="pd-spec-row">
                            <span><i class="bi bi-shield-check me-1 text-success"></i>Garantía</span>
                            <span>{{ $product->warranty }}</span>
                          </div>
                        @endif
                        @if($product->return_policy)
                          <div class="pd-spec-row">
                            <span><i class="bi bi-arrow-return-left me-1 text-primary"></i>Devolución</span>
                            <span>{{ $product->return_policy }}</span>
                          </div>
                        @endif
                        @if($product->shipping_info)
                          <div class="pd-spec-row">
                            <span><i class="bi bi-truck me-1 text-indigo"></i>Envío</span>
                            <span>{{ $product->shipping_info }}</span>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  @endif

                </div>
              </div>
            </div>

            {{-- TAB: RESEÑAS --}}
            <div class="pd-pane" id="tab-reviews">
              <div class="pd-reviews-wrap">
                <div class="row g-4">

                  {{-- Overview --}}
                  <div class="col-lg-4">
                    <div class="pd-reviews-overview">
                      <div class="pd-rev-score">{{ number_format($product->rating, 1) }}</div>
                      <div class="pd-rev-stars">
                        @for($i = 1; $i <= 5; $i++)
                          <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                        @endfor
                      </div>
                      <p>{{ $product->reviews_count }} reseñas verificadas</p>

                      <div class="pd-rev-bars">
                        @foreach([5,4,3,2,1] as $star)
                          @php
                            $count = $product->reviews->where('rating', $star)->count();
                            $pct   = $product->reviews_count > 0 ? ($count / $product->reviews_count) * 100 : 0;
                          @endphp
                          <div class="pd-rev-bar-row">
                            <span>{{ $star }}★</span>
                            <div class="pd-rev-bar"><div class="pd-rev-bar-fill" style="width:{{ $pct }}%"></div></div>
                            <span>{{ $count }}</span>
                          </div>
                        @endforeach
                      </div>

                      <button class="pd-write-review-btn">
                        <i class="bi bi-pencil-square me-2"></i> Escribir reseña
                      </button>
                    </div>
                  </div>

                  {{-- Lista de reseñas --}}
                  <div class="col-lg-8">
                    @forelse($product->reviews as $review)
                      <div class="pd-review-card" id="review-{{ $review->id }}">

                        {{-- Cabecera --}}
                        <div class="pd-rev-header">
                          <div class="pd-rev-avatar">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                          </div>
                          <div class="pd-rev-meta">
                            <div class="pd-rev-name-row">
                              <strong>{{ $review->user->name ?? 'Usuario' }}</strong>
                              @if($review->verified_purchase)
                                <span class="pd-rev-verified">
                                  <i class="bi bi-patch-check-fill me-1"></i>Compra verificada
                                </span>
                              @endif
                            </div>
                            <div class="pd-rev-card-stars">
                              @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                              @endfor
                              <span class="pd-rev-rating-num">{{ $review->rating }}/5</span>
                            </div>
                          </div>
                          <span class="pd-rev-date">{{ $review->created_at->format('d M, Y') }}</span>
                        </div>

                        {{-- Título y cuerpo --}}
                        @if($review->title)
                          <h5 class="pd-rev-title">{{ $review->title }}</h5>
                        @endif
                        <p class="pd-rev-body">{{ $review->comment }}</p>

                        {{-- Imágenes de la reseña --}}
                        @if($review->images->count())
                          <div class="pd-rev-imgs">
                            @foreach($review->images as $ri)
                              <img src="{{ asset('storage/' . $ri->image) }}"
                                   alt="Imagen reseña" loading="lazy">
                            @endforeach
                          </div>
                        @endif

                        {{-- Respuesta del vendedor --}}
                        @if($review->seller_response)
                          <div class="pd-rev-seller-response">
                            <div class="pd-rev-seller-header">
                              <i class="bi bi-building me-1"></i>
                              <strong>Respuesta del vendedor</strong>
                              @if($review->responded_at)
                                <span>· {{ $review->responded_at->format('d M, Y') }}</span>
                              @endif
                            </div>
                            <p>{{ $review->seller_response }}</p>
                          </div>
                        @endif

                        {{-- Pie: votos útil --}}
                        <div class="pd-rev-foot">
                          <span class="pd-rev-helpful-label">¿Fue útil esta reseña?</span>
                          <button class="pd-rev-helpful js-helpful"
                                  data-review-id="{{ $review->id }}"
                                  data-helpful="1">
                            <i class="bi bi-hand-thumbs-up me-1"></i>
                            Sí <span class="pd-rev-helpful-count">({{ $review->helpful_count }})</span>
                          </button>
                          <button class="pd-rev-helpful js-helpful"
                                  data-review-id="{{ $review->id }}"
                                  data-helpful="0">
                            <i class="bi bi-hand-thumbs-down me-1"></i>
                            No <span class="pd-rev-helpful-count">({{ $review->not_helpful_count }})</span>
                          </button>
                        </div>

                      </div>
                    @empty
                      <div class="pd-no-reviews">
                        <i class="bi bi-chat-square-dots"></i>
                        <p>Aún no hay reseñas. ¡Sé el primero en opinar!</p>
                      </div>
                    @endforelse
                  </div>

                </div>
              </div>
            </div>

            {{-- TAB: ENVÍO --}}
            <div class="pd-pane" id="tab-ship">
              <div class="pd-ship-wrap">
                <div class="row g-4">
                  <div class="col-md-4">
                    <div class="pd-ship-card">
                      <div class="pd-ship-icon"><i class="bi bi-truck-front"></i></div>
                      <h5>Envío estándar</h5>
                      <p>Entrega en 3–5 días hábiles. Gratis en pedidos superiores a $75.</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="pd-ship-card">
                      <div class="pd-ship-icon"><i class="bi bi-lightning-charge"></i></div>
                      <h5>Envío express</h5>
                      <p>Entrega en 24–48 horas. Disponible en ciudades principales.</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="pd-ship-card">
                      <div class="pd-ship-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
                      <h5>Devoluciones</h5>
                      <p>{{ $product->return_policy ?? 'Devoluciones sin complicaciones durante 45 días desde la compra.' }}</p>
                    </div>
                  </div>
                  @if($product->shipping_info)
                  <div class="col-12">
                    <div class="pd-ship-info-box">
                      <i class="bi bi-info-circle-fill me-2"></i>
                      {{ $product->shipping_info }}
                    </div>
                  </div>
                  @endif
                </div>
              </div>
            </div>

          </div>{{-- /tab-content --}}
        </div>
      </div>
    </div>

  </div>
</section>

{{-- ════ ESTILOS ════ --}}
<style>
.pd-section { padding: 60px 0 80px; }

/* ── Page title ── */
.page-title h1 { font-family:'Sora',sans-serif; font-size:1.15rem; font-weight:700; color:#111827; margin:0; }

/* ── Galería ── */
.pd-gallery { display:flex; flex-direction:column; gap:16px; position:sticky; top:90px; }

.pd-main-img-wrap {
  position:relative; border-radius:20px; overflow:hidden;
  background:#f9fafb; border:1px solid #eef0f3;
  aspect-ratio:1/1;
}
.pd-main-img {
  width:100%; height:100%; object-fit:contain;
  transition:opacity .25s;
  padding:16px;
}

.pd-img-badge {
  position:absolute; top:16px; left:16px; z-index:2;
  font-size:.72rem; font-weight:800; padding:4px 12px;
  border-radius:20px; color:#fff;
  text-transform:uppercase; letter-spacing:.06em;
}
.pd-badge-sale { background:linear-gradient(135deg,#ef4444,#dc2626); }
.pd-badge-new  { background:linear-gradient(135deg,#10b981,#059669); }

.pd-nav-btn {
  position:absolute; top:50%; transform:translateY(-50%);
  width:36px; height:36px; border-radius:10px;
  background:rgba(255,255,255,.92); border:1px solid #e5e7eb;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; font-size:.95rem; color:#374151;
  transition:all .2s; z-index:3; box-shadow:0 2px 8px rgba(0,0,0,.08);
}
.pd-nav-btn:hover { background:#6366f1; color:#fff; border-color:#6366f1; }
.pd-prev { left:10px; }
.pd-next { right:10px; }

.pd-zoom-hint {
  position:absolute; bottom:12px; left:50%; transform:translateX(-50%);
  font-size:.7rem; color:#9ca3af; white-space:nowrap;
  background:rgba(255,255,255,.85); padding:4px 10px; border-radius:20px;
  pointer-events:none; z-index:4;
}

/* Cursor lupa */
.pd-main-img { cursor:crosshair; }

/* Pane Drift zoom — fuera del wrap, Drift lo posiciona automáticamente */
.pd-zoom-pane {
  position:absolute;
  top:0; left:calc(100% + 20px);
  width:420px; height:420px;
  border-radius:18px; overflow:hidden;
  border:1px solid #c7d2fe;
  box-shadow:0 12px 40px rgba(99,102,241,.18);
  z-index:100;
  pointer-events:none;
  display:none;
}
/* pd-gallery position:relative movida a la regla principal */

.pd-thumbs {
  display:flex; gap:10px; flex-wrap:wrap;
}
.pd-thumb {
  width:72px; height:72px; border-radius:12px; overflow:hidden;
  border:2px solid #eef0f3; cursor:pointer;
  transition:border-color .2s, transform .2s;
  background:#f9fafb;
}
.pd-thumb img { width:100%; height:100%; object-fit:contain; padding:4px; }
.pd-thumb:hover, .pd-thumb.active {
  border-color:#6366f1;
  transform:scale(1.05);
}

/* ── Info panel ── */
.pd-info { display:flex; flex-direction:column; gap:18px; }

.pd-info-header { display:flex; gap:8px; flex-wrap:wrap; }
.pd-cat-badge {
  font-size:.72rem; font-weight:800; text-transform:uppercase;
  letter-spacing:.1em; color:#6366f1; background:#eef2ff;
  border:1px solid #c7d2fe; padding:4px 12px; border-radius:20px;
}
.pd-brand-badge {
  font-size:.72rem; font-weight:700; color:#6b7280;
  background:#f9fafb; border:1px solid #e5e7eb;
  padding:4px 12px; border-radius:20px;
  display:inline-flex; align-items:center;
}

.pd-name {
  font-family:'Sora',sans-serif; font-size:1.55rem; font-weight:800;
  color:#111827; margin:0; line-height:1.3;
}

.pd-rating-row {
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
}
.pd-stars i { color:#fbbf24; font-size:.9rem; }
.pd-rating-val { font-weight:800; color:#111827; font-size:.9rem; }
.pd-rating-count { font-size:.82rem; color:#6b7280; }
.pd-views { font-size:.78rem; color:#9ca3af; margin-left:auto; display:flex; align-items:center; }

.pd-price-block {
  display:flex; align-items:center; gap:12px; flex-wrap:wrap;
  padding:16px 20px; background:#f8f9fb;
  border-radius:14px; border:1px solid #eef0f3;
}
.pd-price-cur { font-family:'Sora',sans-serif; font-size:1.9rem; font-weight:800; color:#6366f1; }
.pd-price-old { font-size:1.1rem; color:#9ca3af; text-decoration:line-through; }
.pd-save-chip {
  font-size:.74rem; font-weight:800; color:#059669;
  background:#f0fdf4; border:1px solid #bbf7d0;
  padding:4px 12px; border-radius:20px; margin-left:auto;
}

.pd-short-desc {
  font-size:.9rem; line-height:1.75; color:#4b5563;
  padding-left:12px; border-left:3px solid #c7d2fe;
}

/* Stock badge */
.pd-stock-badge {
  display:inline-flex; align-items:center; gap:8px;
  padding:9px 16px; border-radius:10px;
  font-size:.84rem; font-weight:600;
}
.pd-stock-ok  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.pd-stock-low { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
.pd-stock-out { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }

/* ── Variantes ── */
.pd-variants { display:flex; flex-direction:column; gap:14px; }
.pd-attr-group { display:flex; flex-direction:column; gap:8px; }
.pd-attr-label {
  display:flex; align-items:center; gap:6px;
  font-size:.82rem; font-weight:700; color:#374151; text-transform:uppercase;
  letter-spacing:.06em;
}
.pd-attr-selected {
  font-size:.8rem; font-weight:600; color:#6366f1;
  text-transform:none; letter-spacing:0;
}
.pd-attr-options { display:flex; flex-wrap:wrap; gap:8px; }

.pd-opt-btn {
  padding:7px 16px; border-radius:9px;
  border:1.5px solid #e5e7eb; background:#fff;
  color:#374151; font-size:.84rem; font-weight:600;
  cursor:pointer; transition:all .2s;
}
.pd-opt-btn:hover { border-color:#6366f1; color:#6366f1; background:#eef2ff; }
.pd-opt-btn.active {
  border-color:#6366f1; background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff; box-shadow:0 3px 10px rgba(99,102,241,.25);
}
.pd-opt-btn.unavailable { opacity:.4; text-decoration:line-through; cursor:not-allowed; }

.pd-variant-alert {
  display:flex; align-items:center; gap:8px;
  padding:10px 14px; border-radius:10px;
  background:#fffbeb; border:1px solid #fde68a;
  color:#92400e; font-size:.84rem; font-weight:600;
}

/* ── Compra ── */
.pd-purchase { display:flex; flex-direction:column; gap:12px; }

.pd-qty-wrap { display:flex; align-items:center; gap:12px; }
.pd-qty-label { font-size:.82rem; font-weight:700; color:#374151; white-space:nowrap; }
.pd-qty {
  display:inline-flex; align-items:center;
  border:1.5px solid #e5e7eb; border-radius:10px; overflow:hidden;
}
.pd-qty-btn {
  width:38px; height:38px; border:none; background:#f9fafb;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; color:#374151; transition:all .2s;
}
.pd-qty-btn:hover { background:#6366f1; color:#fff; }
.pd-qty-input {
  width:56px; height:38px; border:none; text-align:center;
  font-weight:700; font-size:.9rem; color:#111827;
  outline:none; background:#fff;
}
.pd-qty-input::-webkit-inner-spin-button { display:none; }

.pd-actions { display:flex; gap:10px; }
.pd-btn-cart {
  flex:1; padding:13px 20px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff; border:none; border-radius:12px;
  font-weight:700; font-size:.9rem; cursor:pointer;
  transition:all .25s; display:flex; align-items:center; justify-content:center;
  box-shadow:0 4px 16px rgba(99,102,241,.3);
}
.pd-btn-cart:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 8px 24px rgba(99,102,241,.4); }
.pd-btn-cart:disabled { opacity:.5; cursor:not-allowed; transform:none; box-shadow:none; }

.pd-btn-buy {
  padding:13px 18px;
  background:#fff; border:1.5px solid #6366f1;
  color:#6366f1; border-radius:12px;
  font-weight:700; font-size:.9rem; cursor:pointer;
  transition:all .25s; display:flex; align-items:center;
  white-space:nowrap;
}
.pd-btn-buy:hover { background:#eef2ff; }

.pd-fav-form { margin:0; }
.pd-fav-btn {
  width:100%; padding:11px;
  background:#fff; border:1.5px solid #fecaca;
  color:#b91c1c; border-radius:12px;
  font-weight:600; font-size:.86rem; cursor:pointer;
  transition:all .25s; display:flex; align-items:center; justify-content:center;
}
.pd-fav-btn:hover { background:#fef2f2; border-color:#f87171; }

/* Beneficios */
.pd-benefits {
  display:grid; grid-template-columns:1fr 1fr; gap:10px;
}
.pd-benefit {
  display:flex; align-items:center; gap:9px;
  font-size:.8rem; color:#6b7280; font-weight:500;
  padding:9px 12px; background:#f8f9fb; border-radius:10px;
  border:1px solid #eef0f3;
}
.pd-benefit i { color:#6366f1; font-size:1.05rem; flex-shrink:0; }

/* Meta */
.pd-meta {
  display:flex; gap:16px; flex-wrap:wrap;
  font-size:.78rem; color:#9ca3af;
  padding-top:4px; border-top:1px solid #f3f4f6;
}

/* ── TABS ── */
.pd-tabs-wrap {
  background:#fff; border-radius:20px;
  border:1px solid #eef0f3;
  box-shadow:0 2px 16px rgba(0,0,0,.04);
  overflow:hidden;
  position:relative; z-index:10;
}
.pd-tabs-nav {
  display:flex; gap:0;
  background:#f8f9fb; border-bottom:1px solid #eef0f3;
  overflow-x:auto; -webkit-overflow-scrolling:touch;
  scrollbar-width:none;
}
.pd-tabs-nav::-webkit-scrollbar { display:none; }
.pd-tab-btn {
  padding:15px 22px; border:none; background:none;
  color:#6b7280; font-size:.86rem; font-weight:600;
  cursor:pointer; white-space:nowrap;
  transition:all .2s; border-bottom:2px solid transparent;
  display:flex; align-items:center;
  margin-bottom:-1px;
}
.pd-tab-btn:hover { color:#6366f1; background:rgba(99,102,241,.04); }
.pd-tab-btn.active { color:#6366f1; border-bottom-color:#6366f1; background:#fff; }

.pd-tab-content { padding:32px; }
.pd-pane { display:none; }
.pd-pane-active { display:block; }

/* Descripción tab */
.pd-desc-text h3 {
  font-family:'Sora',sans-serif; font-size:1.15rem; font-weight:800;
  color:#111827; margin:0 0 16px;
}
.pd-long-desc { font-size:.92rem; line-height:1.8; color:#4b5563; }
.pd-tags { display:flex; flex-wrap:wrap; gap:8px; margin-top:20px; }
.pd-tag {
  font-size:.72rem; font-weight:700; padding:4px 12px;
  background:#eef2ff; color:#6366f1; border:1px solid #c7d2fe;
  border-radius:20px; cursor:default;
}

.pd-highlights-box {
  background:#f8f9fb; border-radius:16px;
  border:1px solid #eef0f3; padding:20px;
}
.pd-highlights-box h4 {
  font-family:'Sora',sans-serif; font-size:.9rem; font-weight:800;
  color:#111827; margin:0 0 16px;
  display:flex; align-items:center;
}
.pd-highlights-grid { display:flex; flex-direction:column; gap:10px; }
.pd-hl-item {
  display:flex; align-items:flex-start; gap:10px;
  font-size:.84rem; color:#374151;
}
.pd-hl-item i { color:#6366f1; font-size:1rem; margin-top:1px; flex-shrink:0; }

/* Specs tab */
.pd-spec-group { margin-bottom:0; }
.pd-spec-group h4 {
  font-family:'Sora',sans-serif; font-size:.86rem; font-weight:800;
  text-transform:uppercase; letter-spacing:.08em;
  color:#111827; margin:0 0 12px;
  padding-bottom:8px; border-bottom:2px solid #eef2ff;
}
.pd-spec-table { display:flex; flex-direction:column; }
.pd-spec-row {
  display:flex; justify-content:space-between; align-items:center;
  padding:9px 12px; font-size:.84rem;
}
.pd-spec-row:nth-child(odd) { background:#f8f9fb; border-radius:8px; }
.pd-spec-row span:first-child { color:#6b7280; font-weight:600; }
.pd-spec-row span:last-child  { color:#111827; font-weight:700; text-align:right; }

/* Reviews tab */
.pd-reviews-overview {
  background:#f8f9fb; border-radius:16px; border:1px solid #eef0f3;
  padding:24px; text-align:center; position:sticky; top:20px;
}
.pd-rev-score {
  font-family:'Sora',sans-serif; font-size:3.5rem; font-weight:800;
  color:#111827; line-height:1;
}
.pd-rev-stars { display:flex; justify-content:center; gap:3px; margin:8px 0 4px; }
.pd-rev-stars i { color:#fbbf24; font-size:1.1rem; }
.pd-reviews-overview > p { font-size:.8rem; color:#9ca3af; margin:0 0 20px; }

.pd-rev-bars { display:flex; flex-direction:column; gap:7px; margin-bottom:20px; }
.pd-rev-bar-row { display:flex; align-items:center; gap:8px; font-size:.75rem; }
.pd-rev-bar-row span:first-child { width:24px; color:#6b7280; font-weight:700; text-align:right; }
.pd-rev-bar { flex:1; height:7px; background:#e5e7eb; border-radius:10px; overflow:hidden; }
.pd-rev-bar-fill { height:100%; background:linear-gradient(90deg,#6366f1,#8b5cf6); border-radius:10px; }
.pd-rev-bar-row span:last-child { width:22px; color:#9ca3af; }

.pd-write-review-btn {
  width:100%; padding:10px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff; border:none; border-radius:10px;
  font-weight:700; font-size:.84rem; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
  transition:all .25s; box-shadow:0 3px 10px rgba(99,102,241,.25);
}
.pd-write-review-btn:hover { transform:translateY(-1px); }

.pd-review-card {
  background:#fff; border:1px solid #eef0f3; border-radius:16px;
  padding:20px; margin-bottom:14px;
  transition:box-shadow .2s;
}
.pd-review-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }

.pd-rev-header { display:flex; align-items:flex-start; gap:12px; margin-bottom:10px; }
.pd-rev-avatar {
  width:40px; height:40px; border-radius:12px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff; font-weight:800; font-size:1rem;
  display:flex; align-items:center; justify-content:center;
  flex-shrink:0;
}
.pd-rev-meta strong { font-size:.88rem; color:#111827; display:block; }
.pd-rev-card-stars i { color:#fbbf24; font-size:.75rem; }
.pd-rev-date { font-size:.74rem; color:#9ca3af; margin-left:auto; white-space:nowrap; }
.pd-rev-title { font-size:.9rem; font-weight:700; color:#111827; margin:0 0 6px; }
.pd-rev-body { font-size:.85rem; color:#6b7280; line-height:1.65; margin:0; }
.pd-rev-imgs { display:flex; gap:8px; flex-wrap:wrap; margin-top:10px; }
.pd-rev-imgs img { width:60px; height:60px; border-radius:8px; object-fit:cover; }
.pd-rev-foot { margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6; }
.pd-rev-helpful {
  background:none; border:1px solid #e5e7eb; border-radius:8px;
  padding:5px 12px; font-size:.76rem; color:#6b7280;
  cursor:pointer; transition:all .2s;
  display:inline-flex; align-items:center;
}
.pd-rev-helpful:hover { border-color:#6366f1; color:#6366f1; background:#eef2ff; }

.pd-no-reviews {
  text-align:center; padding:48px 20px; color:#9ca3af;
}
.pd-no-reviews i { font-size:3rem; display:block; margin-bottom:12px; }

/* Reviews — campos del modelo Review */
.pd-rev-name-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.pd-rev-verified {
  font-size:.7rem; font-weight:700; color:#059669;
  background:#f0fdf4; border:1px solid #bbf7d0;
  padding:2px 8px; border-radius:20px;
  display:inline-flex; align-items:center;
}
.pd-rev-rating-num { font-size:.72rem; color:#9ca3af; margin-left:4px; }

.pd-rev-seller-response {
  background:#eef2ff; border:1px solid #c7d2fe;
  border-radius:12px; padding:14px 16px; margin-top:12px;
}
.pd-rev-seller-header {
  display:flex; align-items:center; gap:4px;
  font-size:.78rem; color:#4338ca; margin-bottom:6px;
}
.pd-rev-seller-header span { color:#9ca3af; }
.pd-rev-seller-response p { font-size:.84rem; color:#374151; margin:0; line-height:1.6; }

.pd-rev-foot {
  margin-top:12px; padding-top:12px;
  border-top:1px solid #f3f4f6;
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
}
.pd-rev-helpful-label { font-size:.75rem; color:#9ca3af; margin-right:4px; }
.pd-rev-helpful {
  background:none; border:1px solid #e5e7eb; border-radius:8px;
  padding:5px 12px; font-size:.76rem; color:#6b7280;
  cursor:pointer; transition:all .2s;
  display:inline-flex; align-items:center;
}
.pd-rev-helpful:hover { border-color:#6366f1; color:#6366f1; background:#eef2ff; }
.pd-rev-helpful.voted { border-color:#6366f1; color:#6366f1; background:#eef2ff; font-weight:700; }
.pd-rev-helpful-count { margin-left:2px; }

/* Specs — stock coloreado y spec icons */
.pd-spec-stock { font-weight:700; }
.pd-spec-stock.ok  { color:#15803d; }
.pd-spec-stock.low { color:#92400e; }
.pd-spec-stock.out { color:#b91c1c; }
.pd-spec-group h4 {
  font-family:'Sora',sans-serif; font-size:.86rem; font-weight:800;
  text-transform:uppercase; letter-spacing:.08em;
  color:#111827; margin:0 0 12px;
  padding-bottom:8px; border-bottom:2px solid #eef2ff;
  display:flex; align-items:center;
}

/* Shipping tab */
.pd-ship-card {
  background:#f8f9fb; border-radius:16px; border:1px solid #eef0f3;
  padding:24px; text-align:center; height:100%;
  transition:all .3s;
}
.pd-ship-card:hover { border-color:#c7d2fe; box-shadow:0 6px 20px rgba(99,102,241,.1); }
.pd-ship-icon {
  width:56px; height:56px; border-radius:14px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  display:flex; align-items:center; justify-content:center;
  margin:0 auto 14px;
  box-shadow:0 4px 14px rgba(99,102,241,.3);
}
.pd-ship-icon i { font-size:1.5rem; color:#fff; }
.pd-ship-card h5 { font-family:'Sora',sans-serif; font-size:.92rem; font-weight:700; color:#111827; margin:0 0 8px; }
.pd-ship-card p { font-size:.84rem; color:#6b7280; margin:0; line-height:1.6; }

.pd-ship-info-box {
  padding:14px 18px; background:#eef2ff; border-radius:12px;
  border:1px solid #c7d2fe; color:#4338ca;
  font-size:.85rem; font-weight:500;
  display:flex; align-items:flex-start; gap:8px;
}

@media (max-width:991.98px) {
  .pd-gallery { position:static; }
  .pd-benefits { grid-template-columns:1fr; }
  .pd-tab-content { padding:20px; }
}
@media (max-width:575.98px) {
  .pd-name { font-size:1.25rem; }
  .pd-price-cur { font-size:1.5rem; }
  .pd-actions { flex-direction:column; }
  .pd-btn-buy { justify-content:center; }
}
</style>

@endsection

@push('scripts')
<script>
/* ── Galería + Drift zoom ── */
(function(){
  var thumbs  = document.querySelectorAll('.pd-thumb');
  var mainImg = document.getElementById('mainImage');
  var pane    = document.getElementById('zoomPane');
  var images  = Array.prototype.slice.call(thumbs).map(function(t){ return t.dataset.src; });
  var current = 0;
  var driftInstance = null;

  if (!images.length && mainImg) images = [mainImg.src];

  function initDrift() {
    if (!mainImg || !pane) return;
    if (typeof Drift === 'undefined') {
      // Drift aún no cargó, reintentar
      setTimeout(initDrift, 200);
      return;
    }
    if (driftInstance) {
      try { driftInstance.destroy(); } catch(e){}
      driftInstance = null;
    }
    // Asegurarse de que data-zoom esté actualizado
    mainImg.setAttribute('data-zoom', mainImg.src);
    driftInstance = new Drift(mainImg, {
      paneContainer     : pane,
      zoomFactor        : 3,
      hoverBoundingBox  : true,
      handleTouch       : false,
      sourceAttribute   : 'data-zoom',
    });
    window._driftInstance = driftInstance;
  }

  function setImage(idx) {
    current = (idx + images.length) % images.length;
    if (!mainImg) return;
    mainImg.style.opacity = '0';
    setTimeout(function(){
      mainImg.src = images[current];
      mainImg.setAttribute('data-zoom', images[current]);
      mainImg.style.opacity = '1';
      initDrift();
    }, 150);
    thumbs.forEach(function(t, i){ t.classList.toggle('active', i === current); });
  }

  thumbs.forEach(function(t, i){
    t.addEventListener('click', function(){ setImage(i); });
  });
  var prevBtn = document.getElementById('prevImg');
  var nextBtn = document.getElementById('nextImg');
  if (prevBtn) prevBtn.addEventListener('click', function(){ setImage(current - 1); });
  if (nextBtn) nextBtn.addEventListener('click', function(){ setImage(current + 1); });

  // Inicializar cuando todo esté listo
  if (document.readyState === 'complete') {
    initDrift();
  } else {
    window.addEventListener('load', initDrift);
  }
})();

/* ── Cantidad ── */
(function(){
  const input = document.getElementById('quantityInput');
  document.getElementById('qtyDec')?.addEventListener('click', function(){
    if (input && parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
  });
  document.getElementById('qtyInc')?.addEventListener('click', function(){
    if (input) {
      const max = parseInt(input.max) || 999;
      if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
    }
  });
})();

/* ── Variantes ── */
let selectedAttrs = {};

function selectVariantOption(btn) {
  const attr  = btn.dataset.attr;
  const value = btn.dataset.value;

  document.querySelectorAll(`.pd-opt-btn[data-attr="${attr}"]`)
    .forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const label = document.getElementById('sel-' + attr.toLowerCase().replace(/\s+/g, '-'));
  if (label) label.textContent = '— ' + value;

  selectedAttrs[attr] = value;
  findMatchingVariant();
}

function findMatchingVariant() {
  const attrs      = Object.keys(selectedAttrs);
  const totalAttrs = [...new Set([...document.querySelectorAll('[data-attr]')].map(b => b.dataset.attr))].length;

  document.getElementById('variantUnavailable')?.classList.add('d-none');

  if (attrs.length < totalAttrs) { resetCartState(null); return; }

  const match = (typeof VARIANTS !== 'undefined') ? VARIANTS.find(v =>
    attrs.every(key => v.attributes[key] === selectedAttrs[key])
  ) : null;

  if (match) { applyVariant(match); }
  else {
    document.getElementById('variantUnavailable')?.classList.remove('d-none');
    resetCartState(null);
  }
}

function applyVariant(variant) {
  const currency = typeof CURRENCY !== 'undefined' ? CURRENCY : '';

  // Precio
  const priceEl = document.querySelector('.pd-price-cur');
  if (priceEl) priceEl.textContent = currency + parseFloat(variant.price).toFixed(2);

  // Stock máx
  const qi = document.getElementById('quantityInput');
  if (qi) qi.max = variant.stock;

  // Imagen
  if (variant.image) {
    const mi = document.getElementById('mainImage');
    if (mi) {
      mi.style.opacity='0';
      setTimeout(function(){
        mi.src = variant.image;
        mi.setAttribute('data-zoom', variant.image);
        mi.style.opacity='1';
        if (window._driftInstance) { try { window._driftInstance.destroy(); } catch(e){} }
        var dpane = document.getElementById('zoomPane');
        if (typeof Drift !== 'undefined' && dpane) {
          window._driftInstance = new Drift(mi, {
            paneContainer   : dpane,
            zoomFactor      : 3,
            hoverBoundingBox: true,
            handleTouch     : false,
            sourceAttribute : 'data-zoom',
          });
        }
      },150);
    }
  }

  // Stock badge
  const sb = document.getElementById('stockBadge');
  if (sb) {
    sb.className = 'pd-stock-badge ' +
      (variant.stock <= 0 ? 'pd-stock-out' : variant.stock <= 5 ? 'pd-stock-low' : 'pd-stock-ok');
    sb.innerHTML = `<i class="bi bi-${variant.stock<=0?'x-circle-fill':variant.stock<=5?'exclamation-triangle-fill':'check-circle-fill'}"></i>
      <strong>${variant.stock<=0?'Sin stock':variant.stock<=5?'Últimas unidades':'Disponible'}</strong>
      ${variant.stock>0?`<span>· ${variant.stock} unidades</span>`:''}`;
  }

  // Botón
  const btn = document.getElementById('addToCartBtn');
  const inp = document.getElementById('selectedVariantId');
  if (btn) {
    btn.disabled = variant.stock <= 0;
    document.getElementById('cartBtnText').textContent =
      variant.stock <= 0 ? 'Sin stock' : 'Agregar al carrito';
  }
  if (inp) inp.value = variant.id;
}

function resetCartState(msg) {
  const btn = document.getElementById('addToCartBtn');
  if (btn) {
    btn.disabled = true;
    const t = document.getElementById('cartBtnText');
    if (t) t.textContent = msg ?? 'Selecciona opciones';
  }
  const inp = document.getElementById('selectedVariantId');
  if (inp) inp.value = '';
  const qi = document.getElementById('quantityInput');
  if (qi) qi.max = 0;
}

/* ── Tabs — sistema propio sin Bootstrap ── */
(function(){
  var btns  = document.querySelectorAll('.pd-tab-btn');
  var panes = document.querySelectorAll('.pd-pane');

  btns.forEach(function(btn){
    btn.addEventListener('click', function(){
      var target = this.getAttribute('data-target');

      // Botones
      btns.forEach(function(b){ b.classList.remove('active'); });
      this.classList.add('active');

      // Panes
      panes.forEach(function(p){
        if (p.id === target) {
          p.classList.add('pd-pane-active');
        } else {
          p.classList.remove('pd-pane-active');
        }
      });
    });
  });
})();

/* ── Votos útiles en reseñas ── */
(function(){
  var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};

  function csrf() {
    var m = document.querySelector('meta[name="csrf-token"]'); if(m) return m.content;
    var i = document.querySelector('input[name="_token"]');    return i ? i.value : '';
  }

  document.addEventListener('click', function(e){
    var btn = e.target.closest('.js-helpful');
    if (!btn) return;

    if (!IS_AUTH) {
      if (typeof showToast === 'function') showToast('Inicia sesión para valorar reseñas', 'warning');
      return;
    }

    var reviewId = btn.dataset.reviewId;
    var isHelpful = btn.dataset.helpful;
    if (!reviewId || btn.classList.contains('btn-loading')) return;

    btn.classList.add('btn-loading');

    fetch('/reviews/' + reviewId + '/helpful', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrf(), 'Accept':'application/json' },
      body: JSON.stringify({ is_helpful: isHelpful === '1' })
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
      btn.classList.remove('btn-loading');
      if (data.success || data.status === 'ok') {
        // Actualizar contadores en todos los botones del card
        var card = btn.closest('.pd-review-card');
        if (card) {
          var allBtns = card.querySelectorAll('.js-helpful');
          allBtns.forEach(function(b){ b.classList.remove('voted'); });
          btn.classList.add('voted');
          // Actualizar números si vienen en la respuesta
          if (data.helpful_count !== undefined) {
            var helpfulBtn  = card.querySelector('.js-helpful[data-helpful="1"] .pd-rev-helpful-count');
            var notHelpfulBtn = card.querySelector('.js-helpful[data-helpful="0"] .pd-rev-helpful-count');
            if (helpfulBtn)    helpfulBtn.textContent    = '(' + data.helpful_count + ')';
            if (notHelpfulBtn) notHelpfulBtn.textContent = '(' + data.not_helpful_count + ')';
          }
        }
      }
    })
    .catch(function(){ btn.classList.remove('btn-loading'); });
  });
})();
</script>
@endpush