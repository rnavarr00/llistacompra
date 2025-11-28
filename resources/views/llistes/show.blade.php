@extends('layouts.master')
@section('title', $llista->nom)
@section('content')

<div class="container my-5">

    {{-- Capçalera --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="{{ $llista->icona ?? 'bi bi-list-check' }} text-primary fs-2 me-3"></i>
            <h2 class="fw-bold text-primary mb-0">{{ ucfirst($llista->nom) }}</h2>
        </div>

        <a href="{{ route('llistes.index') }}" class="btn btn-outline-primary d-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Tornar
        </a>
    </div>

    {{-- Contenidor principal --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h5 class="fw-semibold mb-4 text-secondary fs-4">
                <i class="bi bi-cart-check me-2"></i> Productes de la llista
            </h5>

            @if ($llista->productes->count())

            <div class="row g-4">

                {{-- TARGETA 1 --}}
                <div class="{{ $hasTwoColumns ? 'col-md-6' : 'col-12' }}">
                    <div class="card shadow-lg border-0 h-100">
                        <div class="card-body">

                            @foreach ($columna1 as $categoria => $productes)

                            {{-- Nom categoria --}}
                            <h5 class="fw-bold text-primary mt-3 mb-2">
                                <i class="bi bi-folder2-open me-2"></i>
                                {{ $categoria }}
                            </h5>

                            {{-- Productes --}}
                            <ul class="list-group list-group-flush mb-3">
                                @foreach ($productes as $producte)
                                <li class="list-group-item py-3 px-2 d-flex align-items-center border-0 border-bottom">

                                    <form action="{{ route('productes.toggle', ['llista_id' => $llista->id, 'producte_id' => $producte['id']]) }}"
                                        method="POST" class="d-flex align-items-center w-100">

                                        @csrf
                                        @method('PATCH')

                                        {{-- Checkbox --}}
                                        <input type="checkbox"
                                            class="form-check-input me-3 fs-5"
                                            onchange="this.form.submit()"
                                            {{ $producte['pivot']['comprat'] ? 'checked' : '' }}>

                                        {{-- Quantitat --}}
                                        <span class="fs-5 fw-semibold me-3 {{ $producte['pivot']['comprat'] ? 'text-decoration-line-through text-muted' : 'text-secondary' }}">
                                            {{ $producte['pivot']['quantitat'] }}x
                                        </span>

                                        {{-- Nom producte --}}
                                        <span class="fs-5 {{ $producte['pivot']['comprat'] ? 'text-decoration-line-through text-muted' : 'fw-medium' }}">
                                            {{ $producte['nom'] }}
                                        </span>

                                    </form>

                                </li>
                                @endforeach
                            </ul>

                            @endforeach

                        </div>
                    </div>
                </div>

                {{-- TARGETA 2 — NOMÉS SI HI HA CATEGORIES --}}
                @if ($hasTwoColumns)
                <div class="col-md-6">
                    <div class="card shadow-lg border-0 h-100">
                        <div class="card-body">

                            @foreach ($columna2 as $categoria => $productes)

                            {{-- Nom categoria --}}
                            <h5 class="fw-bold text-primary mt-3 mb-2">
                                <i class="bi bi-folder2-open me-2"></i>
                                {{ $categoria }}
                            </h5>

                            {{-- Productes --}}
                            <ul class="list-group list-group-flush mb-3">
                                @foreach ($productes as $producte)
                                <li class="list-group-item py-3 px-2 d-flex align-items-center border-0 border-bottom">

                                    <form action="{{ route('productes.toggle', ['llista_id' => $llista->id, 'producte_id' => $producte['id']]) }}"
                                        method="POST" class="d-flex align-items-center w-100">

                                        @csrf
                                        @method('PATCH')

                                        {{-- Checkbox --}}
                                        <input type="checkbox"
                                            class="form-check-input me-3 fs-5"
                                            onchange="this.form.submit()"
                                            {{ $producte['pivot']['comprat'] ? 'checked' : '' }}>

                                        {{-- Quantitat --}}
                                        <span class="fs-5 fw-semibold me-3 {{ $producte['pivot']['comprat'] ? 'text-decoration-line-through text-muted' : 'text-secondary' }}">
                                            {{ $producte['pivot']['quantitat'] }}x
                                        </span>

                                        {{-- Nom producte --}}
                                        <span class="fs-5 {{ $producte['pivot']['comprat'] ? 'text-decoration-line-through text-muted' : 'fw-medium' }}">
                                            {{ $producte['nom'] }}
                                        </span>

                                    </form>

                                </li>
                                @endforeach
                            </ul>

                            @endforeach

                        </div>
                    </div>
                </div>
                @endif

            </div>

            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard-x fs-1 d-block mb-3"></i>
                <p class="mb-0">Encara no hi ha productes en aquesta llista.</p>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection