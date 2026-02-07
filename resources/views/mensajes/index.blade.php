<x-layout>
    <div class="container py-4">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="height: 80vh;">
            <div class="row g-0 h-100">
                
                <div class="col-md-4 col-lg-3 border-end h-100 d-flex flex-column bg-white">
                    <div class="p-3 border-bottom bg-light">
                        <h5 class="fw-bold text-navy mb-0">Mensajes</h5>
                    </div>

                    <div class="flex-grow-1 overflow-auto p-2">
                        @foreach($contactos as $contacto)
                            <a href="{{ route('mensajes.show', $contacto->id) }}" 
                               class="d-flex align-items-center p-3 rounded-4 mb-2 text-decoration-none transition-hover 
                               {{ isset($usuarioActivo) && $usuarioActivo->id == $contacto->id ? 'bg-navy-subtle text-navy' : 'text-dark hover-bg-light' }}">
                                
                                <div class="position-relative">
                                    <img src="{{ $contacto->foto ? asset('storage/' . $contacto->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($contacto->name) }}" 
                                         class="rounded-circle object-fit-cover shadow-sm" width="45" height="45">
                                </div>

                                <div class="ms-3 overflow-hidden">
                                    <h6 class="mb-0 fw-bold text-truncate">{{ $contacto->name }}</h6>
                                    <small class="text-muted text-truncate d-block" style="font-size: 0.8rem;">
                                        {{ $contacto->role == 'doctor' ? 'Doctor' : 'Paciente' }}
                                    </small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-8 col-lg-9 h-100 d-flex flex-column bg-light">
                    @if(isset($usuarioActivo))
                        
                        {{-- CABECERA --}}
                        <div class="p-3 bg-white border-bottom shadow-sm d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="{{ $usuarioActivo->foto ? asset('storage/' . $usuarioActivo->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($usuarioActivo->name) }}" 
                                     class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-0 fw-bold text-navy">{{ $usuarioActivo->name }}</h6>
                                    <small class="text-muted">
                                    @if($usuarioActivo->role == 'doctor')
                                        {{ $usuarioActivo->doctor?->especialidades->pluck('nombre')->join(', ') ?: 'Médico General' }}
                                    @else
                                        Paciente
                                    @endif
                                </small>
                                </div>
                            </div>
                        </div>

                        <div class="flex-grow-1 overflow-auto p-4" id="chatBox" style="background-color: #f8f9fa;">
                            @forelse($mensajes as $msg)
                                @php $esMio = $msg->id_remitente == Auth::id(); @endphp
                                <div class="d-flex mb-3 {{ $esMio ? 'justify-content-end' : 'justify-content-start' }}">
                                    @if(!$esMio)
                                        <img src="{{ $usuarioActivo->foto ? asset('storage/' . $usuarioActivo->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($usuarioActivo->name) }}" 
                                             class="rounded-circle me-2 align-self-end mb-1" width="30" height="30">
                                    @endif

                                    <div class="d-flex flex-column {{ $esMio ? 'align-items-end' : 'align-items-start' }}" style="max-width: 75%;">
                                        <div class="p-3 rounded-4 shadow-sm 
                                            {{ $esMio ? 'bg-navy text-white rounded-bottom-end-0' : 'bg-white text-dark rounded-bottom-start-0' }}">
                                            <p class="mb-0">{{ $msg->contenido }}</p>
                                        </div>
                                        <small class="text-muted mt-1" style="font-size: 0.7rem;">
                                            {{ $msg->created_at->format('h:i A') }}
                                            @if($esMio)
                                                <i class="bi bi-check2-all {{ $msg->leido ? 'text-primary' : '' }}"></i>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 opacity-50">
                                    <i class="bi bi-chat-dots display-1 text-muted"></i>
                                    <p class="mt-3">Inicia la conversación con {{ $usuarioActivo->name }}</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="p-3 bg-white border-top">
                            <form action="{{ route('mensajes.store') }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="id_destinatario" value="{{ $usuarioActivo->id }}">
                                <input type="text" name="contenido" class="form-control rounded-pill bg-light border-0 py-2 ps-4" 
                                       placeholder="Escribe un mensaje..." autocomplete="off" required autofocus>                               
                                <button type="submit" class="btn btn-navy rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                        style="width: 45px; height: 45px;">
                                    <i class="bi bi-send-fill fs-5" style="margin-left: 2px;"></i>
                                </button>
                            </form>
                        </div>                 
                    @else
                        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 opacity-50">
                            <i class="bi bi-chat-dots-fill display-1 text-navy mb-3"></i>
                            <h3 class="fw-bold text-navy">Mensajería de BuscaDoc</h3>
                            <p class="text-muted">Selecciona un contacto para chatear.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var chatBox = document.getElementById("chatBox");
            if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>

    <style>
        .hover-bg-light:hover { background-color: #f8f9fa; }
        .bg-navy-subtle { background-color: #e6f0ff; }
        .rounded-bottom-end-0 { border-bottom-right-radius: 0 !important; }
        .rounded-bottom-start-0 { border-bottom-left-radius: 0 !important; }
    </style>
</x-layout>