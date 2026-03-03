@extends('layouts.web')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Escribir Reseña
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Info del Producto -->
                        <div class="product-info mb-4 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                            alt="{{ $product->name }}"
                                            class="img-fluid rounded">
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <h5>{{ $product->name }}</h5>
                                    <p class="text-muted mb-0">{{ $product->short_description }}</p>
                                    @if($hasPurchased)
                                        <span class="badge bg-success mt-2">
                                            <i class="bi bi-check-circle"></i> Compra verificada
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Formulario -->
                        <form id="reviewForm" enctype="multipart/form-data">
                            @csrf

                            <!-- Rating -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Calificación <span class="text-danger">*</span>
                                </label>
                                <div class="rating-input">
                                    <input type="hidden" name="rating" id="rating" value="0" required>
                                    <div class="stars-clickable">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star star-clickable" 
                                                data-rating="{{ $i }}"
                                                style="font-size: 2rem; cursor: pointer; color: #ddd;"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="invalid-feedback d-block" id="rating-error" style="display: none !important;">
                                    Por favor selecciona una calificación
                                </div>
                            </div>

                            <!-- Título -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Título de tu reseña (opcional)
                                </label>
                                <input type="text" 
                                    class="form-control" 
                                    id="title" 
                                    name="title"
                                    placeholder="Ejemplo: Excelente producto"
                                    maxlength="255">
                            </div>

                            <!-- Comentario -->
                            <div class="mb-3">
                                <label for="comment" class="form-label">
                                    Tu reseña <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                    id="comment" 
                                    name="comment"
                                    rows="5" 
                                    placeholder="Cuéntanos qué te pareció el producto..."
                                    minlength="10"
                                    maxlength="1000"
                                    required></textarea>
                                <small class="text-muted">Mínimo 10 caracteres, máximo 1000</small>
                                <div class="invalid-feedback">
                                    Por favor escribe tu reseña (mínimo 10 caracteres)
                                </div>
                            </div>

                            <!-- Imágenes -->
                            <div class="mb-3">
                                <label for="images" class="form-label">
                                    Agrega fotos (opcional)
                                </label>
                                <input type="file" 
                                    class="form-control" 
                                    id="images" 
                                    name="images[]"
                                    accept="image/*"
                                    multiple
                                    onchange="previewImages(event)">
                                <small class="text-muted">Puedes subir hasta 5 imágenes (máx. 2MB cada una)</small>
                                
                                <!-- Preview de imágenes -->
                                <div id="imagePreview" class="mt-3 d-flex flex-wrap gap-2"></div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('web.product.show', $product->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-send"></i> Enviar Reseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info adicional -->
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Nota:</strong> Tu reseña será revisada por nuestro equipo antes de publicarse. 
                    Esto nos ayuda a mantener la calidad de las reseñas en nuestra plataforma.
                </div>
            </div>
        </div>
    </div>

    <style>
        .star-clickable:hover,
        .star-clickable.active {
            color: #ffc107 !important;
        }

        .preview-image-container {
            position: relative;
            display: inline-block;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <script>
        // Sistema de rating con estrellas
        const stars = document.querySelectorAll('.star-clickable');
        const ratingInput = document.getElementById('rating');
        const ratingError = document.getElementById('rating-error');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;
                ratingError.style.display = 'none';
                
                // Actualizar estrellas visualmente
                stars.forEach(s => {
                    const starRating = s.getAttribute('data-rating');
                    if (starRating <= rating) {
                        s.classList.remove('bi-star');
                        s.classList.add('bi-star-fill', 'active');
                    } else {
                        s.classList.remove('bi-star-fill', 'active');
                        s.classList.add('bi-star');
                    }
                });
            });

            // Hover effect
            star.addEventListener('mouseenter', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach(s => {
                    const starRating = s.getAttribute('data-rating');
                    if (starRating <= rating) {
                        s.style.color = '#ffc107';
                    }
                });
            });

            star.addEventListener('mouseleave', function() {
                const currentRating = ratingInput.value;
                stars.forEach(s => {
                    const starRating = s.getAttribute('data-rating');
                    if (starRating <= currentRating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });

        // Preview de imágenes
        let selectedFiles = [];

        function previewImages(event) {
            const files = Array.from(event.target.files);
            const preview = document.getElementById('imagePreview');
            
            // Limitar a 5 imágenes
            if (selectedFiles.length + files.length > 5) {
                Swal.fire('Error', 'Puedes subir máximo 5 imágenes', 'error');
                return;
            }

            files.forEach((file, index) => {
                if (file.size > 2048 * 1024) { // 2MB
                    Swal.fire('Error', `La imagen ${file.name} es muy grande (máx. 2MB)`, 'error');
                    return;
                }

                selectedFiles.push(file);
                const reader = new FileReader();

                reader.onload = function(e) {
                    const container = document.createElement('div');
                    container.className = 'preview-image-container';
                    container.innerHTML = `
                        <img src="${e.target.result}" class="preview-image" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage(${selectedFiles.length - 1})">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    preview.appendChild(container);
                };

                reader.readAsDataURL(file);
            });
        }

        function removeImage(index) {
            selectedFiles.splice(index, 1);
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            selectedFiles.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.createElement('div');
                    container.className = 'preview-image-container';
                    container.innerHTML = `
                        <img src="${e.target.result}" class="preview-image" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage(${idx})">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    preview.appendChild(container);
                };
                reader.readAsDataURL(file);
            });
        }

        // Enviar formulario
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validar rating
            if (ratingInput.value === '0') {
                ratingError.style.display = 'block';
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

            const formData = new FormData();
            formData.append('rating', ratingInput.value);
            formData.append('title', document.getElementById('title').value);
            formData.append('comment', document.getElementById('comment').value);
            
            // Agregar archivos seleccionados
            selectedFiles.forEach((file, index) => {
                formData.append('images[]', file);
            });

            fetch('{{ route("review.store", $product->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Gracias!',
                        text: data.message,
                        confirmButtonText: 'Ver producto'
                    }).then(() => {
                        window.location.href = '{{ route("web.product.show", $product->id) }}';
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send"></i> Enviar Reseña';
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Ocurrió un error al enviar la reseña', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send"></i> Enviar Reseña';
            });
        });
    </script>
@endsection