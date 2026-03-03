@extends('web.dashboard.layout')

@section('dashboard-content')

{{-- ══ HEADER ══════════════════════════════════════════════════ --}}
<div class="od-header" data-aos="fade-up">
    <div class="od-header-left">
        <a href="{{ route('web.dashboard.orders') }}" class="od-back">
            <i class="bi bi-arrow-left"></i> Volver a Órdenes
        </a>
        <div class="od-title-group">
            <h2>Orden <span class="od-id">#{{ $order->id }}</span></h2>
            <span class="od-date">
                <i class="bi bi-calendar3"></i>
                {{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y — g:i A') }}
            </span>
        </div>
    </div>
    <span class="od-status {{ strtolower($order->status) }}">
        {{ $order->status_label }}
    </span>
</div>

{{-- ══ BODY ═════════════════════════════════════════════════════ --}}
<div class="row g-4">

    {{-- ── COLUMNA PRINCIPAL ─────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Seguimiento --}}
        <div class="od-card" data-aos="fade-up">
            <div class="od-card-head">
                <h3><i class="bi bi-truck"></i> Seguimiento de la Orden</h3>
            </div>
            <div class="od-card-body">

                @if($order->statusHistory && $order->statusHistory->count() > 0)
                    @php
                        $statusConfig = [
                            'PENDING'    => ['icon'=>'bi-clock-history',          'color'=>'#f59e0b', 'label'=>'Pendiente',   'description'=>'Tu pedido ha sido recibido y está siendo procesado'],
                            'PROCESSING' => ['icon'=>'bi-gear-fill',              'color'=>'#06b6d4', 'label'=>'En Proceso',  'description'=>'Estamos preparando tu pedido'],
                            'SHIPPED'    => ['icon'=>'bi-truck',                  'color'=>'#3b82f6', 'label'=>'Enviado',     'description'=>'Tu pedido está en camino'],
                            'DELIVERED'  => ['icon'=>'bi-check-circle-fill',      'color'=>'#22c55e', 'label'=>'Entregado',   'description'=>'Tu pedido ha sido entregado'],
                            'CANCELLED'  => ['icon'=>'bi-x-circle-fill',          'color'=>'#ef4444', 'label'=>'Cancelado',   'description'=>'El pedido ha sido cancelado'],
                            'REFUNDED'   => ['icon'=>'bi-arrow-counterclockwise', 'color'=>'#a855f7', 'label'=>'Reembolsado', 'description'=>'El pedido ha sido reembolsado'],
                        ];
                        $paymentStatusConfig = [
                            'PENDING'  => ['label'=>'Pago Pendiente', 'color'=>'#f59e0b'],
                            'PAID'     => ['label'=>'Pagado',         'color'=>'#22c55e'],
                            'FAILED'   => ['label'=>'Pago Fallido',   'color'=>'#ef4444'],
                            'REFUNDED' => ['label'=>'Reembolsado',    'color'=>'#a855f7'],
                        ];
                    @endphp

                    <div class="tl-wrap">
                        @foreach($order->statusHistory as $history)
                            @php
                                $st = $statusConfig[$history->new_status] ?? [
                                    'icon'        => 'bi-circle',
                                    'color'       => '#6b7280',
                                    'label'       => ucfirst($history->new_status),
                                    'description' => '',
                                ];
                                $isFirst = $loop->first;
                            @endphp

                            <div class="tl-item {{ $isFirst ? 'tl-active' : '' }}">

                                {{-- Línea vertical (entre items) --}}
                                @if(!$loop->last)
                                    <div class="tl-line"></div>
                                @endif

                                {{-- Marcador: círculo con ícono DENTRO --}}
                                <div class="tl-dot" style="background:{{ $st['color'] }}1a; border-color:{{ $st['color'] }};">
                                    <i class="bi {{ $st['icon'] }}" style="color:{{ $st['color'] }};"></i>
                                </div>

                                {{-- Contenido --}}
                                <div class="tl-content">
                                    <div class="tl-top">
                                        <h4 style="color:{{ $st['color'] }};">{{ $st['label'] }}</h4>
                                        <span class="tl-date">
                                            <i class="bi bi-calendar3"></i>
                                            {{ $history->created_at->format('d M, Y') }} &middot; {{ $history->created_at->format('h:i A') }}
                                        </span>
                                    </div>

                                    @if($st['description'])
                                        <p class="tl-desc">{{ $st['description'] }}</p>
                                    @endif

                                    @if($history->notes)
                                        <div class="tl-notes">
                                            <i class="bi bi-chat-left-quote-fill"></i>
                                            <span>{{ $history->notes }}</span>
                                        </div>
                                    @endif

                                    @if($history->old_payment_status !== $history->new_payment_status && $history->new_payment_status)
                                        @php
                                            $ps = $paymentStatusConfig[$history->new_payment_status] ?? [
                                                'label' => ucfirst($history->new_payment_status),
                                                'color' => '#6b7280'
                                            ];
                                        @endphp
                                        <span class="tl-pay-badge"
                                              style="background:{{ $ps['color'] }}18; color:{{ $ps['color'] }}; border-color:{{ $ps['color'] }}40;">
                                            <i class="bi bi-credit-card-fill"></i>
                                            {{ $ps['label'] }}
                                        </span>
                                    @endif

                                    @if($history->changedBy)
                                        <div class="tl-by">
                                            <i class="bi bi-person-circle"></i>
                                            Actualizado por: <strong>{{ $history->changedBy->name }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="od-empty">
                        <i class="bi bi-hourglass-split"></i>
                        <h5>Sin historial de seguimiento</h5>
                        <p>Se actualizará a medida que tu pedido avance.</p>
                    </div>
                @endif

                @if($order->tracking_number)
                    <div class="tracking-box">
                        <div>
                            <div class="tracking-label">
                                <i class="bi bi-upc-scan"></i> Número de seguimiento
                            </div>
                            <div class="tracking-value">{{ $order->tracking_number }}</div>
                            @if($order->shipping_company)
                                <div class="tracking-company">
                                    <i class="bi bi-building me-1"></i>{{ $order->shipping_company }}
                                </div>
                            @endif
                        </div>
                        <button class="btn-copy" onclick="copyTracking(event, '{{ $order->tracking_number }}')">
                            <i class="bi bi-clipboard"></i> Copiar
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Artículos --}}
        <div class="od-card" data-aos="fade-up" data-aos-delay="100">
            <div class="od-card-head">
                <h3><i class="bi bi-box-seam"></i> Artículos ({{ count($order->items) }})</h3>
            </div>
            <div class="od-card-body">
                <div class="items-list">
                    @foreach($order->items as $item)
                        <div class="oi-row {{ !$loop->last ? 'oi-border' : '' }}">

                            {{-- Imagen --}}
                            <div class="oi-img-wrap">
                                @php
                                    $variantImage = $item->variant?->images?->first()?->image
                                                 ?? $item->variant?->image
                                                 ?? null;
                                    $displayImage = $variantImage
                                                 ?? $item->product->images->first()?->image
                                                 ?? 'products/default_ot_image.png';
                                @endphp
                                <img src="{{ asset('storage/' . $displayImage) }}"
                                     alt="{{ $item->product_name }}" loading="lazy">
                                @if($variantImage)
                                    <span class="variant-dot" title="Imagen de variante">
                                        <i class="bi bi-layers-fill"></i>
                                    </span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="oi-info">
                                <h4 class="oi-name">{{ $item->product_name }}</h4>

                                @if($item->variant_label)
                                    <div class="variant-chips">
                                        @foreach($item->variant_attributes as $key => $value)
                                            <span class="variant-chip">
                                                <span class="chip-k">{{ $key }}</span>
                                                <span class="chip-v">{{ $value }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="oi-meta">
                                    <span><i class="bi bi-upc me-1"></i>{{ $item->variant_sku ?? $item->product->sku }}</span>
                                    <span><i class="bi bi-hash me-1"></i>{{ $item->quantity }} unidades</span>
                                </div>
                            </div>

                            {{-- Precio --}}
                            <div class="oi-price">
                                <div class="oi-unit">${{ number_format($item->price, 2) }} × {{ $item->quantity }}</div>
                                <div class="oi-total">${{ number_format($item->price * $item->quantity, 2) }}</div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Dirección --}}
        <div class="od-card" data-aos="fade-up" data-aos-delay="200">
            <div class="od-card-head">
                <h3><i class="bi bi-geo-alt-fill"></i> Dirección de Envío</h3>
            </div>
            <div class="od-card-body">
                <div class="addr-row">
                    <div class="addr-icon">
                        <i class="bi bi-house-door-fill"></i>
                    </div>
                    <div class="addr-info">
                        <h4>{{ $addresses->label }}</h4>
                        <p>
                            {{ $addresses->address_line_1 }}<br>
                            @if($addresses->address_line_2){{ $addresses->address_line_2 }}<br>@endif
                            {{ $addresses->city }}, {{ $addresses->state }} {{ $addresses->postal_code }}<br>
                            {{ $addresses->country }}
                        </p>
                        <span class="addr-phone">
                            <i class="bi bi-telephone-fill"></i> {{ $addresses->phone }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SIDEBAR DERECHO ───────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Resumen de la orden --}}
        <div class="od-card od-sticky" data-aos="fade-up">
            <div class="od-card-head">
                <h3><i class="bi bi-receipt"></i> Resumen</h3>
            </div>
            <div class="od-card-body">
                <div class="sum-list">
                    <div class="sum-row">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="sum-row">
                        <span><i class="bi bi-truck me-1"></i>Envío</span>
                        <span class="{{ $order->shipping == 0 ? 'text-success fw-semibold' : '' }}">
                            {{ $order->shipping == 0 ? 'Gratis' : '$'.number_format($order->shipping, 2) }}
                        </span>
                    </div>
                    @if($order->tax > 0)
                        <div class="sum-row">
                            <span>Impuestos</span>
                            <span>${{ number_format($order->tax, 2) }}</span>
                        </div>
                    @endif
                    <div class="sum-divider"></div>
                    <div class="sum-row sum-total">
                        <span>Total</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Método de pago --}}
        <div class="od-card" data-aos="fade-up" data-aos-delay="100">
            <div class="od-card-head">
                <h3><i class="bi bi-credit-card-fill"></i> Pago</h3>
            </div>
            <div class="od-card-body">
                <div class="pay-row">
                    <div class="pay-icon">
                        <i class="bi bi-credit-card-fill"></i>
                    </div>
                    <div>
                        <div class="pay-type">{{ $order->payment_type }}</div>
                        <div class="pay-method">{{ $order->payment_method }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Método de envío --}}
        <div class="od-card" data-aos="fade-up" data-aos-delay="150">
            <div class="od-card-head">
                <h3><i class="bi bi-box-seam"></i> Envío</h3>
            </div>
            <div class="od-card-body">
                @if(!$order->shipping_method && !$order->shipping_company && !$order->tracking_number)
                    <div class="od-empty od-empty-sm">
                        <i class="bi bi-clock"></i>
                        <p>Los datos de envío aún no han sido asignados.</p>
                    </div>
                @else
                    <div class="ship-row">
                        <div class="ship-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="ship-info">
                            <h4>{{ $order->shipping_method ?? 'Envío pendiente' }}</h4>
                            @if($order->shipping_company)
                                <p><i class="bi bi-building me-1"></i>{{ $order->shipping_company }}</p>
                            @endif
                            @if($order->tracking_number)
                                <div class="ship-tracking">
                                    <code>{{ $order->tracking_number }}</code>
                                    <button class="btn-copy-sm" onclick="copyTracking(event, '{{ $order->tracking_number }}')">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            @endif
                            @if($order->estimated_delivery)
                                <p class="ship-eta">
                                    <i class="bi bi-calendar-check-fill me-1"></i>
                                    Entrega: {{ \Carbon\Carbon::parse($order->estimated_delivery)->format('d M, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Acciones --}}
        <div class="od-actions" data-aos="fade-up" data-aos-delay="200">
            <button class="od-btn od-btn-primary">
                <i class="bi bi-download"></i> Descargar Factura
            </button>
            <button class="od-btn od-btn-secondary">
                <i class="bi bi-question-circle"></i> Centro de Ayuda
            </button>
            @if(in_array(strtolower($order->status), ['processing', 'confirmed']))
                <button class="od-btn od-btn-danger">
                    <i class="bi bi-x-circle"></i> Cancelar Orden
                </button>
            @endif
        </div>

    </div>
</div>

<script>
function copyTracking(event, tracking) {
    navigator.clipboard.writeText(tracking).then(() => {
        var btn = event.target.closest('[onclick]');
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2"></i> Copiado';
        btn.style.background = '#22c55e';
        btn.style.color = '#fff';
        setTimeout(function () {
            btn.innerHTML = orig;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
}
</script>

<style>
/* ══════════════════════════════════════════════════════════
   ORDER DETAIL — consistent with cart + order-confirmation
══════════════════════════════════════════════════════════ */

/* ── Header ── */
.od-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
    padding-bottom: 22px;
    border-bottom: 2px solid #f0f0f2;
}
.od-header-left { display: flex; flex-direction: column; gap: 12px; }

.od-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #6b7280;
    text-decoration: none;
    font-size: .85rem;
    font-weight: 500;
    transition: color .2s;
}
.od-back:hover { color: #6366f1; }

.od-title-group h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #111827;
    margin: 0 0 4px;
}
.od-id { color: #6366f1; }
.od-date {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .82rem;
    color: #9ca3af;
}

/* Status badge */
.od-status {
    padding: 7px 18px;
    border-radius: 20px;
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .02em;
    text-transform: uppercase;
    white-space: nowrap;
}
.od-status.pending    { background:#fff7ed; color:#c2410c; }
.od-status.processing { background:#fffbeb; color:#92400e; }
.od-status.shipped    { background:#eff6ff; color:#1d4ed8; }
.od-status.delivered  { background:#f0fdf4; color:#15803d; }
.od-status.cancelled  { background:#fef2f2; color:#b91c1c; }
.od-status.refunded   { background:#faf5ff; color:#7e22ce; }

/* ── Cards ── */
.od-card {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
}
.od-card-head {
    padding: 16px 22px;
    background: #f8f9fb;
    border-bottom: 1px solid #eef0f3;
}
.od-card-head h3 {
    margin: 0;
    font-size: .97rem;
    font-weight: 700;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}
.od-card-head h3 i { color: #6366f1; }
.od-card-body { padding: 22px; }

.od-sticky { position: sticky; top: 88px; }

/* ── Timeline ── */
.tl-wrap { display: flex; flex-direction: column; }

.tl-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    position: relative;
    padding-bottom: 28px;
}
.tl-item:last-child { padding-bottom: 0; }

/* Línea vertical — elemento independiente absolutamente posicionado */
.tl-line {
    position: absolute;
    left: 19px;           /* centro del dot (dot = 40px → left = 20 - 1px borde) */
    top: 42px;            /* debajo del dot */
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
    z-index: 0;
}
.tl-active .tl-line { background: #c7d2fe; }

/* Dot: círculo con ícono centrado */
.tl-dot {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1;           /* encima de la línea */
    background: #fff;
}
.tl-dot i {
    font-size: .95rem;    /* ícono más pequeño que el círculo */
    line-height: 1;
    display: flex;
}

.tl-active .tl-dot {
    box-shadow: 0 0 0 5px rgba(99,102,241,.15);
}

/* Contenido */
.tl-content { flex: 1; padding-top: 8px; }

.tl-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 5px;
}
.tl-top h4 {
    font-size: .95rem;
    font-weight: 700;
    margin: 0;
}
.tl-date {
    font-size: .76rem;
    color: #9ca3af;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

.tl-desc {
    font-size: .84rem;
    color: #6b7280;
    margin: 0 0 8px;
    line-height: 1.5;
}

.tl-notes {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    background: #f0f7ff;
    border: 1px solid #bfdbfe;
    border-left: 3px solid #3b82f6;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: .82rem;
    color: #1e40af;
    margin-bottom: 8px;
}
.tl-notes i { flex-shrink: 0; margin-top: 1px; }

.tl-pay-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 12px;
    border-radius: 20px;
    border: 1px solid;
    font-size: .75rem;
    font-weight: 700;
    margin-top: 6px;
    margin-bottom: 4px;
}

.tl-by {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: .76rem;
    color: #9ca3af;
    margin-top: 6px;
}
.tl-by strong { color: #6b7280; }

/* Empty state */
.od-empty {
    text-align: center;
    padding: 28px 16px;
    color: #9ca3af;
}
.od-empty i { font-size: 2.5rem; margin-bottom: 10px; display: block; }
.od-empty h5 { font-size: .97rem; color: #6b7280; margin-bottom: 5px; }
.od-empty p  { font-size: .84rem; margin: 0; }
.od-empty-sm { padding: 16px 0; }
.od-empty-sm i { font-size: 1.5rem; }

/* Tracking box */
.tracking-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    background: #f8f9fb;
    border: 1px solid #eef0f3;
    border-radius: 12px;
    padding: 16px 18px;
    margin-top: 20px;
    flex-wrap: wrap;
}
.tracking-label {
    font-size: .75rem;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.tracking-value {
    font-family: 'Courier New', monospace;
    font-size: 1rem;
    font-weight: 700;
    color: #6366f1;
    letter-spacing: .05em;
}
.tracking-company {
    font-size: .8rem;
    color: #6b7280;
    margin-top: 2px;
}

/* ── Items de la orden ── */
.items-list { display: flex; flex-direction: column; }

.oi-row {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: 16px;
    align-items: center;
    padding: 16px 0;
}
.oi-border { border-bottom: 1px solid #f3f4f6; }

.oi-img-wrap {
    position: relative;
    width: 80px; height: 80px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #eef0f3;
    background: #f9fafb;
    flex-shrink: 0;
}
.oi-img-wrap img { width: 100%; height: 100%; object-fit: cover; }

/* Badge imagen variante */
.variant-dot {
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

.oi-name {
    font-size: .93rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 7px;
}

/* Chips de variante */
.variant-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 7px;
}
.variant-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: 20px;
    padding: 2px 9px;
    font-size: .72rem;
}
.chip-k {
    color: #6366f1;
    font-weight: 700;
    text-transform: uppercase;
    font-size: .62rem;
    letter-spacing: .05em;
}
.chip-v { color: #374151; font-weight: 500; }

.oi-meta {
    display: flex;
    gap: 14px;
    font-size: .78rem;
    color: #9ca3af;
    flex-wrap: wrap;
}

.oi-price { text-align: right; }
.oi-unit  { font-size: .8rem; color: #9ca3af; margin-bottom: 2px; }
.oi-total { font-size: 1.05rem; font-weight: 700; color: #6366f1; }

/* ── Dirección ── */
.addr-row { display: flex; gap: 16px; align-items: flex-start; }
.addr-icon {
    flex-shrink: 0;
    width: 46px; height: 46px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.addr-info h4 { font-size: .95rem; font-weight: 700; color: #111827; margin: 0 0 7px; }
.addr-info p  { font-size: .86rem; color: #6b7280; line-height: 1.6; margin: 0 0 8px; }
.addr-phone   { font-size: .82rem; color: #6b7280; display: inline-flex; align-items: center; gap: 6px; }
.addr-phone i { color: #6366f1; }

/* ── Resumen ── */
.sum-list { display: flex; flex-direction: column; gap: 0; }
.sum-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    font-size: .88rem;
    color: #4b5563;
    border-bottom: 1px dashed #f0f0f0;
}
.sum-row:last-child { border-bottom: none; }
.sum-divider { height: 1.5px; background: #e5e7eb; margin: 6px 0; }
.sum-total {
    font-size: 1.05rem;
    font-weight: 800;
    padding-top: 12px;
}
.sum-total span:last-child { color: #6366f1; }

/* ── Pago ── */
.pay-row { display: flex; align-items: center; gap: 14px; }
.pay-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    background: #eff6ff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    color: #3b82f6;
    flex-shrink: 0;
}
.pay-type   { font-size: .93rem; font-weight: 700; color: #111827; }
.pay-method { font-size: .8rem; color: #9ca3af; }

/* ── Envío ── */
.ship-row { display: flex; gap: 14px; align-items: flex-start; }
.ship-icon {
    flex-shrink: 0;
    width: 46px; height: 46px;
    border-radius: 12px;
    background: #eff6ff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    color: #3b82f6;
}
.ship-info h4 { font-size: .93rem; font-weight: 700; color: #111827; margin: 0 0 8px; }
.ship-info p  { font-size: .82rem; color: #6b7280; margin: 0 0 5px; display: flex; align-items: center; gap: 5px; }

.ship-tracking {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f3f4f6;
    border-radius: 8px;
    padding: 6px 10px;
    margin-bottom: 6px;
}
.ship-tracking code {
    font-size: .8rem;
    font-weight: 700;
    color: #6366f1;
    font-family: 'Courier New', monospace;
}
.ship-eta { color: #15803d !important; font-weight: 500; }
.ship-eta i { color: #22c55e !important; }

/* ── Copy buttons ── */
.btn-copy {
    padding: 8px 16px;
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 9px;
    font-size: .83rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .2s;
    white-space: nowrap;
}
.btn-copy:hover { background: #4f46e5; }

.btn-copy-sm {
    background: none;
    border: 1.5px solid #e5e7eb;
    border-radius: 6px;
    width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    color: #9ca3af;
    font-size: .8rem;
    transition: all .2s;
    padding: 0;
}
.btn-copy-sm:hover { border-color: #6366f1; color: #6366f1; }

/* ── Actions ── */
.od-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.od-btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: .9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all .25s;
}
.od-btn-primary  { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; box-shadow: 0 3px 12px rgba(99,102,241,.25); }
.od-btn-primary:hover  { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,.35); }
.od-btn-secondary { background: #f3f4f6; color: #374151; }
.od-btn-secondary:hover { background: #e5e7eb; }
.od-btn-danger   { background: #fef2f2; color: #b91c1c; border: 1.5px solid #fecaca; }
.od-btn-danger:hover { background: #fee2e2; }

/* ── Responsive ── */
@media (max-width: 991.98px) {
    .od-sticky { position: static; }
}
@media (max-width: 767.98px) {
    .oi-row { grid-template-columns: 64px 1fr; }
    .oi-price { display: none; }
    .oi-meta { flex-direction: column; gap: 3px; }
    .od-header { flex-direction: column; }
    .tracking-box { flex-direction: column; }
    .btn-copy { width: 100%; justify-content: center; }
}
</style>

@endsection