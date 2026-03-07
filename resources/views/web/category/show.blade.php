@extends('layouts.web')

@section('content')

{{-- ── Page Title ── --}}
<div class="page-title">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">{{ $category->name }}</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="{{ route('web.index') }}">Inicio</a></li>
        <li><a href="{{ route('web.search') }}">Categorías</a></li>
        <li class="current">{{ $category->name }}</li>
      </ol>
    </nav>
  </div>
</div>

<div class="cat-layout">
  <div class="container">
    <div class="row g-4">

      {{-- ══ SIDEBAR ══════════════════════════════════════════════ --}}
      <div class="col-lg-3">
        <div class="cat-sidebar">

          {{-- Banner de categoría --}}
          @if($category->image)
            <div class="cat-sidebar-banner mb-4">
              <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
              <div class="cat-sidebar-banner-overlay">
                <span>{{ $category->name }}</span>
              </div>
            </div>
          @endif

          {{-- Categorías --}}
          <div class="cat-widget">
            <h4 class="cat-widget-title">
              <i class="bi bi-grid me-2"></i>Categorías
            </h4>
            <ul class="cat-tree">
              @foreach($allCategories as $cat)
                <li class="{{ $cat->id === $category->id ? 'active' : '' }}">
                  <a href="{{ route('web.category.show', $cat) }}">
                    <span>{{ $cat->name }}</span>
                    <span class="cat-count">{{ $cat->products_count }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>

          {{-- Filtro precio --}}
          <div class="cat-widget">
            <h4 class="cat-widget-title">
              <i class="bi bi-currency-dollar me-2"></i>Precio
            </h4>
            <form action="{{ route('web.category.show', $category) }}" method="GET" id="filterForm">
              @foreach(request()->except('price', 'page') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
              @endforeach
              <div class="cat-price-options">
                @php
                  $priceOptions = [
                    ''         => 'Todos los precios',
                    'under_25' => 'Menos de $25',
                    '25_50'    => '$25 a $50',
                    '50_100'   => '$50 a $100',
                    '100_200'  => '$100 a $200',
                    'over_200' => 'Más de $200',
                  ];
                @endphp
                @foreach($priceOptions as $val => $label)
                  <label class="cat-radio">
                    <input type="radio" name="price" value="{{ $val }}"
                      {{ request('price') === $val ? 'checked' : '' }}
                      onchange="document.getElementById('filterForm').submit()">
                    <span>{{ $label }}</span>
                  </label>
                @endforeach
              </div>
            </form>
          </div>

          {{-- Filtro marcas --}}
          @if($brands->count())
            <div class="cat-widget">
              <h4 class="cat-widget-title">
                <i class="bi bi-award me-2"></i>Marca
              </h4>
              <ul class="cat-brand-list">
                @foreach($brands as $brand)
                  <li>
                    <a href="{{ route('web.category.show', $category) }}?{{ http_build_query(array_merge(request()->query(), ['brand' => $brand->id])) }}"
                       class="{{ request('brand') == $brand->id ? 'active' : '' }}">
                      {{ $brand->name }}
                      <span class="cat-count">{{ $brand->products_count ?? '' }}</span>
                    </a>
                  </li>
                @endforeach
              </ul>
              @if(request('brand'))
                <a href="{{ route('web.category.show', $category) }}" class="cat-clear-link">
                  <i class="bi bi-x-circle me-1"></i>Limpiar filtro
                </a>
              @endif
            </div>
          @endif

          {{-- Solo en stock --}}
          <div class="cat-widget">
            <label class="cat-toggle-check">
              <input type="checkbox" id="inStockCheck"
                {{ request('in_stock') ? 'checked' : '' }}
                onchange="toggleStock(this)">
              <span class="cat-toggle-slider"></span>
              <span class="cat-toggle-label">Solo productos en stock</span>
            </label>
          </div>

        </div>
      </div>

      {{-- ══ CONTENIDO PRINCIPAL ══════════════════════════════════ --}}
      <div class="col-lg-9">

        {{-- Toolbar --}}
        <div class="cat-toolbar">
          <div class="cat-toolbar-left">
            <span class="cat-results-count">
              <strong>{{ $products->total() }}</strong> productos
            </span>
            {{-- Filtros activos --}}
            <div class="cat-active-filters">
              @if(request('price'))
                <span class="cat-filter-tag">
                  Precio: {{ $priceOptions[request('price')] ?? '' }}
                  <a href="{{ route('web.category.show', $category) }}?{{ http_build_query(request()->except('price','page')) }}">
                    <i class="bi bi-x"></i>
                  </a>
                </span>
              @endif
              @if(request('search'))
                <span class="cat-filter-tag">
                  "{{ request('search') }}"
                  <a href="{{ route('web.category.show', $category) }}?{{ http_build_query(request()->except('search','page')) }}">
                    <i class="bi bi-x"></i>
                  </a>
                </span>
              @endif
              @if(request('brand'))
                <span class="cat-filter-tag">
                  Marca: {{ $brands->find(request('brand'))?->name }}
                  <a href="{{ route('web.category.show', $category) }}?{{ http_build_query(request()->except('brand','page')) }}">
                    <i class="bi bi-x"></i>
                  </a>
                </span>
              @endif
            </div>
          </div>

          <div class="cat-toolbar-right">
            {{-- Búsqueda --}}
            <form action="{{ route('web.category.show', $category) }}" method="GET" class="cat-search-form">
              @foreach(request()->except('search','page') as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
              @endforeach
              <input type="text" name="search" placeholder="Buscar en {{ $category->name }}..."
                     value="{{ request('search') }}" class="cat-search-input">
              <button type="submit" class="cat-search-btn"><i class="bi bi-search"></i></button>
            </form>

            {{-- Sort --}}
            <select class="cat-sort" onchange="updateSort(this.value)">
              <option value="">Ordenar</option>
              <option value="latest"     {{ request('sort') === 'latest'     ? 'selected' : '' }}>Más recientes</option>
              <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Precio ↑</option>
              <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Precio ↓</option>
              <option value="rating"     {{ request('sort') === 'rating'     ? 'selected' : '' }}>Mejor valorados</option>
              <option value="popular"    {{ request('sort') === 'popular'    ? 'selected' : '' }}>Más vendidos</option>
            </select>

            {{-- Vista --}}
            <div class="cat-view-btns">
              <button class="cat-view-btn active" id="viewGrid" title="Cuadrícula">
                <i class="bi bi-grid-3x3-gap"></i>
              </button>
              <button class="cat-view-btn" id="viewList" title="Lista">
                <i class="bi bi-list-ul"></i>
              </button>
            </div>

            {{-- Por página --}}
            <select class="cat-sort" onchange="updatePerPage(this.value)">
              <option value="12"  {{ request('per_page', 12) == 12  ? 'selected' : '' }}>12 por página</option>
              <option value="24"  {{ request('per_page', 12) == 24  ? 'selected' : '' }}>24 por página</option>
              <option value="48"  {{ request('per_page', 12) == 48  ? 'selected' : '' }}>48 por página</option>
            </select>
          </div>
        </div>

        {{-- Grid de productos --}}
        @if($products->isEmpty())
          <div class="cat-empty">
            <div class="cat-empty-icon"><i class="bi bi-box-seam"></i></div>
            <h3>Sin productos</h3>
            <p>No hay productos disponibles con los filtros seleccionados.</p>
            <a href="{{ route('web.category.show', $category) }}" class="ix-btn-primary">
              <i class="bi bi-arrow-left me-2"></i> Limpiar filtros
            </a>
          </div>
        @else
          <div class="row g-3" id="productGrid">
            @foreach($products as $product)
              @php $isFav = in_array($product->id, $favoriteIds); @endphp
              {{-- ── GRID CARD ── --}}
              <div class="col-6 col-xl-4 cat-col-grid" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 60 }}">
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
                      <span class="sr-badge sr-badge-var"><i class="bi bi-grid-3x3-gap-fill me-1"></i>Variantes</span>
                    @endif

                    <div class="sr-card-overlay">
                      <button type="button"
                              class="sr-act-btn js-fav {{ $isFav ? 'is-fav' : '' }}"
                              data-product-id="{{ $product->id }}"
                              title="{{ $isFav ? 'Quitar de favoritos' : 'Guardar' }}">
                        <i class="bi bi-heart{{ $isFav ? '-fill' : '' }}"></i>
                      </button>
                      <a href="{{ route('web.product.show', $product->id) }}" class="sr-act-btn">
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
                    <span class="sr-card-cat">{{ $product->brand->name ?? $product->category->name }}</span>
                    <h5 class="sr-card-name">
                      <a href="{{ route('web.product.show', $product->id) }}">{{ Str::limit($product->name, 45) }}</a>
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

              {{-- ── LIST CARD ── --}}
              <div class="col-12 cat-col-list d-none">
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
            <div class="sr-pagination mt-5">
              <p class="sr-pag-info">
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

{{-- ══ ESTILOS ══ --}}
<style>
.cat-layout { padding: 40px 0 80px; }

/* ── Sidebar ── */
.cat-sidebar { position: sticky; top: 90px; }

.cat-sidebar-banner {
  border-radius: 16px; overflow: hidden;
  position: relative; height: 160px;
}
.cat-sidebar-banner img {
  width: 100%; height: 100%; object-fit: cover;
}
.cat-sidebar-banner-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(17,6,60,.8), transparent);
  display: flex; align-items: flex-end; padding: 16px;
}
.cat-sidebar-banner-overlay span {
  color: #fff; font-family: 'Sora', sans-serif;
  font-size: 1.1rem; font-weight: 800;
}

.cat-widget {
  background: #fff; border: 1px solid #eef0f3;
  border-radius: 16px; padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.cat-widget-title {
  font-family: 'Sora', sans-serif;
  font-size: .88rem; font-weight: 800; color: #111827;
  margin: 0 0 14px; padding-bottom: 10px;
  border-bottom: 2px solid #eef2ff;
  display: flex; align-items: center;
}
.cat-widget-title i { color: #6366f1; }

/* Árbol de categorías */
.cat-tree { list-style: none; margin: 0; padding: 0; }
.cat-tree li a {
  display: flex; justify-content: space-between; align-items: center;
  padding: 7px 10px; border-radius: 10px;
  text-decoration: none; font-size: .84rem; color: #374151;
  transition: all .2s;
}
.cat-tree li a:hover { background: #eef2ff; color: #6366f1; }
.cat-tree li.active a {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-weight: 700;
}
.cat-tree li.active .cat-count { background: rgba(255,255,255,.2); color: #fff; }
.cat-count {
  font-size: .68rem; font-weight: 700; color: #9ca3af;
  background: #f3f4f6; padding: 2px 7px; border-radius: 10px;
}

/* Radio precio */
.cat-price-options { display: flex; flex-direction: column; gap: 6px; }
.cat-radio {
  display: flex; align-items: center; gap: 8px;
  cursor: pointer; font-size: .84rem; color: #374151;
  padding: 6px 8px; border-radius: 8px; transition: background .2s;
}
.cat-radio:hover { background: #f3f4f6; }
.cat-radio input { accent-color: #6366f1; cursor: pointer; }
.cat-radio input:checked + span { color: #6366f1; font-weight: 600; }

/* Marcas */
.cat-brand-list { list-style: none; margin: 0; padding: 0; }
.cat-brand-list li a {
  display: flex; justify-content: space-between; align-items: center;
  padding: 6px 10px; border-radius: 8px;
  text-decoration: none; font-size: .83rem; color: #374151;
  transition: all .2s;
}
.cat-brand-list li a:hover { background: #eef2ff; color: #6366f1; }
.cat-brand-list li a.active {
  background: #eef2ff; color: #6366f1; font-weight: 700;
}
.cat-clear-link {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: .76rem; color: #ef4444; text-decoration: none;
  margin-top: 8px; transition: color .2s;
}
.cat-clear-link:hover { color: #dc2626; }

/* Toggle stock */
.cat-toggle-check {
  display: flex; align-items: center; gap: 10px;
  cursor: pointer; font-size: .84rem; color: #374151;
  user-select: none;
}
.cat-toggle-check input { display: none; }
.cat-toggle-slider {
  width: 40px; height: 22px; border-radius: 11px;
  background: #e5e7eb; position: relative;
  transition: background .25s; flex-shrink: 0;
}
.cat-toggle-slider::after {
  content: ''; position: absolute;
  top: 3px; left: 3px;
  width: 16px; height: 16px; border-radius: 50%;
  background: #fff; transition: transform .25s;
  box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.cat-toggle-check input:checked + .cat-toggle-slider {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
}
.cat-toggle-check input:checked + .cat-toggle-slider::after {
  transform: translateX(18px);
}

/* ── Toolbar ── */
.cat-toolbar {
  display: flex; justify-content: space-between;
  align-items: center; flex-wrap: wrap; gap: 12px;
  padding: 14px 18px; margin-bottom: 24px;
  background: #fff; border: 1px solid #eef0f3;
  border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.cat-toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.cat-results-count { font-size: .84rem; color: #374151; }
.cat-results-count strong { color: #6366f1; }
.cat-active-filters { display: flex; gap: 6px; flex-wrap: wrap; }
.cat-filter-tag {
  display: inline-flex; align-items: center; gap: 5px;
  background: #eef2ff; color: #6366f1;
  font-size: .72rem; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
}
.cat-filter-tag a { color: #6366f1; text-decoration: none; line-height: 1; }
.cat-filter-tag a:hover { color: #ef4444; }

.cat-toolbar-right {
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.cat-search-form {
  display: flex; border: 1.5px solid #e5e7eb;
  border-radius: 10px; overflow: hidden;
  transition: border-color .2s;
}
.cat-search-form:focus-within { border-color: #6366f1; }
.cat-search-input {
  border: none; padding: 7px 12px; font-size: .83rem;
  color: #374151; outline: none; width: 180px;
}
.cat-search-btn {
  padding: 7px 12px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; border: none; cursor: pointer; font-size: .85rem;
}
.cat-sort {
  padding: 7px 10px; border: 1.5px solid #e5e7eb;
  border-radius: 10px; font-size: .83rem; color: #374151;
  background: #fff; outline: none; cursor: pointer;
  transition: border-color .2s;
}
.cat-sort:focus { border-color: #6366f1; }
.cat-view-btns {
  display: flex; border: 1.5px solid #e5e7eb;
  border-radius: 10px; overflow: hidden;
}
.cat-view-btn {
  width: 34px; height: 34px; border: none; background: #fff;
  color: #9ca3af; display: flex; align-items: center;
  justify-content: center; cursor: pointer;
  font-size: .88rem; transition: all .2s;
}
.cat-view-btn.active { background: #6366f1; color: #fff; }
.cat-view-btn:hover:not(.active) { background: #f3f4f6; color: #374151; }

/* Estado vacío */
.cat-empty {
  text-align: center; padding: 80px 20px;
  background: #fff; border-radius: 20px;
  border: 1px solid #eef0f3;
}
.cat-empty-icon {
  width: 80px; height: 80px; border-radius: 20px;
  background: #f3f4f6; display: flex; align-items: center;
  justify-content: center; margin: 0 auto 20px;
}
.cat-empty-icon i { font-size: 2.2rem; color: #9ca3af; }
.cat-empty h3 { font-family: 'Sora', sans-serif; font-size: 1.3rem; font-weight: 800; color: #111827; margin: 0 0 8px; }
.cat-empty p { color: #6b7280; margin: 0 0 20px; }

/* Paginación — reutiliza ix-pag del index */
.sr-pagination {
  display: flex; justify-content: space-between;
  align-items: center; flex-wrap: wrap; gap: 12px;
  padding-top: 24px; border-top: 1px solid #eef0f3;
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
}

/* sr-card reutilizadas del search */
.sr-card { background:#fff; border:1px solid #eef0f3; border-radius:18px; overflow:hidden; transition:transform .3s,box-shadow .3s,border-color .3s; box-shadow:0 2px 10px rgba(0,0,0,.04); display:flex; flex-direction:column; }
.sr-card:hover { transform:translateY(-5px); box-shadow:0 14px 40px rgba(99,102,241,.12); border-color:#c7d2fe; }
.sr-card-img { position:relative; height:240px; overflow:hidden; background:#f9fafb; flex-shrink:0; }
.sr-card-img img { width:calc(100% - 16px); height:calc(100% - 16px); object-fit:cover; position:absolute; top:8px; left:8px; border-radius:10px; transition:transform .4s; }
.sr-card:hover .sr-card-img img { transform:scale(1.06); }
.sr-badge { position:absolute; z-index:2; width:auto !important; font-size:.66rem; font-weight:800; padding:3px 10px; border-radius:20px; color:#fff; display:inline-flex !important; align-items:center; }
.sr-badge-sale { top:16px; left:16px; background:linear-gradient(135deg,#ef4444,#dc2626); }
.sr-badge-new  { top:16px; left:16px; background:linear-gradient(135deg,#10b981,#059669); }
.sr-badge-var  { top:16px; right:16px; left:auto; background:linear-gradient(135deg,#6366f1,#8b5cf6); }
.sr-card-overlay { position:absolute; top:16px; right:16px; display:flex; flex-direction:column; gap:7px; opacity:0; transform:translateX(10px); transition:all .3s; z-index:3; }
.sr-card:hover .sr-card-overlay { opacity:1; transform:translateX(0); }
.sr-act-btn { width:36px; height:36px; background:#fff; border:none; border-radius:10px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 10px rgba(0,0,0,.12); color:#374151; font-size:.88rem; text-decoration:none; transition:all .2s; }
.sr-act-btn:hover { background:#6366f1; color:#fff; }
.sr-act-btn.is-fav { color:#ef4444; }
.sr-act-btn.is-fav:hover { background:#ef4444 !important; color:#fff !important; }
.sr-cart-slide { position:absolute; bottom:0; left:0; right:0; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; font-size:.8rem; font-weight:700; padding:10px; text-align:center; text-decoration:none; border:none; cursor:pointer; width:100%; display:flex; align-items:center; justify-content:center; transform:translateY(100%); transition:transform .3s; }
.sr-card:hover .sr-cart-slide { transform:translateY(0); }
.sr-card-body { padding:12px 14px 14px; flex:1; display:flex; flex-direction:column; }
.sr-card-cat { font-size:.65rem; font-weight:800; color:#6366f1; text-transform:uppercase; letter-spacing:.08em; margin-bottom:4px; display:block; }
.sr-card-name { font-size:.88rem; font-weight:700; color:#111827; margin:0 0 8px; flex:1; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.sr-card-name a { color:inherit; text-decoration:none; transition:color .2s; }
.sr-card-name a:hover { color:#6366f1; }
.sr-card-meta { display:flex; align-items:center; gap:6px; margin-bottom:8px; flex-wrap:wrap; }
.sr-stars { display:flex; align-items:center; gap:2px; }
.sr-stars i { color:#fbbf24; font-size:.7rem; }
.sr-stars span { font-size:.7rem; color:#9ca3af; margin-left:3px; }
.sr-chip { font-size:.65rem; font-weight:700; padding:2px 7px; border-radius:12px; }
.sr-chip-var { background:#eef2ff; color:#6366f1; border:1px solid #c7d2fe; }
.sr-chip-ok  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.sr-chip-low { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
.sr-chip-out { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
.sr-card-price { display:flex; align-items:center; gap:7px; margin-top:auto; padding-top:8px; }
.sr-p-old { font-size:.75rem; color:#9ca3af; text-decoration:line-through; }
.sr-p-cur { font-size:1rem; font-weight:800; color:#6366f1; }

/* List card */
.sr-list-card { background:#fff; border:1px solid #eef0f3; border-radius:18px; display:flex; align-items:center; gap:20px; padding:16px; transition:transform .3s,box-shadow .3s,border-color .3s; box-shadow:0 2px 10px rgba(0,0,0,.04); }
.sr-list-card:hover { transform:translateX(4px); box-shadow:0 6px 24px rgba(99,102,241,.1); border-color:#c7d2fe; }
.sr-list-img { width:110px; height:110px; border-radius:14px; overflow:hidden; flex-shrink:0; background:#f9fafb; position:relative; }
.sr-list-img img { width:100%; height:100%; object-fit:cover; padding:6px; }
.sr-list-body { flex:1; min-width:0; }
.sr-list-name { font-size:1rem; font-weight:700; color:#111827; margin:4px 0 6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sr-list-name a { color:inherit; text-decoration:none; }
.sr-list-name a:hover { color:#6366f1; }
.sr-list-desc { font-size:.82rem; color:#6b7280; margin:0 0 8px; }
.sr-list-foot { display:flex; flex-direction:column; align-items:flex-end; gap:10px; flex-shrink:0; }
.sr-list-actions { display:flex; gap:8px; align-items:center; }
.sr-list-btn { padding:8px 16px; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none; border-radius:10px; font-size:.82rem; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; transition:all .25s; }
.sr-list-btn:hover { transform:translateY(-1px); color:#fff; }
.sr-list-btn-var { background:linear-gradient(135deg,#8b5cf6,#7c3aed); }
.sr-list-fav { width:36px; height:36px; background:#fff; border:1.5px solid #fecaca; border-radius:10px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#b91c1c; font-size:.9rem; transition:all .2s; }
.sr-list-fav.is-fav { background:#fef2f2; }
.sr-list-fav:hover { background:#ef4444; color:#fff; border-color:#ef4444; }

/* AJAX */
.js-fav.is-fav { color:#ef4444 !important; }
.js-fav.pop i { transform:scale(1.5); }
.js-fav i { transition:transform .2s cubic-bezier(.34,1.56,.64,1); }
.btn-loading { opacity:.6; pointer-events:none; }
.js-cart-form { margin:0; padding:0; }

/* Toast */
#toast-container { position:fixed; bottom:24px; right:24px; z-index:99999; display:flex; flex-direction:column; gap:10px; pointer-events:none; }
.toast-msg { display:flex; align-items:center; gap:12px; padding:14px 20px; border-radius:12px; font-size:.88rem; font-weight:500; min-width:240px; max-width:320px; pointer-events:all; color:#fff; box-shadow:0 8px 30px rgba(0,0,0,.18); animation:tIn .35s cubic-bezier(.34,1.56,.64,1) forwards; }
.toast-msg.t-success{background:#1e1b4b;border-left:4px solid #4ade80;} .toast-msg.t-error{background:#1e1b4b;border-left:4px solid #f87171;} .toast-msg.t-warning{background:#1e1b4b;border-left:4px solid #fbbf24;} .toast-msg.t-info{background:#1e1b4b;border-left:4px solid #60a5fa;}
.toast-msg i{font-size:1.1rem;flex-shrink:0;} .toast-msg.t-success i{color:#4ade80;} .toast-msg.t-error i{color:#f87171;} .toast-msg.t-warning i{color:#fbbf24;} .toast-msg.t-info i{color:#60a5fa;}
.toast-msg.leaving{animation:tOut .25s ease forwards;}
@keyframes tIn{from{opacity:0;transform:translateY(16px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)}}
@keyframes tOut{from{opacity:1;transform:translateY(0) scale(1)}to{opacity:0;transform:translateY(8px) scale(.95)}}

/* Responsive */
@media(max-width:991.98px){
  .cat-sidebar{position:static;}
  .cat-toolbar{flex-direction:column;align-items:flex-start;}
  .cat-toolbar-right{width:100%;}
}
@media(max-width:575.98px){
  .cat-search-input{width:120px;}
  .sr-list-card{flex-direction:column;align-items:flex-start;}
  .sr-list-foot{flex-direction:row;width:100%;justify-content:space-between;}
}
</style>

@push('scripts')
<script>
(function(){
  'use strict';

  var FAV_URL = '{{ route("web.favorites.store") }}';
  var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};

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

  /* Favoritos */
  document.addEventListener('click',function(e){
    var btn=e.target.closest('.js-fav');if(!btn)return;
    if(!IS_AUTH){toast('Inicia sesión para guardar favoritos','warning');return;}
    var pid=btn.getAttribute('data-product-id');
    if(!pid||btn.classList.contains('btn-loading'))return;
    btn.classList.add('btn-loading');
    fetch(FAV_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},body:JSON.stringify({product_id:pid})})
    .then(function(r){return r.json();})
    .then(function(data){
      btn.classList.remove('btn-loading');
      var icon=btn.querySelector('i');
      var added=(data.status==='added'||data.added===true);
      if(added){btn.classList.add('is-fav');if(icon){icon.classList.remove('bi-heart');icon.classList.add('bi-heart-fill');}toast('Añadido a favoritos ❤️','success');}
      else{btn.classList.remove('is-fav');if(icon){icon.classList.remove('bi-heart-fill');icon.classList.add('bi-heart');}toast('Eliminado de favoritos','info');}
      btn.classList.add('pop');setTimeout(function(){btn.classList.remove('pop');},350);
      badge('fav-badge',data.count);
    })
    .catch(function(){btn.classList.remove('btn-loading');toast('Error al actualizar favoritos','error');});
  });

  /* Carrito */
  document.addEventListener('submit',function(e){
    var cartBtn=e.target.querySelector('.js-cart');if(!cartBtn)return;
    e.preventDefault();
    if(!IS_AUTH){toast('Inicia sesión para agregar al carrito','warning');return;}
    var form=e.target,orig=cartBtn.innerHTML;
    cartBtn.classList.add('btn-loading');
    cartBtn.innerHTML='<i class="bi bi-hourglass-split"></i>';
    fetch(form.action,{method:'POST',headers:{'X-CSRF-TOKEN':csrf(),'Accept':'application/json'},body:new FormData(form)})
    .then(function(r){return r.json();})
    .then(function(data){
      cartBtn.classList.remove('btn-loading');
      if(data.status===200||data.success){cartBtn.innerHTML='<i class="bi bi-check-lg"></i>';toast(data.message||'Agregado al carrito 🛒','success');badge('cart-badge',data.count);setTimeout(function(){cartBtn.innerHTML=orig;},1400);}
      else{cartBtn.innerHTML=orig;toast(data.message||'No se pudo agregar','error');}
    })
    .catch(function(){cartBtn.classList.remove('btn-loading');cartBtn.innerHTML=orig;toast('Error de conexión','error');});
  });

  /* Vista grid/list */
  var gridBtn=document.getElementById('viewGrid');
  var listBtn=document.getElementById('viewList');
  var gridCols=document.querySelectorAll('.cat-col-grid');
  var listCols=document.querySelectorAll('.cat-col-list');

  gridBtn&&gridBtn.addEventListener('click',function(){
    gridBtn.classList.add('active');listBtn.classList.remove('active');
    gridCols.forEach(function(c){c.classList.remove('d-none');});
    listCols.forEach(function(c){c.classList.add('d-none');});
  });
  listBtn&&listBtn.addEventListener('click',function(){
    listBtn.classList.add('active');gridBtn.classList.remove('active');
    gridCols.forEach(function(c){c.classList.add('d-none');});
    listCols.forEach(function(c){c.classList.remove('d-none');});
  });

  /* Sort / Per page */
  window.updateSort = function(val){
    var url=new URL(window.location.href);
    url.searchParams.set('sort',val);url.searchParams.delete('page');
    window.location.href=url.toString();
  };
  window.updatePerPage = function(val){
    var url=new URL(window.location.href);
    url.searchParams.set('per_page',val);url.searchParams.delete('page');
    window.location.href=url.toString();
  };
  window.toggleStock = function(el){
    var url=new URL(window.location.href);
    if(el.checked)url.searchParams.set('in_stock','1');
    else url.searchParams.delete('in_stock');
    url.searchParams.delete('page');
    window.location.href=url.toString();
  };
})();
</script>
@endpush

@endsection