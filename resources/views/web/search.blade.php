@extends('layouts.web')

@section('content')

{{-- ── Page Title ── --}}
<div class="page-title">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">
      @if($searchText ?? null)
        Resultados para "<span style="color:#6366f1">{{ $searchText }}</span>"
      @else
        Todos los productos
      @endif
    </h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="{{ route('web.index') }}">Inicio</a></li>
        <li class="current">Búsqueda</li>
      </ol>
    </nav>
  </div>
</div>

<section class="sr-section">
  <div class="container">

    {{-- ── BARRA DE FILTROS ── --}}
    <div class="sr-toolbar" data-aos="fade-up">

      <div class="sr-info">
        @if(!($products->isEmpty() ?? true))
          <span class="sr-count">
            <i class="bi bi-grid me-1"></i>
            {{ $products->total() ?? $products->count() }} productos encontrados
          </span>
        @endif
      </div>

      <div class="sr-toolbar-right">
        {{-- Búsqueda rápida --}}
        <form action="{{ route('web.search') }}" class="sr-quick-search" id="filterForm">
          <input type="text" name="search" value="{{ $searchText ?? '' }}"
                 class="sr-search-input" placeholder="Refinar búsqueda…">
          <button type="submit" class="sr-search-btn"><i class="bi bi-search"></i></button>

          {{-- Filtros ocultos que se mantienen --}}
          @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
          @endif
        </form>

        {{-- Ordenar --}}
        <div class="sr-sort-wrap">
          <select class="sr-sort" onchange="updateSort(this.value)">
            <option value="">Ordenar por</option>
            <option value="latest"      {{ request('sort') === 'latest'      ? 'selected' : '' }}>Más recientes</option>
            <option value="price_asc"   {{ request('sort') === 'price_asc'   ? 'selected' : '' }}>Precio: menor a mayor</option>
            <option value="price_desc"  {{ request('sort') === 'price_desc'  ? 'selected' : '' }}>Precio: mayor a menor</option>
            <option value="rating"      {{ request('sort') === 'rating'      ? 'selected' : '' }}>Mejor valorados</option>
            <option value="popular"     {{ request('sort') === 'popular'     ? 'selected' : '' }}>Más populares</option>
          </select>
        </div>

        {{-- Vista --}}
        <div class="sr-view-btns">
          <button class="sr-view-btn active" id="viewGrid" title="Vista cuadrícula">
            <i class="bi bi-grid-3x3-gap"></i>
          </button>
          <button class="sr-view-btn" id="viewList" title="Vista lista">
            <i class="bi bi-list-ul"></i>
          </button>
        </div>
      </div>
    </div>

    {{-- ── CONTENIDO ── --}}
    @if($products->isEmpty())
      {{-- Estado vacío --}}
      <div class="sr-empty" data-aos="fade-up">
        <div class="sr-empty-icon"><i class="bi bi-search"></i></div>
        <h3>Sin resultados</h3>
        <p>No encontramos productos para
          @if($searchText ?? null)
            "<strong>{{ $searchText }}</strong>"
          @else
            tu búsqueda
          @endif.
        </p>
        <div class="sr-empty-suggestions">
          <p class="sr-empty-hint">Prueba con:</p>
          <ul>
            <li>Términos más generales o diferentes palabras clave</li>
            <li>Revisa la ortografía</li>
            <li>Explora nuestras categorías</li>
          </ul>
        </div>
        <a href="{{ route('web.index') }}" class="sr-empty-btn">
          <i class="bi bi-house me-2"></i> Volver al inicio
        </a>
      </div>

    @else

      {{-- Grid de productos --}}
      <div class="row g-4 sr-grid" id="productGrid" data-aos="fade-up" data-aos-delay="100">
        @foreach($products as $product)
          @php $isFav = in_array($product->id, $favoriteIds ?? []); @endphp

          {{-- ── CARD GRID ── --}}
          <div class="col-xl-3 col-lg-4 col-md-6 sr-col" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 50 }}">
            <div class="sr-card">

              <div class="sr-card-img">
                <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                     alt="{{ $product->name }}" loading="lazy">

                {{-- Badges --}}
                @if($product->is_on_sale)
                  <span class="sr-badge sr-badge-sale">−{{ round($product->discount_percentage) }}%</span>
                @elseif($product->is_new)
                  <span class="sr-badge sr-badge-new">Nuevo</span>
                @endif
                @if($product->has_variants)
                  <span class="sr-badge sr-badge-var"><i class="bi bi-grid-3x3-gap-fill me-1"></i>Variantes</span>
                @endif

                {{-- Hover actions --}}
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

                {{-- Cart btn slide --}}
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
                  <a href="{{ route('web.product.show', $product->id) }}">{{ Str::limit($product->name, 50) }}</a>
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
          </div>

          {{-- ── ROW LIST (hidden by default) ── --}}
          <div class="col-12 sr-col-list d-none">
            <div class="sr-list-card">
              <div class="sr-list-img">
                <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                     alt="{{ $product->name }}" loading="lazy">
                @if($product->is_on_sale)
                  <span class="sr-badge sr-badge-sale">−{{ round($product->discount_percentage) }}%</span>
                @endif
              </div>
              <div class="sr-list-body">
                <span class="sr-card-cat">{{ $product->category->name }}</span>
                <h4 class="sr-list-name">
                  <a href="{{ route('web.product.show', $product->id) }}">{{ $product->name }}</a>
                </h4>
                <p class="sr-list-desc">{{ Str::limit(strip_tags($product->short_description ?? ''), 120) }}</p>
                <div class="sr-stars">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                  @endfor
                  <span>({{ $product->reviews_count }})</span>
                </div>
              </div>
              <div class="sr-list-foot">
                <div class="sr-card-price">
                  @if($product->is_on_sale)
                    <span class="sr-p-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                  @endif
                  <span class="sr-p-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                </div>
                <div class="sr-list-actions">
                  @if($product->has_variants)
                    <a href="{{ route('web.product.show', $product->id) }}" class="sr-list-btn sr-list-btn-var">
                      <i class="bi bi-grid-3x3-gap me-1"></i> Ver opciones
                    </a>
                  @else
                    <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $product->id }}">
                      <input type="hidden" name="quantity" value="1">
                      <button type="submit" class="sr-list-btn js-cart">
                        <i class="bi bi-cart-plus me-1"></i> Agregar
                      </button>
                    </form>
                  @endif
                  <button type="button"
                          class="sr-list-fav js-fav {{ $isFav ? 'is-fav' : '' }}"
                          data-product-id="{{ $product->id }}">
                    <i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

        @endforeach
      </div>

      {{-- Paginación --}}
      @if($products->hasPages())
        <div class="sr-pagination" data-aos="fade-up">
          <p class="sr-pag-info">
            Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }} productos
          </p>
          <div class="ix-pag">
            {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
          </div>
        </div>
      @endif

    @endif

  </div>
</section>

{{-- ══ ESTILOS ══ --}}
<style>
.sr-section { padding: 50px 0 80px; }

/* ── Toolbar ── */
.sr-toolbar {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 12px;
  padding: 16px 20px; margin-bottom: 32px;
  background: #fff; border: 1px solid #eef0f3;
  border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.sr-info { display: flex; align-items: center; }
.sr-count {
  font-size: .84rem; font-weight: 700; color: #374151;
  display: flex; align-items: center;
}
.sr-count i { color: #6366f1; }

.sr-toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.sr-quick-search {
  display: flex; align-items: center;
  border: 1.5px solid #e5e7eb; border-radius: 10px; overflow: hidden;
  transition: border-color .2s, box-shadow .2s;
}
.sr-quick-search:focus-within {
  border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.sr-search-input {
  border: none; padding: 8px 14px; font-size: .85rem;
  color: #374151; outline: none; background: #fff; width: 200px;
}
.sr-search-input::placeholder { color: #9ca3af; }
.sr-search-btn {
  padding: 8px 14px; background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; border: none; cursor: pointer; font-size: .9rem;
  transition: opacity .2s;
}
.sr-search-btn:hover { opacity: .88; }

.sr-sort {
  padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 10px;
  font-size: .84rem; color: #374151; background: #fff;
  cursor: pointer; outline: none; transition: border-color .2s;
}
.sr-sort:focus { border-color: #6366f1; }

.sr-view-btns { display: flex; border: 1.5px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
.sr-view-btn {
  width: 36px; height: 36px; border: none; background: #fff;
  color: #9ca3af; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all .2s; font-size: .9rem;
}
.sr-view-btn.active { background: #6366f1; color: #fff; }
.sr-view-btn:hover:not(.active) { background: #f3f4f6; color: #374151; }

/* ── CARD GRID ── */
.sr-card {
  background: #fff; border: 1px solid #eef0f3; border-radius: 18px;
  overflow: hidden; transition: transform .3s, box-shadow .3s, border-color .3s;
  box-shadow: 0 2px 10px rgba(0,0,0,.04); height: 100%;
  display: flex; flex-direction: column;
}
.sr-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 40px rgba(99,102,241,.12);
  border-color: #c7d2fe;
}

.sr-card-img {
  position: relative; height: 240px; overflow: hidden; background: #f9fafb;
}
.sr-card-img img {
  width: calc(100% - 16px); height: calc(100% - 16px);
  object-fit: cover; position: absolute; top: 8px; left: 8px;
  border-radius: 10px; transition: transform .4s;
}
.sr-card:hover .sr-card-img img { transform: scale(1.06); }

.sr-badge {
  position: absolute; z-index: 2; width: auto !important;
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
  color: #374151; font-size: .88rem; text-decoration: none;
  transition: all .2s;
}
.sr-act-btn:hover, .sr-act-btn.is-fav { background: #6366f1; color: #fff; }
.sr-act-btn.is-fav { color: #ef4444 !important; }
.sr-act-btn.is-fav:hover { background: #ef4444 !important; color: #fff !important; }

.sr-cart-slide {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; font-size: .84rem; font-weight: 700;
  padding: 11px; text-align: center; text-decoration: none;
  border: none; cursor: pointer; width: 100%;
  display: flex; align-items: center; justify-content: center;
  transform: translateY(100%); transition: transform .3s;
}
.sr-card:hover .sr-cart-slide { transform: translateY(0); }
.js-cart-form { margin: 0; }

.sr-card-body { padding: 14px 16px 16px; flex: 1; display: flex; flex-direction: column; }
.sr-card-cat {
  font-size: .68rem; font-weight: 800; color: #6366f1;
  text-transform: uppercase; letter-spacing: .08em; margin-bottom: 5px; display: block;
}
.sr-card-name {
  font-size: .9rem; font-weight: 700; color: #111827; margin: 0 0 8px; flex: 1;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.sr-card-name a { color: inherit; text-decoration: none; transition: color .2s; }
.sr-card-name a:hover { color: #6366f1; }

.sr-card-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
.sr-stars { display: flex; align-items: center; gap: 2px; }
.sr-stars i { color: #fbbf24; font-size: .72rem; }
.sr-stars span { font-size: .72rem; color: #9ca3af; margin-left: 3px; }

.sr-chip { font-size: .68rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; }
.sr-chip-var  { background: #eef2ff; color: #6366f1; border: 1px solid #c7d2fe; }
.sr-chip-ok   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.sr-chip-low  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.sr-chip-out  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

.sr-card-price { display: flex; align-items: center; gap: 8px; }
.sr-p-old { font-size: .78rem; color: #9ca3af; text-decoration: line-through; }
.sr-p-cur { font-size: 1.05rem; font-weight: 800; color: #6366f1; }

/* ── CARD LIST ── */
.sr-list-card {
  background: #fff; border: 1px solid #eef0f3; border-radius: 18px;
  display: flex; align-items: center; gap: 20px; padding: 16px;
  transition: transform .3s, box-shadow .3s, border-color .3s;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.sr-list-card:hover {
  transform: translateX(4px);
  box-shadow: 0 6px 24px rgba(99,102,241,.1);
  border-color: #c7d2fe;
}
.sr-list-img {
  width: 110px; height: 110px; border-radius: 14px;
  overflow: hidden; flex-shrink: 0; background: #f9fafb;
  position: relative;
}
.sr-list-img img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }
.sr-list-body { flex: 1; min-width: 0; }
.sr-list-name {
  font-size: 1rem; font-weight: 700; color: #111827; margin: 4px 0 6px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sr-list-name a { color: inherit; text-decoration: none; }
.sr-list-name a:hover { color: #6366f1; }
.sr-list-desc { font-size: .82rem; color: #6b7280; margin: 0 0 8px; }
.sr-list-foot {
  display: flex; flex-direction: column; align-items: flex-end;
  gap: 10px; flex-shrink: 0;
}
.sr-list-actions { display: flex; gap: 8px; align-items: center; }

.sr-list-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; border: none; border-radius: 10px;
  font-size: .82rem; font-weight: 700; cursor: pointer;
  text-decoration: none; display: inline-flex; align-items: center;
  transition: all .25s;
}
.sr-list-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,.3); color: #fff; }
.sr-list-btn-var { background: linear-gradient(135deg,#8b5cf6,#7c3aed); }

.sr-list-fav {
  width: 36px; height: 36px; background: #fff;
  border: 1.5px solid #fecaca; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #b91c1c; font-size: .9rem;
  transition: all .2s;
}
.sr-list-fav.is-fav { background: #fef2f2; }
.sr-list-fav:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

/* ── Estado vacío ── */
.sr-empty {
  text-align: center; padding: 80px 20px;
  max-width: 500px; margin: 0 auto;
}
.sr-empty-icon {
  width: 88px; height: 88px; border-radius: 20px;
  background: #f3f4f6; display: flex; align-items: center; justify-content: center;
  margin: 0 auto 24px;
}
.sr-empty-icon i { font-size: 2.5rem; color: #9ca3af; }
.sr-empty h3 { font-family:'Sora',sans-serif; font-size: 1.4rem; font-weight: 800; color: #111827; margin: 0 0 8px; }
.sr-empty > p { color: #6b7280; margin: 0 0 20px; }
.sr-empty-suggestions {
  background: #f8f9fb; border-radius: 14px; border: 1px solid #eef0f3;
  padding: 16px 20px; text-align: left; margin-bottom: 24px;
}
.sr-empty-hint { font-size: .82rem; font-weight: 700; color: #374151; margin: 0 0 8px; }
.sr-empty-suggestions ul { margin: 0; padding-left: 20px; }
.sr-empty-suggestions li { font-size: .82rem; color: #6b7280; margin-bottom: 4px; }
.sr-empty-btn {
  display: inline-flex; align-items: center;
  padding: 12px 24px;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; border-radius: 12px; font-weight: 700;
  text-decoration: none; transition: all .25s;
  box-shadow: 0 4px 16px rgba(99,102,241,.3);
}
.sr-empty-btn:hover { transform: translateY(-2px); color: #fff; }

/* ── Paginación ── */
.sr-pagination {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 12px; margin-top: 48px; padding-top: 24px;
  border-top: 1px solid #eef0f3;
}
.sr-pag-info { font-size: .84rem; color: #6b7280; margin: 0; }
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

/* AJAX states */
.js-fav.is-fav { color: #ef4444 !important; }
.btn-loading { opacity: .6; pointer-events: none; }
.js-fav.pop i { transform: scale(1.5); }
.js-fav i { transition: transform .2s cubic-bezier(.34,1.56,.64,1); }

/* Toast */
#toast-container { position:fixed; bottom:24px; right:24px; z-index:99999; display:flex; flex-direction:column; gap:10px; pointer-events:none; }
.toast-msg { display:flex; align-items:center; gap:12px; padding:14px 20px; border-radius:12px; font-size:.88rem; font-weight:500; min-width:240px; max-width:320px; pointer-events:all; color:#fff; box-shadow:0 8px 30px rgba(0,0,0,.18); animation:tIn .35s cubic-bezier(.34,1.56,.64,1) forwards; }
.toast-msg.t-success { background:#1e1b4b; border-left:4px solid #4ade80; }
.toast-msg.t-error   { background:#1e1b4b; border-left:4px solid #f87171; }
.toast-msg.t-warning { background:#1e1b4b; border-left:4px solid #fbbf24; }
.toast-msg.t-info    { background:#1e1b4b; border-left:4px solid #60a5fa; }
.toast-msg i { font-size:1.1rem; flex-shrink:0; }
.toast-msg.t-success i { color:#4ade80; } .toast-msg.t-error i { color:#f87171; } .toast-msg.t-warning i { color:#fbbf24; } .toast-msg.t-info i { color:#60a5fa; }
.toast-msg.leaving { animation:tOut .25s ease forwards; }
@keyframes tIn  { from{opacity:0;transform:translateY(16px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes tOut { from{opacity:1;transform:translateY(0) scale(1)} to{opacity:0;transform:translateY(8px) scale(.95)} }

/* responsive */
@media (max-width:767.98px) {
  .sr-search-input { width: 140px; }
  .sr-list-card { flex-direction: column; align-items: flex-start; }
  .sr-list-foot { flex-direction: row; width: 100%; justify-content: space-between; }
}
@media (max-width:575.98px) {
  .sr-toolbar { flex-direction:column; align-items:flex-start; }
  .sr-toolbar-right { width:100%; flex-wrap:wrap; }
  .sr-quick-search { flex:1; }
  .sr-search-input { width:100%; }
}
</style>

@endsection

@push('scripts')
<script>
(function(){
  'use strict';

  var FAV_URL = '{{ route("web.favorites.store") }}';
  var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};

  /* Toast */
  var wrap = document.createElement('div');
  wrap.id = 'toast-container';
  document.body.appendChild(wrap);
  var ICONS = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };

  function toast(msg, type, ms) {
    var el = document.createElement('div');
    el.className = 'toast-msg t-' + (type||'success');
    el.innerHTML = '<i class="bi '+(ICONS[type]||ICONS.success)+'"></i><span>'+msg+'</span>';
    wrap.appendChild(el);
    setTimeout(function(){ el.classList.add('leaving'); setTimeout(function(){ el.parentNode&&el.remove(); },300); }, ms||3000);
  }

  function csrf() {
    var m = document.querySelector('meta[name="csrf-token"]'); if(m) return m.content;
    var i = document.querySelector('input[name="_token"]'); return i?i.value:'';
  }
  function badge(id, val) { if(val==null) return; var el=document.getElementById(id); if(el) el.textContent=val; }

  /* ── Favoritos ── */
  document.addEventListener('click', function(e){
    var btn = e.target.closest('.js-fav'); if(!btn) return;
    if(!IS_AUTH){ toast('Inicia sesión para guardar favoritos','warning'); return; }
    var pid = btn.getAttribute('data-product-id');
    if(!pid||btn.classList.contains('btn-loading')) return;
    btn.classList.add('btn-loading');
    fetch(FAV_URL,{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},
      body:JSON.stringify({product_id:pid})
    })
    .then(function(r){return r.json();})
    .then(function(data){
      btn.classList.remove('btn-loading');
      var icon = btn.querySelector('i');
      var added = (data.status==='added'||data.added===true);
      if(added){
        btn.classList.add('is-fav');
        if(icon){icon.classList.remove('bi-heart');icon.classList.add('bi-heart-fill');}
        toast('Añadido a favoritos ❤️','success');
      } else {
        btn.classList.remove('is-fav');
        if(icon){icon.classList.remove('bi-heart-fill');icon.classList.add('bi-heart');}
        toast('Eliminado de favoritos','info');
      }
      btn.classList.add('pop');
      setTimeout(function(){btn.classList.remove('pop');},350);
      badge('fav-badge',data.count);
    })
    .catch(function(){btn.classList.remove('btn-loading');toast('Error al actualizar favoritos','error');});
  });

  /* ── Carrito ── */
  document.addEventListener('submit', function(e){
    var cartBtn = e.target.querySelector('.js-cart'); if(!cartBtn) return;
    e.preventDefault();
    if(!IS_AUTH){toast('Inicia sesión para agregar al carrito','warning');return;}
    var form=e.target, orig=cartBtn.innerHTML;
    cartBtn.classList.add('btn-loading');
    cartBtn.innerHTML='<i class="bi bi-hourglass-split"></i>';
    fetch(form.action,{
      method:'POST',
      headers:{'X-CSRF-TOKEN':csrf(),'Accept':'application/json'},
      body:new FormData(form)
    })
    .then(function(r){return r.json();})
    .then(function(data){
      cartBtn.classList.remove('btn-loading');
      if(data.status===200||data.success){
        cartBtn.innerHTML='<i class="bi bi-check-lg"></i>';
        toast(data.message||'Agregado al carrito 🛒','success');
        badge('cart-badge',data.count);
        setTimeout(function(){cartBtn.innerHTML=orig;},1400);
      } else {
        cartBtn.innerHTML=orig;
        toast(data.message||'No se pudo agregar','error');
      }
    })
    .catch(function(){cartBtn.classList.remove('btn-loading');cartBtn.innerHTML=orig;toast('Error de conexión','error');});
  });

  /* ── Vista grid/list ── */
  var gridBtn  = document.getElementById('viewGrid');
  var listBtn  = document.getElementById('viewList');
  var gridCols = document.querySelectorAll('.sr-col');
  var listCols = document.querySelectorAll('.sr-col-list');

  gridBtn && gridBtn.addEventListener('click', function(){
    gridBtn.classList.add('active'); listBtn.classList.remove('active');
    gridCols.forEach(function(c){c.classList.remove('d-none');});
    listCols.forEach(function(c){c.classList.add('d-none');});
    document.getElementById('productGrid').classList.remove('sr-grid-list');
  });

  listBtn && listBtn.addEventListener('click', function(){
    listBtn.classList.add('active'); gridBtn.classList.remove('active');
    gridCols.forEach(function(c){c.classList.add('d-none');});
    listCols.forEach(function(c){c.classList.remove('d-none');});
  });

  /* ── Sort ── */
  window.updateSort = function(val) {
    var url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
  };

})();
</script>
@endpush