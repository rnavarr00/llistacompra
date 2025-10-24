@extends('layouts.master')

@section('title', 'Crear llista')

@section('content')
    <div class="container py-4"> <!-- Añadimos padding vertical con py-4 -->
        <h1 class="mb-4 fw-bold text-decoration-underline">CREAR LLISTA</h1> <!-- Más separación debajo del título -->

        <form action="{{ route('llistes.store') }}" method="POST"> 
            @csrf 
            <div class="form-group mb-3"> <!-- Más separación entre elementos -->
                <label for="listName" class="mb-2">Nom de la llista:</label> <!-- Separación debajo de la etiqueta -->
                <div class="row">
                    <div class="col-md-6"> <!-- Limita el ancho del input al 50% en pantallas medianas -->
                        <input type="text" 
                            class="form-control" 
                            id="listName" 
                            name="nom" 
                            placeholder="Introdueix el nom de la llista"
                            required>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success mt-3"> <!-- Separación encima del botón -->
                <i class="bi bi-save"></i> Guardar llista
            </button>
        </form>
    </div>
@endsection