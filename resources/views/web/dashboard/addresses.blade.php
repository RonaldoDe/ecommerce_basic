@extends('web.dashboard.layout')

@section('dashboard-content')

<div class="section-header" data-aos="fade-up">
    <h2>Mis Direcciones</h2>
    <div class="header-actions">
        <button type="button" class="ds-btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="bi bi-plus-lg me-1"></i> Nueva Dirección
        </button>
    </div>
</div>

<div class="addr-grid">
    @forelse($addresses as $address)
    <div class="addr-card {{ $address->is_default ? 'addr-default' : '' }}" data-aos="fade-up">

        <div class="addr-card-head">
            <div class="addr-icon">
                <i class="bi bi-{{ $address->is_default ? 'star-fill' : 'geo-alt-fill' }}"></i>
            </div>
            <div class="addr-title-group">
                <h4>{{ $address->label ?? 'Dirección' }}</h4>
                @if($address->is_default)
                    <span class="addr-default-badge">
                        <i class="bi bi-check-circle-fill me-1"></i>Predeterminada
                    </span>
                @endif
            </div>
        </div>

        <div class="addr-body">
            <p class="addr-line1">{{ $address->address_line_1 }}</p>
            @if($address->address_line_2)
                <p class="addr-line2">{{ $address->address_line_2 }}</p>
            @endif

            <div class="addr-details">
                <span><i class="bi bi-geo me-1"></i>{{ $address->city }}@if($address->state), {{ $address->state }}@endif @if($address->postal_code) – {{ $address->postal_code }}@endif</span>
                <span><i class="bi bi-flag me-1"></i>{{ $address->country }}</span>
            </div>

            @if($address->reference)
                <div class="addr-ref">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>{{ $address->reference }}</span>
                </div>
            @endif

            <div class="addr-contact">
                @if($address->recipient_name)
                    <span><i class="bi bi-person-fill me-1"></i>{{ $address->recipient_name }}</span>
                @endif
                @if($address->phone)
                    <span><i class="bi bi-telephone-fill me-1"></i>{{ $address->phone }}</span>
                @endif
            </div>
        </div>

        <div class="addr-actions">
            <button class="ds-btn-ghost btn-sm" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">
                <i class="bi bi-pencil me-1"></i> Editar
            </button>
            <form method="POST" action="{{ route('web.addresses.destroy', $address->id) }}" class="d-inline">
                @csrf @method('DELETE')
                <button type="button" class="ds-btn-danger-ghost btn-sm" onclick="confirmDelete(this.form)">
                    <i class="bi bi-trash me-1"></i> Eliminar
                </button>
            </form>
            @if(!$address->is_default)
            <form method="POST" action="{{ route('web.addresses.setDefault', $address) }}" class="d-inline">
                @csrf
                <button class="ds-btn-ghost btn-sm addr-set-default">
                    <i class="bi bi-star me-1"></i> Predeterminar
                </button>
            </form>
            @endif
        </div>

        {{-- Modal editar --}}
        <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content ds-modal">
                    <form method="POST" action="{{ route('web.addresses.update', $address->id) }}">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-pencil me-2 text-indigo"></i>Editar Dirección</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="ds-label">Etiqueta</label>
                                    <input type="text" name="label" class="ds-input" value="{{ $address->label }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="ds-label">Teléfono</label>
                                    <input type="text" name="phone" class="ds-input" value="{{ $address->phone }}">
                                </div>
                                <div class="col-12">
                                    <label class="ds-label">Dirección <span class="text-danger">*</span></label>
                                    <input type="text" name="address_line_1" class="ds-input" value="{{ $address->address_line_1 }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="ds-label">Apto / Piso / Oficina</label>
                                    <input type="text" name="address_line_2" class="ds-input" value="{{ $address->address_line_2 }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="ds-label">Ciudad <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="ds-input" value="{{ $address->city }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="ds-label">Departamento / Estado</label>
                                    <input type="text" name="state" class="ds-input" value="{{ $address->state }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="ds-label">País</label>
                                    <select name="country" class="ds-input">
                                        <option value="CO" @selected($address->country==='CO')>Colombia</option>
                                        <option value="MX" @selected($address->country==='MX')>México</option>
                                        <option value="US" @selected($address->country==='US')>Estados Unidos</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="ds-label">Código Postal</label>
                                    <input type="text" name="postal_code" class="ds-input" value="{{ $address->postal_code }}">
                                </div>
                                <div class="col-12">
                                    <label class="ds-label">Referencia adicional</label>
                                    <textarea name="reference" class="ds-input" rows="2">{{ $address->reference }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="ds-check">
                                        <input class="ds-check-input" type="checkbox" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }}>
                                        <label>Establecer como predeterminada</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="ds-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="ds-btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="ds-empty-state">
        <i class="bi bi-geo-alt"></i>
        <h3>No tienes direcciones guardadas</h3>
        <p>Agrega una dirección para agilizar tus envíos</p>
        <button type="button" class="ds-btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="bi bi-plus-lg me-1"></i> Agregar Dirección
        </button>
    </div>
    @endforelse
</div>

{{-- Modal Agregar --}}
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content ds-modal">
            <form method="POST" action="{{ route('web.addresses.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-geo-alt-fill me-2 text-indigo"></i>Nueva Dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="ds-label">Etiqueta</label>
                            <input type="text" name="label" class="ds-input" placeholder="Casa, Oficina…">
                        </div>
                        <div class="col-md-6">
                            <label class="ds-label">Teléfono</label>
                            <input type="text" name="phone" class="ds-input">
                        </div>
                        <div class="col-12">
                            <label class="ds-label">Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="address_line_1" class="ds-input" required>
                        </div>
                        <div class="col-12">
                            <label class="ds-label">Apto / Piso / Oficina</label>
                            <input type="text" name="address_line_2" class="ds-input">
                        </div>
                        <div class="col-md-6">
                            <label class="ds-label">Ciudad <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="ds-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="ds-label">Departamento / Estado</label>
                            <input type="text" name="state" class="ds-input">
                        </div>
                        <div class="col-md-6">
                            <label class="ds-label">País</label>
                            <select name="country" class="ds-input">
                                <option value="CO">Colombia</option>
                                <option value="MX">México</option>
                                <option value="US">Estados Unidos</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="ds-label">Código Postal</label>
                            <input type="text" name="postal_code" class="ds-input">
                        </div>
                        <div class="col-12">
                            <label class="ds-label">Referencia adicional</label>
                            <textarea name="reference" class="ds-input" rows="2" placeholder="Ej: Portón azul, al lado del parque…"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="ds-check">
                                <input class="ds-check-input" type="checkbox" name="is_default" value="1">
                                <label>Establecer como predeterminada</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ds-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ds-btn-primary">Guardar Dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.addr-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 18px; }

.addr-card {
    background: #fff;
    border: 1.5px solid #eef0f3;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    transition: border-color .25s, box-shadow .25s;
}
.addr-card:hover { border-color: #c7d2fe; box-shadow: 0 6px 22px rgba(99,102,241,.1); }
.addr-default { border-color: #6366f1; box-shadow: 0 4px 18px rgba(99,102,241,.15); }

.addr-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 18px;
    background: #f8f9fb;
    border-bottom: 1px solid #eef0f3;
}
.addr-default .addr-card-head { background: #eef2ff; }

.addr-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 1rem;
    flex-shrink: 0;
}

.addr-title-group h4 { font-size: .95rem; font-weight: 700; color: #111827; margin: 0 0 3px; }

.addr-default-badge {
    display: inline-flex;
    align-items: center;
    font-size: .7rem;
    font-weight: 700;
    background: #6366f1;
    color: #fff;
    padding: 2px 10px;
    border-radius: 20px;
}

.addr-body { padding: 16px 18px; }
.addr-line1 { font-size: .9rem; font-weight: 600; color: #111827; margin: 0 0 2px; }
.addr-line2 { font-size: .84rem; color: #6b7280; margin: 0 0 10px; }

.addr-details { display: flex; flex-direction: column; gap: 3px; margin-bottom: 10px; }
.addr-details span { font-size: .82rem; color: #6b7280; display: flex; align-items: center; }
.addr-details i { color: #a5b4fc; }

.addr-ref {
    display: flex;
    gap: 7px;
    background: #f0f7ff;
    border-left: 3px solid #6366f1;
    border-radius: 0 8px 8px 0;
    padding: 8px 12px;
    font-size: .8rem;
    color: #3730a3;
    margin-bottom: 10px;
}
.addr-ref i { flex-shrink: 0; margin-top: 1px; }

.addr-contact { display: flex; flex-direction: column; gap: 4px; }
.addr-contact span { font-size: .8rem; color: #6b7280; display: flex; align-items: center; gap: 3px; }
.addr-contact i { color: #a5b4fc; }

.addr-actions {
    display: flex;
    gap: 8px;
    padding: 12px 18px;
    border-top: 1px solid #f0f0f2;
    background: #fafafa;
    flex-wrap: wrap;
}

.addr-set-default { color: #d97706; border-color: #fde68a; }
.addr-set-default:hover { background: #fffbeb; border-color: #fbbf24; }
</style>

<script>
function confirmDelete(form) {
    Swal.fire({ title:'¿Eliminar esta dirección?', icon:'warning', showCancelButton:true,
        confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar', confirmButtonColor:'#ef4444'
    }).then(function(r){ if(r.isConfirmed) form.submit(); });
}
</script>

@endsection