@extends('layouts.master')

@section('title', 'Les meves llistes')

@section('content')

<div class="container py-4">

    {{-- BOTÓ AFEGIR LLISTA --}}
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('llistes.create') }}" class="btn btn-primary">
            + Afegir llista
        </a>
    </div>

    {{-- GRAELLA DE TARGETES --}}
    <div class="row g-4">
        @foreach ($llistes as $llista)
        <div class="col-12 col-sm-6 col-md-4">
            {{-- TARGETA --}}
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ route('llistes.show', $llista->id) }}" class="text-decoration-none text-dark">
                    <div class="card text-center h-100 shadow-sm border-0 position-relative list-card"
                        style="background-color: #fff; transition: box-shadow 0.2s ease-in-out;">

                        {{-- MENÚ DE TRES PUNTETS --}}
                        <div class="dropdown position-absolute top-0 end-0 m-2" style="z-index: 1;">
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
                            <h6 class="card-title mb-0 text-capitalize">{{ $llista->nom }}</h6>
                        </div>
                    </div>
                </a>
            </div>

        </div>
        @endforeach
    </div>

    {{-- ESTILS ADDICIONALS --}}
    <style>
        .list-card {
            min-height: 250px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        }

        .list-card:hover {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15) !important;
        }

        .card-title {
            font-size: 1rem;
        }

        .dropdown {
            position: absolute;
            top: 0;
            right: 0;
        }
    </style>

</div>

@endsection