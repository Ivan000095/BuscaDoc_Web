<x-layout>
    <!-- <div class="card md-4">
        <img class="card-img-top w-100" src="{{asset('storage/'.$doctor->image) }}" alt="Title" />
        <div class="card-body">
            <h4 class="card-title">{{$doctor->name}}</h4>
            <p class="card-text">Text</p>
        </div>
    </div> -->
    <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="card h-100 border-50 shadow-sm hover-card">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                
                <img src="{{asset('storage/'.$doctor->image) }}" 
                alt="" 
                class="rounded-circle mb-3 shadow-sm object-fit-cover" 
                style="width: 80px; height: 80px;">
                
                <h5 class="card-title fw-bold custom-text-dark">{{$doctor->name}}</h5>
                <p class="card-text text-muted small">{{ $doctor->descripcion }}</p>
                
                <a href="#" class="btn btn-custom btn-sm stretched-link mt-2 rounded-pill px-4">Entrar</a>
            </div>
        </div>
    </div>
    
    <div class="row">

    </div>
</x-layout>