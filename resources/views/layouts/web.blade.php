@php
  $settings = \App\Models\Ajuste::first();

  $menuCategorias = \App\Models\Category::whereNull('parent_id')->with('children')->where('status',true)->take(8)->get();
  $menuDestacados = \App\Models\Product::where('status',true)->where('featured',true)->take(4)->get();
  $menuNuevos = \App\Models\Product::where('status',true)->latest()->take(4)->get();
  $menuOfertas = \App\Models\Product::where('discount_percentage', '>', 0)
                      ->where('status', true)
                      ->whereNotNull('discount_end_date')
                      ->where('discount_end_date', '>', now())
                      ->with('images')
                      ->take(4)
                      ->get();
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

  <!-- Fonts: Inter + Sora -->
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
     DESIGN SYSTEM
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

  body { font-family: 'Inter', sans-serif; color: var(--ds-text); }

  /* ─── HEADER ─────────────────────────────────────────────── */
  #header {
    background: #fff;
    border-bottom: 1px solid var(--ds-border);
    box-shadow: 0 2px 20px rgba(0,0,0,.05);
  }

  /* Logo */
  .logo .sitename {
    font-family: 'Sora', sans-serif;
    font-weight: 800; font-size: 1.4rem; letter-spacing: -.025em;
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text; margin: 0; line-height: 1;
  }

  /* Search */
  .search-form .input-group {
    border-radius: 12px; overflow: hidden;
    border: 1.5px solid var(--ds-border);
    transition: border-color .2s, box-shadow .2s;
    background: var(--ds-bg);
  }
  .search-form .input-group:focus-within {
    border-color: var(--ds-indigo);
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
  }
  .search-form .form-control {
    border: none; background: var(--ds-bg); padding: 10px 16px;
    font-size: .9rem; color: var(--ds-text); box-shadow: none !important;
  }
  .search-form .form-control::placeholder { color: #9ca3af; }
  .search-form .btn {
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    color: #fff; border: none; padding: 10px 20px;
    font-size: 1rem; transition: opacity .2s; border-radius: 0;
  }
  .search-form .btn:hover { opacity: .88; color: #fff; }

  /* Header action buttons */
  .header-action-btn {
    position: relative; display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 12px; border-radius: 10px; border: none;
    background: transparent; color: var(--ds-text); font-size: .88rem;
    font-weight: 500; cursor: pointer; text-decoration: none;
    transition: background .2s, color .2s; white-space: nowrap;
  }
  .header-action-btn:hover { background: var(--ds-bg); color: var(--ds-indigo); text-decoration: none; }
  .header-action-btn i { font-size: 1.3rem; line-height: 1; }

  /* Badges */
  .header-action-btn .badge {
    position: absolute; top: 4px; right: 4px;
    min-width: 17px; height: 17px; padding: 0 4px;
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    color: #fff; border-radius: 10px; font-size: .6rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff; line-height: 1;
  }

  /* Account dropdown */
  .account-dropdown .dropdown-menu {
    border-radius: 18px; border: 1px solid var(--ds-border);
    box-shadow: 0 16px 48px rgba(0,0,0,.12);
    padding: 0; overflow: hidden; min-width: 250px; margin-top: 10px !important;
  }
  .account-dropdown .dropdown-header {
    padding: 18px 20px;
    background: linear-gradient(135deg, var(--ds-dark) 0%, var(--ds-dark-2) 100%);
  }
  .account-dropdown .dropdown-header h6 {
    font-family: 'Sora', sans-serif; font-size: .9rem;
    font-weight: 700; color: #fff; margin: 0 0 3px;
  }
  .account-dropdown .dropdown-header p { font-size: .75rem; color: rgba(255,255,255,.55); margin: 0; }
  .account-dropdown .dropdown-header .sitename { color: #a5b4fc; -webkit-text-fill-color: #a5b4fc; background: none; }
  .account-dropdown .dropdown-body { padding: 8px; background: #fff; }
  .account-dropdown .dropdown-body .dropdown-item {
    padding: 9px 12px; border-radius: 9px; font-size: .86rem;
    color: var(--ds-text); display: flex; align-items: center; gap: 9px;
    transition: background .18s, color .18s;
  }
  .account-dropdown .dropdown-body .dropdown-item i { color: var(--ds-indigo); font-size: 1rem; width: 16px; text-align: center; }
  .account-dropdown .dropdown-body .dropdown-item:hover { background: #eef2ff; color: var(--ds-indigo); }
  .account-dropdown .dropdown-footer { padding: 12px; background: var(--ds-bg); border-top: 1px solid var(--ds-border); }
  .account-dropdown .dropdown-footer .btn-primary {
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple));
    border: none; border-radius: 10px; font-weight: 600; font-size: .86rem;
    padding: 9px; box-shadow: 0 3px 10px rgba(99,102,241,.25); transition: all .25s;
  }
  .account-dropdown .dropdown-footer .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,.35); }
  .account-dropdown .dropdown-footer .btn-outline-primary {
    border: 1.5px solid var(--ds-indigo); color: var(--ds-indigo);
    border-radius: 10px; font-weight: 600; font-size: .86rem; padding: 8px; transition: all .25s;
  }
  .account-dropdown .dropdown-footer .btn-outline-primary:hover { background: var(--ds-indigo); color: #fff; }

  .hero-description {
    color: rgba(255,255,255,.75) !important;
    font-size: 1rem; line-height: 1.7; margin: 0 0 28px; max-width: 480px;
  }

  /* ─── NAVBAR ─────────────────────────────────────────────── */
  .header-nav {
    background: linear-gradient(135deg, var(--ds-dark) 0%, var(--ds-dark-2) 55%, #4338ca 100%);
  }
  .navmenu a, .navmenu a:focus {
    color: rgba(255,255,255,.72) !important; font-size: .87rem; font-weight: 500;
    padding: 13px 15px !important; border-radius: 8px; transition: color .2s, background .2s;
  }
  .navmenu a:hover, .navmenu .active, .navmenu .active:focus {
    color: #fff !important; background: rgba(255,255,255,.1);
  }
  .navmenu .toggle-dropdown { color: rgba(255,255,255,.4); font-size: .7rem; }

  /* Dropdown simple */
  .navmenu .dropdown ul {
    background: #fff; border-radius: 14px; border: 1px solid var(--ds-border);
    box-shadow: 0 12px 36px rgba(0,0,0,.1); padding: 8px; min-width: 190px; margin-top: 4px;
  }
  .navmenu .dropdown ul li a { color: var(--ds-text) !important; padding: 9px 14px !important; border-radius: 8px; font-size: .86rem; }
  .navmenu .dropdown ul li a:hover { background: #eef2ff; color: var(--ds-indigo) !important; }

  /* ═══════════════════════════════════════════════════════════
     MEGAMENÚ — tabs con el mismo look que los botones del navbar
  ═══════════════════════════════════════════════════════════ */

  /* Franja de tabs: mismo gradiente oscuro que .header-nav */
  .megamenu-tabs {
    background: linear-gradient(135deg, var(--ds-dark) 0%, var(--ds-dark-2) 55%, #4338ca 100%);
    padding: 0 6px;
    border-radius: 14px 14px 0 0;
  }
  .megamenu-tabs .nav-tabs {
    border-bottom: none;
    gap: 2px;
  }

  /* Botones de pestaña — mismo estilo que .navmenu a */
  .megamenu-tabs .nav-tabs .nav-link {
    color: rgba(255,255,255,.72) !important;
    font-size: .86rem;
    font-weight: 500;
    padding: 12px 16px !important;
    border-radius: 8px 8px 0 0;
    background: transparent;
    border: none;
    transition: color .2s, background .2s;
    white-space: nowrap;
  }
  .megamenu-tabs .nav-tabs .nav-link:hover {
    color: #fff !important;
    background: rgba(255,255,255,.1);
  }
  /* Tab activo: fondo semitransparente + línea inferior en acento */
  .megamenu-tabs .nav-tabs .nav-link.active {
    color: #fff !important;
    background: rgba(255,255,255,.14);
    box-shadow: inset 0 -3px 0 var(--ds-purple);
  }

  /* Panel de contenido */
  .megamenu-content {
    background: #fff;
    border: 1px solid var(--ds-border);
    border-top: none;
    border-radius: 0 0 14px 14px;
    padding: 18px;
  }

  /* Imagen categoría — tamaño fijo */
  .category-thumb {
    width: 100%; height: 120px; overflow: hidden;
    border-radius: 10px; margin-bottom: 8px;
  }
  .category-thumb img {
    width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s;
  }
  .category-thumb:hover img { transform: scale(1.05); }

  /* Imagen producto — tamaño fijo */
  .product-card .product-image {
    width: 100%; height: 130px; overflow: hidden; border-radius: 10px;
    margin-bottom: 8px; background: var(--ds-bg); position: relative;
  }
  .product-card .product-image img {
    width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s;
  }
  .product-card .product-image:hover img { transform: scale(1.05); }

  /* ─── TARJETAS DE MARCA ──────────────────────────────────── */
  .brand-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 12px;
  }

  .brand-card {
    display: flex; flex-direction: column; align-items: center; gap: 7px;
    padding: 14px 10px;
    border: 1.5px solid var(--ds-border);
    border-radius: 14px;
    background: #fff;
    text-decoration: none;
    transition: border-color .2s, box-shadow .2s, transform .2s;
    cursor: pointer;
  }
  .brand-card:hover {
    border-color: var(--ds-indigo);
    box-shadow: 0 4px 18px rgba(99,102,241,.14);
    transform: translateY(-3px);
    text-decoration: none;
  }

  /* Logo con tamaño fijo — object-contain para no recortar */
  .brand-logo {
    width: 70px; height: 42px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; border-radius: 8px;
  }
  .brand-logo img {
    width: 100%; height: 100%; object-fit: contain;
  }

  /* Placeholder cuando no hay logo */
  .brand-logo-placeholder {
    width: 70px; height: 42px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #eef2ff, #f3f0ff);
    border-radius: 8px;
    font-family: 'Sora', sans-serif;
    font-weight: 800; font-size: .75rem;
    color: var(--ds-indigo); text-align: center; line-height: 1.2;
    letter-spacing: -.01em;
  }

  .brand-name {
    font-size: .79rem; font-weight: 600;
    color: var(--ds-text); text-align: center; line-height: 1.2;
  }
  .brand-card:hover .brand-name { color: var(--ds-indigo); }

  .brand-count {
    font-size: .7rem; color: var(--ds-muted); font-weight: 400;
  }

  /* Footer del megamenú */
  .megamenu-footer {
    margin-top: 14px; padding-top: 12px;
    border-top: 1px solid var(--ds-border); text-align: right;
  }
  .ver-todos {
    font-size: .82rem; font-weight: 600; color: var(--ds-indigo);
    text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
    transition: gap .2s, opacity .2s;
  }
  .ver-todos:hover { opacity: .8; gap: 9px; }

  /* ─── PAGE TITLE ─────────────────────────────────────────── */
  .page-title {
    padding: 16px 0; background: var(--ds-bg) !important;
    border-bottom: 1px solid var(--ds-border);
  }
  .page-title h1 { font-family: 'Sora', sans-serif; font-size: 1.3rem; font-weight: 800; color: var(--ds-text); margin: 0; }
  .breadcrumbs ol { display: flex; align-items: center; gap: 6px; list-style: none; padding: 0; margin: 0; font-size: .79rem; }
  .breadcrumbs ol li a { color: var(--ds-indigo); text-decoration: none; transition: opacity .2s; }
  .breadcrumbs ol li a:hover { opacity: .75; }
  .breadcrumbs ol li.current { color: var(--ds-muted); }
  .breadcrumbs ol li + li::before { content: '/'; color: #d1d5db; margin-right: 6px; }

  /* ─── FOOTER ─────────────────────────────────────────────── */
  #footer {
    background: linear-gradient(160deg, var(--ds-dark) 0%, #1a1760 55%, #2d2780 100%);
    color: rgba(255,255,255,.65);
  }
  .footer-main { padding: 60px 0 44px; }
  .footer-about .sitename {
    font-family: 'Sora', sans-serif; font-weight: 800; font-size: 1.4rem;
    background: linear-gradient(135deg, #a5b4fc, #c4b5fd);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text; display: inline-block;
  }
  .footer-about p { font-size: .86rem; line-height: 1.75; color: rgba(255,255,255,.5); margin: 0; }
  .footer-widget h4 {
    font-family: 'Sora', sans-serif; font-size: .82rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: .1em; color: #fff;
    margin-bottom: 18px; padding-bottom: 10px; position: relative;
  }
  .footer-widget h4::after {
    content: ''; position: absolute; bottom: 0; left: 0; width: 24px; height: 2px;
    background: linear-gradient(90deg, var(--ds-indigo), var(--ds-purple)); border-radius: 2px;
  }
  .social-links h5 { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.38); margin: 0 0 10px; }
  .social-icons { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }
  .social-icons a {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
    color: rgba(255,255,255,.6); font-size: .9rem;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; transition: all .25s;
  }
  .social-icons a:hover { background: var(--ds-indigo); border-color: var(--ds-indigo); color: #fff; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(99,102,241,.4); }
  .footer-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 9px; }
  .footer-links a { color: rgba(255,255,255,.5); text-decoration: none; font-size: .86rem; display: inline-flex; align-items: center; gap: 7px; transition: color .2s, padding-left .2s; }
  .footer-links a::before { content: ''; display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: var(--ds-indigo); opacity: .55; flex-shrink: 0; transition: opacity .2s; }
  .footer-links a:hover { color: #fff; padding-left: 5px; }
  .footer-links a:hover::before { opacity: 1; }
  .footer-contact { display: flex; flex-direction: column; gap: 11px; }
  .contact-item { display: flex; align-items: flex-start; gap: 10px; font-size: .86rem; color: rgba(255,255,255,.55); }
  .contact-item i { color: #a5b4fc; font-size: .95rem; flex-shrink: 0; margin-top: 1px; }
  .footer-bottom { border-top: 1px solid rgba(255,255,255,.07); padding: 22px 0; }
  .footer-bottom .copyright p { font-size: .82rem; color: rgba(255,255,255,.38); margin: 0; }
  .footer-bottom .copyright .sitename,
  .footer-bottom .copyright strong.sitename { color: rgba(255,255,255,.65); font-weight: 700; -webkit-text-fill-color: rgba(255,255,255,.65); background: none; }
  .payment-methods .payment-icons { display: flex; gap: 10px; align-items: center; }
  .payment-methods .payment-icons i { font-size: 1.4rem; color: rgba(255,255,255,.3); transition: color .2s, transform .2s; }
  .payment-methods .payment-icons i:hover { color: rgba(255,255,255,.75); transform: scale(1.1); }
  .legal-links { display: flex; gap: 16px; }
  .legal-links a { font-size: .78rem; color: rgba(255,255,255,.35); text-decoration: none; transition: color .2s; }
  .legal-links a:hover { color: rgba(255,255,255,.8); }

  /* Scroll top */
  .scroll-top { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple)); color: #fff; box-shadow: 0 4px 14px rgba(99,102,241,.4); transition: transform .25s, box-shadow .25s; }
  .scroll-top:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(99,102,241,.5); color: #fff; }

  /* SweetAlert */
  .swal2-popup { border-radius: 18px !important; font-family: 'Inter', sans-serif !important; border: 1px solid var(--ds-border) !important; }
  .swal2-confirm { background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple)) !important; border-radius: 10px !important; font-weight: 700 !important; box-shadow: 0 3px 12px rgba(99,102,241,.3) !important; }
  .swal2-cancel { border-radius: 10px !important; font-weight: 600 !important; }

  /* Mobile nav toggle */
  .mobile-nav-toggle { font-size: 1.5rem; cursor: pointer; color: var(--ds-text); transition: color .2s; }
  .mobile-nav-toggle:hover { color: var(--ds-indigo); }

  /* ─── MEGAMENÚ — componentes de producto ───────────────────── */

  /* Precio: igual que .sr-p-old / .sr-p-cur del index */
  .mm-price {
    display: flex; align-items: center; gap: 6px;
    margin: 4px 0 10px;
  }
  .mm-p-old {
    font-size: .76rem; color: #9ca3af;
    text-decoration: line-through; font-weight: 500;
  }
  .mm-p-cur {
    font-size: 1rem; font-weight: 800; color: var(--ds-indigo);
  }

  /* Botón "Ver producto" / "Comprar ahora" — igual que .btn-deal del index */
  .mm-btn-deal {
    display: flex; align-items: center; justify-content: center;
    width: 100%; padding: 9px 12px;
    background: linear-gradient(135deg, var(--ds-indigo), var(--ds-purple)) !important;
    color: #fff !important; border-radius: 10px;
    text-decoration: none; font-weight: 700; font-size: .82rem;
    transition: all .25s;
    box-shadow: 0 3px 10px rgba(99,102,241,.22);
    margin-top: auto;
  }
  .mm-btn-deal:link,
  .mm-btn-deal:visited,
  .mm-btn-deal:hover,
  .mm-btn-deal:focus,
  .mm-btn-deal:active,
  a.mm-btn-deal,
  a.mm-btn-deal:hover {
    color: #fff !important;
    -webkit-text-fill-color: #fff !important;
    text-decoration: none !important;
    transition: all .25s;
  }
  .mm-btn-deal:hover {
    /* background: linear-gradient(135deg, var(--ds-indigo-d), var(--ds-indigo)) !important; */
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(99,102,241,.45);
  }

  /* "Ver todos…" — igual que .ix-see-all del index (visible, con color indigo) */
  .ix-see-all,
  .megamenu-content .ix-see-all,
  .megamenu-footer .ix-see-all {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .82rem; font-weight: 700;
    color: var(--ds-indigo) !important;
    text-decoration: none;
    transition: gap .2s, opacity .2s;
    white-space: nowrap;
  }
  .ix-see-all:hover,
  .megamenu-footer .ix-see-all:hover {
    color: var(--ds-indigo-d) !important;
    gap: 8px;
  }

  /* Categorías dentro del megamenú */
  .category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 18px;
  }
  .category-column h4 {
    font-family: 'Sora', sans-serif;
    font-size: .82rem; font-weight: 800;
    color: var(--ds-text); margin: 0 0 8px;
  }
  .category-column h4 a {
    color: inherit; text-decoration: none; transition: color .2s;
  }
  .category-column h4 a:hover { color: var(--ds-indigo); }
  .mm-cat-count { color: var(--ds-muted); font-weight: 400; font-size: .75rem; }
  .category-column ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 5px; }
  .category-column ul li a {
    font-size: .81rem; color: var(--ds-muted); text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
    transition: color .18s, padding-left .18s;
  }
  .category-column ul li a::before {
    content: ''; width: 4px; height: 4px; border-radius: 50%;
    background: var(--ds-indigo); opacity: .45; flex-shrink: 0;
    transition: opacity .18s;
  }
  .category-column ul li a:hover { color: var(--ds-indigo); padding-left: 4px; }
  .category-column ul li a:hover::before { opacity: 1; }
  .mm-ver-mas {
    font-size: .78rem; font-weight: 700; color: var(--ds-indigo);
    text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
    transition: gap .2s;
  }
  .mm-ver-mas:hover { gap: 7px; }

  /* product-info dentro del megamenú necesita flex para empujar el botón abajo */
  .megamenu-content .product-card .product-info {
    display: flex; flex-direction: column;
    padding: 10px 6px 6px;
  }
  .megamenu-content .product-card .product-info h5 {
    font-size: .83rem; font-weight: 700; color: var(--ds-text);
    margin: 0 0 2px; flex: 1;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  }

  </style>

  @stack('styles')
</head>

{{--
  VARIABLES REQUERIDAS (View Composer):
  ──────────────────────────────────────────────────────────────────
  $menuDestacados  → Product::where('featured',true)->with('images')->take(4)->get()
  $menuNuevos      → Product::latest()->with('images')->take(4)->get()
  $menuOfertas     → Product::whereNotNull('sale_price')
                          ->whereColumn('sale_price','<','price')
                          ->with('images')->take(4)->get()
  $menuMarcas      → Brand::where('active',true)
                          ->withCount('products')
                          ->orderByDesc('products_count')
                          ->take(8)->get()
  ──────────────────────────────────────────────────────────────────
--}}

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
              <button class="btn" type="submit"><i class="bi bi-search"></i></button>
            </div>
          </form>

          {{-- Acciones --}}
          <div class="header-actions d-flex align-items-center gap-1 flex-shrink-0">

            <button class="header-action-btn d-xl-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mobileSearch">
              <i class="bi bi-search"></i>
            </button>

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
                  <a class="dropdown-item" href="{{ route('web.dashboard.settings') }}"><i class="bi bi-person-circle"></i> Mi perfil</a>
                  <a class="dropdown-item" href="{{ route('web.dashboard.orders') }}"><i class="bi bi-box-seam"></i> Mis pedidos</a>
                  <a class="dropdown-item" href="{{ route('web.dashboard.wishlist') }}"><i class="bi bi-heart"></i> Favoritos</a>
                  <a class="dropdown-item" href="{{ route('web.dashboard.settings') }}"><i class="bi bi-gear"></i> Configuración</a>
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
                    <a href="{{ route('web.register') }}" class="btn btn-outline-primary w-100">Registrarse</a>
                  @endif
                </div>
              </div>
            </div>

            <a href="{{ route('web.dashboard.wishlist') }}" class="header-action-btn d-none d-md-flex">
              <i class="bi bi-heart"></i>
              @php $favoriteProducts = Auth::check() ? count(auth()->user()->favoriteProducts) : 0; @endphp
              <span class="badge" id="fav-badge">{{ $favoriteProducts }}</span>
            </a>

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
            <li><a href="{{ route('web.about') }}">Nosotros</a></li>
            <li><a href="{{ route('web.categories') }}">Categorías</a></li>

            {{-- ══ CATÁLOGO — Mega Menú ══ --}}
            <li class="products-megamenu-1">
              <a href="#">
                <span>Catálogo</span>
                <i class="bi bi-chevron-down toggle-dropdown"></i>
              </a>

              {{-- ── Versión móvil ── --}}
              <ul class="mobile-megamenu">
                <li><a href="{{ route('web.categories') }}?sort=featured">⭐ Destacados</a></li>
                <li><a href="{{ route('web.categories') }}?sort=latest">🆕 Nuevos</a></li>
                <li><a href="{{ route('web.categories') }}?sale=1">🏷️ Ofertas</a></li>
                @foreach($menuCategorias ?? [] as $cat)
                  @if($cat->children->count())
                    <li class="dropdown">
                      <a href="{{ route('web.category', $cat->id) }}">
                        <span>{{ $cat->name }}</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                      </a>
                      <ul>
                        @foreach($cat->children as $child)
                          <li><a href="{{ route('web.category', $child->id) }}">{{ $child->name }}</a></li>
                        @endforeach
                      </ul>
                    </li>
                  @else
                    <li><a href="{{ route('web.category', $cat->id) }}">{{ $cat->name }}</a></li>
                  @endif
                @endforeach
              </ul>

              {{-- ── Versión desktop ── --}}
              <div class="desktop-megamenu">

                {{-- Tabs con estilo igual al navbar --}}
                <div class="megamenu-tabs">
                  <ul class="nav nav-tabs" id="productMegaMenuTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" data-bs-toggle="tab"
                              data-bs-target="#tab-destacados" type="button" role="tab">
                        ⭐ Destacados
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" data-bs-toggle="tab"
                              data-bs-target="#tab-nuevos" type="button" role="tab">
                        🆕 Nuevos
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" data-bs-toggle="tab"
                              data-bs-target="#tab-ofertas" type="button" role="tab">
                        🏷️ Ofertas
                      </button>
                    </li>
                    {{-- <li class="nav-item" role="presentation">
                      <button class="nav-link" data-bs-toggle="tab"
                              data-bs-target="#tab-categorias" type="button" role="tab">
                        📂 Categorías
                      </button>
                    </li> --}}
                  </ul>
                </div>

                {{-- Paneles --}}
                <div class="megamenu-content tab-content">

                  {{-- ── TAB: DESTACADOS ── --}}
                  <div class="tab-pane fade show active" id="tab-destacados" role="tabpanel">
                    <div class="product-grid">
                      @forelse($menuDestacados ?? [] as $product)
                        <div class="product-card">
                          <div class="product-image">
                            <a href="{{ route('web.product.show', $product->id) }}">
                              <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                                   alt="{{ $product->name }}" loading="lazy">
                            </a>
                            @if($product->is_on_sale)
                              <span class="badge-sale">
                                -{{ round($product->discount_percentage) }}%
                              </span>
                            @endif
                          </div>
                          <div class="product-info">
                            <h5>{{ Str::limit($product->name, 30) }}</h5>
                            <div class="mm-price">
                              @if($product->is_on_sale)
                                <span class="mm-p-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                              @endif
                              <span class="mm-p-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                            </div>
                            <a href="{{ route('web.product.show', $product->id) }}" class="mm-btn-deal">
                              <i class="bi bi-eye me-1"></i> Ver producto
                            </a>
                          </div>
                        </div>
                      @empty
                        <p class="text-muted px-3 py-2">No hay productos destacados.</p>
                      @endforelse
                    </div>
                    <div class="megamenu-footer">
                      <a href="{{ route('web.search', ['bestSellers' => 1]) }}?sort=featured" class="ix-see-all">
                        Ver todos los destacados <i class="bi bi-arrow-right ms-1"></i>
                      </a>
                    </div>
                  </div>

                  {{-- ── TAB: NUEVOS ── --}}
                  <div class="tab-pane fade" id="tab-nuevos" role="tabpanel">
                    <div class="product-grid">
                      @forelse($menuNuevos ?? [] as $product)
                        <div class="product-card">
                          <div class="product-image">
                            <a href="{{ route('web.product.show', $product->id) }}">
                              <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                                   alt="{{ $product->name }}" loading="lazy">
                            </a>
                            <span class="badge-new">Nuevo</span>
                          </div>
                          <div class="product-info">
                            <h5>{{ Str::limit($product->name, 30) }}</h5>
                            <div class="mm-price">
                              @if($product->is_on_sale)
                                <span class="mm-p-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                              @endif
                              <span class="mm-p-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                            </div>
                            <a href="{{ route('web.product.show', $product->id) }}" class="mm-btn-deal">
                              <i class="bi bi-eye me-1"></i> Ver producto
                            </a>
                          </div>
                        </div>
                      @empty
                        <p class="text-muted px-3 py-2">No hay productos nuevos.</p>
                      @endforelse
                    </div>
                    <div class="megamenu-footer">
                      <a href="{{ route('web.search', ['newProducts' => 1]) }}?sort=latest" class="ix-see-all">
                        Ver todos los nuevos <i class="bi bi-arrow-right ms-1"></i>
                      </a>
                    </div>
                  </div>

                  {{-- ── TAB: OFERTAS ── --}}
                  <div class="tab-pane fade" id="tab-ofertas" role="tabpanel">
                    <div class="product-grid">
                      @forelse($menuOfertas ?? [] as $product)
                        <div class="product-card">
                          <div class="product-image">
                            <a href="{{ route('web.product.show', $product->id) }}">
                              <img src="{{ asset('storage/' . ($product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                                   alt="{{ $product->name }}" loading="lazy">
                            </a>
                            @if($product->is_on_sale)
                              <span class="badge-sale">
                                -{{ round($product->discount_percentage) }}%
                              </span>
                            @endif
                          </div>
                          <div class="product-info">
                            <h5>{{ Str::limit($product->name, 30) }}</h5>
                            <div class="mm-price">
                              @if($product->is_on_sale)
                                <span class="mm-p-old">{{ $settings->badge }}{{ number_format($product->selling_price, 2) }}</span>
                              @endif
                              <span class="mm-p-cur">{{ $settings->badge }}{{ number_format($product->final_price, 2) }}</span>
                            </div>
                            <a href="{{ route('web.product.show', $product->id) }}" class="mm-btn-deal">
                              <i class="bi bi-lightning-fill me-1"></i> Comprar ahora
                            </a>
                          </div>
                        </div>
                      @empty
                        <p class="text-muted px-3 py-2">No hay productos en oferta.</p>
                      @endforelse
                    </div>
                    <div class="megamenu-footer">
                      <a href="{{ route('web.search', ['flashDeals' => 1]) }}" class="ix-see-all">
                        Ver todas las ofertas <i class="bi bi-arrow-right ms-1"></i>
                      </a>
                    </div>
                  </div>

                </div>{{-- /megamenu-content --}}
              </div>{{-- /desktop-megamenu --}}
            </li>
            {{-- ══ FIN CATÁLOGO ══ --}}

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