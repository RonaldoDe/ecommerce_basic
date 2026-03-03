@php
    $user = App\Models\User::find(Auth::id());
    if (!$user) abort(404);

    $ordersCount   = $user->orders()->count();
    $wishlistCount = $user->favoriteProducts()->count();
@endphp

@extends('layouts.web')

@section('content')

<!-- Page Title -->
<div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Mi Cuenta</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('web.index') }}">Inicio</a></li>
                <li class="current">Mi Cuenta</li>
            </ol>
        </nav>
    </div>
</div>

<section id="account" class="account section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">

        {{-- Mobile toggle --}}
        <div class="d-lg-none mb-3">
            <button class="dash-mobile-toggle" type="button"
                    data-bs-toggle="collapse" data-bs-target="#profileMenu">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>Menú de cuenta</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </button>
        </div>

        <div class="row g-4 align-items-start">

            {{-- ══ SIDEBAR ══════════════════════════════════════ --}}
            <div class="col-lg-3">
                <div class="dash-sidebar collapse d-lg-block" id="profileMenu" data-aos="fade-right">

                    {{-- Avatar + nombre --}}
                    <div class="dash-profile">
                        <div class="dash-avatar-wrap">
                            <img src="{{ asset('assets/img/person/person-f-1.webp') }}"
                                 alt="{{ Auth::user()->name }}" loading="lazy">
                            <span class="dash-online" title="Activo"></span>
                        </div>
                        <h4>{{ Auth::user()->name }}</h4>
                        <span class="dash-member-badge">
                            <i class="bi bi-award-fill"></i> Miembro Premium
                        </span>
                    </div>

                    {{-- Estadísticas rápidas --}}
                    <div class="dash-stats">
                        <div class="dash-stat">
                            <span class="ds-num">{{ $ordersCount }}</span>
                            <span class="ds-label">Órdenes</span>
                        </div>
                        <div class="dash-stat-divider"></div>
                        <div class="dash-stat">
                            <span class="ds-num">{{ $wishlistCount }}</span>
                            <span class="ds-label">Favoritos</span>
                        </div>
                    </div>

                    {{-- Navegación --}}
                    <nav class="dash-nav">
                        @php
                            $navItems = [
                                ['route' => 'web.dashboard.orders',    'icon' => 'bi-box-seam',    'label' => 'Mis Órdenes',       'badge' => $ordersCount],
                                ['route' => 'web.dashboard.wishlist',  'icon' => 'bi-heart',       'label' => 'Lista de Deseos',   'badge' => $wishlistCount],
                                ['route' => 'web.dashboard.payment',   'icon' => 'bi-wallet2',     'label' => 'Métodos de Pago',   'badge' => null],
                                ['route' => 'web.dashboard.reviews',   'icon' => 'bi-star',        'label' => 'Mis Reseñas',       'badge' => null],
                                ['route' => 'web.dashboard.addresses', 'icon' => 'bi-geo-alt',     'label' => 'Direcciones',       'badge' => null],
                                ['route' => 'web.dashboard.settings',  'icon' => 'bi-gear',        'label' => 'Configuración',     'badge' => null],
                            ];
                        @endphp

                        <ul>
                            @foreach($navItems as $item)
                                <li>
                                    <a href="{{ route($item['route']) }}"
                                       class="dash-nav-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                                        <span class="dnl-icon">
                                            <i class="bi {{ $item['icon'] }}"></i>
                                        </span>
                                        <span class="dnl-label">{{ $item['label'] }}</span>
                                        @if($item['badge'])
                                            <span class="dnl-badge">{{ $item['badge'] }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="dash-nav-footer">
                            <a href="#" class="dash-nav-link">
                                <span class="dnl-icon"><i class="bi bi-question-circle"></i></span>
                                <span class="dnl-label">Centro de Ayuda</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dash-nav-link dash-logout w-100">
                                    <span class="dnl-icon"><i class="bi bi-box-arrow-right"></i></span>
                                    <span class="dnl-label">Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </div>

            {{-- ══ CONTENIDO ═════════════════════════════════════ --}}
            <div class="col-lg-9">
                <div class="dash-content" data-aos="fade-up" data-aos-delay="150">
                    @yield('dashboard-content')
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* ══════════════════════════════════════════════════════════
   DASHBOARD — mismo sistema de diseño que carrito / órdenes
══════════════════════════════════════════════════════════ */

.account { padding: 56px 0; }

/* ── Mobile toggle ── */
.dash-mobile-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px 18px;
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    font-weight: 600;
    font-size: .92rem;
    color: #374151;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
    transition: background .2s;
}
.dash-mobile-toggle:hover { background: #f8f9fb; }
.dash-mobile-toggle i:first-child { color: #6366f1; font-size: 1.1rem; }

/* ── SIDEBAR ── */
.dash-sidebar {
    background: linear-gradient(160deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(99,102,241,.25);
    color: #fff;
    position: sticky;
    top: 88px;
}

/* Perfil */
.dash-profile {
    text-align: center;
    padding: 32px 24px 20px;
    border-bottom: 1px solid rgba(255,255,255,.1);
}

.dash-avatar-wrap {
    position: relative;
    width: 88px; height: 88px;
    margin: 0 auto 14px;
}
.dash-avatar-wrap img {
    width: 100%; height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,.3);
}
.dash-online {
    position: absolute;
    bottom: 3px; right: 3px;
    width: 14px; height: 14px;
    background: #4ade80;
    border-radius: 50%;
    border: 2px solid #312e81;
    box-shadow: 0 0 0 3px rgba(74,222,128,.3);
}

.dash-profile h4 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 8px;
}

.dash-member-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(251,191,36,.15);
    border: 1px solid rgba(251,191,36,.3);
    color: #fbbf24;
    font-size: .75rem;
    font-weight: 700;
    padding: 3px 12px;
    border-radius: 20px;
    letter-spacing: .04em;
}

/* Stats */
.dash-stats {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    padding: 18px 24px;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
.dash-stat {
    flex: 1;
    text-align: center;
}
.dash-stat-divider {
    width: 1px;
    height: 32px;
    background: rgba(255,255,255,.15);
}
.ds-num {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    margin-bottom: 3px;
}
.ds-label {
    font-size: .72rem;
    font-weight: 600;
    color: rgba(255,255,255,.5);
    text-transform: uppercase;
    letter-spacing: .07em;
}

/* Nav */
.dash-nav { padding: 14px 14px 18px; }
.dash-nav ul { list-style: none; padding: 0; margin: 0 0 8px; }
.dash-nav ul li { margin-bottom: 2px; }

.dash-nav-link {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 14px;
    border-radius: 10px;
    color: rgba(255,255,255,.65);
    text-decoration: none;
    font-size: .88rem;
    font-weight: 500;
    transition: all .2s;
    width: 100%;
    background: none;
    border: none;
    cursor: pointer;
    text-align: left;
}
.dash-nav-link:hover {
    background: rgba(255,255,255,.1);
    color: #fff;
}
.dash-nav-link.active {
    background: rgba(255,255,255,.15);
    color: #fff;
    font-weight: 700;
}
.dash-nav-link.active .dnl-icon {
    background: rgba(255,255,255,.2);
    color: #fff;
}

.dnl-icon {
    flex-shrink: 0;
    width: 32px; height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,.08);
    display: flex; align-items: center; justify-content: center;
    font-size: .92rem;
    transition: background .2s;
}
.dash-nav-link:hover .dnl-icon {
    background: rgba(255,255,255,.15);
}

.dnl-label { flex: 1; }

.dnl-badge {
    background: rgba(255,255,255,.2);
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
    min-width: 22px;
    text-align: center;
}
.dash-nav-link.active .dnl-badge {
    background: #6366f1;
}

.dash-nav-footer {
    border-top: 1px solid rgba(255,255,255,.1);
    padding-top: 10px;
    margin-top: 4px;
}

.dash-logout:hover {
    background: rgba(239,68,68,.2) !important;
    color: #fca5a5 !important;
}
.dash-logout:hover .dnl-icon {
    background: rgba(239,68,68,.2) !important;
    color: #fca5a5 !important;
}

/* ── CONTENIDO ── */
.dash-content {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #eef0f3;
    box-shadow: 0 2px 16px rgba(0,0,0,.05);
    padding: 32px;
    min-height: 500px;
}

/* Section header reutilizable por las sub-vistas */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f2;
    flex-wrap: wrap;
    gap: 14px;
}
.section-header h2 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 800;
    color: #111827;
}
.header-actions { display: flex; gap: 10px; align-items: center; }

/* Responsive */
@media (max-width: 991.98px) {
    .dash-sidebar { position: static; border-radius: 14px; }
    .dash-content { padding: 22px 18px; }
}

/* ══ SHARED DESIGN SYSTEM — agregar al <style> del dashboard layout ══

/* Buttons */
.ds-btn-primary {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff; border: none; border-radius: 10px;
    font-size: .88rem; font-weight: 700; cursor: pointer;
    text-decoration: none;
    box-shadow: 0 3px 12px rgba(99,102,241,.25);
    transition: all .25s;
}
.ds-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,.35); color: #fff; }

.ds-btn-ghost {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 8px 16px;
    background: #fff; border: 1.5px solid #e5e7eb;
    border-radius: 9px; color: #374151;
    font-size: .83rem; font-weight: 600; cursor: pointer;
    transition: all .2s; text-decoration: none;
}
.ds-btn-ghost:hover { background: #f3f4f6; color: #111827; }

/* Inputs */
.ds-label { display: block; font-size: .79rem; font-weight: 700; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
.ds-input {
    display: block; width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e5e7eb; border-radius: 10px;
    font-size: .9rem; color: #111827; background: #fff;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}
.ds-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

.ds-input-icon-wrap { position: relative; }
.ds-input-icon-wrap i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: .9rem; pointer-events: none; }
.ds-input-icon { padding-left: 36px; }

.ds-check { display: flex; align-items: center; gap: 10px; cursor: pointer; }
.ds-check-input { width: 1.1rem; height: 1.1rem; border: 1.5px solid #c7d2fe; border-radius: 4px; cursor: pointer; accent-color: #6366f1; }
.ds-check label { font-size: .86rem; color: #374151; cursor: pointer; margin: 0; }

/* Modal */
.ds-modal { border-radius: 18px; overflow: hidden; border: 1px solid #eef0f3; }
.ds-modal .modal-header { background: #f8f9fb; border-bottom: 1px solid #eef0f3; padding: 16px 22px; }
.ds-modal .modal-title { font-size: .97rem; font-weight: 700; color: #111827; }
.ds-modal .modal-footer { background: #f8f9fb; border-top: 1px solid #eef0f3; padding: 14px 22px; }
.ds-modal .modal-body { padding: 22px; }

/* Empty state */
.ds-empty-state { grid-column: 1/-1; text-align: center; padding: 60px 20px; }
.ds-empty-state i { font-size: 3.5rem; color: #d1d5db; display: block; margin-bottom: 14px; }
.ds-empty-state h3 { font-size: 1.05rem; font-weight: 700; color: #374151; margin-bottom: 7px; }
.ds-empty-state p { font-size: .86rem; color: #9ca3af; margin-bottom: 18px; }

.text-indigo { color: #6366f1; }

.ds-btn-danger-ghost {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 14px;
    background: #fff;
    border: 1.5px solid #fecaca;
    border-radius: 8px;
    color: #b91c1c;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
}
.ds-btn-danger-ghost:hover {
    background: #fef2f2;
    border-color: #f87171;
}
</style>

@endsection