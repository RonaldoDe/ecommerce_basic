@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>Imágenes del Producto</h1>
            <p class="text-muted mb-0">{{ $product->name }}</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-upload"></i> Subir Imagen
            </button>
            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info">
                <i class="bi bi-eye"></i> Ver Producto
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <hr>

    <!-- Información del Producto -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong><i class="bi bi-box"></i> Código:</strong> {{ $product->code }}
                    </div>
                    <div class="col-md-3">
                        <strong><i class="bi bi-tag"></i> Categoría:</strong> {{ $product->category->name }}
                    </div>
                    <div class="col-md-3">
                        <strong><i class="bi bi-images"></i> Imágenes:</strong> {{ $product->images->count() }}
                    </div>
                    <div class="col-md-3">
                        <strong><i class="bi bi-boxes"></i> Stock:</strong> {{ $product->stock }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Galería de Imágenes -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-images"></i> Galería de Imágenes
                        <span class="badge bg-light text-dark ms-2">{{ $product->images->count() }} imágenes</span>
                    </h4>
                </div>
                <div class="card-body">
                    @if($product->images->count() > 0)
                        <div class="row">
                            @foreach ($product->images as $index => $image)
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="image-card">
                                        <div class="image-wrapper">
                                            <img src="{{ asset('storage/'.$image->image) }}" 
                                                alt="{{ $product->name }}" 
                                                class="img-fluid">
                                            @if($index === 0)
                                                <span class="badge-primary-image">
                                                    <i class="bi bi-star-fill"></i> Principal
                                                </span>
                                            @endif
                                        </div>
                                        <div class="image-actions">
                                            <button type="button" 
                                                class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal{{ $image->id }}">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>
                                            <form action="{{ route('admin.products.removeImage', $image->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(this.form)">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                        <div class="image-info">
                                            <small class="text-muted">Imagen {{ $index + 1 }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Ver Imagen -->
                                <div class="modal fade" id="viewModal{{ $image->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $product->name }} - Imagen {{ $index + 1 }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/'.$image->image) }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="img-fluid"
                                                    style="max-height: 70vh;">
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ asset('storage/'.$image->image) }}" 
                                                    download="{{ $product->code }}_{{ $index + 1 }}.jpg" 
                                                    class="btn btn-success">
                                                    <i class="bi bi-download"></i> Descargar
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-image" style="font-size: 5rem; color: #ccc;"></i>
                            <h4 class="mt-3 text-muted">No hay imágenes cargadas</h4>
                            <p class="text-muted">Sube la primera imagen de este producto</p>
                            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="bi bi-upload"></i> Subir Primera Imagen
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Subir Imagen -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-upload"></i> Subir Nueva Imagen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.products.uploadImage', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="image" class="form-label">
                                Seleccionar imagen <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-camera"></i>
                                </span>
                                <input type="file" 
                                    name="image" 
                                    id="image" 
                                    class="form-control @error('image') is-invalid @enderror" 
                                    accept="image/*" 
                                    onchange="previewImage(event)"
                                    required>
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Formatos permitidos: JPG, PNG, GIF, SVG, WEBP. Máximo 2MB.
                            </small>
                        </div>

                        <!-- Vista Previa -->
                        <div class="text-center" id="previewContainer" style="display: none;">
                            <label class="form-label">Vista Previa:</label>
                            <div class="preview-wrapper">
                                <img id="preview" src="" alt="Preview" class="img-fluid rounded border">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Subir Imagen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .image-card {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            background: #fff;
        }

        .image-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .image-wrapper {
            position: relative;
            padding-top: 100%;
            background: #f8f9fa;
            overflow: hidden;
        }

        .image-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .badge-primary-image {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ffc107;
            color: #000;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            z-index: 10;
        }

        .image-actions {
            padding: 10px;
            display: flex;
            gap: 5px;
            justify-content: center;
            background: #f8f9fa;
        }

        .image-info {
            padding: 10px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .preview-wrapper {
            max-width: 400px;
            margin: 0 auto;
            padding: 10px;
        }

        .preview-wrapper img {
            max-height: 300px;
            width: auto;
        }

        .empty-state {
            min-height: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    </style>

    <script>
        function previewImage(event) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('previewContainer');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        function confirmDelete(form) {
            Swal.fire({
                title: '¿Eliminar esta imagen?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Cerrar modal después de subir
        @if(session('status') == 200)
            var uploadModal = document.getElementById('uploadModal');
            if(uploadModal) {
                var modal = bootstrap.Modal.getInstance(uploadModal);
                if(modal) modal.hide();
            }
        @endif
    </script>
@endsection