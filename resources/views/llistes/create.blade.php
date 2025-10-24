@extends('layouts.master')

@section('title', 'Crear llista')

@section('content')
    <div class="container py-4"> <!-- Añadimos padding vertical con py-4 -->
        <h1 class="mb-4 fw-bold text-decoration-underline">CREAR LLISTA</h1> <!-- Más separación debajo del título -->

        <form action="{{ route('llistes.store') }}" method="POST"> 
            @csrf 
            <div class="form-group mb-3"> <!-- Más separación entre elementos -->

                <!-- Nom de la llista -->
                <label for="listName" class="mb-2">Nom de la llista:</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" 
                            class="form-control" 
                            id="listName" 
                            name="nom" 
                            placeholder="Introdueix el nom de la llista"
                            required>
                    </div>
                </div>

                <!-- Nom del producte, del que sortiràn sugerències -->
                <label for="nomProducte" class="mb-2 mt-3">Nom del producte:</label>
                <div class="row">
                    <div class="col-md-6 position-relative">
                        <!-- Què veu l'usuari -->
                        <input type="text" 
                            class="form-control" 
                            id="nomProducte" 
                            name="producte_input" 
                            placeholder="Introdueix el producte"
                            autocomplete="off">
                        
                        <!-- Aquest camp no es veu, ens serveix per enviar el producte amb l'id a la BD -->
                        <input type="hidden" name="producte_id" id="producte_id">

                        <!-- Contenidor on s'ensenyaràn les sugerències -->
                        <div id="suggestions" class="list-group position-absolute" style="z-index:1000;"></div>
                    </div>
                </div>

            </div>

            
            <button type="submit" class="btn btn-success mt-3"> <!-- Separación encima del botón -->
                <i class="bi bi-save"></i> Guardar llista
            </button>
        </form>
    </div>

    {{-- Script que ens ajudarà a autocompletar el que l'usuari escrigui al producte --}}
    <script>
        // Agafem el valor que l'usuari escriu a nomProducte 
        const producteInput = document.getElementById('nomProducte');
        const suggestions = document.getElementById('suggestions');

        producteInput.addEventListener('input', function() {
            const query = this.value.trim(); 

            // Netejem el que hi havia abans
            suggestions.innerHTML = '';

            // En cas que no hagin escrit res, no sortirà cap suggerència
            if (query.length === 0) {
                return; 
            }

            // Aquí pondremos las sugerencias filtradas más adelante
            // Por ahora, solo mostramos algo fijo como prueba
            const dummyProducts = ['Manzana', 'Mandarina', 'Mango'];
            const matches = dummyProducts.filter(p => p.toLowerCase().startsWith(query.toLowerCase()));

            matches.forEach(product => {
                const item = document.createElement('div');
                item.classList.add('list-group-item', 'list-group-item-action');
                item.textContent = product;
                suggestions.appendChild(item);
            });
        });
    </script>
@endsection