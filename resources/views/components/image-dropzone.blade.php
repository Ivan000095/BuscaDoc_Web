{{-- Ejemplo de uso:
<x-image-dropzone
    name="image"
    :current-image="isset($product) && $product->hasImage() ? $product->image_url : null"
    :current-image-alt="isset($product) ? $product->name : ''"
    :error="$errors->first('image')"
    currentimageclass="col-sm-4 col-md-5 col-lg-4"
    dropzoneclass="col-sm-8 col-md-7 col-lg-8"
    title="Arrastra tu nueva imagen aquí"
    subtitle="o haz clic para seleccionar"
    help-text="Formatos: JPG, PNG, GIF, SVG, WEBP"
    :max-size="5"
    :show-current-image="true"
    dropzone-height="200px"
/>

--}}

@props([
    'name' => 'image',
    'id' => null,
    'currentImage' => null,
    'currentImageAlt' => '',
    'required' => false,
    'maxSize' => 5,
    'accept' => 'image/*',
    'allowedTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'],
    'showCurrentImage' => true,
    'currentimageclass' => 'col-12',
    'dropzoneclass' => 'col-12',
    'dropzoneHeight' => '200px',
    'title' => 'Arrastra tu imagen aquí',
    'subtitle' => 'o haz clic para seleccionar',
    'helpText' => 'Formatos: JPG, PNG, GIF, SVG, WEBP',
    'error' => null
])

@php
    $componentId = $id ?? 'imageDropzone_' . \Str::random(8);
    $inputId = $componentId . '_input';
    $dropzoneId = $componentId . '_dropzone';
    $contentId = $componentId . '_content';
    $previewId = $componentId . '_preview';
    $previewImageId = $componentId . '_previewImage';
    $overlayId = $componentId . '_overlay';
    $removeBtnId = $componentId . '_remove';
    $changeBtnId = $componentId . '_change';
    $maxSizeBytes = $maxSize * 1024 * 1024;

    // Asegurar que allowedTypes sea un array
    $allowedTypesArray = is_array($allowedTypes) ? $allowedTypes : ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
@endphp

{{-- <div class="" data-image-dropzone-component="{{ $componentId }}"> --}}
    <!-- Mostrar imagen actual si existe -->
    @if($showCurrentImage && $currentImage)
        <div class="{{ $currentimageclass }} ">
            <label class="form-label text-muted mb-2">
                <i class="bi bi-image-fill text-primary me-2"></i>Imagen actual:
            </label>
            <div class="card border-0 shadow-sm mx-auto" style="max-width: 200px;">
                <img src="{{ $currentImage }}"
                     alt="{{ $currentImageAlt }}"
                     class="img-fluid rounded shadow-sm"
                     style="max-height: 200px; object-fit: cover;">
                {{-- <div class="card-body p-3 text-center">
                </div> --}}
            </div>
        </div>
    @endif

    <!-- Campo de imagen con drag & drop -->
    <div class="{{ $dropzoneclass }}">
        <div id="{{ $dropzoneId }}"
             class="border border-2 border-dashed border-secondary rounded-3 p-4 text-center bg-light position-relative image-dropzone {{ $error ? 'border-danger' : '' }}"
             style="min-height: {{ $dropzoneHeight }}; cursor: pointer; transition: all 0.3s ease;">

            <!-- Contenido inicial del dropzone -->
            <div id="{{ $contentId }}" class="d-flex flex-column align-items-center justify-content-center h-100 dropzone-content">
                <div class="mb-3">
                    <i class="bi bi-cloud-upload display-1 text-primary dropzone-icon"></i>
                </div>
                <h5 class="text-primary fw-bold mb-2">{{ $title }}</h5>
                <p class="text-muted mb-2">{{ $subtitle }}</p>
                <small class="text-muted">{{ $helpText }} (máx. {{ $maxSize }}MB)</small>
            </div>

            <!-- Vista previa de la imagen -->
            <div id="{{ $previewId }}"
                 class="position-absolute top-0 start-0 w-100 h-100 d-none d-flex align-items-center justify-content-center bg-white rounded-3 image-preview">
                <img id="{{ $previewImageId }}"
                     src=""
                     alt="Vista previa"
                     class="img-fluid rounded shadow-sm"
                     style="max-height: calc({{ $dropzoneHeight }} - 40px);">

                <!-- Botones overlay -->
                <div class="position-absolute top-0 end-0 p-2 opacity-0 preview-overlay"
                     id="{{ $overlayId }}"
                     style="transition: opacity 0.3s ease;">
                    <div class="btn-group-vertical">
                        <button type="button"
                                id="{{ $removeBtnId }}"
                                class="btn btn-danger btn-sm mb-1"
                                title="Eliminar imagen">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button type="button"
                                id="{{ $changeBtnId }}"
                                class="btn btn-primary btn-sm"
                                title="Cambiar imagen">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input file oculto -->
        <input type="file"
               id="{{ $inputId }}"
               name="{{ $name }}"
               accept="{{ $accept }}"
               class="d-none"
               {{ $required ? 'required' : '' }}
               {{ $attributes }}>

        <!-- Mensaje de error -->
        @if($error)
            <div class="invalid-feedback d-block">
                {{ $error }}
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            /* Efecto hover para el dropzone */
            [data-image-dropzone-component="{{ $componentId }}"] .image-dropzone:hover {
                border-color: #0d6efd !important;
                background-color: rgba(13, 110, 253, 0.05) !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }

            /* Estado dragover */
            [data-image-dropzone-component="{{ $componentId }}"] .image-dropzone.dragover {
                border-color: #0d6efd !important;
                background-color: rgba(13, 110, 253, 0.1) !important;
                box-shadow: 0 0 20px rgba(13, 110, 253, 0.3);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                console.log('Initializing image dropzone: {{ $componentId }}');

                // Elementos del DOM específicos de este componente
                const $component = $('[data-image-dropzone-component="{{ $componentId }}"]');
                const $dropzone = $('#{{ $dropzoneId }}');
                const $imageInput = $('#{{ $inputId }}');
                const $dropzoneContent = $('#{{ $contentId }}');
                const $imagePreview = $('#{{ $previewId }}');
                const $previewImage = $('#{{ $previewImageId }}');
                const $previewOverlay = $('#{{ $overlayId }}');
                const $removeImageBtn = $('#{{ $removeBtnId }}');
                const $changeImageBtn = $('#{{ $changeBtnId }}');

                // Configuración
                const config = {
                    allowedTypes: {!! json_encode($allowedTypesArray) !!},
                    maxSize: {{ $maxSizeBytes }},
                    maxSizeText: '{{ $maxSize }}MB'
                };

                // Verificar que existan los elementos
                if ($dropzone.length === 0 || $imageInput.length === 0) {
                    console.error('Required elements not found for component: {{ $componentId }}');
                    return;
                }

                console.log('Component {{ $componentId }} initialized successfully');

                // Click en dropzone para abrir selector
                $dropzone.on('click', function(e) {
                    if (!$(e.target).closest('button').length) {
                        console.log('Dropzone clicked, opening file selector...');
                        $imageInput.click();
                    }
                });

                // Cambio en el input file
                $imageInput.on('change', function(e) {
                    console.log('File input changed');
                    const file = e.target.files[0];
                    if (file) {
                        console.log('File selected:', {
                            name: file.name,
                            size: file.size,
                            type: file.type
                        });
                        showPreview(file);
                    }
                });

                // Eventos drag & drop
                $dropzone.on('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('dragover');
                });

                $dropzone.on('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('dragover');
                });

                $dropzone.on('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('dragover');

                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        console.log('File dropped:', files[0].name);
                        // Asignar archivo al input
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(files[0]);
                            $imageInput[0].files = dt.files;
                            showPreview(files[0]);
                        } catch (error) {
                            console.error('Error setting dropped file:', error);
                            showPreview(files[0]);
                        }
                    }
                });

                // Mostrar overlay al hover en la preview
                $imagePreview.on('mouseenter', function() {
                    $previewOverlay.removeClass('opacity-0').addClass('opacity-100');
                }).on('mouseleave', function() {
                    $previewOverlay.removeClass('opacity-100').addClass('opacity-0');
                });

                // Botón eliminar imagen
                $removeImageBtn.on('click', function(e) {
                    e.stopPropagation();
                    removeImage();
                });

                // Botón cambiar imagen
                $changeImageBtn.on('click', function(e) {
                    e.stopPropagation();
                    $imageInput.click();
                });

                // Función para mostrar vista previa
                function showPreview(file) {
                    console.log('Showing preview for:', file.name);

                    // Validación de tipo
                    if (!config.allowedTypes.includes(file.type)) {
                        alert('Por favor selecciona una imagen válida. Tipos permitidos: ' +
                              config.allowedTypes.map(type => type.replace('image/', '')).join(', '));
                        removeImage();
                        return;
                    }

                    // Validación de tamaño
                    if (file.size > config.maxSize) {
                        alert('La imagen no debe superar los ' + config.maxSizeText);
                        removeImage();
                        return;
                    }

                    // Mostrar vista previa
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        console.log('Preview loaded successfully for component: {{ $componentId }}');
                        $previewImage.attr('src', e.target.result);
                        $dropzoneContent.hide();
                        $imagePreview.removeClass('d-none');
                        $dropzone.removeClass('border-danger');
                    };
                    reader.onerror = function() {
                        console.error('Error reading file');
                        alert('Error al leer el archivo');
                        removeImage();
                    };
                    reader.readAsDataURL(file);
                }

                // Función para remover imagen
                function removeImage() {
                    console.log('Removing image from component: {{ $componentId }}');
                    $imageInput.val('');
                    $previewImage.attr('src', '');
                    $dropzoneContent.show();
                    $imagePreview.addClass('d-none');
                    $previewOverlay.removeClass('opacity-100').addClass('opacity-0');
                }

                // Exponer funciones públicas para el componente
                window['imageDropzone_{{ $componentId }}'] = {
                    removeImage: removeImage,
                    showPreview: showPreview,
                    getFile: function() {
                        return $imageInput[0].files[0] || null;
                    },
                    setRequired: function(required) {
                        if (required) {
                            $imageInput.attr('required', 'required');
                        } else {
                            $imageInput.removeAttr('required');
                        }
                    },
                    validate: function() {
                        const file = $imageInput[0].files[0];
                        if (!file) {
                            return !$imageInput.is('[required]');
                        }

                        if (!config.allowedTypes.includes(file.type)) {
                            return false;
                        }

                        if (file.size > config.maxSize) {
                            return false;
                        }

                        return true;
                    },
                    getConfig: function() {
                        return config;
                    }
                };
            });
        </script>
    @endpush
{{-- </div> --}}

