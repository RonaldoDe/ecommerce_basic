@extends('layouts.web')

@section('content')

    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Mis productos</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Inicio</a></li>
            <li class="current">Cuenta</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Account Section -->
    <section id="account" class="account section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <!-- Mobile Menu Toggle -->
        <div class="mobile-menu d-lg-none mb-4">
          <button class="mobile-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#profileMenu">
            <i class="bi bi-grid"></i>
            <span>Menu</span>
          </button>
        </div>

        <div class="row g-4">
          <!-- Profile Menu -->
          <div class="col-lg-3">
            <div class="profile-menu collapse d-lg-block" id="profileMenu">
              <!-- User Info -->
              <div class="user-info" data-aos="fade-right">
                <div class="user-avatar">
                  <img src="{{ asset('storage/' . $settings->logo) }}" alt="Profile" loading="lazy">
                  <span class="status-badge"><i class="bi bi-shield-check"></i></span>
                </div>
                <h4>{{ Auth::user()->name ?? 'Usuario' }}</h4>
                <div class="user-status">
                  <i class="bi bi-award"></i>
                  <span>{{ Auth::user()->roles->first()->name ?? 'Usuario' }}</span>
                </div>
              </div>

              <!-- Navigation Menu -->
              <nav class="menu-nav">
                <ul class="nav flex-column" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#orders">
                      <i class="bi bi-heart"></i>
                      <span>Mis productos</span>
                      <span class="badge">{{ count($favoriteProducts) }}</span>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>

          <!-- Content Area -->
          <div class="col-lg-9">
            <div class="content-area">
              <div class="tab-content">
                <!-- Orders Tab -->
                <div class="tab-pane fade show active" id="orders">
                  <div class="section-header" data-aos="fade-up">
                    <h2>Mis productos favoritos</h2>
                  </div>

                  <div class="wishlist-grid">
                    <!-- Wishlist Item 1 -->
                    @foreach ($favoriteProducts as $favoriteProduct)
                        <div class="wishlist-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="wishlist-image">
                                <img src="{{ asset('storage/' . ($favoriteProduct->product->images->first()?->image ?? 'products/default_ot_image.png')) }}" alt="Product" loading="lazy">
                                <form action="{{ route('web.favorites.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $favoriteProduct->product->id }}">
                                    <button class="btn-remove" type="submit" aria-label="Remove from wishlist">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <div class="sale-badge">{{ $favoriteProduct->product->stock }} disponibles</div>
                            </div>
                            <div class="wishlist-content">
                                <h4>{{ $favoriteProduct->product->name }}</h4>
                                <div class="product-meta">
                                <div class="rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                    <span>(4.5)</span>
                                </div>
                                <div class="price">
                                    <span class="current">{{ $settings->badge . $favoriteProduct->product->selling_price }}</span>
                                </div>
                                </div>
                                <button type="button" class="btn-add-cart">Agregar al carrito</button>
                            </div>
                        </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Account Section -->

@endsection