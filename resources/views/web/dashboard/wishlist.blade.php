@extends('web.dashboard.layout')

@section('dashboard-content')

<div class="section-header" data-aos="fade-up">
    <h2>Mi Lista de Deseos</h2>
    <div class="header-actions">
        <button type="button" class="ds-btn-primary btn-add-all">
            <i class="bi bi-cart-plus me-1"></i> Agregar Todo al Carrito
        </button>
    </div>
</div>

<div class="wl-grid">
    @forelse($favoriteProducts as $item)
    <div class="wl-card" data-aos="fade-up" id="wishlist-card-{{ $item->product->id }}">

        <div class="wl-img-wrap">
            <img src="{{ asset('storage/' . ($item->product->images->first()?->image ?? 'products/default_ot_image.png')) }}"
                 alt="{{ $item->product->name }}" loading="lazy">

            <button type="button" class="wl-remove js-fav-remove"
                    data-product-id="{{ $item->product->id }}"
                    data-card-id="wishlist-card-{{ $item->product->id }}"
                    aria-label="Eliminar de favoritos">
                <i class="bi bi-heart-fill"></i>
            </button>

            <span class="wl-stock-badge">{{ $item->product->stock }} disponibles</span>

            @if($item->product->discount_percentage > 0)
                <span class="wl-discount-badge">−{{ $item->product->discount_percentage }}%</span>
            @endif
        </div>

        <div class="wl-body">
            <a href="{{ route('web.product.show', $item->product->id) }}" class="wl-name">
                {{ $item->product->name }}
            </a>

            <div class="wl-rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= $item->product->rating ? '-fill' : '' }}"></i>
                @endfor
                <span>({{ $item->product->rating }})</span>
            </div>

            <div class="wl-price">
                <span class="wl-price-current">{{ $settings->badge }}{{ number_format($item->product->final_price, 2) }}</span>
                @if($item->product->is_on_sale)
                    <span class="wl-price-old">{{ $settings->badge }}{{ number_format($item->product->selling_price, 2) }}</span>
                @endif
            </div>

            @if($item->product->stock > 0)
                @if($item->product->has_variants)
                    <a href="{{ route('web.product.show', $item->product->id) }}" class="wl-btn wl-btn-variants">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Ver opciones
                    </a>
                @else
                    <form action="{{ route('web.cart.store') }}" method="POST" class="js-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="wl-btn wl-btn-cart js-cart">
                            <i class="bi bi-cart-plus me-1"></i> Agregar al carrito
                        </button>
                    </form>
                @endif
            @else
                <button type="button" class="wl-btn wl-btn-notify">
                    <i class="bi bi-bell me-1"></i> Notificarme
                </button>
            @endif
        </div>
    </div>
    @empty
    <div class="ds-empty-state">
        <i class="bi bi-heart"></i>
        <h3>Tu lista de deseos está vacía</h3>
        <p>Guarda los productos que te gusten para comprarlos más tarde</p>
        <a href="{{ route('web.index') }}" class="ds-btn-primary">
            <i class="bi bi-shop me-1"></i> Explorar Productos
        </a>
    </div>
    @endforelse
</div>

<style>
/* Grid */
.wl-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 18px;
}

/* Card */
.wl-card {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    transition: transform .3s, box-shadow .3s;
}
.wl-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(99,102,241,.12);
}
.wl-card.removing {
    opacity: 0;
    transform: scale(.92);
    transition: opacity .3s, transform .3s;
}

/* Image */
.wl-img-wrap {
    position: relative;
    padding-top: 100%;
    overflow: hidden;
    background: #f9fafb;
}
.wl-img-wrap img {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .4s;
}
.wl-card:hover .wl-img-wrap img { transform: scale(1.05); }

/* Remove button */
.wl-remove {
    position: absolute;
    top: 10px; right: 10px;
    width: 34px; height: 34px;
    background: #fff;
    border: none;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
    color: #ef4444;
    font-size: .88rem;
    transition: all .25s;
    z-index: 2;
}
.wl-remove:hover { background: #ef4444; color: #fff; }
.wl-remove.btn-loading { opacity: .45; pointer-events: none; }

/* Badges */
.wl-stock-badge,
.wl-discount-badge {
    position: absolute;
    left: 10px;
    font-size: .7rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
    z-index: 2;
}
.wl-stock-badge   { top: 10px; background: #1e1b4b; color: #fff; }
.wl-discount-badge { top: 36px; background: #ef4444; color: #fff; }

/* Body */
.wl-body { padding: 14px 16px 16px; }

.wl-name {
    display: block;
    font-size: .9rem;
    font-weight: 700;
    color: #111827;
    text-decoration: none;
    line-height: 1.3;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color .2s;
    min-height: 2.4em;
}
.wl-name:hover { color: #6366f1; }

.wl-rating {
    display: flex;
    align-items: center;
    gap: 2px;
    margin-bottom: 8px;
}
.wl-rating i { color: #fbbf24; font-size: .78rem; }
.wl-rating span { font-size: .76rem; color: #9ca3af; margin-left: 4px; }

.wl-price { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
.wl-price-current { font-size: 1.05rem; font-weight: 800; color: #6366f1; }
.wl-price-old     { font-size: .8rem; color: #9ca3af; text-decoration: line-through; }

/* Buttons */
.wl-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 10px;
    font-size: .84rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .25s;
    text-align: center;
}
.wl-btn-cart     { background: linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; box-shadow:0 3px 10px rgba(99,102,241,.25); }
.wl-btn-cart:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,.35); }
.wl-btn-cart.btn-loading { opacity: .6; pointer-events: none; }
.wl-btn-variants { background: #eef2ff; color: #6366f1; border: 1.5px solid #c7d2fe; }
.wl-btn-variants:hover { background: #e0e7ff; color: #4f46e5; }
.wl-btn-notify   { background: #f3f4f6; color: #6b7280; }
.wl-btn-notify:hover { background: #e5e7eb; }

.js-cart-form { margin: 0; }

/* Empty state — hereda .ds-empty-state del layout */
</style>

<script>
(function () {
    'use strict';

    /* ── Toast ── */
    var wrap = document.createElement('div');
    wrap.id = 'wl-toast-container';
    document.body.appendChild(wrap);
    var ICONS = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };

    function toast(msg, type, ms) {
        var el = document.createElement('div');
        el.className = 'wl-toast t-' + (type || 'success');
        el.innerHTML = '<i class="bi ' + (ICONS[type] || ICONS.success) + '"></i><span>' + msg + '</span>';
        wrap.appendChild(el);
        setTimeout(function () { el.classList.add('leaving'); setTimeout(function () { el.parentNode && el.remove(); }, 300); }, ms || 3000);
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]'); if (m) return m.content;
        var i = document.querySelector('input[name="_token"]');    return i ? i.value : '';
    }
    function updateCartBadge(c) { if (c == null) return; var el = document.getElementById('cart-badge'); if (el) el.textContent = c; }
    function updateFavBadge(c)  { if (c == null) return; var el = document.getElementById('fav-badge');  if (el) el.textContent = c; }

    /* Eliminar favorito */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-fav-remove'); if (!btn) return;
        var pid = btn.dataset.productId, cid = btn.dataset.cardId;
        Swal.fire({ title:'¿Eliminar de favoritos?', icon:'warning', showCancelButton:true,
            confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar', confirmButtonColor:'#ef4444'
        }).then(function (r) {
            if (!r.isConfirmed) return;
            btn.classList.add('btn-loading');
            fetch('{{ route("web.favorites.store") }}', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},
                body: JSON.stringify({ product_id: pid })
            }).then(function(r){ return r.json(); }).then(function(data){
                if (data.status === 'removed' || data.added === false) {
                    var card = document.getElementById(cid);
                    if (card) {
                        card.classList.add('removing');
                        setTimeout(function(){
                            card.remove();
                            if (!document.querySelector('.wl-card')) {
                                document.querySelector('.wl-grid').innerHTML =
                                    '<div class="ds-empty-state"><i class="bi bi-heart"></i><h3>Tu lista de deseos está vacía</h3><p>Guarda los productos que te gusten aquí</p><a href="{{ route("web.index") }}" class="ds-btn-primary"><i class="bi bi-shop me-1"></i>Explorar Productos</a></div>';
                            }
                        }, 320);
                    }
                    toast('Eliminado de favoritos', 'info');
                    updateFavBadge(data.count);
                } else { btn.classList.remove('btn-loading'); toast('El producto se agregó de nuevo a favoritos','warning'); }
            }).catch(function(){ btn.classList.remove('btn-loading'); toast('Error al eliminar','error'); });
        });
    });

    /* Agregar todo */
    var addAllBtn = document.querySelector('.btn-add-all');
    if (addAllBtn) {
        addAllBtn.addEventListener('click', function () {
            var forms = document.querySelectorAll('.js-cart-form');
            var variantCount = document.querySelectorAll('.wl-btn-variants').length;
            if (!forms.length) { toast(variantCount ? 'Todos requieren seleccionar opciones' : 'No hay productos para agregar', 'warning', 4000); return; }
            addAllBtn.disabled = true; addAllBtn.textContent = 'Agregando…';
            var reqs = Array.from(forms).map(function(form){
                return fetch(form.action,{ method:'POST', headers:{'X-CSRF-TOKEN':csrf(),'Accept':'application/json'}, body:new FormData(form) })
                    .then(function(r){ return r.json(); }).catch(function(){ return { success:false }; });
            });
            Promise.all(reqs).then(function(results){
                addAllBtn.disabled = false; addAllBtn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Agregar Todo al Carrito';
                var added  = results.filter(function(d){ return d.success||d.status===200; }).length;
                var failed = results.filter(function(d){ return !d.success&&d.status!==200; }).length;
                var lastCount = null; results.forEach(function(d){ if(d.count!=null) lastCount=d.count; });
                updateCartBadge(lastCount);
                var parts = [];
                if (added)        parts.push(added + ' producto' + (added>1?'s':'') + ' agregado' + (added>1?'s':'') + ' ✓');
                if (variantCount) parts.push(variantCount + ' con variantes: selecciónalos manualmente');
                if (failed)       parts.push(failed + ' sin stock o con error');
                toast(parts.join(' · ') || 'No se pudo agregar ningún producto', (added&&!variantCount&&!failed)?'success':(added?'warning':'error'), 5000);
            });
        });
    }

    /* Agregar al carrito individual */
    document.addEventListener('submit', function (e) {
        var cartBtn = e.target.querySelector('.js-cart'); if (!cartBtn) return;
        e.preventDefault();
        var form = e.target, orig = cartBtn.innerHTML;
        cartBtn.classList.add('btn-loading'); cartBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        fetch(form.action,{ method:'POST', headers:{'X-CSRF-TOKEN':csrf(),'Accept':'application/json'}, body:new FormData(form) })
        .then(function(r){ return r.json(); }).then(function(data){
            cartBtn.classList.remove('btn-loading');
            if (data.status===200||data.success) {
                cartBtn.innerHTML = '<i class="bi bi-check-lg"></i> ¡Agregado!';
                toast(data.message||'Agregado al carrito', 'success');
                updateCartBadge(data.count);
                setTimeout(function(){ cartBtn.innerHTML = orig; }, 1600);
            } else { cartBtn.innerHTML = orig; toast(data.message||'No se pudo agregar','error'); }
        }).catch(function(){ cartBtn.classList.remove('btn-loading'); cartBtn.innerHTML = orig; toast('Error de conexión','error'); });
    });

})();
</script>

<style>
/* Toast */
#wl-toast-container { position:fixed; bottom:24px; right:24px; z-index:99999; display:flex; flex-direction:column; gap:10px; pointer-events:none; }
.wl-toast { display:flex; align-items:center; gap:12px; padding:14px 20px; border-radius:12px; font-size:.88rem; font-weight:500; min-width:240px; max-width:320px; pointer-events:all; color:#fff; box-shadow:0 8px 30px rgba(0,0,0,.18); animation:wlIn .35s cubic-bezier(.34,1.56,.64,1) forwards; }
.wl-toast.t-success { background:#1e1b4b; border-left:4px solid #4ade80; }
.wl-toast.t-error   { background:#1e1b4b; border-left:4px solid #f87171; }
.wl-toast.t-warning { background:#1e1b4b; border-left:4px solid #fbbf24; }
.wl-toast.t-info    { background:#1e1b4b; border-left:4px solid #60a5fa; }
.wl-toast i { font-size:1.1rem; flex-shrink:0; }
.wl-toast.t-success i { color:#4ade80; } .wl-toast.t-error i { color:#f87171; } .wl-toast.t-warning i { color:#fbbf24; } .wl-toast.t-info i { color:#60a5fa; }
.wl-toast.leaving { animation:wlOut .25s ease forwards; }
@keyframes wlIn  { from{opacity:0;transform:translateY(16px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes wlOut { from{opacity:1;transform:translateY(0) scale(1)} to{opacity:0;transform:translateY(8px) scale(.95)} }
</style>

@endsection