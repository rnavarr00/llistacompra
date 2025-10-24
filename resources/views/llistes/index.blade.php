@extends('layouts.master')
@section('title', 'Les meves llistes')
@section('content')

<div class="container">

    {{-- ENCAPÇALAMENT AMB BOTÓ DE CREAR LLISTA --}}
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2 class="fw-bold text-primary mb-0 fs-3">
            <i class="bi bi-list-check me-2 fs-3"></i> Les meves llistes
        </h2>

        <a href="{{ route('llistes.create') }}"
            class="btn btn-primary btn-lg px-4 d-flex align-items-center justify-content-center shadow-sm">
            <i class="bi bi-plus-lg fs-3"></i>
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
                        <li><a class="dropdown-item" href="{{ route('llistes.edit', $llista->id) }}">Editar</a></li>
                        <li><a class="dropdown-item text-danger" href="#">Eliminar</a></li>
                        <li><a class="dropdown-item" href="#">Compartir</a></li>
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