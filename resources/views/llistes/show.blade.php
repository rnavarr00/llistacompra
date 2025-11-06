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

            <h5 class="fw-semibold mb-3 text-secondary">
                <i class="bi bi-cart-check me-2"></i> Productes de la llista
            </h5>

            @if ($llista->productes->count())
            <ul class="list-group list-group-flush">
                @foreach ($llista->productes as $producte)
                <li class="list-group-item py-3 px-2 d-flex align-items-center justify-content-between border-0 border-bottom">
                    <form action="{{ route('productes.toggle', ['llista_id' => $llista->id, 'producte_id' => $producte->id]) }}"
                        method="POST" class="d-flex align-items-center w-100">
                        @csrf
                        @method('PATCH')

                        {{-- Checkbox --}}
                        <input type="checkbox"
                            class="form-check-input me-3 fs-5"
                            onchange="this.form.submit()"
                            {{ $producte->pivot->comprat ? 'checked' : '' }}>

                        {{-- Quantitat en gris (tatxada si està comprat) --}}
                        <span class="fs-5 fw-semibold me-3 {{ $producte->pivot->comprat ? 'text-decoration-line-through text-muted' : 'text-secondary' }}">
                            {{ $producte->pivot->quantitat ?? 1 }}x
                        </span>

                        {{-- Nom del producte (tatxat si està comprat) --}}
                        <span class="fs-5 {{ $producte->pivot->comprat ? 'text-decoration-line-through text-muted' : 'fw-medium' }}">
                            {{ $producte->nom }}
                        </span>
                    </form>
                </li>
                @endforeach
            </ul>
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