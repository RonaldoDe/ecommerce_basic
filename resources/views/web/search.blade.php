@extends('layouts.web')

@section('content')
<!-- Best Sellers Section -->
    <section id="best-sellers" class="best-sellers section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Resultados de la busqueda para "{{ $search ?? '' }}"</h2>
        <p>Encuentra los productos que buscas</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">

            @if ($products->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        No se encontraron resultados para "{{ $search ?? '' }}"
                    </div>
                </div>
            @else
                @foreach ($products as $product)
                    <!-- Product -->
                        <div class="col-lg-3 col-md-6">
                            <div class="product-item">
                            <div class="product-image">
                                {{-- <div class="product-badge sale-badge">20% off</div> --}}
                                <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}" alt="Product Image" class="img-fluid" loading="lazy">
                                <div class="product-actions">
                                <button class="action-btn wishlist-btn">
                                    <i class="bi bi-heart"></i>
                                </button>
                                {{-- <button class="action-btn compare-btn">
                                    <i class="bi bi-arrow-left-right"></i>
                                </button> --}}
                                <a href="{{ route('web.product.show', $product->id) }}" class="action-btn quickview-btn">
                                    <i class="bi bi-zoom-in"></i>
                                </a>
                                </div>
                                <button class="cart-btn">Agregar al carrito</button>
                            </div>
                            <div class="product-info">
                                <div class="product-category">{{ $product->name }}</div>
                                <h4 class="product-name"><a href="{{ route('web.product.show', $product->id) }}">{{ $product->short_description }}</a></h4>
                                <div class="product-rating">
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                                <span class="rating-count">(38)</span><br><br>
                                <spam class="badge bg-danger">{{ $product->stock }} disponibles</spam>
                                </div>
                                <div class="product-price">
                                {{-- <span class="old-price">$240.00</span> --}}
                                <span class="current-price">{{ $settings->badge . $product->selling_price }}</span>
                                </div>
                                {{-- <div class="color-swatches">
                                <span class="swatch active" style="background-color: #1f2937;"></span>
                                <span class="swatch" style="background-color: #f59e0b;"></span>
                                <span class="swatch" style="background-color: #8b5cf6;"></span>
                                </div> --}}
                            </div>
                            </div>
                        </div>
                        <!-- End Product -->
                @endforeach

                @if($products->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4 px-3" style="border-b">
                        <div class="text-muted">
                            Mostrando de {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} productos
                        </div>
                        <div>
                            {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            @endif
        </div>

      </div>

    </section><!-- /Best Sellers Section -->
@endsection