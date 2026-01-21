<style>
    .menu-card:hover {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        transition: box-shadow 0.2s;
        cursor: pointer;
    }
</style>
<div class="card menu-card" onclick="{{ $funToDo }}">
    <div class="card-body text-center">
        <img src="{{ $imgPath }}" width="60px" height="60px" class="d-block mx-auto rounded-circle"
            alt="imagen de {{ $cardTitle }}" />
        <h4 class="card-title">{{ $cardTitle }}</h4>
        <p class="card-text">{{ $cardDescr }}</p>
    </div>
</div>

<script></script>
