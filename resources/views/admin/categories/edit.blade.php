@extends('layouts.admin')

@section('content')
<h1>Editar categoría</h1>
<hr>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h4>Datos de la categoría</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category->id) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $category->name) }}">
                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                <input type="text" name="slug" id="slug" readonly
                                       class="form-control @error('slug') is-invalid @enderror"
                                       value="{{ old('slug', $category->slug) }}">
                                @error('slug')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Categoría padre</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-diagram-2"></i></span>
                                <select name="parent_id" class="form-select">
                                    <option value="">— Ninguna (categoría principal) —</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Orden <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-sort-numeric-up"></i></span>
                                <input type="number" name="order" min="0"
                                       class="form-control @error('order') is-invalid @enderror"
                                       value="{{ old('order', $category->order) }}">
                                @error('order')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', $category->status ? '1' : '0') == '1' ? 'selected' : '' }}>
                                    Activa
                                </option>
                                <option value="0" {{ old('status', $category->status ? '1' : '0') == '0' ? 'selected' : '' }}>
                                    Inactiva
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                        @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Imagen actual --}}
                    <div class="mb-4">
                        <label class="form-label">Imagen</label>

                        @if($category->image)
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <img src="{{ $category->image_url }}" alt="Imagen actual"
                                     class="rounded border" style="height:80px;object-fit:cover;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="remove_image" id="remove_image" value="1">
                                    <label class="form-check-label text-danger" for="remove_image">
                                        Eliminar imagen actual
                                    </label>
                                </div>
                            </div>
                        @endif

                        <input type="file" name="image" id="imageInput" accept="image/*"
                               class="form-control @error('image') is-invalid @enderror"
                               onchange="previewImage(this)">
                        @error('image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        <div id="imagePreview" class="mt-2 d-none">
                            <img id="previewImg" src="" alt="Vista previa"
                                 class="rounded border" style="max-height:150px;">
                        </div>
                        <small class="text-muted">JPG, PNG o WEBP. Máximo 2MB. Deja vacío para conservar la actual.</small>
                    </div>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('name').addEventListener('input', function () {
        document.getElementById('slug').value = this.value.toLowerCase()
            .replace(/[áàäâ]/g,'a').replace(/[éèëê]/g,'e')
            .replace(/[íìïî]/g,'i').replace(/[óòöô]/g,'o')
            .replace(/[úùüû]/g,'u').replace(/[ñ]/g,'n')
            .replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
    });

    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const img     = document.getElementById('previewImg');
        if (input.files && input.files[0]) {
            img.src = URL.createObjectURL(input.files[0]);
            preview.classList.remove('d-none');
        }
    }
</script>
@endpush
@endsection