@extends('layouts.admin')

@section('content')

<div class="page-heading d-flex align-items-center gap-3 mb-4">
  <div class="ab-ad-icon">
    <i class="bi bi-building"></i>
  </div>
  <div>
    <h3 class="mb-0">Página «Nosotros»</h3>
    <p class="text-muted mb-0" style="font-size:.83rem">Gestiona el contenido de la página pública de presentación</p>
  </div>
</div>

{{-- Tabs de navegación --}}
<ul class="nav nav-tabs ab-ad-tabs mb-4" id="aboutTabs" role="tablist">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-hero"    type="button"><i class="bi bi-image me-1"></i> Hero</button></li>
  <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-about"   type="button"><i class="bi bi-info-circle me-1"></i> Contenido</button></li>
  <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-stats"   type="button"><i class="bi bi-bar-chart me-1"></i> Estadísticas</button></li>
  <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-why"     type="button"><i class="bi bi-patch-check me-1"></i> ¿Por qué?</button></li>
  <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-timeline"type="button"><i class="bi bi-clock-history me-1"></i> Historia</button></li>
  <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-team"    type="button"><i class="bi bi-people me-1"></i> Equipo</button></li>
  <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-cta"     type="button"><i class="bi bi-megaphone me-1"></i> CTA</button></li>
</ul>

<form action="{{ route('admin.pages.about.update') }}" method="POST" enctype="multipart/form-data">
  @csrf @method('PUT')

  <div class="tab-content">

    {{-- ══ HERO ═══════════════════════════════════════════════ --}}
    <div class="tab-pane fade show active" id="tab-hero">
      <div class="ab-ad-card">
        <div class="ab-ad-card-head"><i class="bi bi-image me-2"></i>Sección Hero</div>
        <div class="ab-ad-card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="ab-label">Etiqueta (badge)</label>
              <input type="text" name="hero_label" class="form-control" value="{{ old('hero_label', $about->hero_label) }}" placeholder="Quiénes Somos">
            </div>
            <div class="col-md-6">
              <label class="ab-label">Título principal</label>
              <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $about->hero_title) }}" placeholder="Nuestra Historia">
            </div>
            <div class="col-12">
              <label class="ab-label">Subtítulo</label>
              <textarea name="hero_subtitle" class="form-control" rows="2" placeholder="Descripción breve...">{{ old('hero_subtitle', $about->hero_subtitle) }}</textarea>
            </div>
            <div class="col-12">
              <label class="ab-label">Imagen de fondo <span class="text-muted">(opcional — si no hay, se usa el gradiente indigo)</span></label>
              <div class="ab-img-upload" id="heroImgWrap">
                @if($about->hero_image)
                  <img src="{{ asset('storage/'.$about->hero_image) }}" class="ab-img-preview" id="heroPreview">
                @else
                  <div class="ab-img-placeholder-sm" id="heroPreview">
                    <i class="bi bi-image"></i><span>Sin imagen</span>
                  </div>
                @endif
                <label class="ab-img-btn" for="heroImageInput"><i class="bi bi-upload me-1"></i>Subir imagen</label>
                <input type="file" id="heroImageInput" name="hero_image" accept="image/*" class="d-none" onchange="previewImg(this,'heroPreview')">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ CONTENIDO ══════════════════════════════════════════ --}}
    <div class="tab-pane fade" id="tab-about">
      <div class="ab-ad-card">
        <div class="ab-ad-card-head"><i class="bi bi-file-text me-2"></i>Sección "Sobre nosotros"</div>
        <div class="ab-ad-card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="ab-label">Título de sección</label>
              <input type="text" name="about_title" class="form-control" value="{{ old('about_title', $about->about_title) }}">
            </div>
            <div class="col-12">
              <label class="ab-label">Descripción (párrafo 1)</label>
              <textarea name="about_description" class="form-control" rows="4">{{ old('about_description', $about->about_description) }}</textarea>
            </div>
            <div class="col-12">
              <label class="ab-label">Descripción (párrafo 2)</label>
              <textarea name="about_description_2" class="form-control" rows="4">{{ old('about_description_2', $about->about_description_2) }}</textarea>
            </div>
            <div class="col-12">
              <label class="ab-label">Imagen de la sección</label>
              <div class="ab-img-upload">
                @if($about->about_image)
                  <img src="{{ asset('storage/'.$about->about_image) }}" class="ab-img-preview" id="aboutPreview">
                @else
                  <div class="ab-img-placeholder-sm" id="aboutPreview">
                    <i class="bi bi-image"></i><span>Sin imagen</span>
                  </div>
                @endif
                <label class="ab-img-btn" for="aboutImageInput"><i class="bi bi-upload me-1"></i>Subir imagen</label>
                <input type="file" id="aboutImageInput" name="about_image" accept="image/*" class="d-none" onchange="previewImg(this,'aboutPreview')">
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- MVV --}}
      <div class="ab-ad-card mt-3">
        <div class="ab-ad-card-head"><i class="bi bi-layers me-2"></i>Misión / Visión / Valores</div>
        <div class="ab-ad-card-body">
          <div class="row g-3">
            @foreach([
              ['mission','Misión','bi-bullseye'],
              ['vision','Visión','bi-eye'],
              ['values','Valores','bi-heart'],
            ] as [$key, $label, $icon])
            <div class="col-md-4">
              <div class="ab-mvv-block">
                <div class="ab-mvv-ico"><i class="bi {{ $icon }}"></i></div>
                <label class="ab-label">Título</label>
                <input type="text" name="{{ $key }}_title" class="form-control mb-2"
                       value="{{ old("{$key}_title", $about->{"{$key}_title"}) }}">
                <label class="ab-label">Texto</label>
                <textarea name="{{ $key }}_text" class="form-control" rows="3">{{ old("{$key}_text", $about->{"{$key}_text"}) }}</textarea>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ══ ESTADÍSTICAS ════════════════════════════════════════ --}}
    <div class="tab-pane fade" id="tab-stats">
      <div class="ab-ad-card">
        <div class="ab-ad-card-head d-flex justify-content-between align-items-center">
          <span><i class="bi bi-bar-chart me-2"></i>Estadísticas (máx. 4)</span>
          <button type="button" class="ab-add-btn" onclick="addStat()"><i class="bi bi-plus me-1"></i>Agregar</button>
        </div>
        <div class="ab-ad-card-body">
          <p class="text-muted small mb-3">Usa íconos de <a href="https://icons.getbootstrap.com" target="_blank">Bootstrap Icons</a> (ej: <code>bi-people-fill</code>)</p>
          <div id="statsContainer">
            @foreach($about->stats ?? [] as $i => $stat)
            <div class="ab-repeat-row">
              <div class="row g-2 align-items-end">
                <div class="col-md-4">
                  <label class="ab-label">Ícono BI</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi {{ $stat['icon'] ?? 'bi-star' }}" id="statIconPrev{{ $i }}"></i></span>
                    <input type="text" name="stat_icon[]" class="form-control"
                           value="{{ $stat['icon'] ?? '' }}"
                           placeholder="bi-people-fill"
                           oninput="updateIcon(this,'statIconPrev{{ $i }}')">
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="ab-label">Valor</label>
                  <input type="text" name="stat_value[]" class="form-control" value="{{ $stat['value'] ?? '' }}" placeholder="10,000+">
                </div>
                <div class="col-md-4">
                  <label class="ab-label">Etiqueta</label>
                  <input type="text" name="stat_label[]" class="form-control" value="{{ $stat['label'] ?? '' }}" placeholder="Clientes felices">
                </div>
                <div class="col-md-1">
                  <button type="button" class="ab-del-btn" onclick="this.closest('.ab-repeat-row').remove()">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ══ ¿POR QUÉ? ══════════════════════════════════════════ --}}
    <div class="tab-pane fade" id="tab-why">
      <div class="ab-ad-card">
        <div class="ab-ad-card-head d-flex justify-content-between align-items-center">
          <span><i class="bi bi-patch-check me-2"></i>¿Por qué elegirnos?</span>
          <button type="button" class="ab-add-btn" onclick="addWhy()"><i class="bi bi-plus me-1"></i>Agregar</button>
        </div>
        <div class="ab-ad-card-body">
          <div id="whyContainer">
            @foreach($about->why_us ?? [] as $i => $why)
            <div class="ab-repeat-row">
              <div class="row g-2 align-items-end">
                <div class="col-md-3">
                  <label class="ab-label">Ícono BI</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi {{ $why['icon'] ?? 'bi-check' }}" id="whyIconPrev{{ $i }}"></i></span>
                    <input type="text" name="why_icon[]" class="form-control"
                           value="{{ $why['icon'] ?? '' }}"
                           placeholder="bi-shield-check"
                           oninput="updateIcon(this,'whyIconPrev{{ $i }}')">
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="ab-label">Título</label>
                  <input type="text" name="why_title[]" class="form-control" value="{{ $why['title'] ?? '' }}">
                </div>
                <div class="col-md-5">
                  <label class="ab-label">Descripción</label>
                  <input type="text" name="why_desc[]" class="form-control" value="{{ $why['description'] ?? '' }}">
                </div>
                <div class="col-md-1">
                  <button type="button" class="ab-del-btn" onclick="this.closest('.ab-repeat-row').remove()">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ══ TIMELINE ════════════════════════════════════════════ --}}
    <div class="tab-pane fade" id="tab-timeline">
      <div class="ab-ad-card">
        <div class="ab-ad-card-head d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clock-history me-2"></i>Línea de tiempo / Historia</span>
          <button type="button" class="ab-add-btn" onclick="addTimeline()"><i class="bi bi-plus me-1"></i>Agregar evento</button>
        </div>
        <div class="ab-ad-card-body">
          <div id="timelineContainer">
            @foreach($about->timeline ?? [] as $i => $ev)
            <div class="ab-repeat-row">
              <div class="row g-2 align-items-end">
                <div class="col-md-2">
                  <label class="ab-label">Año</label>
                  <input type="text" name="tl_year[]" class="form-control" value="{{ $ev['year'] ?? '' }}" placeholder="2020">
                </div>
                <div class="col-md-3">
                  <label class="ab-label">Título</label>
                  <input type="text" name="tl_title[]" class="form-control" value="{{ $ev['title'] ?? '' }}">
                </div>
                <div class="col-md-6">
                  <label class="ab-label">Descripción</label>
                  <input type="text" name="tl_desc[]" class="form-control" value="{{ $ev['description'] ?? '' }}">
                </div>
                <div class="col-md-1">
                  <button type="button" class="ab-del-btn" onclick="this.closest('.ab-repeat-row').remove()">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ══ EQUIPO ══════════════════════════════════════════════ --}}
    <div class="tab-pane fade" id="tab-team">
      <div class="ab-ad-card">
        <div class="ab-ad-card-head d-flex justify-content-between align-items-center">
          <span><i class="bi bi-people me-2"></i>Miembros del equipo</span>
          <button type="button" class="ab-add-btn" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="bi bi-plus me-1"></i>Agregar miembro
          </button>
        </div>
        <div class="ab-ad-card-body">
          @forelse($team as $member)
          <div class="ab-member-row">
            <div class="ab-member-photo">
              @if($member->photo)
                <img src="{{ asset('storage/'.$member->photo) }}" alt="{{ $member->name }}">
              @else
                <div class="ab-member-initials">{{ strtoupper(substr($member->name,0,2)) }}</div>
              @endif
            </div>
            <div class="ab-member-info">
              <strong>{{ $member->name }}</strong>
              <span>{{ $member->role }}</span>
              <div class="ab-member-badges">
                @if($member->active)
                  <span class="badge bg-success">Activo</span>
                @else
                  <span class="badge bg-secondary">Inactivo</span>
                @endif
                <span class="badge bg-light text-dark">Orden: {{ $member->order }}</span>
              </div>
            </div>
            <div class="ab-member-actions">
              <button type="button" class="ab-icon-btn ab-icon-edit"
                      onclick="openEditMember({{ $member->id }}, {{ $member->toJson() }})">
                <i class="bi bi-pencil"></i>
              </button>
              <form action="{{ route('admin.pages.about.members.destroy', $member) }}" method="POST"
                    onsubmit="return confirm('¿Eliminar este miembro?')">
                @csrf @method('DELETE')
                <button type="submit" class="ab-icon-btn ab-icon-del"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </div>
          @empty
            <p class="text-muted text-center py-3">No hay miembros en el equipo todavía.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- ══ CTA ══════════════════════════════════════════════════ --}}
    <div class="tab-pane fade" id="tab-cta">
      <div class="ab-ad-card">
        <div class="ab-ad-card-head"><i class="bi bi-megaphone me-2"></i>Llamada a la acción (CTA)</div>
        <div class="ab-ad-card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="ab-label">Título</label>
              <input type="text" name="cta_title" class="form-control" value="{{ old('cta_title', $about->cta_title) }}">
            </div>
            <div class="col-12">
              <label class="ab-label">Descripción</label>
              <textarea name="cta_description" class="form-control" rows="2">{{ old('cta_description', $about->cta_description) }}</textarea>
            </div>
            <div class="col-md-3">
              <label class="ab-label">Botón 1 — Texto</label>
              <input type="text" name="cta_btn_text" class="form-control" value="{{ old('cta_btn_text', $about->cta_btn_text) }}">
            </div>
            <div class="col-md-3">
              <label class="ab-label">Botón 1 — URL</label>
              <input type="text" name="cta_btn_url" class="form-control" value="{{ old('cta_btn_url', $about->cta_btn_url) }}">
            </div>
            <div class="col-md-3">
              <label class="ab-label">Botón 2 — Texto <span class="text-muted">(opcional)</span></label>
              <input type="text" name="cta_btn2_text" class="form-control" value="{{ old('cta_btn2_text', $about->cta_btn2_text) }}">
            </div>
            <div class="col-md-3">
              <label class="ab-label">Botón 2 — URL</label>
              <input type="text" name="cta_btn2_url" class="form-control" value="{{ old('cta_btn2_url', $about->cta_btn2_url) }}">
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /tab-content --}}

  {{-- Botón guardar sticky --}}
  <div class="ab-save-bar">
    <a href="{{ route('web.about') }}" target="_blank" class="ab-preview-btn">
      <i class="bi bi-eye me-1"></i> Vista previa
    </a>
    <button type="submit" class="ab-save-btn">
      <i class="bi bi-floppy me-2"></i> Guardar cambios
    </button>
  </div>

</form>

{{-- ══ MODAL: Agregar miembro ══════════════════════════════════ --}}
<div class="modal fade" id="addMemberModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Agregar miembro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.pages.about.members.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          @include('admin.pages._member_form')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL: Editar miembro ════════════════════════════════════ --}}
<div class="modal fade" id="editMemberModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar miembro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editMemberForm" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-body" id="editMemberBody">
          @include('admin.pages._member_form')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ ESTILOS ══════════════════════════════════════════════════ --}}
<style>
.ab-ad-icon {
  width: 48px; height: 48px; border-radius: 14px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.3rem; flex-shrink: 0;
}

/* Tabs */
.ab-ad-tabs .nav-link {
  color: #6b7280; font-size: .84rem; font-weight: 600;
  padding: 9px 16px; border-radius: 8px 8px 0 0;
  border: 1.5px solid transparent; border-bottom: none;
  transition: all .2s;
}
.ab-ad-tabs .nav-link.active {
  color: #6366f1; border-color: #e5e7eb;
  background: #fff; border-bottom-color: #fff;
}

/* Cards */
.ab-ad-card {
  background: #fff; border: 1.5px solid #e5e7eb;
  border-radius: 16px; overflow: hidden; margin-bottom: 16px;
}
.ab-ad-card-head {
  padding: 14px 20px;
  background: #f8f9fb;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 700; font-size: .88rem; color: #374151;
}
.ab-ad-card-body { padding: 20px; }

/* Labels */
.ab-label { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: 4px; display: block; }

/* Imagen upload */
.ab-img-upload { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.ab-img-preview { width: 120px; height: 80px; object-fit: cover; border-radius: 10px; border: 1.5px solid #e5e7eb; }
.ab-img-placeholder-sm {
  width: 120px; height: 80px; border-radius: 10px;
  border: 1.5px dashed #d1d5db;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  color: #9ca3af; font-size: .75rem; gap: 4px;
}
.ab-img-placeholder-sm i { font-size: 1.4rem; }
.ab-img-btn {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 8px 16px; border-radius: 9px;
  background: #eef2ff; color: #6366f1;
  font-size: .82rem; font-weight: 600; cursor: pointer;
  border: 1.5px solid #c7d2fe; transition: all .2s;
}
.ab-img-btn:hover { background: #e0e7ff; }

/* Repeat rows (stats, why, timeline) */
.ab-repeat-row {
  background: #f9fafb; border: 1.5px solid #e5e7eb;
  border-radius: 12px; padding: 14px 16px; margin-bottom: 10px;
}

/* Add / Del buttons */
.ab-add-btn {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 6px 14px; border-radius: 8px;
  background: #eef2ff; color: #6366f1;
  font-size: .8rem; font-weight: 700; border: 1.5px solid #c7d2fe;
  cursor: pointer; transition: all .2s;
}
.ab-add-btn:hover { background: #e0e7ff; }
.ab-del-btn {
  width: 36px; height: 36px; border-radius: 9px;
  background: #fef2f2; color: #e11d48; border: 1.5px solid #fecaca;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all .2s; font-size: .9rem;
}
.ab-del-btn:hover { background: #fee2e2; }

/* MVV block */
.ab-mvv-block {
  background: #f8f9fb; border: 1.5px solid #e5e7eb;
  border-radius: 14px; padding: 16px;
}
.ab-mvv-ico {
  width: 36px; height: 36px; border-radius: 9px;
  background: linear-gradient(135deg, #eef2ff, #f3f0ff);
  display: flex; align-items: center; justify-content: center;
  color: #6366f1; font-size: 1rem; margin-bottom: 10px;
}

/* Member row */
.ab-member-row {
  display: flex; align-items: center; gap: 14px;
  padding: 14px; background: #f9fafb;
  border: 1.5px solid #e5e7eb; border-radius: 14px; margin-bottom: 10px;
}
.ab-member-photo {
  width: 52px; height: 52px; border-radius: 12px;
  overflow: hidden; flex-shrink: 0; background: #eef2ff;
}
.ab-member-photo img { width: 100%; height: 100%; object-fit: cover; }
.ab-member-initials {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Sora', sans-serif; font-weight: 800; font-size: 1rem;
  color: #6366f1;
}
.ab-member-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.ab-member-info strong { font-size: .9rem; color: #111827; }
.ab-member-info span   { font-size: .78rem; color: #6b7280; }
.ab-member-badges      { display: flex; gap: 6px; margin-top: 3px; }
.ab-member-actions     { display: flex; gap: 8px; }
.ab-icon-btn {
  width: 34px; height: 34px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem; border: 1.5px solid; cursor: pointer; transition: all .2s;
}
.ab-icon-edit { background: #eef2ff; color: #6366f1; border-color: #c7d2fe; }
.ab-icon-edit:hover { background: #e0e7ff; }
.ab-icon-del  { background: #fef2f2; color: #e11d48; border-color: #fecaca; }
.ab-icon-del:hover { background: #fee2e2; }

/* Save bar */
.ab-save-bar {
  position: sticky; bottom: 0;
  background: #fff; border-top: 1.5px solid #e5e7eb;
  padding: 14px 20px; margin: 16px -16px 0;
  display: flex; justify-content: flex-end; gap: 12px; align-items: center;
  box-shadow: 0 -4px 20px rgba(0,0,0,.06); z-index: 10;
}
.ab-save-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 10px 26px; border-radius: 10px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-weight: 700; font-size: .9rem; border: none;
  box-shadow: 0 4px 14px rgba(99,102,241,.35); cursor: pointer; transition: all .25s;
}
.ab-save-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,.45); }
.ab-preview-btn {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 9px 18px; border-radius: 10px;
  border: 1.5px solid #c7d2fe; color: #6366f1;
  font-size: .84rem; font-weight: 600; text-decoration: none; transition: all .2s;
}
.ab-preview-btn:hover { background: #eef2ff; color: #6366f1; }
</style>

@push('scripts')
<script>
// ── Preview de imagen ─────────────────────────────────────────
function previewImg(input, targetId) {
  const target = document.getElementById(targetId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      if (target.tagName === 'IMG') {
        target.src = e.target.result;
      } else {
        // Reemplazar placeholder por <img>
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'ab-img-preview';
        img.id = targetId;
        target.replaceWith(img);
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// ── Update icono en tiempo real ───────────────────────────────
function updateIcon(input, previewId) {
  const el = document.getElementById(previewId);
  if (el) { el.className = 'bi ' + input.value.trim(); }
}

// ── Agregar fila Stat ─────────────────────────────────────────
let statCount = {{ count($about->stats ?? []) }};
function addStat() {
  const i = 'new' + statCount++;
  document.getElementById('statsContainer').insertAdjacentHTML('beforeend', `
    <div class="ab-repeat-row">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="ab-label">Ícono BI</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-star" id="statIconPrev${i}"></i></span>
            <input type="text" name="stat_icon[]" class="form-control" placeholder="bi-people-fill"
                   oninput="updateIcon(this,'statIconPrev${i}')">
          </div>
        </div>
        <div class="col-md-3">
          <label class="ab-label">Valor</label>
          <input type="text" name="stat_value[]" class="form-control" placeholder="10,000+">
        </div>
        <div class="col-md-4">
          <label class="ab-label">Etiqueta</label>
          <input type="text" name="stat_label[]" class="form-control" placeholder="Clientes felices">
        </div>
        <div class="col-md-1">
          <button type="button" class="ab-del-btn" onclick="this.closest('.ab-repeat-row').remove()">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    </div>`);
}

// ── Agregar fila Why ──────────────────────────────────────────
let whyCount = {{ count($about->why_us ?? []) }};
function addWhy() {
  const i = 'new' + whyCount++;
  document.getElementById('whyContainer').insertAdjacentHTML('beforeend', `
    <div class="ab-repeat-row">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="ab-label">Ícono BI</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-check" id="whyIconPrev${i}"></i></span>
            <input type="text" name="why_icon[]" class="form-control" placeholder="bi-shield-check"
                   oninput="updateIcon(this,'whyIconPrev${i}')">
          </div>
        </div>
        <div class="col-md-3">
          <label class="ab-label">Título</label>
          <input type="text" name="why_title[]" class="form-control">
        </div>
        <div class="col-md-5">
          <label class="ab-label">Descripción</label>
          <input type="text" name="why_desc[]" class="form-control">
        </div>
        <div class="col-md-1">
          <button type="button" class="ab-del-btn" onclick="this.closest('.ab-repeat-row').remove()">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    </div>`);
}

// ── Agregar fila Timeline ─────────────────────────────────────
let tlCount = {{ count($about->timeline ?? []) }};
function addTimeline() {
  document.getElementById('timelineContainer').insertAdjacentHTML('beforeend', `
    <div class="ab-repeat-row">
      <div class="row g-2 align-items-end">
        <div class="col-md-2">
          <label class="ab-label">Año</label>
          <input type="text" name="tl_year[]" class="form-control" placeholder="{{ date('Y') }}">
        </div>
        <div class="col-md-3">
          <label class="ab-label">Título</label>
          <input type="text" name="tl_title[]" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="ab-label">Descripción</label>
          <input type="text" name="tl_desc[]" class="form-control">
        </div>
        <div class="col-md-1">
          <button type="button" class="ab-del-btn" onclick="this.closest('.ab-repeat-row').remove()">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    </div>`);
}

// ── Abrir modal edición miembro ───────────────────────────────
function openEditMember(id, data) {
  const form  = document.getElementById('editMemberForm');
  const body  = document.getElementById('editMemberBody');
  form.action = `/admin/pages/about/members/${id}`;

  // Rellenar campos
  ['name','role','bio','linkedin','twitter','email','order'].forEach(f => {
    const el = body.querySelector(`[name="${f}"]`);
    if (el) el.value = data[f] ?? '';
  });
  const activeEl = body.querySelector('[name="active"]');
  if (activeEl) activeEl.checked = !!data.active;

  const modal = new bootstrap.Modal(document.getElementById('editMemberModal'));
  modal.show();
}
</script>
@endpush

@endsection