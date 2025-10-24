@extends('layouts.master')

@section('title', $llista->nom)

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">
        <i class="{{ $llista->icona ?? 'bi bi-list-check' }}"></i>
        {{ $llista->nom }}
    </h2>

    <p class="text-muted">Aquí es mostraran els elements d’aquesta llista.</p>

    <a href="{{ route('llistes.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Tornar a les meves llistes
    </a>
</div>
@endsection
