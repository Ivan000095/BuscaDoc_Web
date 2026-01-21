@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('messages.Welcome') }}</div>

                <div class="card-body">
                    @guest
                        <h4>Bienvenido invitado!!!</h4>
                        {{-- <p class="mb-4">Por favor, inicia sesión para acceder al menú principal.</p> --}}
                        <p class="mt-4">Aquí puedes acceder a la información de contenido público</p>
                    @else
                        <h4>¡Hola, {{ Auth::user()->name }} !</h4>
                        <p class="mb-4">Has iniciado sesión correctamente.</p>
                        <p class="mb-4">En breve serás redireccionado...</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Ir al Menú Principal') }}</a>
                    @endguest
                </div>
            </div>

            
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .gap-3 {
        gap: 1rem;
    }
    .d-flex {
        display: flex;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
@auth
    // redireccinar a home en 5 segs
    setTimeout(function() {
        window.location.href = "{{ route('home') }}";
    }, 2000);
@endauth

</script>
@endpush
