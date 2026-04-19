<x-layout>
    @push('styles')
    <style>
        /* Scrollbar moderno y discreto para el área de contactos y mensajes */
        .chat-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .chat-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(17, 42, 70, 0.15);
            border-radius: 10px;
        }
        .chat-scroll:hover::-webkit-scrollbar-thumb {
            background-color: rgba(17, 42, 70, 0.3);
        }

        /* Ajustes de hover y estados activos para contactos */
        .contact-item {
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        .contact-item:hover {
            background-color: var(--bg-app);
            transform: translateX(3px);
        }
        .contact-active {
            background-color: var(--brand-navy-subtle) !important;
            border-color: rgba(17, 42, 70, 0.1) !important;
            box-shadow: inset 3px 0 0 0 var(--brand-navy);
        }

        /* Input de mensaje moderno */
        .chat-input-wrapper {
            background-color: var(--bg-app);
            border: 1px solid rgba(17, 42, 70, 0.05);
            border-radius: 50px;
            padding: 4px 4px 4px 15px;
            transition: all 0.3s ease;
        }
        .chat-input-wrapper:focus-within {
            background-color: var(--bg-surface);
            box-shadow: 0 0 0 3px var(--brand-navy-subtle);
            border-color: rgba(17, 42, 70, 0.2);
        }
        .chat-input {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }
        
        /* Burbujas de chat */
        .bubble-received {
            background-color: var(--bg-surface);
            border: 1px solid rgba(17, 42, 70, 0.05);
            color: var(--text-main);
            border-bottom-left-radius: 4px !important;
        }
        .bubble-sent {
            background-color: var(--brand-navy);
            color: white;
            border-bottom-right-radius: 4px !important;
        }
    </style>
    @endpush

    <div class="container py-4">
        @if(Auth::user())
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden bg-surface" style="height: 82vh;">
                <div class="row g-0 h-100">
                    
                    <div class="col-md-4 col-lg-3 h-100 d-flex flex-column" style="border-right: 1px solid rgba(17, 42, 70, 0.08);">
                        <div class="p-3" style="border-bottom: 1px solid rgba(17, 42, 70, 0.05);">
                            <h5 class="fw-bold text-navy mb-0 d-flex align-items-center">
                                <i class="bi bi-chat-left-text me-2 opacity-75"></i> Mensajes
                            </h5>
                        </div>

                        <div class="flex-grow-1 overflow-auto p-2 chat-scroll bg-surface">
                            @forelse($contactos as $contacto)
                                @php
                                    $isActive = isset($usuarioActivo) && $usuarioActivo->id == $contacto->id;
                                @endphp
                                <a href="{{ route('mensajes.show', $contacto->id) }}" 
                                   class="d-flex align-items-center p-3 rounded-4 mb-2 text-decoration-none contact-item {{ $isActive ? 'contact-active' : 'text-main' }}">
                                    
                                    <div class="position-relative">
                                        <img src="{{ $contacto->foto ? asset('storage/' . $contacto->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($contacto->name) }}" 
                                             class="rounded-circle object-fit-cover shadow-sm border border-2 border-white" width="48" height="48">
                                        @if($isActive)
                                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                                        @endif
                                    </div>

                                    <div class="ms-3 overflow-hidden">
                                        <h6 class="mb-0 fw-bold text-truncate" style="color: {{ $isActive ? 'var(--brand-navy)' : 'inherit' }};">
                                            {{ $contacto->name }}
                                        </h6>
                                        <small class="text-muted text-truncate d-block mt-1" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                                            <i class="bi {{ $contacto->role == 'doctor' ? 'bi-heart-pulse' : 'bi-person' }} opacity-75 me-1"></i>
                                            {{ $contacto->role == 'doctor' ? 'Doctor' : 'Paciente' }}
                                        </small>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center p-4 opacity-50 mt-4">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2 text-muted" style="font-size: 0.9rem;">No tienes conversaciones recientes.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-md-8 col-lg-9 h-100 d-flex flex-column" style="background-color: var(--bg-app);">
                        @if(isset($usuarioActivo))            
                            <div class="p-3 bg-surface d-flex align-items-center justify-content-between" style="border-bottom: 1px solid rgba(17, 42, 70, 0.05); z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <img src="{{$usuarioActivo->foto ? asset('storage/' . $usuarioActivo->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($usuarioActivo->name)}}" 
                                         class="rounded-circle me-3 shadow-sm object-fit-cover" width="42" height="42" style="border: 2px solid var(--bg-app);">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-navy">{{$usuarioActivo->name}}</h6>
                                        <small class="text-muted d-flex align-items-center" style="font-size: 0.8rem;">
                                            <span class="d-inline-block bg-success rounded-circle me-2" style="width: 6px; height: 6px;"></span>
                                            @if($usuarioActivo->role == 'doctor')
                                                {{ $usuarioActivo->doctor?->especialidades->pluck('nombre')->join(', ') ?: 'Médico General' }}
                                            @else
                                                Paciente
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="opacity-50">
                                    <i class="bi bi-shield-check fs-5 text-success" title="Chat privado"></i>
                                </div>
                            </div>

                            <div class="flex-grow-1 overflow-auto p-4 chat-scroll position-relative" id="chatBox">
                                </div>

                            <div class="p-3 bg-surface" style="border-top: 1px solid rgba(17, 42, 70, 0.05);">
                                <form id="formChat" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" id="destinatarioId" value="{{$usuarioActivo->id}}">
                                    
                                    <div class="chat-input-wrapper flex-grow-1 d-flex align-items-center">
                                        <input type="text" id="inputMensaje" class="form-control chat-input py-2" 
                                            placeholder="Escribe tu mensaje aquí..." autocomplete="off" required autofocus>                               
                                    </div>

                                    <button type="submit" class="btn btn-navy rounded-circle shadow-sm d-flex align-items-center justify-content-center flex-shrink-0" 
                                            style="width: 48px; height: 48px; transition: transform 0.2s;">
                                        <x-mcf-send class="fs-5" style="margin-left: -2px; margin-top: 2px;"/>
                                    </button>
                                </form>
                            </div>        
                        @else
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5">
                                <div class="bg-navy-subtle rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                    <i class="bi bi-chat-quote fs-1 text-navy opacity-75"></i>
                                </div>
                                <h4 class="fw-bold text-navy">Tus Mensajes</h4>
                                <p class="text-muted" style="max-width: 300px;">Selecciona una conversación del panel izquierdo para comenzar a chatear.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
                <div class="col-md-6 col-lg-5 text-center fade-in">
                    <div class="card border-0 shadow-soft rounded-4 p-5 bg-surface">
                        <i class="bi bi-lock-fill display-1 text-navy opacity-50 mb-3"></i>
                        <h3 class="fw-bold text-navy">Acceso Restringido</h3>
                        <p class="text-muted mb-4">Para poder enviar y recibir mensajes con doctores o pacientes, necesitas iniciar sesión en tu cuenta de BuscaDoc.</p>
                        <a href="{{ route('login') }}" class="btn btn-navy rounded-pill px-5 py-2 fw-bold">
                            Iniciar sesión
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(isset($usuarioActivo) && Auth::user())
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-app.js";
        import { getDatabase, ref, onValue, query, orderByChild, equalTo } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-database.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCjY3XJoaq7uGe8TdaQFw_c2YLJZSQUqpY",
            authDomain: "buscadoc-b204b.firebaseapp.com",
            projectId: "buscadoc-b204b",
            storageBucket: "buscadoc-b204b.firebasestorage.app",
            messagingSenderId: "754493965978",
            appId: "1:754493965978:web:769a90bb14471891594123",
            measurementId: "G-8DYH8H2H2H"
        };

        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);

        const authId = {{ Auth::id() ?? 0 }};
        const destId = {{ $usuarioActivo->id }};
        const chatId = authId < destId ? `${authId}_${destId}` : `${destId}_${authId}`;

        const mensajesRef = query(ref(db, 'mensajes'), orderByChild('chat_id'), equalTo(chatId));
        const chatBox = document.getElementById("chatBox");

        onValue(mensajesRef, (snapshot) => {
            chatBox.innerHTML = ''; 
            const datos = snapshot.val();

            if (datos) {
                const mensajesArray = Object.keys(datos).map(key => datos[key]);
                
                mensajesArray.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                mensajesArray.forEach(msg => {
                    const isMine = msg.id_remitente == authId;
                    const alignmentClass = isMine ? 'justify-content-end' : 'justify-content-start';
                    const flexAlign = isMine ? 'align-items-end' : 'align-items-start';
                    const bgClass = isMine ? 'bubble-sent' : 'bubble-received shadow-sm';
                    
                    let imgHtml = '';
                    if (!isMine) {
                        imgHtml = `<img src="{{ $usuarioActivo->foto ? asset('storage/' . $usuarioActivo->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($usuarioActivo->name) }}" class="rounded-circle me-2 align-self-end mb-1 border border-white shadow-sm object-fit-cover" width="28" height="28">`;
                    }

                    const timeString = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    
                    let checkIcon = '';
                    if (isMine) {
                        const checkClass = msg.leido ? 'text-info' : 'text-white-50';
                        checkIcon = `<i class="bi bi-check2-all ${checkClass}"></i>`;
                    }

                    chatBox.innerHTML += `
                        <div class="d-flex mb-3 ${alignmentClass} fade-in">
                            ${imgHtml}
                            <div class="d-flex flex-column ${flexAlign}" style="max-width: 75%;">
                                <div class="p-3 rounded-4 ${bgClass}">
                                    <p class="mb-0 lh-sm" style="font-size: 0.95rem;">${msg.contenido}</p>
                                </div>
                                <small class="text-muted mt-1 px-1" style="font-size: 0.7rem; font-weight: 600;">
                                    ${timeString} ${checkIcon}
                                </small>
                            </div>
                        </div>
                    `;
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            } else {
                chatBox.innerHTML = `
                    <div class="text-center py-5 opacity-50 h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-chat-heart display-4 text-muted mb-3"></i>
                        <p class="mt-2 fw-bold text-navy">Este es el inicio de tu conversación con {{ $usuarioActivo->name }}</p>
                        <small>Envía un mensaje para comenzar a chatear.</small>
                    </div>
                `;
            }
        });

        const formChat = document.getElementById("formChat");
        const inputMensaje = document.getElementById("inputMensaje");
        const destinatarioId = document.getElementById("destinatarioId").value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        formChat.addEventListener("submit", async function(e) {
            e.preventDefault();

            const contenido = inputMensaje.value;
            if (!contenido.trim()) return;

            inputMensaje.value = ''; 
            
            const now = new Date();
            const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            chatBox.innerHTML += `
                <div class="d-flex mb-3 justify-content-end fade-in">
                    <div class="d-flex flex-column align-items-end" style="max-width: 75%;">
                        <div class="p-3 rounded-4 bubble-sent">
                            <p class="mb-0 lh-sm" style="font-size: 0.95rem;">${contenido}</p>
                        </div>
                        <small class="text-muted mt-1 px-1" style="font-size: 0.7rem; font-weight: 600;">
                            ${timeString} <i class="bi bi-clock text-muted ms-1" title="Enviando..."></i>
                        </small>
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                await fetch("{{ route('mensajes.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id_destinatario: destinatarioId,
                        contenido: contenido
                    })
                });
            } catch (error) {
                console.error("Hubo un error al enviar el mensaje:", error);
                inputMensaje.value = contenido; 
            }
        });

    </script>
    @endif
</x-layout>