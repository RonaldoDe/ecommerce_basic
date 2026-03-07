@extends('layouts.web')

@section('content')

{{-- ══ HERO ═══════════════════════════════════════════════════════════ --}}
<section class="ab-hero">
  @if($about->hero_image)
    <div class="ab-hero-bg" style="background-image:url('{{ asset('storage/' . $about->hero_image) }}')"></div>
    <div class="ab-hero-overlay"></div>
  @else
    <div class="ab-hero-gradient"></div>
  @endif

  <div class="container position-relative z-1">
    <div class="row justify-content-center text-center">
      <div class="col-lg-7" data-aos="fade-up">
        <span class="ab-label">{{ $about->hero_label }}</span>
        <h1 class="ab-hero-title">{{ $about->hero_title }}</h1>
        @if($about->hero_subtitle)
          <p class="ab-hero-sub">{{ $about->hero_subtitle }}</p>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- ══ STATS ════════════════════════════════════════════════════════════ --}}
@if($about->stats && count($about->stats))
<section class="ab-stats">
  <div class="container">
    <div class="ab-stats-grid">
      @foreach($about->stats as $stat)
        <div class="ab-stat-item" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}">
          <div class="ab-stat-icon">
            <i class="bi {{ $stat['icon'] ?? 'bi-star' }}"></i>
          </div>
          <div class="ab-stat-value">{{ $stat['value'] }}</div>
          <div class="ab-stat-label">{{ $stat['label'] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ══ SOBRE NOSOTROS ══════════════════════════════════════════════════ --}}
<section class="ab-about section">
  <div class="container">
    <div class="row align-items-center gy-5">

      {{-- Imagen --}}
      <div class="col-lg-5" data-aos="fade-right">
        <div class="ab-img-wrap">
          @if($about->about_image)
            <img src="{{ asset('storage/' . $about->about_image) }}"
                 alt="{{ $about->about_title }}" class="ab-img-main">
          @else
            <div class="ab-img-placeholder">
              <i class="bi bi-shop"></i>
            </div>
          @endif
          {{-- Floating badge --}}
          <div class="ab-img-badge">
            <i class="bi bi-award-fill"></i>
            <span>+{{ date('Y') - 2020 }} años<br>de experiencia</span>
          </div>
        </div>
      </div>

      {{-- Texto --}}
      <div class="col-lg-7" data-aos="fade-left">
        <span class="ix-section-label">{{ $about->about_title }}</span>
        <h2 class="ab-section-title">Conoce nuestra<br><span class="ab-title-grad">historia y valores</span></h2>

        @if($about->about_description)
          <p class="ab-text">{{ $about->about_description }}</p>
        @endif
        @if($about->about_description_2)
          <p class="ab-text">{{ $about->about_description_2 }}</p>
        @endif

        {{-- Misión / Visión / Valores --}}
        <div class="ab-mvv-grid mt-4">
          <div class="ab-mvv-card ab-mvv-mission">
            <div class="ab-mvv-icon"><i class="bi bi-bullseye"></i></div>
            <h5>{{ $about->mission_title }}</h5>
            <p>{{ $about->mission_text }}</p>
          </div>
          <div class="ab-mvv-card ab-mvv-vision">
            <div class="ab-mvv-icon"><i class="bi bi-eye"></i></div>
            <h5>{{ $about->vision_title }}</h5>
            <p>{{ $about->vision_text }}</p>
          </div>
          <div class="ab-mvv-card ab-mvv-values">
            <div class="ab-mvv-icon"><i class="bi bi-heart"></i></div>
            <h5>{{ $about->values_title }}</h5>
            <p>{{ $about->values_text }}</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ══ ¿POR QUÉ ELEGIRNOS? ════════════════════════════════════════════ --}}
@if($about->why_us && count($about->why_us))
<section class="ab-why section" style="background:#f8f9fb;">
  <div class="container">
    <div class="ix-section-head ix-section-center mb-5" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Ventajas</span>
        <h2 class="ab-section-title">¿Por qué elegirnos?</h2>
      </div>
    </div>
    <div class="row gy-4">
      @foreach($about->why_us as $item)
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
          <div class="ab-why-card">
            <div class="ab-why-icon">
              <i class="bi {{ $item['icon'] ?? 'bi-check-circle' }}"></i>
            </div>
            <h5>{{ $item['title'] }}</h5>
            <p>{{ $item['description'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ══ LÍNEA DE TIEMPO ════════════════════════════════════════════════ --}}
@if($about->timeline && count($about->timeline))
<section class="ab-timeline section">
  <div class="container">
    <div class="ix-section-head ix-section-center mb-5" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Nuestra trayectoria</span>
        <h2 class="ab-section-title">Historia de la empresa</h2>
      </div>
    </div>
    <div class="ab-tl-wrap">
      @foreach($about->timeline as $i => $event)
        <div class="ab-tl-item {{ $loop->even ? 'ab-tl-right' : 'ab-tl-left' }}"
             data-aos="{{ $loop->even ? 'fade-left' : 'fade-right' }}">
          <div class="ab-tl-dot"></div>
          <div class="ab-tl-card">
            <span class="ab-tl-year">{{ $event['year'] }}</span>
            <h5>{{ $event['title'] }}</h5>
            <p>{{ $event['description'] }}</p>
          </div>
        </div>
      @endforeach
      <div class="ab-tl-line"></div>
    </div>
  </div>
</section>
@endif

{{-- ══ EQUIPO ══════════════════════════════════════════════════════════ --}}
@if($team->count())
<section class="ab-team section" style="background:#f8f9fb;">
  <div class="container">
    <div class="ix-section-head ix-section-center mb-5" data-aos="fade-up">
      <div>
        <span class="ix-section-label">Personas</span>
        <h2 class="ab-section-title">Nuestro equipo</h2>
      </div>
    </div>
    <div class="row gy-4 justify-content-center">
      @foreach($team as $member)
        <div class="col-xl-3 col-lg-4 col-md-6"
             data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
          <div class="ab-team-card">
            <div class="ab-team-photo">
              @if($member->photo)
                <img src="{{ asset('storage/' . $member->photo) }}"
                     alt="{{ $member->name }}">
              @else
                <div class="ab-team-photo-placeholder">
                  {{ strtoupper(substr($member->name, 0, 2)) }}
                </div>
              @endif
            </div>
            <div class="ab-team-info">
              <h5>{{ $member->name }}</h5>
              <span class="ab-team-role">{{ $member->role }}</span>
              @if($member->bio)
                <p>{{ Str::limit($member->bio, 100) }}</p>
              @endif
              <div class="ab-team-social">
                @if($member->linkedin)
                  <a href="{{ $member->linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <i class="bi bi-linkedin"></i>
                  </a>
                @endif
                @if($member->twitter)
                  <a href="{{ $member->twitter }}" target="_blank" rel="noopener" aria-label="Twitter/X">
                    <i class="bi bi-twitter-x"></i>
                  </a>
                @endif
                @if($member->email)
                  <a href="mailto:{{ $member->email }}" aria-label="Email">
                    <i class="bi bi-envelope-fill"></i>
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ══ CTA ═════════════════════════════════════════════════════════════ --}}
<section class="ab-cta">
  <div class="container">
    <div class="ab-cta-inner" data-aos="fade-up">
      <h2>{{ $about->cta_title }}</h2>
      @if($about->cta_description)
        <p>{{ $about->cta_description }}</p>
      @endif
      <div class="ab-cta-actions">
        <a href="{{ $about->cta_btn_url }}" class="ix-btn-primary">
          <i class="bi bi-grid-3x3-gap me-2"></i>{{ $about->cta_btn_text }}
        </a>
        @if($about->cta_btn2_text)
          <a href="{{ $about->cta_btn2_url }}" class="ix-btn-ghost">
            <i class="bi bi-envelope me-2"></i>{{ $about->cta_btn2_text }}
          </a>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- ══ ESTILOS ══════════════════════════════════════════════════════════ --}}
@push('styles')
<style>
/* ── Tokens del design system ─────────────────────────────── */
/* --ds-indigo:#6366f1 | --ds-purple:#8b5cf6 | --ds-dark:#1e1b4b */

.ab-section-title {
  font-family: 'Sora', sans-serif;
  font-size: 1.8rem; font-weight: 800; color: #111827; line-height: 1.2; margin: 8px 0 18px;
}
.ab-title-grad {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.ab-text { font-size: .95rem; color: #4b5563; line-height: 1.75; margin-bottom: 14px; }

/* ─── HERO ────────────────────────────────────────────────── */
.ab-hero {
  position: relative; min-height: 420px;
  display: flex; align-items: center; justify-content: center;
  padding: 100px 0 80px; overflow: hidden;
}
.ab-hero-gradient,
/* Overlay muy sutil */
.ab-hero-overlay {
  position: absolute; inset: 0;
  background: rgba(30, 27, 75, 0.30);
}
.ab-hero-bg {
  position: absolute; inset: 0;
  background-size: cover; background-position: center;
}
/* .ab-hero-overlay { background: rgba(30,27,75,.82); } */
.ab-hero-gradient::after {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E");
}

.ab-label {
  display: inline-block;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.2);
  color: #a5b4fc; font-size: .78rem; font-weight: 700;
  padding: 5px 16px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: .08em; margin-bottom: 18px;
}
.ab-hero-title {
  font-family: 'Sora', sans-serif;
  font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 800;
  color: #fff; line-height: 1.15; margin: 0 0 18px;
}
.ab-hero-sub {
  font-size: 1.05rem; color: rgba(255,255,255,.72); line-height: 1.7; margin: 0 auto; max-width: 560px;
}

/* ─── STATS ───────────────────────────────────────────────── */
.ab-stats {
  padding: 0;
  margin-top: -48px;
  position: relative; z-index: 2;
}
.ab-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 0;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0,0,0,.1);
  overflow: hidden;
  border: 1px solid #eef0f3;
}
.ab-stat-item {
  display: flex; flex-direction: column; align-items: center;
  padding: 32px 20px;
  border-right: 1px solid #eef0f3;
  transition: background .25s;
}
.ab-stat-item:last-child { border-right: none; }
.ab-stat-item:hover { background: #fafbff; }

.ab-stat-icon {
  width: 48px; height: 48px; border-radius: 14px;
  background: linear-gradient(135deg, #eef2ff, #f3f0ff);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 12px;
}
.ab-stat-icon i { font-size: 1.3rem; color: #6366f1; }
.ab-stat-value {
  font-family: 'Sora', sans-serif; font-size: 1.7rem; font-weight: 800;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  line-height: 1; margin-bottom: 5px;
}
.ab-stat-label { font-size: .8rem; color: #6b7280; font-weight: 500; text-align: center; }

/* ─── ABOUT ───────────────────────────────────────────────── */
.ab-about { padding: 90px 0; background: #fff; }

.ab-img-wrap { position: relative; }
.ab-img-main {
  width: 100%; border-radius: 24px;
  box-shadow: 0 16px 50px rgba(99,102,241,.15);
  aspect-ratio: 4/3; object-fit: cover;
}
.ab-img-placeholder {
  width: 100%; aspect-ratio: 4/3; border-radius: 24px;
  background: linear-gradient(135deg, #eef2ff, #f3f0ff);
  display: flex; align-items: center; justify-content: center;
}
.ab-img-placeholder i { font-size: 5rem; color: #c7d2fe; }

.ab-img-badge {
  position: absolute; bottom: -18px; right: -18px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; border-radius: 18px; padding: 14px 18px;
  display: flex; align-items: center; gap: 10px;
  box-shadow: 0 8px 24px rgba(99,102,241,.35);
  font-size: .82rem; font-weight: 700; line-height: 1.35;
  max-width: 170px;
}
.ab-img-badge i { font-size: 1.4rem; flex-shrink: 0; }

/* MVV cards */
.ab-mvv-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;
}
.ab-mvv-card {
  padding: 18px; border-radius: 16px;
  border: 1.5px solid #eef0f3;
  transition: border-color .25s, box-shadow .25s;
}
.ab-mvv-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 16px rgba(99,102,241,.1); }
.ab-mvv-icon {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 10px; font-size: 1.1rem;
}
.ab-mvv-mission .ab-mvv-icon { background: #eef2ff; color: #6366f1; }
.ab-mvv-vision  .ab-mvv-icon { background: #f0fdf4; color: #16a34a; }
.ab-mvv-values  .ab-mvv-icon { background: #fef2f2; color: #e11d48; }
.ab-mvv-card h5 { font-family: 'Sora', sans-serif; font-size: .85rem; font-weight: 800; color: #111827; margin: 0 0 5px; }
.ab-mvv-card p  { font-size: .78rem; color: #6b7280; margin: 0; line-height: 1.55; }

/* ─── WHY US ──────────────────────────────────────────────── */
.ab-why { padding: 80px 0; }
.ab-why-card {
  background: #fff; border-radius: 20px;
  border: 1.5px solid #eef0f3; padding: 28px 22px;
  height: 100%; text-align: center;
  transition: transform .3s, box-shadow .3s, border-color .3s;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.ab-why-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 36px rgba(99,102,241,.12);
  border-color: #c7d2fe;
}
.ab-why-icon {
  width: 64px; height: 64px; border-radius: 18px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
  box-shadow: 0 6px 18px rgba(99,102,241,.3);
  transition: transform .3s;
}
.ab-why-card:hover .ab-why-icon { transform: scale(1.08) rotate(-4deg); }
.ab-why-icon i { font-size: 1.6rem; color: #fff; }
.ab-why-card h5 { font-family: 'Sora', sans-serif; font-size: .95rem; font-weight: 800; color: #111827; margin: 0 0 8px; }
.ab-why-card p  { font-size: .84rem; color: #6b7280; margin: 0; line-height: 1.6; }

/* ─── TIMELINE ────────────────────────────────────────────── */
.ab-timeline { padding: 80px 0; background: #fff; }
.ab-tl-wrap {
  position: relative; max-width: 800px;
  margin: 0 auto; padding: 0 20px;
}
.ab-tl-line {
  position: absolute; top: 0; bottom: 0; left: 50%;
  width: 2px; transform: translateX(-50%);
  background: linear-gradient(180deg, #6366f1, #8b5cf6, #c4b5fd);
}
.ab-tl-item {
  position: relative; width: 46%; margin-bottom: 36px;
}
.ab-tl-left  { left: 0; text-align: right; padding-right: 36px; }
.ab-tl-right { left: 54%; text-align: left; padding-left: 36px; }

.ab-tl-dot {
  position: absolute; top: 16px;
  width: 14px; height: 14px; border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border: 3px solid #fff;
  box-shadow: 0 0 0 3px #c7d2fe;
}
.ab-tl-left  .ab-tl-dot { right: -7px; }
.ab-tl-right .ab-tl-dot { left: -7px; }

.ab-tl-card {
  background: #fff; border: 1.5px solid #eef0f3;
  border-radius: 16px; padding: 18px 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,.05);
  transition: box-shadow .25s, border-color .25s;
}
.ab-tl-card:hover { border-color: #c7d2fe; box-shadow: 0 6px 22px rgba(99,102,241,.1); }
.ab-tl-year {
  display: inline-block;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-size: .72rem; font-weight: 800;
  padding: 3px 12px; border-radius: 20px; margin-bottom: 8px;
  letter-spacing: .06em;
}
.ab-tl-card h5 { font-family: 'Sora', sans-serif; font-size: .9rem; font-weight: 800; color: #111827; margin: 0 0 5px; }
.ab-tl-card p  { font-size: .82rem; color: #6b7280; margin: 0; line-height: 1.55; }

/* ─── TEAM ────────────────────────────────────────────────── */
.ab-team { padding: 80px 0; }
.ab-team-card {
  background: #fff; border-radius: 20px;
  border: 1.5px solid #eef0f3;
  overflow: hidden; text-align: center;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
  transition: transform .3s, box-shadow .3s, border-color .3s;
}
.ab-team-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 36px rgba(99,102,241,.12);
  border-color: #c7d2fe;
}
.ab-team-photo {
  height: 200px; overflow: hidden;
  background: linear-gradient(135deg, #eef2ff, #f3f0ff);
}
.ab-team-photo img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
.ab-team-card:hover .ab-team-photo img { transform: scale(1.06); }
.ab-team-photo-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Sora', sans-serif; font-weight: 800; font-size: 2.5rem;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.ab-team-info { padding: 18px 16px 20px; }
.ab-team-info h5 { font-family: 'Sora', sans-serif; font-size: .95rem; font-weight: 800; color: #111827; margin: 0 0 3px; }
.ab-team-role {
  display: inline-block; font-size: .76rem; font-weight: 700;
  color: #6366f1; background: #eef2ff;
  padding: 2px 10px; border-radius: 20px; margin-bottom: 10px;
}
.ab-team-info p { font-size: .8rem; color: #6b7280; margin: 0 0 12px; line-height: 1.55; }
.ab-team-social { display: flex; justify-content: center; gap: 8px; }
.ab-team-social a {
  width: 32px; height: 32px; border-radius: 8px;
  background: #f3f4f6; color: #6b7280;
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem; text-decoration: none;
  transition: all .2s;
}
.ab-team-social a:hover { background: #6366f1; color: #fff; }

/* ─── CTA ─────────────────────────────────────────────────── */
.ab-cta {
  padding: 80px 0;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 55%, #4338ca 100%);
  position: relative; overflow: hidden;
}
.ab-cta::after {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E");
  pointer-events: none;
}
.ab-cta-inner {
  position: relative; z-index: 1;
  text-align: center; max-width: 620px; margin: 0 auto;
}
.ab-cta-inner h2 {
  font-family: 'Sora', sans-serif; font-size: 2rem; font-weight: 800;
  color: #fff; margin: 0 0 14px;
}
.ab-cta-inner p {
  font-size: 1rem; color: rgba(255,255,255,.72); margin: 0 0 30px; line-height: 1.7;
}
.ab-cta-actions { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; }

/* Reutilizar botones del design system del index */
.ix-btn-primary {
  display: inline-flex; align-items: center;
  padding: 13px 26px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff !important; border-radius: 12px;
  font-weight: 700; font-size: .9rem; text-decoration: none;
  box-shadow: 0 4px 20px rgba(99,102,241,.4); transition: all .25s;
}
.ix-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(99,102,241,.5); }
.ix-btn-ghost {
  display: inline-flex; align-items: center;
  padding: 12px 24px;
  background: rgba(255,255,255,.1);
  border: 1.5px solid rgba(255,255,255,.25);
  color: #fff !important; border-radius: 12px;
  font-weight: 600; font-size: .9rem; text-decoration: none; transition: all .25s;
}
.ix-btn-ghost:hover { background: rgba(255,255,255,.18); }

/* Section label (heredado del layout pero redefinido por seguridad) */
.ix-section-label {
  display: inline-block; font-size: .72rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: .12em;
  color: #6366f1; background: #eef2ff;
  padding: 4px 12px; border-radius: 20px; margin-bottom: 8px;
}
.ix-section-head {
  display: flex; justify-content: space-between; align-items: flex-end;
  margin-bottom: 40px; flex-wrap: wrap; gap: 12px;
}
.ix-section-center { justify-content: center; text-align: center; }

/* Responsive */
@media (max-width: 991.98px) {
  .ab-mvv-grid { grid-template-columns: 1fr; }
  .ab-img-badge { bottom: -10px; right: -10px; max-width: 145px; font-size: .75rem; }
  .ab-tl-line { display: none; }
  .ab-tl-item { width: 100%; left: 0 !important; text-align: left !important; padding: 0 0 0 24px !important; }
  .ab-tl-dot  { left: 0 !important; right: auto !important; }
}
@media (max-width: 575.98px) {
  .ab-stats-grid { grid-template-columns: 1fr 1fr; }
  .ab-stat-item:nth-child(2n) { border-right: none; }
  .ab-stat-item { border-bottom: 1px solid #eef0f3; }
  .ab-hero-title { font-size: 1.8rem; }
  .ab-cta-inner h2 { font-size: 1.5rem; }
}
</style>
@endpush

@endsection