@extends('layouts.web')

@section('content')

{{-- Page Title --}}
<div class="page-title">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">
      @if(request('category'))
        {{ $allCategories->firstWhere('id', request('category'))?->name ?? 'Categorías' }}
      @else
        Todos los Productos
      @endif
    </h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="{{ route('web.index') }}">Inicio</a></li>
        <li class="current">Categorías</li>
      </ol>
    </nav>
  </div>
</div>

<div class="cat-page-wrap">
  <div class="container">
    <div class="row g-0">

      {{-- ══ SIDEBAR ══════════════════════════════════════════════ --}}
      <div class="col-lg-3 pe-lg-4" id="catSidebarCol">
        <div class="cat-sidebar" id="catSidebar">

          <button class="cat-sidebar-close d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
          </button>

          {{-- Widget: Categorías --}}
          <div class="cat-widget">
            <h3 class="cat-widget-title">
                <i class="bi bi-grid-3x3-gap me-2"></i>Categorías
            </h3>

            {{-- Buscador --}}
            <div class="cat-tree-search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="catTreeSearch" placeholder="Buscar categoría...">
            </div>

            {{-- Lista --}}
            <ul class="cat-tree list-unstyled mb-0" id="catTreeList">

                {{-- Todas --}}
                <li class="cat-tree-item {{ !request('category') ? 'active' : '' }}" data-name="todas las categorías">
                <a href="{{ route('web.categories') }}?{{ http_build_query(request()->except(['category','page'])) }}"
                    class="cat-tree-link">
                    <span>Todas las categorías</span>
                    <span class="cat-count">{{ $allCategories->sum('products_count') }}</span>
                </a>
                </li>

                @foreach($allCategories as $index => $cat)
                @php
                    $hasChildren  = $cat->children->count() > 0;
                    $isActive     = request('category') == $cat->id
                                    || $cat->children->contains('id', request('category'));
                    $collapseId   = 'cat-sub-' . $cat->id;
                @endphp

                <li class="cat-tree-item {{ $isActive ? 'active' : '' }} {{ $index >= 10 ? 'cat-tree-hidden' : '' }}"
                    data-name="{{ strtolower($cat->name) }}">

                    @if($hasChildren)
                    {{-- Categoría con hijos — desplegable --}}
                    <div class="cat-tree-header {{ $isActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $collapseId }}"
                        aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                        <a href="{{ route('web.categories') }}?{{ http_build_query(array_merge(request()->except(['category','page']), ['category' => $cat->id])) }}"
                        class="cat-tree-link cat-tree-parent"
                        onclick="event.stopPropagation()">
                        <span>{{ $cat->name }}</span>
                        <span class="cat-count">{{ $cat->products_count }}</span>
                        </a>
                        <span class="cat-tree-toggle">
                        <i class="bi bi-chevron-down"></i>
                        </span>
                    </div>

                    <ul id="{{ $collapseId }}"
                        class="cat-subtree list-unstyled collapse {{ $isActive ? 'show' : '' }}">
                        @foreach($cat->children as $child)
                        <li class="cat-tree-item {{ request('category') == $child->id ? 'active' : '' }}"
                            data-name="{{ strtolower($child->name) }}">
                            <a href="{{ route('web.categories') }}?{{ http_build_query(array_merge(request()->except(['category','page']), ['category' => $child->id])) }}"
                            class="cat-tree-link cat-tree-child">
                            <span>{{ $child->name }}</span>
                            <span class="cat-count">{{ $child->products_count }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    @else
                    {{-- Categoría sin hijos — link directo --}}
                    <a href="{{ route('web.categories') }}?{{ http_build_query(array_merge(request()->except(['category','page']), ['category' => $cat->id])) }}"
                        class="cat-tree-link">
                        <span>{{ $cat->name }}</span>
                        <span class="cat-count">{{ $cat->products_count }}</span>
                    </a>
                    @endif

                </li>
                @endforeach

            </ul>

            {{-- Ver más (si hay más de 10) --}}
            @if($allCategories->count() > 10)
                <button class="cat-show-more" id="catShowMore">
                <i class="bi bi-chevron-down me-1"></i>
                Ver {{ $allCategories->count() - 10 }} más
                </button>
            @endif

            </div>

          {{-- Widget: Precio --}}
          <div class="cat-widget">
            <h3 class="cat-widget-title">
              <i class="bi bi-currency-dollar me-2"></i>Rango de Precio
            </h3>
            <form action="{{ route('web.categories') }}" method="GET" id="priceForm">
              @foreach(request()->except(['price_min','price_max','page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
              @endforeach

              <div class="cat-price-display">
                <span class="cat-price-badge" id="priceMinLabel">
                  {{ $settings->badge }}{{ request('price_min', $priceMin) }}
                </span>
                <div class="cat-price-sep"></div>
                <span class="cat-price-badge" id="priceMaxLabel">
                  {{ $settings->badge }}{{ request('price_max', $priceMax) }}
                </span>
              </div>

              <div class="cat-range-wrap mt-3">
                <div class="cat-range-track">
                  <div class="cat-range-fill" id="rangeFill"></div>
                </div>
                <input type="range" class="cat-range" id="rangeMin"
                       name="price_min"
                       min="{{ $priceMin }}" max="{{ $priceMax }}"
                       value="{{ request('price_min', $priceMin) }}">
                <input type="range" class="cat-range" id="rangeMax"
                       name="price_max"
                       min="{{ $priceMin }}" max="{{ $priceMax }}"
                       value="{{ request('price_max', $priceMax) }}">
              </div>

              <div class="row g-2 mt-3">
                <div class="col-6">
                  <div class="cat-price-input-wrap">
                    <span>{{ $settings->badge }}</span>
                    <input type="number" id="inputMin" placeholder="Min"
                           min="{{ $priceMin }}" max="{{ $priceMax }}"
                           value="{{ request('price_min', $priceMin) }}">
                  </div>
                </div>
                <div class="col-6">
                  <div class="cat-price-input-wrap">
                    <span>{{ $settings->badge }}</span>
                    <input type="number" id="inputMax" placeholder="Max"
                           min="{{ $priceMin }}" max="{{ $priceMax }}"
                           value="{{ request('price_max', $priceMax) }}">
                  </div>
                </div>
              </div>

              <button type="submit" class="cat-apply-btn mt-3">
                <i class="bi bi-funnel me-1"></i>Aplicar Filtro
              </button>
            </form>
          </div>

          {{-- Widget: Marcas --}}
          @if($allBrands->count())
          <div class="cat-widget">
            <h3 class="cat-widget-title">
              <i class="bi bi-award me-2"></i>Filtrar por Marca
            </h3>
            <div class="cat-brand-search-wrap">
              <i class="bi bi-search"></i>
              <input type="text" id="brandSearch" placeholder="Buscar marcas...">
            </div>
            <div class="cat-brand-list" id="brandList">
              @foreach($allBrands as $brand)
                <div class="cat-brand-item" data-name="{{ strtolower($brand->name) }}">
                  <label class="cat-check-label">
                    <a href="{{ route('web.categories') }}?{{ http_build_query(array_merge(request()->query(), ['brand' => $brand->id, 'page' => 1])) }}"
                       class="d-flex justify-content-between align-items-center w-100 text-decoration-none {{ request('brand') == $brand->id ? 'active' : '' }}">
                      <span>{{ $brand->name }}</span>
                      <span class="cat-count">{{ $brand->active_count }}</span>
                    </a>
                  </label>
                </div>
              @endforeach
            </div>
            @if(request('brand'))
              <a href="{{ route('web.categories') }}?{{ http_build_query(request()->except(['brand','page'])) }}"
                 class="cat-clear-link mt-2">
                <i class="bi bi-x-circle me-1"></i>Limpiar marca
              </a>
            @endif
          </div>
          @endif

          {{-- Widget: En stock --}}
          <div class="cat-widget">
            <div class="cat-toggle-row">
              <div>
                <p class="cat-toggle-title mb-0">Solo en stock</p>
                <small class="text-muted">Ocultar agotados</small>
              </div>
              <div class="cat-toggle-track {{ request('in_stock') ? 'on' : '' }}" id="stockToggle"
                   data-url="{{ route('web.categories') }}"
                   data-params="{{ json_encode(request()->except(['in_stock','page'])) }}">
                <div class="cat-toggle-thumb"></div>
              </div>
            </div>
          </div>

        </div>
      </div>

      {{-- overlay mobile --}}
      <div class="cat-overlay d-lg-none" id="sidebarOverlay"></div>

      {{-- ══ CONTENIDO ══════════════════════════════════════════════ --}}
      <div class="col-lg-9">

        {{-- Toolbar --}}
        <div class="cat-toolbar mb-4">
          {{-- izquierda --}}
          <div class="cat-toolbar-left">
            <button class="cat-filter-btn d-lg-none" id="sidebarOpen">
              <i class="bi bi-funnel-fill me-1"></i>Filtros
              @if(request()->hasAny(['category','price_min','price_max','brand','in_stock']))
                <span class="cat-filter-dot"></span>
              @endif
            </button>
            <span class="cat-results-text">
              <strong>{{ $products->total() }}</strong> productos
            </span>
            {{-- Tags activos --}}
            @if(request('category'))
              <span class="cat-ftag">
                {{ $allCategories->firstWhere('id', request('category'))?->name }}
                <a href="{{ route('web.categories') }}?{{ http_build_query(request()->except(['category','page'])) }}"><i class="bi bi-x"></i></a>
              </span>
            @endif
            @if(request('brand'))
              <span class="cat-ftag">
                {{ $allBrands->firstWhere('id', request('brand'))?->name }}
                <a href="{{ route('web.categories') }}?{{ http_build_query(request()->except(['brand','page'])) }}"><i class="bi bi-x"></i></a>
              </span>
            @endif
            @if(request('price_min') || request('price_max'))
              <span class="cat-ftag">
                {{ $settings->badge }}{{ request('price_min', $priceMin) }} – {{ $settings->badge }}{{ request('price_max', $priceMax) }}
                <a href="{{ route('web.categories') }}?{{ http_build_query(request()->except(['price_min','price_max','page'])) }}"><i class="bi bi-x"></i></a>
              </span>
            @endif
            @if(request()->hasAny(['category','price_min','price_max','brand','in_stock','search']))
              <a href="{{ route('web.categories') }}" class="cat-clear-all-btn">
                <i class="bi bi-x-circle me-1"></i>Limpiar todo
              </a>
            @endif
          </div>

          {{-- derecha --}}
          <div class="cat-toolbar-right">
            {{-- Búsqueda --}}
            <form action="{{ route('web.categories') }}" method="GET" class="cat-tb-search">
              @foreach(request()->except(['search','page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
              @endforeach
              <input type="text" name="search" placeholder="Buscar productos..."
                     value="{{ request('search') }}">
              <button type="submit"><i class="bi bi-search"></i></button>
            </form>

            {{-- Sort --}}
            <select class="cat-select" id="sortSelect">
              <option value=""          {{ !request('sort')              ? 'selected':'' }}>Destacados</option>
              <option value="latest"    {{ request('sort')==='latest'    ? 'selected':'' }}>Más recientes</option>
              <option value="price_asc" {{ request('sort')==='price_asc' ? 'selected':'' }}>Precio ↑</option>
              <option value="price_desc"{{ request('sort')==='price_desc'? 'selected':'' }}>Precio ↓</option>
              <option value="rating"    {{ request('sort')==='rating'    ? 'selected':'' }}>Mejor valorados</option>
              <option value="popular"   {{ request('sort')==='popular'   ? 'selected':'' }}>Más vendidos</option>
            </select>

            {{-- Vista --}}
            <div class="cat-view-group">
              <button class="cat-view-btn active" id="viewGrid"><i class="bi bi-grid-3x3-gap"></i></button>
              <button class="cat-view-btn" id="viewList"><i class="bi bi-list-ul"></i></button>
            </div>

            {{-- Por página --}}
            <select class="cat-select" id="perPageSelect">
              <option value="12" {{ request('per_page',12)==12  ? 'selected':'' }}>12</option>
              <option value="24" {{ request('per_page',12)==24  ? 'selected':'' }}>24</option>
              <option value="48" {{ request('per_page',12)==48  ? 'selected':'' }}>48</option>
            </select>
          </div>
        </div>

        {{-- Resultados info --}}
        <p class="cat-results-info mb-3">
          Mostrando <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
          de <strong>{{ $products->total() }}</strong> productos
        </p>

        {{-- ── GRID VIEW ── --}}
        @if($products->isEmpty())
          <div class="cat-empty">
            <div class="cat-empty-icon"><i class="bi bi-box-seam"></i></div>
            <h3>Sin resultados</h3>
            <p>No encontramos productos con esos filtros.</p>
            <a href="{{ route('web.categories') }}" class="cat-empty-btn">
              <i class="bi bi-arrow-left me-2"></i>Ver todos
            </a>
          </div>
        @else

          {{-- GRID --}}
          <div class="row g-3" id="gridView">
            @foreach($products as $product)
              @php $isFav = in_array($product->id, $favoriteIds); @endphp
              <div class="col-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 50 }}">
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
                      <span class="sr-badge sr-badge-var"><i class="bi bi-grid-3x3-gap-fill me-1"></i>Var.</span>
                    @endif
                    <div class="sr-card-overlay">
                      <button type="button" class="sr-act-btn js-fav {{ $isFav ? 'is-fav':'' }}"
                              data-product-id="{{ $product->id }}">
                        <i class="bi bi-heart{{ $isFav ? '-fill':'' }}"></i>
                      </button>
                      <a href="{{ route('web.product.show', $product->id) }}" class="sr-act-btn">
                        <i class="bi bi-eye"></i>
                      </a>
                    </div>
                    @if($product->has_variants)
                      <a href="{{ route('web.product.show', $product->id) }}" class="sr-cart-slide">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Ver opciones
                      </a>
                    @else
                      <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="sr-cart-slide js-cart">
                          <i class="bi bi-cart-plus me-1"></i>Agregar
                        </button>
                      </form>
                    @endif
                  </div>
                  <div class="sr-card-body">
                    <span class="sr-card-cat">{{ $product->category->name }}</span>
                    <h5 class="sr-card-name">
                      <a href="{{ route('web.product.show', $product->id) }}">{{ Str::limit($product->name, 45) }}</a>
                    </h5>
                    <div class="sr-card-meta">
                      <div class="sr-stars">
                        @for($i=1;$i<=5;$i++)
                          <i class="bi bi-star{{ $i<=round($product->rating)?'-fill':'' }}"></i>
                        @endfor
                        <span>({{ $product->reviews_count }})</span>
                      </div>
                      @if($product->has_variants)
                        <span class="sr-chip sr-chip-var">{{ $product->variants->count() }} vars.</span>
                      @else
                        <span class="sr-chip {{ $product->stock>5?'sr-chip-ok':($product->stock>0?'sr-chip-low':'sr-chip-out') }}">
                          {{ $product->stock>0 ? $product->stock.' uds.' : 'Agotado' }}
                        </span>
                      @endif
                    </div>
                    <div class="sr-card-price">
                      @if($product->is_on_sale)
                        <span class="sr-p-old">{{ $settings->badge }}{{ number_format($product->selling_price,2) }}</span>
                      @endif
                      <span class="sr-p-cur">{{ $settings->badge }}{{ number_format($product->final_price,2) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- LIST VIEW --}}
          <div id="listView" class="d-none">
            @foreach($products as $product)
              @php $isFav = in_array($product->id, $favoriteIds); @endphp
              <div class="sr-list-card mb-3">
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
                    @for($i=1;$i<=5;$i++)
                      <i class="bi bi-star{{ $i<=round($product->rating)?'-fill':'' }}"></i>
                    @endfor
                    <span>({{ $product->reviews_count }})</span>
                  </div>
                </div>
                <div class="sr-list-foot">
                  <div class="sr-card-price mb-2">
                    @if($product->is_on_sale)
                      <span class="sr-p-old">{{ $settings->badge }}{{ number_format($product->selling_price,2) }}</span>
                    @endif
                    <span class="sr-p-cur">{{ $settings->badge }}{{ number_format($product->final_price,2) }}</span>
                  </div>
                  <div class="d-flex gap-2 align-items-center">
                    @if($product->has_variants)
                      <a href="{{ route('web.product.show', $product->id) }}" class="sr-list-btn sr-list-btn-var">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Ver opciones
                      </a>
                    @else
                      <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="sr-list-btn js-cart">
                          <i class="bi bi-cart-plus me-1"></i>Agregar
                        </button>
                      </form>
                    @endif
                    <button type="button" class="sr-list-fav js-fav {{ $isFav?'is-fav':'' }}"
                            data-product-id="{{ $product->id }}">
                      <i class="bi bi-heart{{ $isFav?'-fill':'' }}"></i>
                    </button>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Paginación --}}
          @if($products->hasPages())
            <div class="cat-pagination mt-5">
              <p class="cat-results-info">
                Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }}
                de {{ $products->total() }} productos
              </p>
              <div class="ix-pag">
                {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
              </div>
            </div>
          @endif
        @endif

      </div>
    </div>
  </div>
</div>

{{-- ══ ESTILOS ══════════════════════════════════════════════════════ --}}
<style>
/* ─── Layout ─── */
.cat-page-wrap { padding: 36px 0 80px; background: #f8f9fb; }

/* ─── Sidebar ─── */
.cat-sidebar { position: sticky; top: 88px; }

@media (max-width: 991.98px) {
  .cat-sidebar {
    position: fixed; top: 0; left: -320px; bottom: 0; width: 300px;
    background: #fff; z-index: 1050; overflow-y: auto;
    padding: 24px 20px; transition: left .35s cubic-bezier(.4,0,.2,1);
    box-shadow: 4px 0 32px rgba(0,0,0,.12);
  }
  .cat-sidebar.open { left: 0; }
  .cat-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 1049;
  }
  .cat-overlay.show { display: block; }
}

.cat-sidebar-close {
  position: absolute; top: 16px; right: 16px;
  width: 32px; height: 32px; border: none; background: #f3f4f6;
  border-radius: 8px; display: flex; align-items: center;
  justify-content: center; font-size: .9rem; color: #374151; cursor: pointer;
}

/* ─── Widget base ─── */
.cat-widget {
  background: #fff; border: 1px solid #eef0f3;
  border-radius: 16px; padding: 18px 16px;
  margin-bottom: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.cat-widget-title {
  font-family: 'Sora', sans-serif;
  font-size: .88rem; font-weight: 800; color: #111827;
  margin: 0 0 14px; padding-bottom: 10px;
  border-bottom: 2px solid #eef2ff;
  display: flex; align-items: center;
}
.cat-widget-title i { color: #6366f1; }

/* ─── Árbol categorías ─── */
.cat-tree { margin: 0; padding: 0; }
.cat-tree li { list-style: none; }
.cat-tree-link {
  display: flex; justify-content: space-between; align-items: center;
  padding: 7px 10px; border-radius: 10px;
  text-decoration: none; font-size: .83rem; color: #374151;
  transition: all .2s;
}
.cat-tree-link:hover { background: #eef2ff; color: #6366f1; }
.cat-tree li.active .cat-tree-link {
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; font-weight: 700;
}
.cat-tree li.active .cat-count { background: rgba(255,255,255,.2); color: #fff; }
.cat-count {
  font-size: .65rem; font-weight: 700; color: #9ca3af;
  background: #f3f4f6; padding: 2px 7px; border-radius: 10px;
  white-space: nowrap;
}

/* ─── Price range ─── */
.cat-price-display {
  display: flex; align-items: center; gap: 8px;
  justify-content: space-between;
}
.cat-price-badge {
  font-size: .88rem; font-weight: 700; color: #6366f1;
  background: #eef2ff; padding: 4px 12px; border-radius: 10px;
}
.cat-price-sep { flex: 1; height: 1px; background: #e5e7eb; }

.cat-range-wrap {
  position: relative; height: 20px;
  display: flex; align-items: center;
}
.cat-range-track {
  position: absolute; width: 100%; height: 4px;
  background: #e5e7eb; border-radius: 4px;
}
.cat-range-fill {
  position: absolute; height: 4px;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  border-radius: 4px;
}
.cat-range {
  position: absolute; width: 100%;
  -webkit-appearance: none; appearance: none;
  height: 4px; background: transparent;
  pointer-events: none;
}
.cat-range::-webkit-slider-thumb {
  -webkit-appearance: none; appearance: none;
  width: 18px; height: 18px; border-radius: 50%;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  cursor: pointer; pointer-events: all;
  box-shadow: 0 2px 8px rgba(99,102,241,.4);
  border: 2px solid #fff;
}
.cat-price-input-wrap {
  display: flex; align-items: center; gap: 4px;
  border: 1.5px solid #e5e7eb; border-radius: 8px;
  padding: 6px 10px; font-size: .83rem;
  transition: border-color .2s;
}
.cat-price-input-wrap:focus-within { border-color: #6366f1; }
.cat-price-input-wrap span { color: #9ca3af; font-size: .8rem; }
.cat-price-input-wrap input {
  border: none; outline: none; width: 100%;
  font-size: .83rem; color: #374151;
  background: transparent;
}
.cat-apply-btn {
  display: flex; align-items: center; justify-content: center;
  width: 100%; padding: 10px;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; border: none; border-radius: 10px;
  font-size: .84rem; font-weight: 700; cursor: pointer;
  transition: all .25s;
}
.cat-apply-btn:hover { box-shadow: 0 4px 14px rgba(99,102,241,.4); transform: translateY(-1px); }

/* ─── Marcas ─── */
.cat-brand-search-wrap {
  display: flex; align-items: center; gap: 8px;
  border: 1.5px solid #e5e7eb; border-radius: 10px;
  padding: 7px 12px; margin-bottom: 10px;
  transition: border-color .2s;
}
.cat-brand-search-wrap:focus-within { border-color: #6366f1; }
.cat-brand-search-wrap i { color: #9ca3af; font-size: .85rem; }
.cat-brand-search-wrap input {
  border: none; outline: none; font-size: .83rem;
  color: #374151; width: 100%; background: transparent;
}
.cat-brand-list { max-height: 200px; overflow-y: auto; }
.cat-brand-list::-webkit-scrollbar { width: 4px; }
.cat-brand-list::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 4px; }
.cat-brand-item { margin-bottom: 2px; }
.cat-check-label a {
  padding: 6px 10px; border-radius: 8px;
  font-size: .83rem; color: #374151; transition: all .2s;
}
.cat-check-label a:hover { background: #eef2ff; color: #6366f1; }
.cat-check-label a.active { color: #6366f1; font-weight: 700; background: #eef2ff; }
.cat-clear-link {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: .75rem; color: #ef4444; text-decoration: none; transition: color .2s;
}
.cat-clear-link:hover { color: #dc2626; }

/* ─── Toggle ─── */
.cat-toggle-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.cat-toggle-title { font-size: .84rem; font-weight: 700; color: #111827; }
.cat-toggle-track {
  width: 44px; height: 24px; border-radius: 12px;
  background: #e5e7eb; position: relative;
  cursor: pointer; transition: background .25s; flex-shrink: 0;
}
.cat-toggle-track.on { background: linear-gradient(135deg,#6366f1,#8b5cf6); }
.cat-toggle-thumb {
  width: 18px; height: 18px; border-radius: 50%;
  background: #fff; position: absolute; top: 3px; left: 3px;
  transition: transform .25s; box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.cat-toggle-track.on .cat-toggle-thumb { transform: translateX(20px); }

/* ─── Toolbar ─── */
.cat-toolbar {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 10px;
  background: #fff; border: 1px solid #eef0f3;
  border-radius: 16px; padding: 12px 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.cat-toolbar-left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.cat-toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.cat-filter-btn {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 7px 14px;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; border: none; border-radius: 10px;
  font-size: .82rem; font-weight: 700; cursor: pointer;
  position: relative;
}
.cat-filter-dot {
  width: 8px; height: 8px; background: #fbbf24;
  border-radius: 50%; position: absolute; top: -3px; right: -3px;
  border: 2px solid #fff;
}
.cat-results-text { font-size: .84rem; color: #374151; }
.cat-results-text strong { color: #6366f1; }

.cat-ftag {
  display: inline-flex; align-items: center; gap: 5px;
  background: #eef2ff; color: #6366f1;
  font-size: .72rem; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
}
.cat-ftag a { color: #6366f1; text-decoration: none; line-height: 1; }
.cat-ftag a:hover { color: #ef4444; }
.cat-clear-all-btn {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: .76rem; font-weight: 700; color: #ef4444;
  text-decoration: none; transition: color .2s;
}
.cat-clear-all-btn:hover { color: #dc2626; }

.cat-tb-search {
  display: flex; border: 1.5px solid #e5e7eb;
  border-radius: 10px; overflow: hidden; transition: border-color .2s;
}
.cat-tb-search:focus-within { border-color: #6366f1; }
.cat-tb-search input {
  border: none; padding: 7px 12px; font-size: .82rem;
  color: #374151; outline: none; width: 160px; background: #fff;
}
.cat-tb-search button {
  padding: 7px 12px;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; border: none; cursor: pointer; font-size: .84rem;
}

.cat-select {
  padding: 7px 10px; border: 1.5px solid #e5e7eb;
  border-radius: 10px; font-size: .82rem; color: #374151;
  background: #fff; outline: none; cursor: pointer;
  transition: border-color .2s;
}
.cat-select:focus { border-color: #6366f1; }

.cat-view-group { display: flex; border: 1.5px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
.cat-view-btn {
  width: 34px; height: 34px; border: none; background: #fff;
  color: #9ca3af; display: flex; align-items: center;
  justify-content: center; cursor: pointer; font-size: .88rem; transition: all .2s;
}
.cat-view-btn.active { background: #6366f1; color: #fff; }
.cat-view-btn:hover:not(.active) { background: #f3f4f6; color: #374151; }

.cat-results-info { font-size: .82rem; color: #6b7280; margin: 0; }

/* ─── Empty state ─── */
.cat-empty {
  text-align: center; padding: 80px 20px;
  background: #fff; border-radius: 20px; border: 1px solid #eef0f3;
}
.cat-empty-icon {
  width: 80px; height: 80px; border-radius: 20px;
  background: #f3f4f6; display: flex; align-items: center;
  justify-content: center; margin: 0 auto 20px;
}
.cat-empty-icon i { font-size: 2.2rem; color: #9ca3af; }
.cat-empty h3 { font-family:'Sora',sans-serif; font-size:1.3rem; font-weight:800; color:#111827; margin:0 0 8px; }
.cat-empty p { color:#6b7280; margin:0 0 20px; }
.cat-empty-btn {
  display: inline-flex; align-items: center;
  padding: 11px 22px;
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; border-radius: 12px; text-decoration: none;
  font-weight: 700; font-size: .88rem; transition: all .25s;
}
.cat-empty-btn:hover { transform: translateY(-2px); color: #fff; }

/* ─── Paginación ─── */
.cat-pagination {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 12px;
  padding-top: 24px; border-top: 1px solid #eef0f3;
}
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
}

/* ─── sr-card (product cards) ─── */
.sr-card{background:#fff;border:1px solid #eef0f3;border-radius:18px;overflow:hidden;transition:transform .3s,box-shadow .3s,border-color .3s;box-shadow:0 2px 10px rgba(0,0,0,.04);display:flex;flex-direction:column}
.sr-card:hover{transform:translateY(-5px);box-shadow:0 14px 40px rgba(99,102,241,.12);border-color:#c7d2fe}
.sr-card-img{position:relative;height:240px;overflow:hidden;background:#f9fafb;flex-shrink:0}
.sr-card-img img{width:calc(100% - 16px);height:calc(100% - 16px);object-fit:cover;position:absolute;top:8px;left:8px;border-radius:10px;transition:transform .4s}
.sr-card:hover .sr-card-img img{transform:scale(1.06)}
.sr-badge{position:absolute;z-index:2;width:auto!important;font-size:.66rem;font-weight:800;padding:3px 10px;border-radius:20px;color:#fff;display:inline-flex!important;align-items:center}
.sr-badge-sale{top:16px;left:16px;background:linear-gradient(135deg,#ef4444,#dc2626)}
.sr-badge-new{top:16px;left:16px;background:linear-gradient(135deg,#10b981,#059669)}
.sr-badge-var{top:16px;right:16px;left:auto;background:linear-gradient(135deg,#6366f1,#8b5cf6)}
.sr-card-overlay{position:absolute;top:16px;right:16px;display:flex;flex-direction:column;gap:7px;opacity:0;transform:translateX(10px);transition:all .3s;z-index:3}
.sr-card:hover .sr-card-overlay{opacity:1;transform:translateX(0)}
.sr-act-btn{width:36px;height:36px;background:#fff;border:none;border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 10px rgba(0,0,0,.12);color:#374151;font-size:.88rem;text-decoration:none;transition:all .2s}
.sr-act-btn:hover{background:#6366f1;color:#fff}
.sr-act-btn.is-fav{color:#ef4444}
.sr-act-btn.is-fav:hover{background:#ef4444!important;color:#fff!important}
.sr-cart-slide{position:absolute;bottom:0;left:0;right:0;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-size:.8rem;font-weight:700;padding:10px;text-align:center;text-decoration:none;border:none;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;transform:translateY(100%);transition:transform .3s}
.sr-card:hover .sr-cart-slide{transform:translateY(0)}
.sr-card-body{padding:12px 14px 14px;flex:1;display:flex;flex-direction:column}
.sr-card-cat{font-size:.65rem;font-weight:800;color:#6366f1;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;display:block}
.sr-card-name{font-size:.88rem;font-weight:700;color:#111827;margin:0 0 8px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.sr-card-name a{color:inherit;text-decoration:none;transition:color .2s}
.sr-card-name a:hover{color:#6366f1}
.sr-card-meta{display:flex;align-items:center;gap:6px;margin-bottom:8px;flex-wrap:wrap}
.sr-stars{display:flex;align-items:center;gap:2px}
.sr-stars i{color:#fbbf24;font-size:.7rem}
.sr-stars span{font-size:.7rem;color:#9ca3af;margin-left:3px}
.sr-chip{font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:12px}
.sr-chip-var{background:#eef2ff;color:#6366f1;border:1px solid #c7d2fe}
.sr-chip-ok{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
.sr-chip-low{background:#fffbeb;color:#92400e;border:1px solid #fde68a}
.sr-chip-out{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
.sr-card-price{display:flex;align-items:center;gap:7px;margin-top:auto;padding-top:8px}
.sr-p-old{font-size:.75rem;color:#9ca3af;text-decoration:line-through}
.sr-p-cur{font-size:1rem;font-weight:800;color:#6366f1}

/* ─── List card ─── */
.sr-list-card{background:#fff;border:1px solid #eef0f3;border-radius:18px;display:flex;align-items:center;gap:20px;padding:16px;transition:transform .3s,box-shadow .3s,border-color .3s;box-shadow:0 2px 10px rgba(0,0,0,.04)}
.sr-list-card:hover{transform:translateX(4px);box-shadow:0 6px 24px rgba(99,102,241,.1);border-color:#c7d2fe}
.sr-list-img{width:110px;height:110px;border-radius:14px;overflow:hidden;flex-shrink:0;background:#f9fafb;position:relative}
.sr-list-img img{width:100%;height:100%;object-fit:cover;padding:6px}
.sr-list-body{flex:1;min-width:0}
.sr-list-name{font-size:1rem;font-weight:700;color:#111827;margin:4px 0 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sr-list-name a{color:inherit;text-decoration:none}
.sr-list-name a:hover{color:#6366f1}
.sr-list-desc{font-size:.82rem;color:#6b7280;margin:0 0 8px}
.sr-list-foot{display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0}
.sr-list-btn{padding:8px 16px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:10px;font-size:.82rem;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;transition:all .25s}
.sr-list-btn:hover{transform:translateY(-1px);color:#fff}
.sr-list-btn-var{background:linear-gradient(135deg,#8b5cf6,#7c3aed)}
.sr-list-fav{width:36px;height:36px;background:#fff;border:1.5px solid #fecaca;border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#b91c1c;font-size:.9rem;transition:all .2s}
.sr-list-fav.is-fav{background:#fef2f2}
.sr-list-fav:hover{background:#ef4444;color:#fff;border-color:#ef4444}

/* ─── AJAX ─── */
.js-fav.is-fav{color:#ef4444!important}
.js-fav.pop i{transform:scale(1.5)}
.js-fav i{transition:transform .2s cubic-bezier(.34,1.56,.64,1)}
.btn-loading{opacity:.6;pointer-events:none}
.js-cart-form{margin:0;padding:0}

/* ─── Toast ─── */
#toast-container{position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.toast-msg{display:flex;align-items:center;gap:12px;padding:14px 20px;border-radius:12px;font-size:.88rem;font-weight:500;min-width:240px;max-width:320px;pointer-events:all;color:#fff;box-shadow:0 8px 30px rgba(0,0,0,.18);animation:tIn .35s cubic-bezier(.34,1.56,.64,1) forwards}
.toast-msg.t-success{background:#1e1b4b;border-left:4px solid #4ade80}
.toast-msg.t-error{background:#1e1b4b;border-left:4px solid #f87171}
.toast-msg.t-warning{background:#1e1b4b;border-left:4px solid #fbbf24}
.toast-msg.t-info{background:#1e1b4b;border-left:4px solid #60a5fa}
.toast-msg i{font-size:1.1rem;flex-shrink:0}
.toast-msg.t-success i{color:#4ade80}.toast-msg.t-error i{color:#f87171}.toast-msg.t-warning i{color:#fbbf24}.toast-msg.t-info i{color:#60a5fa}
.toast-msg.leaving{animation:tOut .25s ease forwards}
@keyframes tIn{from{opacity:0;transform:translateY(16px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)}}
@keyframes tOut{from{opacity:1;transform:translateY(0) scale(1)}to{opacity:0;transform:translateY(8px) scale(.95)}}

/* ─── Responsive ─── */
@media(max-width:991.98px){
  .cat-toolbar{flex-direction:column;align-items:flex-start}
  .cat-toolbar-right{width:100%;justify-content:flex-end}
}
@media(max-width:575.98px){
  .cat-tb-search input{width:110px}
  .sr-list-card{flex-direction:column;align-items:flex-start}
  .sr-list-foot{flex-direction:row;width:100%;justify-content:space-between;align-items:center}
}

/* ── Buscador del árbol ── */
.cat-tree-search-wrap {
  display: flex; align-items: center; gap: 8px;
  border: 1.5px solid #e5e7eb; border-radius: 10px;
  padding: 7px 12px; margin-bottom: 10px;
  transition: border-color .2s; background: #fafafa;
}
.cat-tree-search-wrap:focus-within { border-color: #6366f1; background: #fff; }
.cat-tree-search-wrap i { color: #9ca3af; font-size: .82rem; flex-shrink: 0; }
.cat-tree-search-wrap input {
  border: none; outline: none; font-size: .82rem;
  color: #374151; width: 100%; background: transparent;
}

/* ── Cat tree header (desplegable) ── */
.cat-tree-header {
  display: flex; align-items: center; justify-content: space-between;
  border-radius: 10px; cursor: pointer;
  transition: background .2s;
  padding-right: 6px;
}
.cat-tree-header:hover { background: #eef2ff; }
.cat-tree-header .cat-tree-link { flex: 1; }
.cat-tree-parent { border-radius: 10px 0 0 10px; }

.cat-tree-toggle {
  width: 28px; height: 28px; display: flex; align-items: center;
  justify-content: center; color: #9ca3af; font-size: .72rem;
  flex-shrink: 0; transition: color .2s;
}
.cat-tree-header:hover .cat-tree-toggle { color: #6366f1; }
.cat-tree-toggle i {
  transition: transform .3s cubic-bezier(.4,0,.2,1);
  display: block;
}
.cat-tree-header:not(.collapsed) .cat-tree-toggle i {
  transform: rotate(180deg);
}

/* ── Subárbol ── */
.cat-subtree {
  margin: 4px 0 4px 12px;
  border-left: 2px solid #e0e7ff;
  padding-left: 8px;
}
.cat-subtree .cat-tree-link {
  padding: 5px 10px;
  font-size: .8rem;
}
.cat-subtree .cat-tree-item.active .cat-tree-link {
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color: #fff; font-weight: 700;
}
.cat-subtree .cat-tree-item.active .cat-count {
  background: rgba(255,255,255,.2); color: #fff;
}
.cat-tree-child::before {
  content: ''; display: inline-block;
  width: 6px; height: 6px; border-radius: 50%;
  background: #c7d2fe; margin-right: 6px; flex-shrink: 0;
}

/* ── Ver más ── */
.cat-show-more {
  display: flex; align-items: center; justify-content: center;
  width: 100%; padding: 8px 12px; margin-top: 8px;
  border: 1.5px dashed #c7d2fe; border-radius: 10px;
  background: transparent; color: #6366f1;
  font-size: .8rem; font-weight: 700; cursor: pointer;
  transition: all .2s;
}
.cat-show-more:hover {
  background: #eef2ff; border-style: solid;
}

/* ── Hidden items (> 10) ── */
.cat-tree-hidden { display: none; }
</style>

{{-- ══ JAVASCRIPT ══════════════════════════════════════════════════ --}}
<script>
(function(){
  'use strict';

  var FAV_URL = '{{ route("web.favorites.store") }}';
  var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};
  var BADGE   = '{{ $settings->badge }}';
  var PRICE_MIN = {{ $priceMin }};
  var PRICE_MAX = {{ $priceMax }};

  /* ── Toast ── */
  var wrap = document.createElement('div');
  wrap.id = 'toast-container';
  document.body.appendChild(wrap);
  var ICONS = {success:'bi-check-circle-fill',error:'bi-x-circle-fill',warning:'bi-exclamation-triangle-fill',info:'bi-info-circle-fill'};
  function toast(msg,type,ms){
    var el=document.createElement('div');
    el.className='toast-msg t-'+(type||'success');
    el.innerHTML='<i class="bi '+(ICONS[type]||ICONS.success)+'"></i><span>'+msg+'</span>';
    wrap.appendChild(el);
    setTimeout(function(){el.classList.add('leaving');setTimeout(function(){el.parentNode&&el.remove();},300);},ms||3000);
  }
  function csrf(){var m=document.querySelector('meta[name="csrf-token"]');if(m)return m.content;var i=document.querySelector('input[name="_token"]');return i?i.value:'';}
  function badge(id,val){if(val==null)return;var el=document.getElementById(id);if(el)el.textContent=val;}

  /* ── Sidebar mobile ── */
  var sidebar  = document.getElementById('catSidebar');
  var overlay  = document.getElementById('sidebarOverlay');
  var openBtn  = document.getElementById('sidebarOpen');
  var closeBtn = document.getElementById('sidebarClose');
  if(openBtn) openBtn.addEventListener('click',function(){sidebar.classList.add('open');overlay.classList.add('show');});
  if(closeBtn) closeBtn.addEventListener('click',function(){sidebar.classList.remove('open');overlay.classList.remove('show');});
  if(overlay) overlay.addEventListener('click',function(){sidebar.classList.remove('open');overlay.classList.remove('show');});

  /* ── Grid / List toggle ── */
  var gridBtn  = document.getElementById('viewGrid');
  var listBtn  = document.getElementById('viewList');
  var gridView = document.getElementById('gridView');
  var listView = document.getElementById('listView');
  if(gridBtn) gridBtn.addEventListener('click',function(){
    gridBtn.classList.add('active');listBtn.classList.remove('active');
    gridView.classList.remove('d-none');listView.classList.add('d-none');
  });
  if(listBtn) listBtn.addEventListener('click',function(){
    listBtn.classList.add('active');gridBtn.classList.remove('active');
    listView.classList.remove('d-none');gridView.classList.add('d-none');
  });

  /* ── Búsqueda + mostrar más del árbol de categorías ── */
    var catSearch  = document.getElementById('catTreeSearch');
    var showMore   = document.getElementById('catShowMore');
    var treeItems  = document.querySelectorAll('#catTreeList > .cat-tree-item');
    var allVisible = false;

    // Mostrar más
    /* ── Mostrar más ── */
    if(showMore) {
        showMore.addEventListener('click', function(){
            allVisible = !allVisible;

            treeItems.forEach(function(item, idx){
            if(idx >= 10) {
                // Forzar display con inline style para superar la clase CSS
                item.style.display = allVisible ? 'block' : 'none';
            }
            });

            showMore.innerHTML = allVisible
            ? '<i class="bi bi-chevron-up me-1"></i> Ver menos'
            : '<i class="bi bi-chevron-down me-1"></i> Ver ' + (treeItems.length - 10) + ' más';
        });
    }

    // Búsqueda
    if(catSearch) {
        catSearch.addEventListener('input', function(){
            var q = this.value.toLowerCase().trim();

            treeItems.forEach(function(item, idx){
            if(!q) {
                // Sin búsqueda: respetar si el botón está expandido o no
                item.style.display = (idx >= 10 && !allVisible) ? 'none' : 'block';
                return;
            }
            var parentName = (item.getAttribute('data-name') || '').toLowerCase();
            var childNames = Array.from(item.querySelectorAll('.cat-subtree .cat-tree-item'))
                                .map(function(c){ return (c.getAttribute('data-name')||'').toLowerCase(); })
                                .join(' ');
            var match = parentName.includes(q) || childNames.includes(q);
            item.style.display = match ? 'block' : 'none';

            if(match && childNames.includes(q) && !parentName.includes(q)) {
                var collapse = item.querySelector('.cat-subtree');
                if(collapse && !collapse.classList.contains('show')) {
                new bootstrap.Collapse(collapse, {show: true});
                var header = item.querySelector('.cat-tree-header');
                if(header) header.classList.remove('collapsed');
                }
            }
            });

            if(showMore) showMore.style.display = q ? 'none' : '';
        });
    }

  /* ── Sort / Per page ── */
  window.updateQuery = function(key, val){
    var url = new URL(window.location.href);
    url.searchParams.set(key, val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
  };
  var sortSel = document.getElementById('sortSelect');
  if(sortSel) sortSel.addEventListener('change',function(){ updateQuery('sort', this.value); });
  var perSel = document.getElementById('perPageSelect');
  if(perSel) perSel.addEventListener('change',function(){ updateQuery('per_page', this.value); });

  /* ── Price range slider ── */
  var rangeMin   = document.getElementById('rangeMin');
  var rangeMax   = document.getElementById('rangeMax');
  var fill       = document.getElementById('rangeFill');
  var labelMin   = document.getElementById('priceMinLabel');
  var labelMax   = document.getElementById('priceMaxLabel');
  var inputMin   = document.getElementById('inputMin');
  var inputMax   = document.getElementById('inputMax');

  function updateFill(){
    if(!rangeMin||!rangeMax||!fill) return;
    var min  = parseInt(rangeMin.min), max = parseInt(rangeMax.max);
    var valMin = parseInt(rangeMin.value), valMax = parseInt(rangeMax.value);
    var left = ((valMin - min)/(max - min))*100;
    var right = 100 - ((valMax - min)/(max - min))*100;
    fill.style.left  = left + '%';
    fill.style.right = right + '%';
    if(labelMin) labelMin.textContent = BADGE + valMin;
    if(labelMax) labelMax.textContent = BADGE + valMax;
    if(inputMin) inputMin.value = valMin;
    if(inputMax) inputMax.value = valMax;
  }

  if(rangeMin) rangeMin.addEventListener('input',function(){
    if(parseInt(rangeMin.value) > parseInt(rangeMax.value)) rangeMin.value = rangeMax.value;
    updateFill();
  });
  if(rangeMax) rangeMax.addEventListener('input',function(){
    if(parseInt(rangeMax.value) < parseInt(rangeMin.value)) rangeMax.value = rangeMin.value;
    updateFill();
  });
  if(inputMin) inputMin.addEventListener('change',function(){
    rangeMin.value = this.value; updateFill();
  });
  if(inputMax) inputMax.addEventListener('change',function(){
    rangeMax.value = this.value; updateFill();
  });
  updateFill();

  /* ── Búsqueda de marcas ── */
  var brandSearch = document.getElementById('brandSearch');
  if(brandSearch) brandSearch.addEventListener('input',function(){
    var q = this.value.toLowerCase();
    document.querySelectorAll('.cat-brand-item').forEach(function(item){
      item.style.display = item.getAttribute('data-name').includes(q) ? '' : 'none';
    });
  });

  /* ── Toggle stock ── */
  var stockToggle = document.getElementById('stockToggle');
  if(stockToggle) stockToggle.addEventListener('click',function(){
    var isOn = this.classList.toggle('on');
    var params = JSON.parse(this.getAttribute('data-params'));
    var url = new URL(this.getAttribute('data-url'));
    Object.entries(params).forEach(function(e){ url.searchParams.set(e[0], e[1]); });
    if(isOn) url.searchParams.set('in_stock','1');
    else url.searchParams.delete('in_stock');
    url.searchParams.delete('page');
    window.location.href = url.toString();
  });

  /* ── Favoritos ── */
  document.addEventListener('click',function(e){
    var btn = e.target.closest('.js-fav'); if(!btn) return;
    if(!IS_AUTH){toast('Inicia sesión para guardar favoritos','warning');return;}
    var pid = btn.getAttribute('data-product-id');
    if(!pid||btn.classList.contains('btn-loading')) return;
    btn.classList.add('btn-loading');
    fetch(FAV_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},body:JSON.stringify({product_id:pid})})
    .then(function(r){return r.json();})
    .then(function(data){
      btn.classList.remove('btn-loading');
      var icon = btn.querySelector('i');
      var added = (data.status==='added'||data.added===true);
      if(added){btn.classList.add('is-fav');if(icon){icon.classList.remove('bi-heart');icon.classList.add('bi-heart-fill');}toast('Añadido a favoritos ❤️','success');}
      else{btn.classList.remove('is-fav');if(icon){icon.classList.remove('bi-heart-fill');icon.classList.add('bi-heart');}toast('Eliminado de favoritos','info');}
      btn.classList.add('pop');setTimeout(function(){btn.classList.remove('pop');},350);
      badge('fav-badge',data.count);
    })
    .catch(function(){btn.classList.remove('btn-loading');toast('Error al actualizar favoritos','error');});
  });

  /* ── Carrito ── */
  document.addEventListener('submit',function(e){
    var cartBtn = e.target.querySelector('.js-cart'); if(!cartBtn) return;
    e.preventDefault();
    if(!IS_AUTH){toast('Inicia sesión para agregar al carrito','warning');return;}
    var form = e.target, orig = cartBtn.innerHTML;
    cartBtn.classList.add('btn-loading');
    cartBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    fetch(form.action,{method:'POST',headers:{'X-CSRF-TOKEN':csrf(),'Accept':'application/json'},body:new FormData(form)})
    .then(function(r){return r.json();})
    .then(function(data){
      cartBtn.classList.remove('btn-loading');
      if(data.status===200||data.success){
        cartBtn.innerHTML='<i class="bi bi-check-lg"></i>';
        toast(data.message||'Agregado al carrito 🛒','success');
        badge('cart-badge',data.count);
        setTimeout(function(){cartBtn.innerHTML=orig;},1400);
      } else {cartBtn.innerHTML=orig;toast(data.message||'No se pudo agregar','error');}
    })
    .catch(function(){cartBtn.classList.remove('btn-loading');cartBtn.innerHTML=orig;toast('Error de conexión','error');});
  });

})();
</script>

@endsection