@extends('layouts.master')
@section('title', 'Les meves llistes')
@section('content')

<div class="container">

    {{-- TÍTOL PRINCIPAL --}}
    <h1 class="text-center my-5">Les meves llistes</h1>

    {{-- BOTÓ DE NOVA LLISTA --}}
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('llistes.create') }}"
            class="btn btn-primary btn-lg px-4 py-2 shadow-sm fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg fs-5"></i>
            +
        </a>
    </div>



    {{-- GRAELLA DE LLISTES --}}
    <div class="row g-4">

        @forelse ($llistes as $llista)
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card h-100 text-center shadow-sm border-0 position-relative list-card"
                style="cursor:pointer; transition:box-shadow 0.2s ease-in-out;">

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

                {{-- COS DE LA TARGETA --}}
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    {{-- ICONA (es mostrarà la seleccionada o una genèrica) --}}
                    <div class="fs-1 mb-3 text-secondary">
                        <i class="{{ $llista->icona ?? 'bi bi-list-check' }}"></i>
                    </div>
                    {{-- NOM DE LA LLISTA --}}
                    <h5 class="card-title mb-0 text-capitalize">{{ $llista->nom }}</h5>
                </div>
            </div>
        </div>

        @empty
        {{-- ESTAT BUIT: cap llista creada --}}
        <div class="col-12 text-center mt-5">
            <p class="text-muted">Encara no tens cap llista creada.</p>
            <a href="{{ route('llistes.create') }}" class="btn btn-outline-primary">
                + Crea la teva primera llista
            </a>
        </div>
        @endforelse

    </div>



    {{-- ESTILS ADDICIONALS --}}
    <style>
        .list-card:hover {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    @endsection