@extends('layouts.web')

@section('content')

<!-- Page Title -->
<div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Confirmación de orden</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('web.index') }}">Inicio</a></li>
                <li class="current">Confirmación de orden</li>
            </ol>
        </nav>
    </div>
</div>

<section id="order-confirmation" class="order-confirmation section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="oc-wrapper">
            <div class="row g-0">

                {{-- ══════════════════════════════════════
                     SIDEBAR IZQUIERDO
                ══════════════════════════════════════ --}}
                <div class="col-lg-4 oc-sidebar" data-aos="fade-right">
                    <div class="sidebar-inner">

                        {{-- Animación éxito --}}
                        <div class="success-ring">
                            <div class="success-ring-outer"></div>
                            <div class="success-ring-inner">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        </div>

                        {{-- Número de orden --}}
                        <div class="order-id-block">
                            <span class="order-label">Número de orden</span>
                            <h4 class="order-number">#{{ session('order_id', 'N/A') }}</h4>
                            <span class="order-date">
                                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d/m/Y — H:i') }}
                            </span>
                        </div>

                        {{-- Stepper --}}
                        <div class="stepper">
                            @php
                                $steps = [
                                    ['label' => 'Confirmado', 'icon' => 'bi-check-circle-fill',   'state' => 'done'],
                                    ['label' => 'Procesando', 'icon' => 'bi-gear-fill',            'state' => 'active'],
                                    ['label' => 'Enviado',    'icon' => 'bi-truck',               'state' => ''],
                                    ['label' => 'Entregado',  'icon' => 'bi-house-check-fill',    'state' => ''],
                                ];
                            @endphp
                            @foreach($steps as $i => $step)
                                <div class="stepper-step {{ $step['state'] }}">
                                    <div class="stepper-dot">
                                        <i class="bi {{ $step['icon'] }}"></i>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="stepper-line {{ $step['state'] === 'done' ? 'done' : '' }}"></div>
                                    @endif
                                    <span class="stepper-label">{{ $step['label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Resumen de precios --}}
                        <div class="price-box">
                            <h6 class="price-box-title">Resumen de orden</h6>
                            <ul class="price-list">
                                @if(session('cart_subtotal'))
                                    <li>
                                        <span>Subtotal</span>
                                        <span>{{ $settings->badge ?? '$' }}{{ number_format(session('cart_subtotal'), 2) }}</span>
                                    </li>
                                @endif
                                @if(session('discount_amount') && session('discount_amount') > 0)
                                    <li class="pl-discount">
                                        <span>
                                            Descuento
                                            @if(session('coupon_code'))
                                                <small class="ms-1 opacity-75">({{ session('coupon_code') }})</small>
                                            @endif
                                        </span>
                                        <span>−{{ $settings->badge ?? '$' }}{{ number_format(session('discount_amount'), 2) }}</span>
                                    </li>
                                @endif
                                <li>
                                    <span><i class="bi bi-truck me-1"></i> Envío</span>
                                    <span class="text-success fw-semibold">Gratis</span>
                                </li>
                                <li class="pl-total">
                                    <span>Total</span>
                                    <span>{{ $settings->badge ?? '$' }}{{ number_format(session('order_total', 0), 2) }}</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Entrega estimada --}}
                        <div class="delivery-box">
                            <h6><i class="bi bi-calendar-check me-1"></i> Entrega estimada</h6>
                            <p class="delivery-dates">
                                {{ now()->addDays(5)->format('d/m') }} – {{ now()->addDays(7)->format('d/m/Y') }}
                            </p>
                            <p class="delivery-method">
                                <i class="bi bi-truck me-1"></i> Envío estándar gratuito
                            </p>
                        </div>

                        {{-- Soporte --}}
                        <div class="support-box">
                            <h6>¿Necesitas ayuda?</h6>
                            <a href="#" class="support-link">
                                <i class="bi bi-chat-dots-fill"></i> Contactar soporte
                            </a>
                            <a href="#" class="support-link">
                                <i class="bi bi-question-circle-fill"></i> Preguntas frecuentes
                            </a>
                        </div>

                    </div>
                </div>

                {{-- ══════════════════════════════════════
                     CONTENIDO PRINCIPAL
                ══════════════════════════════════════ --}}
                <div class="col-lg-8 oc-main" data-aos="fade-in">

                    {{-- Mensaje de gracias --}}
                    <div class="thankyou-header">
                        <div class="confetti-line">🎉</div>
                        <h1>¡Gracias por tu orden!</h1>
                        <p>Hemos recibido tu pedido y comenzaremos a procesarlo de inmediato.
                           Recibirás actualizaciones por correo electrónico.</p>
                    </div>

                    {{-- Detalles de envío --}}
                    <div class="oc-card" data-aos="fade-up">
                        <div class="oc-card-head collapsed-toggle" data-target="shipping-body">
                            <h3><i class="bi bi-geo-alt-fill"></i> Detalles de envío</h3>
                            <i class="bi bi-chevron-down toggle-icon"></i>
                        </div>
                        <div class="oc-card-body" id="shipping-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Enviar a</label>
                                        <address>
                                            {{ Auth::user()->name }}<br>
                                            {{ session('address', 'Dirección no proporcionada') }}
                                        </address>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-group">
                                        <label>Contacto</label>
                                        <p class="mb-1"><i class="bi bi-envelope me-1 text-muted"></i> {{ Auth::user()->email }}</p>
                                        @if(Auth::user()->phone)
                                            <p class="mb-0"><i class="bi bi-telephone me-1 text-muted"></i> {{ Auth::user()->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(session('customer_notes'))
                                <div class="notes-block mt-3">
                                    <i class="bi bi-chat-left-quote-fill"></i>
                                    <p>{{ session('customer_notes') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Detalles de pago --}}
                    <div class="oc-card" data-aos="fade-up">
                        <div class="oc-card-head collapsed-toggle" data-target="payment-body">
                            <h3><i class="bi bi-credit-card-fill"></i> Detalles de pago</h3>
                            <i class="bi bi-chevron-down toggle-icon"></i>
                        </div>
                        <div class="oc-card-body" id="payment-body">
                            <div class="payment-row">
                                <div class="payment-logo">
                                    <i class="bi bi-paypal"></i>
                                </div>
                                <div>
                                    <div class="payment-title">PayPal</div>
                                    <div class="payment-subtitle">
                                        <i class="bi bi-shield-check-fill text-success me-1"></i>
                                        Pago procesado exitosamente
                                    </div>
                                </div>
                                <span class="payment-ok ms-auto">
                                    <i class="bi bi-check-circle-fill"></i> Aprobado
                                </span>
                            </div>

                            @if(session('coupon_code'))
                                <div class="coupon-confirm mt-4">
                                    <i class="bi bi-scissors me-2"></i>
                                    Cupón <strong>{{ session('coupon_code') }}</strong> aplicado —
                                    <span class="text-success fw-bold ms-1">
                                        −{{ $settings->badge ?? '$' }}{{ number_format(session('discount_amount', 0), 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Artículos de la orden --}}
                    @if(session('order_items'))
                        <div class="oc-card" data-aos="fade-up">
                            <div class="oc-card-head collapsed-toggle" data-target="items-body">
                                <h3><i class="bi bi-bag-check-fill"></i> Artículos de la orden</h3>
                                <i class="bi bi-chevron-down toggle-icon"></i>
                            </div>
                            <div class="oc-card-body" id="items-body">
                                @foreach(session('order_items', []) as $item)
                                    <div class="oc-item {{ !$loop->last ? 'oc-item-border' : '' }}">

                                        {{-- Imagen: variante primero, luego producto --}}
                                        <div class="oc-item-img-wrap">
                                            @php
                                                $imgSrc = !empty($item['variant_image'])
                                                    ? asset('storage/' . $item['variant_image'])
                                                    : (!empty($item['image'])
                                                        ? asset('storage/' . $item['image'])
                                                        : asset('assets/img/no-image.png'));
                                                $isVariantImg = !empty($item['variant_image']);
                                            @endphp
                                            <img src="{{ $imgSrc }}"
                                                 alt="{{ $item['product_name'] ?? 'Producto' }}"
                                                 loading="lazy">
                                            @if($isVariantImg)
                                                <span class="variant-img-badge" title="Imagen de variante">
                                                    <i class="bi bi-layers-fill"></i>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="oc-item-info">
                                            <h5 class="oc-item-name">{{ $item['product_name'] ?? 'Producto' }}</h5>

                                            {{-- Chips de atributos de variante --}}
                                            @if(!empty($item['variant_label']))
                                                <div class="oc-variant-chips">
                                                    @foreach(explode(' / ', $item['variant_label']) as $chip)
                                                        @php
                                                            $parts = explode(':', $chip, 2);
                                                        @endphp
                                                        <span class="oc-chip">
                                                            @if(count($parts) === 2)
                                                                <span class="chip-k">{{ trim($parts[0]) }}</span>
                                                                <span class="chip-v">{{ trim($parts[1]) }}</span>
                                                            @else
                                                                <span class="chip-v">{{ trim($chip) }}</span>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="oc-item-pricing">
                                                <span class="oc-qty">{{ $item['quantity'] }} ×</span>
                                                <span class="oc-unit">{{ $settings->badge ?? '$' }}{{ number_format($item['price'], 2) }}</span>
                                                <span class="oc-subtotal">= {{ $settings->badge ?? '$' }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Botones de acción --}}
                    <div class="oc-actions" data-aos="fade-up">
                        <a href="{{ route('web.index') }}" class="oc-btn-back">
                            <i class="bi bi-arrow-left me-1"></i> Volver a la tienda
                        </a>
                        <a href="{{ route('web.dashboard.orders') }}" class="oc-btn-orders">
                            Ver mis pedidos <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Accordion toggle para las cards
document.querySelectorAll('.collapsed-toggle').forEach(function (header) {
    header.addEventListener('click', function () {
        var bodyId = this.dataset.target;
        var body   = document.getElementById(bodyId);
        var icon   = this.querySelector('.toggle-icon');
        if (!body) return;
        var isOpen = body.classList.toggle('collapsed');
        if (icon) icon.style.transform = isOpen ? 'rotate(-90deg)' : 'rotate(0deg)';
    });
});
</script>

<style>
/* ══════════════════════════════════════════════════════════
   ORDER CONFIRMATION — Estilos
══════════════════════════════════════════════════════════ */

.oc-wrapper {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 40px rgba(0,0,0,.08);
    border: 1px solid #eef0f3;
}

/* ── SIDEBAR ── */
.oc-sidebar {
    background: linear-gradient(160deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
    color: #fff;
}

.sidebar-inner {
    padding: 40px 28px;
    display: flex;
    flex-direction: column;
    gap: 28px;
    min-height: 100%;
}

/* Anillo de éxito */
.success-ring {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    height: 80px;
}
.success-ring-outer {
    position: absolute;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,.1);
    animation: pulse 2s ease-in-out infinite;
}
.success-ring-inner {
    width: 60px; height: 60px;
    border-radius: 50%;
    background: #4ade80;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    color: #fff;
    position: relative;
    z-index: 1;
    box-shadow: 0 0 0 6px rgba(74,222,128,.25);
}
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: .6; }
    50%       { transform: scale(1.15); opacity: .3; }
}

/* ID de orden */
.order-id-block { text-align: center; }
.order-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; opacity: .6; }
.order-number { font-size: 1.5rem; font-weight: 800; color: #fff; margin: 4px 0; }
.order-date { font-size: .8rem; opacity: .65; }

/* Stepper */
.stepper {
    display: flex;
    flex-direction: column;
    gap: 0;
}
.stepper-step {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    position: relative;
}
.stepper-dot {
    width: 36px; height: 36px;
    flex-shrink: 0;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
    border: 2px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem;
    color: rgba(255,255,255,.5);
    transition: all .3s;
    position: relative;
    z-index: 1;
}
.stepper-step.done .stepper-dot {
    background: #4ade80;
    border-color: #4ade80;
    color: #fff;
}
.stepper-step.active .stepper-dot {
    background: #fbbf24;
    border-color: #fbbf24;
    color: #fff;
    box-shadow: 0 0 0 5px rgba(251,191,36,.25);
    animation: stepper-pulse 1.8s ease-in-out infinite;
}
@keyframes stepper-pulse {
    0%, 100% { box-shadow: 0 0 0 4px rgba(251,191,36,.25); }
    50%       { box-shadow: 0 0 0 10px rgba(251,191,36,.08); }
}
.stepper-label {
    font-size: .82rem;
    font-weight: 600;
    color: rgba(255,255,255,.55);
    padding-top: 8px;
}
.stepper-step.done .stepper-label,
.stepper-step.active .stepper-label { color: #fff; }

.stepper-line {
    position: absolute;
    left: 17px;
    top: 36px;
    width: 2px;
    height: 28px;
    background: rgba(255,255,255,.15);
    z-index: 0;
}
.stepper-line.done { background: #4ade80; }

/* Price box */
.price-box {
    background: rgba(255,255,255,.07);
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid rgba(255,255,255,.12);
}
.price-box-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    opacity: .6;
    margin-bottom: 12px;
}
.price-list {
    list-style: none;
    padding: 0; margin: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.price-list li {
    display: flex;
    justify-content: space-between;
    font-size: .86rem;
    color: rgba(255,255,255,.75);
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255,255,255,.08);
}
.price-list li:last-child { border-bottom: none; }
.pl-discount { color: #86efac !important; }
.pl-total {
    font-size: 1rem !important;
    font-weight: 800 !important;
    color: #fff !important;
    padding-top: 4px;
}

/* Delivery box */
.delivery-box {
    background: rgba(255,255,255,.07);
    border-radius: 12px;
    padding: 16px 20px;
    border: 1px solid rgba(255,255,255,.12);
}
.delivery-box h6 { font-size: .8rem; opacity: .65; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 8px; }
.delivery-dates { font-size: 1rem; font-weight: 700; color: #fff; margin: 0 0 4px; }
.delivery-method { font-size: .82rem; opacity: .65; margin: 0; }

/* Support box */
.support-box h6 { font-size: .75rem; opacity: .55; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 10px; }
.support-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,.7);
    text-decoration: none;
    font-size: .85rem;
    padding: 7px 0;
    border-bottom: 1px solid rgba(255,255,255,.08);
    transition: color .2s;
}
.support-link:last-child { border-bottom: none; }
.support-link:hover { color: #fff; }
.support-link i { font-size: 1rem; color: #a5b4fc; }

/* ── MAIN CONTENT ── */
.oc-main { padding: 40px 36px; }

/* Thankyou header */
.thankyou-header {
    margin-bottom: 28px;
}
.confetti-line {
    font-size: 1.6rem;
    margin-bottom: 8px;
}
.thankyou-header h1 {
    font-size: 1.9rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 10px;
}
.thankyou-header p {
    color: #6b7280;
    font-size: .95rem;
    line-height: 1.6;
    margin: 0;
}

/* OC Cards */
.oc-card {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 18px;
    box-shadow: 0 1px 8px rgba(0,0,0,.04);
}

.oc-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    background: #f8f9fb;
    cursor: pointer;
    user-select: none;
    border-bottom: 1px solid #eef0f3;
    transition: background .2s;
}
.oc-card-head:hover { background: #f1f3f8; }
.oc-card-head h3 {
    font-size: .95rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.oc-card-head h3 i { color: #6366f1; }
.toggle-icon { color: #9ca3af; font-size: .85rem; transition: transform .3s; }

.oc-card-body { padding: 22px; }
.oc-card-body.collapsed { display: none; }

/* Detail groups */
.detail-group label {
    display: block;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #9ca3af;
    margin-bottom: 6px;
}
.detail-group address,
.detail-group p {
    font-size: .9rem;
    color: #374151;
    line-height: 1.6;
    margin: 0;
}

.notes-block {
    display: flex;
    gap: 12px;
    background: #f0f7ff;
    border: 1px solid #bfdbfe;
    border-left: 4px solid #3b82f6;
    border-radius: 10px;
    padding: 14px 16px;
    color: #1e40af;
    font-size: .88rem;
}
.notes-block i { font-size: 1rem; flex-shrink: 0; margin-top: 2px; }

/* Payment row */
.payment-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 0;
}
.payment-logo {
    width: 48px; height: 48px;
    border-radius: 10px;
    background: #eff6ff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    color: #0070ba;
}
.payment-title { font-weight: 700; color: #111827; }
.payment-subtitle { font-size: .82rem; color: #6b7280; }
.payment-ok {
    font-size: .78rem;
    font-weight: 700;
    color: #15803d;
    background: #dcfce7;
    border-radius: 20px;
    padding: 4px 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.coupon-confirm {
    background: #f0fdf4;
    border: 1px dashed #86efac;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: .86rem;
    color: #374151;
}

/* ── Items de la orden ── */
.oc-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 0;
}
.oc-item-border { border-bottom: 1px solid #f3f4f6; }

.oc-item-img-wrap {
    flex-shrink: 0;
    position: relative;
    width: 80px; height: 80px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #eef0f3;
    background: #f9fafb;
}
.oc-item-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
}

/* Badge imagen variante */
.variant-img-badge {
    position: absolute;
    bottom: 4px; right: 4px;
    background: #6366f1;
    color: #fff;
    width: 18px; height: 18px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .52rem;
    border: 2px solid #fff;
}

.oc-item-info { flex: 1; min-width: 0; }

.oc-item-name {
    font-size: .95rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 7px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Chips de variante */
.oc-variant-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 8px;
}
.oc-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: .72rem;
}
.chip-k {
    color: #6366f1;
    font-weight: 700;
    text-transform: uppercase;
    font-size: .63rem;
    letter-spacing: .05em;
}
.chip-v { color: #374151; font-weight: 500; }

/* Precios del item */
.oc-item-pricing {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.oc-qty {
    font-size: .82rem;
    color: #9ca3af;
    font-weight: 600;
}
.oc-unit {
    font-size: .9rem;
    font-weight: 700;
    color: #374151;
}
.oc-subtotal {
    font-size: .82rem;
    color: #9ca3af;
}

/* ── Acciones ── */
.oc-actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    margin-top: 8px;
}
.oc-btn-back,
.oc-btn-orders {
    flex: 1;
    min-width: 160px;
    padding: 13px 20px;
    border-radius: 12px;
    font-weight: 700;
    font-size: .92rem;
    text-align: center;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .25s;
}
.oc-btn-back {
    background: #f3f4f6;
    color: #374151;
    border: 1.5px solid #e5e7eb;
}
.oc-btn-back:hover { background: #e5e7eb; color: #111827; }

.oc-btn-orders {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    border: none;
    box-shadow: 0 4px 14px rgba(99,102,241,.3);
}
.oc-btn-orders:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(99,102,241,.4);
    color: #fff;
}

/* ── Responsive ── */
@media (max-width: 991.98px) {
    .oc-main { padding: 28px 20px; }
    .sidebar-inner { padding: 28px 20px; }
    .oc-sidebar {
        border-radius: 0 0 0 0;
    }
    .oc-actions { flex-direction: column; }
    .oc-btn-back, .oc-btn-orders { min-width: 100%; }
}
</style>
@endsection