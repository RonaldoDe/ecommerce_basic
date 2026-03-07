@extends('layouts.web')

@section('content')

<!-- Page Title -->
<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Carrito</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="{{ route('web.index') }}">Inicio</a></li>
        <li class="current">Carrito</li>
      </ol>
    </nav>
  </div>
</div>

<section id="cart" class="cart section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    @php $cart = auth()->user()->cart; @endphp

    @if($cart->count() === 0)

      {{-- ── Carrito vacío ────────────────────────────────── --}}
      <div class="empty-cart text-center py-5">
        <i class="bi bi-cart-x empty-cart-icon"></i>
        <h3 class="mt-3">Tu carrito está vacío</h3>
        <p class="text-muted mb-4">Aún no has agregado ningún producto</p>
        <a href="{{ route('web.index') }}" class="btn btn-primary px-5">
          <i class="bi bi-arrow-left me-2"></i>Explorar productos
        </a>
      </div>

    @else

    <div class="row g-4">

      {{-- ══════════════════════════════════════════════
           COLUMNA IZQUIERDA — Items
      ══════════════════════════════════════════════ --}}
      <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
        <div class="cart-card">

          {{-- Cabecera columnas --}}
          <div class="cart-col-header d-none d-lg-grid">
            <span>Producto</span>
            <span class="text-center">Precio</span>
            <span class="text-center">Cantidad</span>
            <span class="text-center">Subtotal</span>
          </div>

          @php $subtotal = 0; @endphp

          @foreach ($cart as $item)
            @php
              // ── Imagen: variante primero, luego producto ──
              $variantImage = null;
              if ($item->variant) {
                  $variantImage = $item->variant->images?->first()?->image
                               ?? $item->variant->image
                               ?? null;
              }
              $displayImage = $variantImage
                           ?? $item->product->images->first()?->image
                           ?? 'products/default_ot_image.png';

              // ── Precio: variante o producto ──
              $unitPrice = $item->variant?->price ?? $item->product->selling_price;
              $itemTotal = $unitPrice * $item->quantity;
              $subtotal += $itemTotal;

              // ── Atributos de variante ──
              $variantAttrs = [];
              if ($item->variant) {
                  $raw = $item->variant->getAttribute('attributes');
                  if (is_array($raw))  $variantAttrs = $raw;
                  elseif (is_string($raw)) $variantAttrs = json_decode($raw, true) ?? [];
              }

              // ── Stock disponible ──
              $availableStock = $item->variant?->stock ?? $item->product->stock;
            @endphp

            <div class="cart-item" id="cart-item-{{ $item->id }}">
              <div class="cart-item-grid">

                {{-- Producto --}}
                <div class="ci-product">
                  <a href="{{ route('web.product.show', $item->product->id) }}" class="ci-img-wrap">
                    <img src="{{ asset('storage/' . $displayImage) }}"
                         alt="{{ $item->product->name }}"
                         loading="lazy">
                    @if($item->variant && $variantImage)
                      <span class="variant-dot" title="Imagen de variante">
                        <i class="bi bi-layers-fill"></i>
                      </span>
                    @endif
                  </a>

                  <div class="ci-info">
                    <a href="{{ route('web.product.show', $item->product->id) }}" class="ci-name">
                      {{ $item->product->name }}
                    </a>

                    {{-- Chips de atributos de variante --}}
                    @if(count($variantAttrs) > 0)
                      <div class="variant-chips">
                        @foreach($variantAttrs as $key => $val)
                          <span class="variant-chip">
                            <span class="chip-key">{{ ucfirst($key) }}</span>
                            <span class="chip-val">{{ $val }}</span>
                          </span>
                        @endforeach
                      </div>
                    @endif

                    {{-- Stock --}}
                    @if($availableStock > 5)
                      <span class="stock-pill ok"><i class="bi bi-check-circle-fill"></i> {{ $availableStock }} disponibles</span>
                    @elseif($availableStock > 0)
                      <span class="stock-pill low"><i class="bi bi-exclamation-triangle-fill"></i> ¡Solo {{ $availableStock }}!</span>
                    @else
                      <span class="stock-pill none"><i class="bi bi-x-circle-fill"></i> Sin stock</span>
                    @endif

                    {{-- Eliminar --}}
                    <form action="{{ route('web.cart.destroy', $item->id) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="ci-remove"
                              onclick="confirmDelete(this.form, '¿Eliminar este producto del carrito?')">
                        <i class="bi bi-trash3"></i> Eliminar
                      </button>
                    </form>
                  </div>
                </div>

                {{-- Precio --}}
                <div class="ci-price text-lg-center">
                  <span class="label-mobile">Precio</span>
                  <div>
                    <span class="price-main">{{ $settings->badge }}{{ number_format($unitPrice, 2) }}</span>
                    @if($item->variant && $item->variant->price && $item->variant->price != $item->product->selling_price)
                      <del class="price-orig d-block">{{ $settings->badge }}{{ number_format($item->product->selling_price, 2) }}</del>
                    @endif
                  </div>
                </div>

                {{-- Cantidad --}}
                <div class="ci-qty text-lg-center">
                  <span class="label-mobile">Cantidad</span>
                  <form action="{{ route('web.cart.update', $item->id) }}" method="POST"
                        class="qty-form" id="qf-{{ $item->id }}">
                    @csrf
                    @method('PUT')
                    <div class="qty-wrap">
                      <button type="button" class="qty-btn"
                              data-dir="-1"
                              data-form="qf-{{ $item->id }}"
                              data-max="{{ $availableStock }}"
                              {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                        <i class="bi bi-dash"></i>
                      </button>
                      <input type="number"
                             class="qty-input"
                             name="quantity"
                             value="{{ $item->quantity }}"
                             min="1"
                             max="{{ $availableStock }}"
                             readonly>
                      <button type="button" class="qty-btn"
                              data-dir="1"
                              data-form="qf-{{ $item->id }}"
                              data-max="{{ $availableStock }}"
                              {{ $item->quantity >= $availableStock ? 'disabled' : '' }}>
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                  </form>
                </div>

                {{-- Subtotal --}}
                <div class="ci-subtotal text-lg-center">
                  <span class="label-mobile">Subtotal</span>
                  <span class="subtotal-val">{{ $settings->badge }}{{ number_format($itemTotal, 2) }}</span>
                </div>

              </div>
            </div>
          @endforeach

          {{-- Acciones: cupón + limpiar --}}
          <div class="cart-actions-bar">
            <div class="coupon-wrap">
              <form action="{{ route('web.cart.apply-coupon') }}" method="POST">
                @csrf
                <div class="coupon-box">
                  <i class="bi bi-tag-fill"></i>
                  <input type="text"
                         name="coupon_code"
                         class="coupon-input @error('coupon_code') is-invalid @enderror"
                         placeholder="Código de cupón"
                         value="{{ session('coupon_code', old('coupon_code')) }}">
                  <button class="coupon-btn" type="submit">Aplicar</button>
                </div>
                @error('coupon_code')
                  <p class="text-danger small mt-1">{{ $message }}</p>
                @enderror
              </form>

              @if(session('coupon_code'))
                <div class="coupon-applied">
                  <span><i class="bi bi-check-circle-fill me-1 text-success"></i>
                    Cupón "{{ session('coupon_code') }}" aplicado
                  </span>
                  <form action="{{ route('web.cart.remove-coupon') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-coupon-remove">
                      <i class="bi bi-x"></i> Quitar
                    </button>
                  </form>
                </div>
              @endif
            </div>

            <form action="{{ route('web.cart.clear') }}" method="POST">
              @csrf
              <button type="button" class="btn-clear"
                      onclick="confirmDelete(this.form, '¿Vaciar el carrito completo?')">
                <i class="bi bi-trash3 me-1"></i> Vaciar carrito
              </button>
            </form>
          </div>

        </div>
      </div>

      {{-- ══════════════════════════════════════════════
           COLUMNA DERECHA — Resumen
      ══════════════════════════════════════════════ --}}
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
        <div class="summary-card">

          <div class="summary-head">
            <h5><i class="bi bi-receipt me-2"></i>Resumen del pedido</h5>
          </div>

          <div class="summary-body">
            @php
              $discount = session('discount_amount', 0);
              $total    = $subtotal - $discount;
            @endphp

            <div class="summary-row">
              <span>Subtotal
                <small class="text-muted ms-1">({{ $cart->count() }} {{ $cart->count() == 1 ? 'producto' : 'productos' }})</small>
              </span>
              <span>{{ $settings->badge }}{{ number_format($subtotal, 2) }}</span>
            </div>

            @if($discount > 0)
              <div class="summary-row summary-discount">
                <span>
                  <i class="bi bi-scissors me-1"></i> Descuento
                  @if(session('coupon_code'))
                    <small class="text-muted">({{ session('coupon_code') }})</small>
                  @endif
                </span>
                <span class="text-success fw-bold">−{{ $settings->badge }}{{ number_format($discount, 2) }}</span>
              </div>
            @endif

            <div class="summary-row">
              <span><i class="bi bi-truck me-1"></i> Envío</span>
              <span class="text-success fw-semibold">Gratis</span>
            </div>
          </div>

          <div class="summary-divider"></div>

          <div class="summary-total">
            <span>Total a pagar</span>
            <span class="total-num">{{ $settings->badge }}{{ number_format($total, 2) }}</span>
          </div>

          @if($discount > 0)
            <div class="savings-pill">
              <i class="bi bi-piggy-bank-fill me-1"></i>
              ¡Ahorras {{ $settings->badge }}{{ number_format($discount, 2) }} con este cupón!
            </div>
          @endif

          {{-- Checkout --}}
          <form action="{{ route('web.paypal.payment') }}" method="POST" class="summary-form">
            @csrf

            <div class="mb-3">
              <label for="customer_notes" class="form-label small text-muted">
                <i class="bi bi-chat-left-text me-1"></i> Notas (opcional)
              </label>
              <textarea class="form-control form-control-sm @error('customer_notes') is-invalid @enderror"
                        name="customer_notes"
                        id="customer_notes"
                        rows="2"
                        placeholder="Instrucciones especiales…">{{ old('customer_notes', session('customer_notes')) }}</textarea>
              @error('customer_notes')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
              @enderror
            </div>

            <input type="hidden" name="total"    value="{{ $total }}">
            <input type="hidden" name="subtotal" value="{{ $subtotal }}">
            <input type="hidden" name="discount" value="{{ $discount }}">

            <button type="submit" class="btn-checkout w-100">
              <i class="bi bi-lock-fill me-2"></i> Pagar con PayPal
              <i class="bi bi-paypal ms-1"></i>
            </button>
          </form>

          <a href="{{ route('web.index') }}" class="btn-continue w-100">
            <i class="bi bi-arrow-left me-1"></i> Seguir comprando
          </a>

          <div class="payment-icons-row">
            <i class="bi bi-credit-card" title="Tarjeta"></i>
            <i class="bi bi-paypal"      title="PayPal"></i>
            <i class="bi bi-wallet2"     title="Wallet"></i>
            <i class="bi bi-shield-lock-fill" title="Pago seguro"></i>
          </div>

        </div>
      </div>

    </div>
    @endif
  </div>
</section>

{{-- ── Scripts ─────────────────────────────────────────────────── --}}
<script>
function confirmDelete(form, message) {
  event.preventDefault();
  Swal.fire({
    title: message,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#ef4444',
  }).then(r => { if (r.isConfirmed) form.submit(); });
}

// +/- de cantidad con autosubmit
document.addEventListener('click', function (e) {
  var btn = e.target.closest('.qty-btn[data-form]');
  if (!btn) return;

  var form  = document.getElementById(btn.dataset.form);
  var input = form.querySelector('.qty-input');
  var max   = parseInt(btn.dataset.max) || 99;
  var val   = parseInt(input.value) || 1;
  var dir   = parseInt(btn.dataset.dir);

  var next = val + dir;
  if (next < 1 || next > max) return;
  input.value = next;

  // actualizar disabled
  form.querySelectorAll('.qty-btn').forEach(function (b) {
    var d = parseInt(b.dataset.dir);
    b.disabled = (d === -1 && next <= 1) || (d === 1 && next >= max);
  });

  clearTimeout(form._t);
  form._t = setTimeout(() => form.submit(), 650);
});
</script>

{{-- ── Estilos ─────────────────────────────────────────────────── --}}
<style>
/* ── Empty state ── */
.empty-cart { padding: 80px 20px; }
.empty-cart-icon { font-size: 4.5rem; color: #d1d5db; }

/* ── Cart card (wrapper items) ── */
.cart-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #eef0f3;
  box-shadow: 0 2px 16px rgba(0,0,0,.05);
  overflow: hidden;
}

/* ── Header columnas ── */
.cart-col-header {
  display: grid;
  grid-template-columns: 1fr 110px 130px 110px;
  gap: 12px;
  padding: 12px 24px;
  background: #f8f9fb;
  border-bottom: 1px solid #eef0f3;
  font-size: .75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .07em;
  color: #9ca3af;
}

/* ── Item ── */
.cart-item {
  border-bottom: 1px solid #f3f4f6;
  transition: background .2s;
}
.cart-item:last-of-type { border-bottom: none; }
.cart-item:hover { background: #fafbff; }

.cart-item-grid {
  display: grid;
  grid-template-columns: 1fr 110px 130px 110px;
  gap: 12px;
  align-items: center;
  padding: 18px 24px;
}

/* ── Producto ── */
.ci-product {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.ci-img-wrap {
  flex-shrink: 0;
  position: relative;
  width: 86px;
  height: 86px;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #eef0f3;
  display: block;
  background: #f9fafb;
}
.ci-img-wrap img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform .35s;
}
.ci-img-wrap:hover img { transform: scale(1.07); }

/* Badge imagen variante */
.variant-dot {
  position: absolute;
  bottom: 5px; right: 5px;
  background: #6366f1;
  color: #fff;
  width: 18px; height: 18px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .55rem;
  border: 2px solid #fff;
}

/* Info producto */
.ci-info { flex: 1; min-width: 0; }

.ci-name {
  display: block;
  font-size: .92rem;
  font-weight: 600;
  color: #111827;
  text-decoration: none;
  line-height: 1.3;
  margin-bottom: 7px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: color .2s;
}
.ci-name:hover { color: var(--accent-color, #6366f1); }

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
.chip-key {
  color: #6366f1;
  font-weight: 700;
  text-transform: uppercase;
  font-size: .63rem;
  letter-spacing: .05em;
}
.chip-val { color: #374151; font-weight: 500; }

/* Stock pills */
.stock-pill {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: .7rem;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 20px;
  margin-bottom: 7px;
}
.stock-pill.ok   { background: #dcfce7; color: #15803d; }
.stock-pill.low  { background: #fff7ed; color: #c2410c; }
.stock-pill.none { background: #fee2e2; color: #b91c1c; }

/* Botón eliminar */
.ci-remove {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  font-size: .78rem;
  color: #9ca3af;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: color .2s;
}
.ci-remove:hover { color: #ef4444; }

/* ── Precio ── */
.price-main {
  font-size: 1rem;
  font-weight: 700;
  color: #111827;
}
.price-orig { font-size: .78rem; color: #9ca3af; }

/* ── Cantidad ── */
.qty-wrap {
  display: inline-flex;
  align-items: center;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
  background: #fff;
}
.qty-btn {
  width: 32px; height: 32px;
  background: none;
  border: none;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  color: #6b7280;
  font-size: .85rem;
  transition: background .18s, color .18s;
}
.qty-btn:hover:not(:disabled) { background: #f3f4f6; color: #111; }
.qty-btn:disabled { opacity: .3; cursor: not-allowed; }
.qty-input {
  width: 38px;
  text-align: center;
  border: none;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
  font-weight: 700;
  font-size: .88rem;
  color: #111827;
  padding: 0;
  -moz-appearance: textfield;
}
.qty-input::-webkit-inner-spin-button,
.qty-input::-webkit-outer-spin-button { -webkit-appearance: none; }

/* ── Subtotal ── */
.subtotal-val {
  font-weight: 700;
  font-size: 1rem;
  color: var(--accent-color, #6366f1);
}

/* ── Barra de acciones (cupón + limpiar) ── */
.cart-actions-bar {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
  padding: 18px 24px;
  background: #f8f9fb;
  border-top: 1px solid #eef0f3;
}

.coupon-box {
  display: flex;
  align-items: center;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
  background: #fff;
  max-width: 320px;
}
.coupon-box i { padding: 0 12px; color: #6366f1; font-size: .95rem; }
.coupon-input {
  flex: 1;
  border: none;
  outline: none;
  padding: 9px 4px;
  font-size: .88rem;
  background: transparent;
}
.coupon-btn {
  padding: 9px 16px;
  background: #6366f1;
  color: #fff;
  border: none;
  font-weight: 600;
  font-size: .83rem;
  cursor: pointer;
  transition: background .2s;
}
.coupon-btn:hover { background: #4f46e5; }

.coupon-applied {
  margin-top: 8px;
  font-size: .82rem;
  display: flex;
  align-items: center;
  gap: 6px;
}
.btn-coupon-remove {
  background: none;
  border: none;
  color: #ef4444;
  font-size: .8rem;
  cursor: pointer;
  transition: opacity .2s;
  padding: 0;
}
.btn-coupon-remove:hover { opacity: .7; }

.btn-clear {
  background: none;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: .83rem;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  transition: all .2s;
  white-space: nowrap;
}
.btn-clear:hover { border-color: #ef4444; color: #ef4444; background: #fff5f5; }

/* ══════════════════════════════════════════════
   SUMMARY CARD
══════════════════════════════════════════════ */
.summary-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #eef0f3;
  box-shadow: 0 2px 16px rgba(0,0,0,.05);
  overflow: hidden;
  position: sticky;
  top: 88px;
}

.summary-head {
  padding: 16px 22px;
  background: #f8f9fb;
  border-bottom: 1px solid #eef0f3;
}
.summary-head h5 {
  margin: 0;
  font-size: .97rem;
  font-weight: 700;
  color: #111827;
}

.summary-body { padding: 18px 22px 8px; }

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 9px 0;
  font-size: .88rem;
  color: #4b5563;
  border-bottom: 1px dashed #f0f0f0;
}
.summary-row:last-child { border-bottom: none; }

.summary-discount {
  background: #f0fdf4;
  border: 1px dashed #86efac;
  border-radius: 8px;
  padding: 9px 12px;
  margin: 4px 0;
}

.summary-divider {
  height: 2px;
  background: #f3f4f6;
  margin: 6px 22px 14px;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 22px 14px;
}
.summary-total > span:first-child {
  font-weight: 700;
  font-size: .97rem;
  color: #111827;
}
.total-num {
  font-size: 1.65rem;
  font-weight: 800;
  color: var(--accent-color, #6366f1);
  line-height: 1;
}

.savings-pill {
  margin: 0 22px 14px;
  background: linear-gradient(135deg, #dcfce7, #bbf7d0);
  border: 1px solid #86efac;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: .8rem;
  font-weight: 600;
  color: #15803d;
  text-align: center;
}

.summary-form { padding: 0 22px 16px; }

/* Botón checkout */
.btn-checkout {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 13px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: .97rem;
  cursor: pointer;
  transition: all .3s;
  text-align: center;
  box-shadow: 0 4px 15px rgba(102,126,234,.3);
  letter-spacing: .02em;
}
.btn-checkout:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(102,126,234,.45);
}

.btn-continue {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px;
  margin: 8px 22px 0;
  color: #6b7280;
  text-decoration: none;
  font-size: .86rem;
  border-radius: 8px;
  transition: background .2s, color .2s;
  font-weight: 500;
}
.btn-continue:hover { background: #f3f4f6; color: #374151; }

.payment-icons-row {
  display: flex;
  justify-content: center;
  gap: 16px;
  padding: 14px 22px;
  color: #c4c9d4;
  font-size: 1.25rem;
  border-top: 1px solid #f3f4f6;
  margin-top: 8px;
}
.payment-icons-row i:hover { color: #9ca3af; }

/* ── Labels mobile ── */
.label-mobile {
  display: none;
  font-size: .73rem;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: .06em;
  margin-bottom: 4px;
}

/* ── Responsive ── */
@media (max-width: 991.98px) {
  .cart-item-grid {
    grid-template-columns: 1fr;
    gap: 14px;
    padding: 16px;
  }
  .cart-col-header { display: none !important; }
  .label-mobile { display: block; }
  .cart-actions-bar { flex-direction: column; }
  .coupon-box { max-width: 100%; }
  .summary-card { position: static; }
}
</style>
@endsection