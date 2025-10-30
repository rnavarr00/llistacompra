@extends('layouts.master')

@section('title', 'Eliminar llista')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold text-danger">Eliminar llista</h1>

    <p>Estàs segur que vols eliminar la llista <strong>{{ $llista->nom }}</strong>?</p>

    <form action="{{ route('llistes.destroy', $llista->id) }}" method="POST">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
        <a href="{{ route('llistes') }}" class="btn btn-secondary">Cancel·lar</a>
    </form>
</div>
@endsection
