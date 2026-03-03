@extends('web.dashboard.layout')

@section('dashboard-content')

<div class="section-header" data-aos="fade-up">
    <h2>Configuración</h2>
</div>

<div class="st-content">

    {{-- Información Personal --}}
    <div class="st-section" data-aos="fade-up">
        <div class="st-section-head">
            <div class="st-section-icon"><i class="bi bi-person-fill"></i></div>
            <div>
                <h3>Información Personal</h3>
                <p>Actualiza tu nombre, email y teléfono</p>
            </div>
        </div>
        <form action="{{ route('web.dashboard.settings.update') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="ds-label">Nombre</label>
                    <input type="text" class="ds-input" name="first_name"
                           value="{{ explode(' ', $user->name)[0] }}" required>
                </div>
                <div class="col-md-6">
                    <label class="ds-label">Apellido</label>
                    <input type="text" class="ds-input" name="last_name"
                           value="{{ explode(' ', $user->name)[1] ?? '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="ds-label">Email</label>
                    <div class="ds-input-icon-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" class="ds-input ds-input-icon" name="email"
                               value="{{ $user->email }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="ds-label">Teléfono</label>
                    <div class="ds-input-icon-wrap">
                        <i class="bi bi-telephone"></i>
                        <input type="tel" class="ds-input ds-input-icon" name="phone"
                               value="{{ $user->phone ?? '' }}">
                    </div>
                </div>
            </div>
            <div class="st-form-foot">
                <button type="submit" class="ds-btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Preferencias de email --}}
    <div class="st-section" data-aos="fade-up" data-aos-delay="100">
        <div class="st-section-head">
            <div class="st-section-icon"><i class="bi bi-bell-fill"></i></div>
            <div>
                <h3>Notificaciones</h3>
                <p>Controla qué correos quieres recibir</p>
            </div>
        </div>
        <div class="prefs-list">
            @php
                $prefs = [
                    ['id'=>'orderUpdates', 'label'=>'Actualizaciones de órdenes',   'desc'=>'Estado y seguimiento de tus pedidos', 'checked'=>true,  'icon'=>'bi-box-seam'],
                    ['id'=>'promotions',   'label'=>'Promociones y ofertas',         'desc'=>'Descuentos y ofertas especiales',      'checked'=>false, 'icon'=>'bi-tag-fill'],
                    ['id'=>'newsletter',   'label'=>'Boletín informativo',            'desc'=>'Novedades y contenido semanal',        'checked'=>true,  'icon'=>'bi-newspaper'],
                    ['id'=>'recommendations','label'=>'Recomendaciones personalizadas','desc'=>'Sugerencias basadas en tus compras', 'checked'=>false, 'icon'=>'bi-stars'],
                ];
            @endphp
            @foreach($prefs as $p)
            <div class="pref-item">
                <div class="pref-icon">
                    <i class="bi {{ $p['icon'] }}"></i>
                </div>
                <div class="pref-info">
                    <h4>{{ $p['label'] }}</h4>
                    <p>{{ $p['desc'] }}</p>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="{{ $p['id'] }}" {{ $p['checked'] ? 'checked' : '' }}>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Seguridad --}}
    <div class="st-section" data-aos="fade-up" data-aos-delay="200">
        <div class="st-section-head">
            <div class="st-section-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div>
                <h3>Seguridad</h3>
                <p>Actualiza tu contraseña</p>
            </div>
        </div>
        <form action="{{ route('web.dashboard.settings.password') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="ds-label">Contraseña Actual</label>
                    <input type="password" class="ds-input" name="current_password" required>
                </div>
                <div class="col-md-6">
                    <label class="ds-label">Nueva Contraseña</label>
                    <input type="password" class="ds-input" name="password" id="newPwd" required>
                </div>
                <div class="col-md-6">
                    <label class="ds-label">Confirmar Contraseña</label>
                    <input type="password" class="ds-input" name="password_confirmation" required>
                </div>
            </div>

            <div class="pwd-req">
                <span class="pwd-req-title"><i class="bi bi-shield-check me-1"></i> Requisitos</span>
                <div class="pwd-req-list">
                    <span class="req-item" id="req-len"><i class="bi bi-circle me-1"></i> 8 caracteres mínimo</span>
                    <span class="req-item" id="req-upper"><i class="bi bi-circle me-1"></i> Una mayúscula</span>
                    <span class="req-item" id="req-num"><i class="bi bi-circle me-1"></i> Un número</span>
                    <span class="req-item" id="req-special"><i class="bi bi-circle me-1"></i> Un carácter especial</span>
                </div>
            </div>

            <div class="st-form-foot">
                <button type="submit" class="ds-btn-primary">
                    <i class="bi bi-lock-fill me-1"></i> Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

    {{-- Zona de peligro --}}
    <div class="st-section st-danger-zone" data-aos="fade-up" data-aos-delay="300">
        <div class="st-section-head">
            <div class="st-section-icon st-icon-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <h3>Zona de Peligro</h3>
                <p>Acciones irreversibles sobre tu cuenta</p>
            </div>
        </div>
        <div class="danger-warn">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Eliminar cuenta permanentemente</strong>
                <p>Una vez eliminada, todos tus datos, órdenes e historial desaparecerán para siempre.</p>
            </div>
        </div>
        <button type="button" class="st-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="bi bi-trash3 me-1"></i> Eliminar mi cuenta
        </button>
    </div>

</div>

{{-- Modal eliminar cuenta --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ds-modal">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#b91c1c;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>¿Eliminar cuenta?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="danger-warn mb-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <p>Esta acción es <strong>permanente e irreversible</strong>. Todos tus datos serán eliminados.</p>
                </div>
                <label class="ds-label">Para confirmar, escribe <strong>ELIMINAR</strong>:</label>
                <input type="text" class="ds-input" id="confirmDeleteInput" placeholder="ELIMINAR">
            </div>
            <div class="modal-footer">
                <button type="button" class="ds-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="st-btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="bi bi-trash3 me-1"></i> Eliminar Cuenta
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ── Settings layout ── */
.st-content { display: flex; flex-direction: column; gap: 20px; }

.st-section {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 8px rgba(0,0,0,.04);
}

.st-section-head {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px 22px;
    background: #f8f9fb;
    border-bottom: 1px solid #eef0f3;
}
.st-section-head > div:last-child { flex: 1; }
.st-section-head h3 { font-size: .97rem; font-weight: 700; color: #111827; margin: 0 0 2px; }
.st-section-head p  { font-size: .8rem; color: #9ca3af; margin: 0; }

.st-section-icon {
    flex-shrink: 0;
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: .95rem;
}
.st-icon-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

/* Form inside section */
.st-section form { padding: 22px; }
.st-form-foot { margin-top: 20px; display: flex; justify-content: flex-end; }

/* Preferences */
.prefs-list { padding: 14px 22px 20px; display: flex; flex-direction: column; gap: 4px; }
.pref-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    border-radius: 12px;
    transition: background .2s;
}
.pref-item:hover { background: #f8f9fb; }
.pref-icon {
    flex-shrink: 0;
    width: 36px; height: 36px;
    border-radius: 9px;
    background: #eef2ff;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
    color: #6366f1;
}
.pref-info { flex: 1; }
.pref-info h4 { font-size: .88rem; font-weight: 600; color: #111827; margin: 0 0 2px; }
.pref-info p  { font-size: .78rem; color: #9ca3af; margin: 0; }
.form-check-input { width: 2.6rem; height: 1.35rem; cursor: pointer; }
.form-check-input:checked { background-color: #6366f1; border-color: #6366f1; }

/* Password requirements */
.pwd-req {
    background: #f8f9fb;
    border: 1px solid #eef0f3;
    border-left: 4px solid #6366f1;
    border-radius: 0 10px 10px 0;
    padding: 12px 16px;
    margin-top: 14px;
}
.pwd-req-title { font-size: .78rem; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: .05em; }
.pwd-req-list { display: flex; flex-wrap: wrap; gap: 6px 16px; margin-top: 8px; }
.req-item { font-size: .78rem; color: #9ca3af; display: inline-flex; align-items: center; transition: color .2s; }
.req-item.ok { color: #15803d; }
.req-item.ok i { color: #22c55e; }

/* Danger zone */
.st-danger-zone { border-color: #fecaca; }
.st-danger-zone .st-section-head { background: #fff5f5; border-bottom-color: #fecaca; }

.danger-warn {
    display: flex;
    gap: 12px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 10px;
    padding: 14px 16px;
    margin: 18px 22px 0;
    font-size: .86rem;
    color: #92400e;
}
.danger-warn i { font-size: 1.1rem; color: #f59e0b; flex-shrink: 0; margin-top: 1px; }
.danger-warn p  { margin: 0; }

.st-btn-danger {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 14px 22px 20px;
    padding: 10px 20px;
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    color: #b91c1c;
    border-radius: 10px;
    font-size: .88rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
}
.st-btn-danger:hover { background: #fee2e2; border-color: #f87171; color: #991b1b; }
.st-btn-danger:disabled { opacity: .45; pointer-events: none; }
</style>

<script>
/* Password strength checker */
var newPwd = document.getElementById('newPwd');
if (newPwd) {
    newPwd.addEventListener('input', function() {
        var v = this.value;
        set('req-len',     v.length >= 8);
        set('req-upper',   /[A-Z]/.test(v));
        set('req-num',     /[0-9]/.test(v));
        set('req-special', /[^A-Za-z0-9]/.test(v));
    });
    function set(id, ok) {
        var el = document.getElementById(id); if (!el) return;
        el.classList.toggle('ok', ok);
        el.querySelector('i').className = 'bi me-1 bi-' + (ok ? 'check-circle-fill' : 'circle');
    }
}

/* Confirm delete */
var confirmInput = document.getElementById('confirmDeleteInput');
var confirmBtn   = document.getElementById('confirmDeleteBtn');
if (confirmInput) {
    confirmInput.addEventListener('input', function() {
        confirmBtn.disabled = this.value !== 'ELIMINAR';
    });
}
</script>

@endsection