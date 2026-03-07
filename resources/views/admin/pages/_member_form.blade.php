<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label fw-semibold">Nombre *</label>
    <input type="text" name="name" class="form-control" required placeholder="Ej: Ana García">
  </div>
  <div class="col-md-6">
    <label class="form-label fw-semibold">Cargo / Rol *</label>
    <input type="text" name="role" class="form-control" required placeholder="Ej: CEO & Fundadora">
  </div>
  <div class="col-12">
    <label class="form-label fw-semibold">Biografía</label>
    <textarea name="bio" class="form-control" rows="3" placeholder="Breve descripción del miembro..."></textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label fw-semibold">Foto</label>
    <input type="file" name="photo" class="form-control" accept="image/*">
    <small class="text-muted">Máx. 2MB — se reemplaza si ya tiene foto</small>
  </div>
  <div class="col-md-3">
    <label class="form-label fw-semibold">Orden</label>
    <input type="number" name="order" class="form-control" value="0" min="0">
  </div>
  <div class="col-md-3 d-flex align-items-end">
    <div class="form-check form-switch mb-1">
      <input class="form-check-input" type="checkbox" name="active" value="1" checked>
      <label class="form-check-label fw-semibold">Activo</label>
    </div>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold"><i class="bi bi-linkedin me-1 text-primary"></i>LinkedIn</label>
    <input type="url" name="linkedin" class="form-control" placeholder="https://linkedin.com/in/...">
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold"><i class="bi bi-twitter-x me-1"></i>Twitter / X</label>
    <input type="url" name="twitter" class="form-control" placeholder="https://x.com/...">
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold"><i class="bi bi-envelope me-1 text-danger"></i>Email</label>
    <input type="email" name="email" class="form-control" placeholder="correo@empresa.com">
  </div>
</div>