@php
  $settings = \App\Models\Ajuste::first();
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>{{ $settings->name }}</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link href="{{ asset('storage/' . $settings->logo) }}" rel="icon">
  <link href="{{ asset('storage/' . $settings->logo) }}" rel="apple-touch-icon">

  <!-- Fonts: Inter + Sora (mismo que dashboard) -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/aos/aos.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/drift-zoom/drift-basic.css')}}" rel="stylesheet">

  <!-- Main CSS -->
  <link href="{{asset('assets/css/main.css')}}" rel="stylesheet">

  <style>
  /* ══════════════════════════════════════════════════════════════
     DESIGN SYSTEM — Mismo sistema que dashboard / carrito / órdenes
     Acento: #6366f1 indigo → #8b5cf6 purple  |  Dark: #1e1b4b
  ══════════════════════════════════════════════════════════════ */
  :root {
    --ds-indigo:   #6366f1;
    --ds-indigo-d: #4f46e5;
    --ds-purple:   #8b5cf6;
    --ds-dark:     #1e1b4b;
    --ds-dark-2:   #312e81;
    --ds-text:     #111827;
    --ds-muted:    #6b7280;
    --ds-border:   #eef0f3;
    --ds-bg:       #f8f9fb;
  }

  body {
    font-family: 'Inter', sans-serif;
    color: var(--ds-text);
  }

  /* ────────────────────────────────────────────────────────────
     HEADER — barra superior blanca
  ──────────────────────────────────────────────────────────── */
  #header {
    background: #fff;
    border-bottom: 1px solid var(--ds-border);
    box-shadow: 0 2px 20px rgba(0,0,0,.05);
  }

  /* Logo */
  .logo .sitename {
    font-family: 'Sora', sans-serif;
    font-weight: 800;
    font-size: 1.4rem;
    letter-spacing: -.025em;
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
    line-height: 1;
  }

  /* Search */
  .search-form .input-group {
    border-radius: 12px;
    overflow: hidden;
    border: 1.5px solid var(--ds-border);
    transition: border-color .2s, box-shadow .2s;
    background: var(--ds-bg);
  }
  .search-form .input-group:focus-within {
    border-color: var(--ds-indigo);
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
  }
  .search-form .form-control {
    border: none;
    background: var(--ds-bg);
    padding: 10px 16px;
    font-size: .9rem;
    color: var(--ds-text);
    box-shadow: none !important;
  }
  .search-form .form-control::placeholder { color: #9ca3af; }
  .search-form .btn {
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 1rem;
    transition: opacity .2s;
    border-radius: 0;
  }
  .search-form .btn:hover { opacity: .88; color: #fff; }

  /* Header action buttons */
  .header-action-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: var(--ds-text);
    font-size: .88rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s, color .2s;
    white-space: nowrap;
  }
  .header-action-btn:hover {
    background: var(--ds-bg);
    color: var(--ds-indigo);
    text-decoration: none;
  }
  .header-action-btn i {
    font-size: 1.3rem;
    line-height: 1;
  }

  /* Badge de carrito / favoritos */
  .header-action-btn .badge {
    position: absolute;
    top: 4px; right: 4px;
    min-width: 17px; height: 17px;
    padding: 0 4px;
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    color: #fff;
    border-radius: 10px;
    font-size: .6rem;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff;
    line-height: 1;
  }

  /* ── Account Dropdown ── */
  .account-dropdown .dropdown-menu {
    border-radius: 18px;
    border: 1px solid var(--ds-border);
    box-shadow: 0 16px 48px rgba(0,0,0,.12);
    padding: 0;
    overflow: hidden;
    min-width: 250px;
    margin-top: 10px !important;
  }

  .account-dropdown .dropdown-header {
    padding: 18px 20px;
    background: linear-gradient(135deg, var(--ds-dark) 0%, var(--ds-dark-2) 100%);
  }
  .account-dropdown .dropdown-header h6 {
    font-family: 'Sora', sans-serif;
    font-size: .9rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 3px;
  }
  .account-dropdown .dropdown-header p {
    font-size: .75rem;
    color: rgba(255,255,255,.55);
    margin: 0;
  }
  .account-dropdown .dropdown-header .sitename {
    color: #a5b4fc;
    -webkit-text-fill-color: #a5b4fc;
    background: none;
  }

  .account-dropdown .dropdown-body {
    padding: 8px;
    background: #fff;
  }
  .account-dropdown .dropdown-body .dropdown-item {
    padding: 9px 12px;
    border-radius: 9px;
    font-size: .86rem;
    color: var(--ds-text);
    display: flex; align-items: center; gap: 9px;
    transition: background .18s, color .18s;
  }
  .account-dropdown .dropdown-body .dropdown-item i {
    color: var(--ds-indigo);
    font-size: 1rem;
    width: 16px; text-align: center;
  }
  .account-dropdown .dropdown-body .dropdown-item:hover {
    background: #eef2ff;
    color: var(--ds-indigo);
  }

  .account-dropdown .dropdown-footer {
    padding: 12px;
    background: var(--ds-bg);
    border-top: 1px solid var(--ds-border);
  }
  .account-dropdown .dropdown-footer .btn-primary {
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: .86rem;
    padding: 9px;
    box-shadow: 0 3px 10px rgba(99,102,241,.25);
    transition: all .25s;
  }
  .account-dropdown .dropdown-footer .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(99,102,241,.35);
  }
  .account-dropdown .dropdown-footer .btn-outline-primary {
    border: 1.5px solid var(--ds-indigo);
    color: var(--ds-indigo);
    border-radius: 10px;
    font-weight: 600;
    font-size: .86rem;
    padding: 8px;
    transition: all .25s;
  }
  .account-dropdown .dropdown-footer .btn-outline-primary:hover {
    background: var(--ds-indigo);
    color: #fff;
  }

  .hero-description {
  color: rgba(255,255,255,.75) !important;
  font-size: 1rem; line-height: 1.7;
  margin: 0 0 28px; max-width: 480px;
}

  /* ────────────────────────────────────────────────────────────
     NAVBAR — gradiente oscuro (mismo que sidebar del dashboard)
  ──────────────────────────────────────────────────────────── */
  .header-nav {
    background: linear-gradient(135deg, var(--ds-dark) 0%, var(--ds-dark-2) 55%, #4338ca 100%);
  }

  .navmenu a,
  .navmenu a:focus {
    color: rgba(255,255,255,.72) !important;
    font-size: .87rem;
    font-weight: 500;
    padding: 13px 15px !important;
    border-radius: 8px;
    transition: color .2s, background .2s;
  }
  .navmenu a:hover,
  .navmenu .active,
  .navmenu .active:focus {
    color: #fff !important;
    background: rgba(255,255,255,.1);
  }
  .navmenu .toggle-dropdown {
    color: rgba(255,255,255,.4);
    font-size: .7rem;
  }

  /* Dropdown en navbar */
  .navmenu .dropdown ul {
    background: #fff;
    border-radius: 14px;
    border: 1px solid var(--ds-border);
    box-shadow: 0 12px 36px rgba(0,0,0,.1);
    padding: 8px;
    min-width: 190px;
    margin-top: 4px;
  }
  .navmenu .dropdown ul li a {
    color: var(--ds-text) !important;
    padding: 9px 14px !important;
    border-radius: 8px;
    font-size: .86rem;
  }
  .navmenu .dropdown ul li a:hover {
    background: #eef2ff;
    color: var(--ds-indigo) !important;
  }

  /* Megamenu tabs */
  .megamenu-tabs .nav-tabs {
    border-bottom: 2px solid var(--ds-border);
    gap: 2px;
    padding: 0 4px;
  }
  .megamenu-tabs .nav-tabs .nav-link {
    color: var(--ds-muted) !important;
    font-size: .82rem;
    font-weight: 600;
    padding: 8px 14px !important;
    border-radius: 8px 8px 0 0;
    background: none;
    border: none;
    transition: color .2s;
  }
  .megamenu-tabs .nav-tabs .nav-link.active {
    color: var(--ds-indigo) !important;
    background: #eef2ff;
    border-bottom: 2px solid var(--ds-indigo);
  }

  /* ────────────────────────────────────────────────────────────
     PAGE TITLE
  ──────────────────────────────────────────────────────────── */
  .page-title {
    padding: 16px 0;
    background: var(--ds-bg) !important;
    border-bottom: 1px solid var(--ds-border);
  }
  .page-title h1 {
    font-family: 'Sora', sans-serif;
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--ds-text);
    margin: 0;
  }
  .breadcrumbs ol {
    display: flex; align-items: center; gap: 6px;
    list-style: none; padding: 0; margin: 0;
    font-size: .79rem;
  }
  .breadcrumbs ol li a { color: var(--ds-indigo); text-decoration: none; transition: opacity .2s; }
  .breadcrumbs ol li a:hover { opacity: .75; }
  .breadcrumbs ol li.current { color: var(--ds-muted); }
  .breadcrumbs ol li + li::before { content: '/'; color: #d1d5db; margin-right: 6px; }

  /* ────────────────────────────────────────────────────────────
     FOOTER
  ──────────────────────────────────────────────────────────── */
  #footer {
    background: linear-gradient(160deg, var(--ds-dark) 0%, #1a1760 55%, #2d2780 100%);
    color: rgba(255,255,255,.65);
  }

  .footer-main { padding: 60px 0 44px; }

  /* Logo en footer */
  .footer-about .sitename {
    font-family: 'Sora', sans-serif;
    font-weight: 800;
    font-size: 1.4rem;
    background: linear-gradient(135deg, #a5b4fc, #c4b5fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-block;
  }
  .footer-about p {
    font-size: .86rem;
    line-height: 1.75;
    color: rgba(255,255,255,.5);
    margin: 0;
  }

  /* Widget headers */
  .footer-widget h4 {
    font-family: 'Sora', sans-serif;
    font-size: .82rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #fff;
    margin-bottom: 18px;
    padding-bottom: 10px;
    position: relative;
  }
  .footer-widget h4::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 24px; height: 2px;
    background: linear-gradient(90deg, var(--ds-indigo), var(--ds-purple));
    border-radius: 2px;
  }

  /* Social */
  .social-links h5 {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(255,255,255,.38);
    margin: 0 0 10px;
  }
  .social-icons { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }
  .social-icons a {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    color: rgba(255,255,255,.6);
    font-size: .9rem;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    transition: all .25s;
  }
  .social-icons a:hover {
    background: var(--ds-indigo);
    border-color: var(--ds-indigo);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(99,102,241,.4);
  }

  /* Footer links */
  .footer-links {
    list-style: none;
    padding: 0; margin: 0;
    display: flex; flex-direction: column; gap: 9px;
  }
  .footer-links a {
    color: rgba(255,255,255,.5);
    text-decoration: none;
    font-size: .86rem;
    display: inline-flex; align-items: center; gap: 7px;
    transition: color .2s, padding-left .2s;
  }
  .footer-links a::before {
    content: '';
    display: inline-block;
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--ds-indigo);
    opacity: .55;
    flex-shrink: 0;
    transition: opacity .2s;
  }
  .footer-links a:hover { color: #fff; padding-left: 5px; }
  .footer-links a:hover::before { opacity: 1; }

  /* Footer contact */
  .footer-contact { display: flex; flex-direction: column; gap: 11px; }
  .contact-item {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: .86rem;
    color: rgba(255,255,255,.55);
  }
  .contact-item i {
    color: #a5b4fc;
    font-size: .95rem;
    flex-shrink: 0;
    margin-top: 1px;
  }

  /* Footer bottom */
  .footer-bottom {
    border-top: 1px solid rgba(255,255,255,.07);
    padding: 22px 0;
  }
  .footer-bottom .copyright p {
    font-size: .82rem;
    color: rgba(255,255,255,.38);
    margin: 0;
  }
  .footer-bottom .copyright .sitename {
    color: rgba(255,255,255,.65);
    font-weight: 700;
    -webkit-text-fill-color: rgba(255,255,255,.65);
    background: none;
  }
  .footer-bottom .copyright strong.sitename {
    color: rgba(255,255,255,.65);
    font-weight: 700;
  }

  .payment-methods .payment-icons {
    display: flex; gap: 10px; align-items: center;
  }
  .payment-methods .payment-icons i {
    font-size: 1.4rem;
    color: rgba(255,255,255,.3);
    transition: color .2s, transform .2s;
  }
  .payment-methods .payment-icons i:hover {
    color: rgba(255,255,255,.75);
    transform: scale(1.1);
  }

  .legal-links { display: flex; gap: 16px; }
  .legal-links a {
    font-size: .78rem;
    color: rgba(255,255,255,.35);
    text-decoration: none;
    transition: color .2s;
  }
  .legal-links a:hover { color: rgba(255,255,255,.8); }

  /* ────────────────────────────────────────────────────────────
     SCROLL TOP
  ──────────────────────────────────────────────────────────── */
  .scroll-top {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    color: #fff;
    box-shadow: 0 4px 14px rgba(99,102,241,.4);
    transition: transform .25s, box-shadow .25s;
  }
  .scroll-top:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(99,102,241,.5);
    color: #fff;
  }

  /* ────────────────────────────────────────────────────────────
     SWEETALERT override
  ──────────────────────────────────────────────────────────── */
  .swal2-popup {
    border-radius: 18px !important;
    font-family: 'Inter', sans-serif !important;
    border: 1px solid var(--ds-border) !important;
  }
  .swal2-confirm {
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple)) !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 3px 12px rgba(99,102,241,.3) !important;
  }
  .swal2-cancel {
    border-radius: 10px !important;
    font-weight: 600 !important;
  }

  /* Mobile nav toggle */
  .mobile-nav-toggle {
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--ds-text);
    transition: color .2s;
  }
  .mobile-nav-toggle:hover { color: var(--ds-indigo); }
  </style>

  @stack('styles')
</head>

<body class="index-page">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <header id="header" class="header sticky-top">

    {{-- ── Barra superior blanca ── --}}
    <div class="main-header">
      <div class="container-fluid container-xl">
        <div class="d-flex py-3 align-items-center gap-3 justify-content-between">

          {{-- Logo --}}
          <a href="{{ url('/') }}" class="logo d-flex align-items-center text-decoration-none flex-shrink-0">
            <h1 class="sitename">{{ $settings->name }}</h1>
          </a>

          {{-- Search desktop --}}
          <form class="search-form flex-grow-1 d-none d-xl-block" action="{{ route('web.search') }}">
            <div class="input-group">
              <input type="text" name="search" value="{{ $search ?? '' }}"
                     class="form-control" placeholder="Buscar productos, marcas, categorías…">
              <button class="btn" type="submit">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>

          {{-- Acciones --}}
          <div class="header-actions d-flex align-items-center gap-1 flex-shrink-0">

            {{-- Search mobile --}}
            <button class="header-action-btn d-xl-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mobileSearch">
              <i class="bi bi-search"></i>
            </button>

            {{-- Cuenta --}}
            <div class="dropdown account-dropdown">
              <button class="header-action-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Cuenta' }}</span>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <div class="dropdown-header">
                  <h6>Bienvenido a <span class="sitename">{{ $settings->name }}</span></h6>
                  <p class="mb-0">Gestiona tu cuenta y pedidos</p>
                </div>
                <div class="dropdown-body">
                  <a class="dropdown-item" href="{{ route('web.dashboard.settings') }}">
                    <i class="bi bi-person-circle"></i> Mi perfil
                  </a>
                  <a class="dropdown-item" href="{{ route('web.dashboard.orders') }}">
                    <i class="bi bi-box-seam"></i> Mis pedidos
                  </a>
                  <a class="dropdown-item" href="{{ route('web.dashboard.wishlist') }}">
                    <i class="bi bi-heart"></i> Favoritos
                  </a>
                  <a class="dropdown-item" href="{{ route('web.dashboard.settings') }}">
                    <i class="bi bi-gear"></i> Configuración
                  </a>
                </div>
                <div class="dropdown-footer">
                  @if(Auth::check())
                    <form action="{{ route('logout') }}" method="post">
                      @csrf
                      <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                      </button>
                    </form>
                  @else
                    <a href="{{ route('web.login') }}" class="btn btn-primary w-100 mb-2">
                      <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
                    </a>
                    <a href="{{ route('web.register') }}" class="btn btn-outline-primary w-100">
                      Registrarse
                    </a>
                  @endif
                </div>
              </div>
            </div>

            {{-- Favoritos --}}
            <a href="{{ route('web.dashboard.wishlist') }}" class="header-action-btn d-none d-md-flex">
              <i class="bi bi-heart"></i>
              @php $favoriteProducts = Auth::check() ? count(auth()->user()->favoriteProducts) : 0; @endphp
              <span class="badge" id="fav-badge">{{ $favoriteProducts }}</span>
            </a>

            {{-- Carrito --}}
            <a href="{{ route('web.cart.index') }}" class="header-action-btn">
              <i class="bi bi-cart3"></i>
              @php $cartProducts = Auth::check() ? count(auth()->user()->cart) : 0; @endphp
              <span class="badge" id="cart-badge">{{ $cartProducts }}</span>
            </a>

            <i class="mobile-nav-toggle d-xl-none bi bi-list ms-1"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Navbar indigo ── --}}
    <div class="header-nav">
      <div class="container-fluid container-xl position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="{{ route('web.index') }}" class="{{ request()->routeIs('web.index') ? 'active' : '' }}">Inicio</a></li>
            <li><a href="about.html">Nosotros</a></li>
            <li><a href="category.html">Categorías</a></li>
            <li><a href="{{ route('web.cart.index') }}">Carrito</a></li>
            <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#">Dropdown 1</a></li>
                <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="#">Deep Dropdown 1</a></li>
                    <li><a href="#">Deep Dropdown 2</a></li>
                    <li><a href="#">Deep Dropdown 3</a></li>
                    <li><a href="#">Deep Dropdown 4</a></li>
                    <li><a href="#">Deep Dropdown 5</a></li>
                  </ul>
                </li>
                <li><a href="#">Dropdown 2</a></li>
                <li><a href="#">Dropdown 3</a></li>
                <li><a href="#">Dropdown 4</a></li>
              </ul>
            </li>

            <!-- Products Mega Menu -->
            <li class="products-megamenu-1"><a href="#"><span>Megamenu</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul class="mobile-megamenu">
                <li><a href="#">Featured Products</a></li>
                <li><a href="#">New Arrivals</a></li>
                <li><a href="#">Sale Items</a></li>
                <li class="dropdown"><a href="#"><span>Clothing</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="#">Men's Wear</a></li>
                    <li><a href="#">Women's Wear</a></li>
                    <li><a href="#">Kids Collection</a></li>
                    <li><a href="#">Sportswear</a></li>
                    <li><a href="#">Accessories</a></li>
                  </ul>
                </li>
                <li class="dropdown"><a href="#"><span>Electronics</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="#">Smartphones</a></li>
                    <li><a href="#">Laptops</a></li>
                    <li><a href="#">Audio Devices</a></li>
                    <li><a href="#">Smart Home</a></li>
                    <li><a href="#">Accessories</a></li>
                  </ul>
                </li>
              </ul>
              <div class="desktop-megamenu">
                <div class="megamenu-tabs">
                  <ul class="nav nav-tabs" id="productMegaMenuTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="featured-tab" data-bs-toggle="tab" data-bs-target="#featured-content-1862" type="button" aria-selected="true" role="tab">Featured</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="new-tab" data-bs-toggle="tab" data-bs-target="#new-content-1862" type="button" aria-selected="false" tabindex="-1" role="tab">New Arrivals</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="sale-tab" data-bs-toggle="tab" data-bs-target="#sale-content-1862" type="button" aria-selected="false" tabindex="-1" role="tab">Sale</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="category-tab" data-bs-toggle="tab" data-bs-target="#category-content-1862" type="button" aria-selected="false" tabindex="-1" role="tab">Categories</button>
                    </li>
                  </ul>
                </div>
                <div class="megamenu-content tab-content">
                  <div class="tab-pane fade show active" id="featured-content-1862" role="tabpanel" aria-labelledby="featured-tab">
                    <div class="product-grid">
                      <div class="product-card">
                        <div class="product-image"><img src="assets/img/product/product-1.webp" alt="Featured Product" loading="lazy"></div>
                        <div class="product-info"><h5>Premium Headphones</h5><p class="price">$129.99</p><a href="#" class="btn-view">View Product</a></div>
                      </div>
                      <div class="product-card">
                        <div class="product-image"><img src="assets/img/product/product-2.webp" alt="Featured Product" loading="lazy"></div>
                        <div class="product-info"><h5>Smart Watch</h5><p class="price">$199.99</p><a href="#" class="btn-view">View Product</a></div>
                      </div>
                      <div class="product-card">
                        <div class="product-image"><img src="assets/img/product/product-3.webp" alt="Featured Product" loading="lazy"></div>
                        <div class="product-info"><h5>Wireless Earbuds</h5><p class="price">$89.99</p><a href="#" class="btn-view">View Product</a></div>
                      </div>
                      <div class="product-card">
                        <div class="product-image"><img src="assets/img/product/product-4.webp" alt="Featured Product" loading="lazy"></div>
                        <div class="product-info"><h5>Bluetooth Speaker</h5><p class="price">$79.99</p><a href="#" class="btn-view">View Product</a></div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="new-content-1862" role="tabpanel" aria-labelledby="new-tab">
                    <div class="product-grid">
                      <div class="product-card">
                        <div class="product-image"><img src="assets/img/product/product-5.webp" alt="New Arrival" loading="lazy"><span class="badge-new">New</span></div>
                        <div class="product-info"><h5>Fitness Tracker</h5><p class="price">$69.99</p><a href="#" class="btn-view">View Product</a></div>
                      </div>
                      <div class="product-card">
                        <div class="product-image"><img src="assets/img/product/product-6.webp" alt="New Arrival" loading="lazy"><span class="badge-new">New</span></div>
                        <div class="product-info"><h5>Wireless Charger</h5><p class="price">$39.99</p><a href="#" class="btn-view">View Product</a></div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="sale-content-1862" role="tabpanel" aria-labelledby="sale-tab">
                    <div class="product-grid">
                      <div class="product-card">
                        <div class="product-image"><img src="assets/img/product/product-9.webp" alt="Sale Product" loading="lazy"><span class="badge-sale">-30%</span></div>
                        <div class="product-info"><h5>Wireless Keyboard</h5><p class="price"><span class="original-price">$89.99</span> $62.99</p><a href="#" class="btn-view">View Product</a></div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="category-content-1862" role="tabpanel" aria-labelledby="category-tab">
                    <div class="category-grid">
                      <div class="category-column"><h4>Clothing</h4><ul><li><a href="#">Men's Wear</a></li><li><a href="#">Women's Wear</a></li></ul></div>
                      <div class="category-column"><h4>Electronics</h4><ul><li><a href="#">Smartphones</a></li><li><a href="#">Laptops</a></li></ul></div>
                    </div>
                  </div>
                </div>
              </div>
            </li>

            <li><a href="contact.html">Contacto</a></li>
          </ul>
        </nav>
      </div>
    </div>

    {{-- ── Buscador mobile ── --}}
    <div class="collapse" id="mobileSearch">
      <div class="container py-2" style="background:#fff; border-top:1px solid var(--ds-border);">
        <form class="search-form" action="{{ route('web.search') }}">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar productos…">
            <button class="btn" type="submit"><i class="bi bi-search"></i></button>
          </div>
        </form>
      </div>
    </div>

  </header>

  <main class="main">
    @yield('content')
  </main>

  {{-- ══ FOOTER ══════════════════════════════════════════════════ --}}
  <footer id="footer" class="footer">
    <div class="footer-main">
      <div class="container">
        <div class="row gy-5">

          <div class="col-lg-4 col-md-6">
            <div class="footer-widget footer-about">
              <a href="{{ url('/') }}" class="d-block mb-3 text-decoration-none">
                <span class="sitename">{{ $settings->name }}</span>
              </a>
              <p class="hero-description">Tu tienda de confianza. Encontrarás los mejores productos al mejor precio, con envío rápido y seguro a todo el país.</p>
              <div class="social-links mt-4">
                <h5 class="hero-description">Síguenos</h5>
                <div class="social-icons">
                  <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                  <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                  <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                  <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                  <a href="#" aria-label="Pinterest"><i class="bi bi-pinterest"></i></a>
                  <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="footer-widget">
              <h4>Tienda</h4>
              <ul class="footer-links">
                <li><a href="category.html" style="color: #c3c6c9;">Nuevos productos</a></li>
                <li><a href="category.html" style="color: #c3c6c9;">Más vendidos</a></li>
                <li><a href="category.html" style="color: #c3c6c9;">Ofertas</a></li>
                <li><a href="category.html" style="color: #c3c6c9;">Categorías</a></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="footer-widget">
              <h4>Soporte</h4>
              <ul class="footer-links">
                <li><a href="#" style="color: #c3c6c9;">Centro de ayuda</a></li>
                <li><a href="#" style="color: #c3c6c9;">Envíos y entregas</a></li>
                <li><a href="#" style="color: #c3c6c9;">Devoluciones</a></li>
                <li><a href="contact.html" style="color: #c3c6c9;">Contáctanos</a></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="footer-widget">
              <h4>Contacto</h4>
              <div class="footer-contact">
                <div class="contact-item">
                  <i class="bi bi-geo-alt-fill text-white"></i>
                  <span style="color: #c3c6c9;">{{ $settings->address ?? 'Dirección de la tienda' }}</span>
                </div>
                <div class="contact-item">
                  <i class="bi bi-telephone-fill text-white"></i>
                  <span style="color: #c3c6c9;">{{ $settings->phone ?? '+57 000 000 0000' }}</span>
                </div>
                <div class="contact-item">
                  <i class="bi bi-envelope-fill text-white"></i>
                  <span style="color: #c3c6c9;">{{ $settings->email ?? 'info@tienda.com' }}</span>
                </div>
                <div class="contact-item">
                  <i class="bi bi-clock-fill text-white"></i>
                  <span style="color: #c3c6c9;">Lun – Vie: 8:00am – 6:00pm</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <div class="row gy-3 align-items-center">
          <div class="col-lg-6 col-md-12">
            <div class="copyright">
              <p>© {{ date('Y') }} <strong class="sitename">{{ $settings->name }}</strong>. Todos los derechos reservados.</p>
            </div>
          </div>
          <div class="col-lg-6 col-md-12">
            <div class="d-flex flex-wrap justify-content-lg-end justify-content-center align-items-center gap-4">
              <div class="payment-methods">
                <div class="payment-icons">
                  <i class="bi bi-credit-card" aria-label="Tarjeta"></i>
                  <i class="bi bi-paypal" aria-label="PayPal"></i>
                  <i class="bi bi-apple" aria-label="Apple Pay"></i>
                  <i class="bi bi-wallet2" aria-label="Wallet"></i>
                </div>
              </div>
              <div class="legal-links">
                <a href="tos.html">Términos</a>
                <a href="privacy.html">Privacidad</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <div id="preloader"></div>

  <!-- Vendor JS -->
  <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/vendor/php-email-form/validate.js')}}"></script>
  <script src="{{asset('assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
  <script src="{{asset('assets/vendor/aos/aos.js')}}"></script>
  <script src="{{asset('assets/vendor/glightbox/js/glightbox.min.js')}}"></script>
  <script src="{{asset('assets/vendor/drift-zoom/Drift.min.js')}}"></script>
  <script src="{{asset('assets/vendor/purecounter/purecounter_vanilla.js')}}"></script>
  <script src="{{asset('assets/js/main.js')}}"></script>

  @if(Session::has('message') && Session::has('icon'))
    @php $status = Session::get('status'); @endphp
    <script>
      (function(){
        var s = {{ $status ?? 200 }};
        var showConfirm = (s === 500 || s === 400);
        Swal.fire({
          position: 'center',
          icon: '{{ Session::get("icon") }}',
          title: '{{ Session::get("message") }}',
          showConfirmButton: showConfirm,
          timer: showConfirm ? undefined : 2000
        });
      })();
    </script>
  @endif

  @stack('scripts')
</body>
</html>