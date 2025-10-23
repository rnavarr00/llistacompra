@extends('layouts.master')

@section('title', 'Les meves llistes')

@section('content')

<div class="container py-4">


    {{-- BOTÓ AFEGIR LLISTA --}}
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('llistes.create') }}" class="btn btn-primary">
            + 
        </a>
    </div>

    {{-- GRAELLA DE TARGETES --}}
    <div class="row g-4">
        @foreach ($llistes as $llista)
        <div class="col-12 col-sm-6 col-md-4">
            {{-- TARGETA --}}
            <div class="card text-center h-100 shadow-sm border-0 position-relative list-card"
                
                style="cursor: pointer; transition: box-shadow 0.2s ease-in-out;">

                {{-- MENÚ DE TRES PUNTETS --}}
                <div class="dropdown position-absolute top-0 end-0 m-2">
                    <button class="btn btn-light btn-sm" type="button" id="menu{{ $llista->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menu{{ $llista->id }}">
                        <li><a class="dropdown-item" href="{{ route('llistes.edit', $llista->id) }}">✏️ Editar</a></li>
                        <li><a class="dropdown-item text-danger" href="#">❌ Eliminar</a></li>
                        <li><a class="dropdown-item" href="#">🤝 Compartir</a></li>
                    </ul>
                </div>

                {{-- CONTINGUT DE LA TARGETA --}}
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    {{-- ICONA --}}
                    <div class="fs-1 mb-2 text-secondary">
                        <i class="{{ $llista->icona ?? 'bi bi-list-check' }}"></i>
                    </div>
                    {{-- NOM --}}
                    <h5 class="card-title mb-0 text-capitalize">{{ $llista->nom }}</h5>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- {{-- PAGINACIÓ --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $llistes->links('pagination::bootstrap-4') }}
    </div> -->

    {{-- ESTILS ADDICIONALS (HOVER, ETC.) --}}
    <style>
        .list-card:hover {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15) !important;
        }
    </style>

</div>

@endsection