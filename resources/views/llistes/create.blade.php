@extends('layouts.master')

@section('title', 'Crear llista')

@section('content')
    <div class="container">
        <h1>Crear llista</h1>
        
        <form action="{{ route('llistes.store') }}" method="POST"> 
            @csrf 
            <div class="form-group">
                <label for="listName">Nom de la llista:</label>
                <input type="text" 
                       class="form-control" 
                       id="listName" 
                       name="nom" 
                       placeholder="Introdueix el nom de la llista"
                       required>
            </div>
            
            {{-- Puedes añadir otros campos aquí si es necesario --}}
            
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Guardar llista
            </button>
        </form>
        
    </div>
@endsection