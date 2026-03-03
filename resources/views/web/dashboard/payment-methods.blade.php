@extends('web.dashboard.layout')

@section('dashboard-content')

<div class="section-header" data-aos="fade-up">
    <h2>Métodos de Pago</h2>
    <div class="header-actions">
        <button type="button" class="ds-btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="bi bi-plus-lg me-1"></i> Agregar Tarjeta
        </button>
    </div>
</div>

<div class="pm-grid">
    @forelse($paymentMethods as $method)
    <div class="pm-card {{ $method->is_default ? 'pm-default' : '' }}" data-aos="fade-up">

        <div class="pm-chip-row">
            <span class="pm-chip"></span>
            <span class="pm-chip"></span>
        </div>

        <div class="pm-number">•••• •••• •••• {{ $method->last_four }}</div>

        <div class="pm-meta">
            <div>
                <div class="pm-meta-label">Vence</div>
                <div class="pm-meta-val">{{ $method->expiry_month }}/{{ $method->expiry_year }}</div>
            </div>
            <div class="text-end">
                <div class="pm-meta-label">Tipo</div>
                <div class="pm-meta-val">{{ $method->type }}</div>
            </div>
        </div>

        @if($method->is_default)
            <span class="pm-default-badge"><i class="bi bi-check-circle-fill me-1"></i> Predeterminada</span>
        @endif

        <div class="pm-actions">
            <button type="button" class="pm-btn pm-btn-edit">
                <i class="bi bi-pencil"></i> Editar
            </button>
            <button type="button" class="pm-btn pm-btn-remove">
                <i class="bi bi-trash"></i> Eliminar
            </button>
            @if(!$method->is_default)
            <button type="button" class="pm-btn pm-btn-set-default" style="flex:0 0 100%">
                <i class="bi bi-star"></i> Hacer predeterminada
            </button>
            @endif
        </div>
    </div>
    @empty
    <div class="ds-empty-state">
        <i class="bi bi-credit-card"></i>
        <h3>No tienes tarjetas guardadas</h3>
        <p>Agrega una tarjeta para hacer tus compras más rápidas</p>
        <button type="button" class="ds-btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="bi bi-plus-lg me-1"></i> Agregar Tarjeta
        </button>
    </div>
    @endforelse
</div>

<!-- Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ds-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-credit-card-fill me-2 text-indigo"></i>Agregar Método de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="mb-3">
                        <label class="ds-label">Nombre en la Tarjeta</label>
                        <input type="text" class="ds-input" placeholder="Ej: Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label class="ds-label">Número de Tarjeta</label>
                        <input type="text" class="ds-input" maxlength="19" placeholder="•••• •••• •••• ••••" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-7">
                            <label class="ds-label">Fecha de Expiración</label>
                            <input type="text" class="ds-input" placeholder="MM/AA" required>
                        </div>
                        <div class="col-5">
                            <label class="ds-label">CVV</label>
                            <input type="text" class="ds-input" maxlength="3" placeholder="•••" required>
                        </div>
                    </div>
                    <div class="ds-check">
                        <input class="ds-check-input" type="checkbox" id="setDefault">
                        <label for="setDefault">Establecer como predeterminada</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="ds-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="ds-btn-primary">Guardar Tarjeta</button>
            </div>
        </div>
    </div>
</div>

<style>
.pm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 20px; }

/* Tarjeta estilo crédito */
.pm-card {
    background: linear-gradient(135deg, #312e81 0%, #4338ca 60%, #6366f1 100%);
    border-radius: 18px;
    padding: 24px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(99,102,241,.3);
    transition: transform .3s, box-shadow .3s;
}
.pm-card::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,.07);
    pointer-events: none;
}
.pm-card:hover { transform: translateY(-4px); box-shadow: 0 14px 36px rgba(99,102,241,.4); }
.pm-default { background: linear-gradient(135deg, #1e1b4b 0%, #7c3aed 60%, #a855f7 100%); }

.pm-chip-row { display: flex; gap: 6px; margin-bottom: 24px; }
.pm-chip {
    width: 34px; height: 26px;
    background: rgba(255,255,255,.25);
    border-radius: 4px;
    border: 1px solid rgba(255,255,255,.2);
}

.pm-number {
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: .18em;
    margin-bottom: 20px;
    font-family: 'Courier New', monospace;
}

.pm-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
}
.pm-meta-label { font-size: .65rem; text-transform: uppercase; letter-spacing: .1em; opacity: .6; margin-bottom: 2px; }
.pm-meta-val   { font-size: .88rem; font-weight: 700; }

.pm-default-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 20px;
    padding: 3px 12px;
    font-size: .72rem;
    font-weight: 700;
    margin-bottom: 14px;
}

.pm-actions { display: flex; gap: 8px; flex-wrap: wrap; position: relative; z-index: 1; }
.pm-btn {
    flex: 1;
    padding: 8px 10px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    border-radius: 8px;
    font-size: .78rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    transition: background .2s;
    backdrop-filter: blur(6px);
}
.pm-btn:hover { background: rgba(255,255,255,.27); }
.pm-btn-remove:hover { background: rgba(239,68,68,.4); border-color: rgba(239,68,68,.5); }
</style>
@endsection