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
                        <input 
                            type="text" 
                            class="form-control" 
                            id="nomProducte" 
                            name="producte_input" 
                            placeholder="Introdueix el producte"
                            autocomplete="off"
                            data-search-url="{{ route('productes.search') }}"
                        >
                        
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
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('nomProducte');
        const suggestions = document.getElementById('suggestions');
        const hiddenInput = document.getElementById('producte_id');

        const searchUrl = input.dataset.searchUrl;
        let timeout = null; // para controlar el "debounce"

        input.addEventListener('input', function() {
            const query = this.value.trim();
            hiddenInput.value = ''; // limpiamos el id si el texto cambia
            suggestions.innerHTML = '';

            // Si no hay texto, no buscamos
            if (query.length === 0) return;

            // Cancelar cualquier búsqueda anterior y esperar un poco (debounce)
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions.innerHTML = ''; // limpiar anteriores
                        data.forEach(product => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.textContent = product.nom;

                            // Al hacer clic, rellenamos el input visible y oculto
                            item.addEventListener('click', () => {
                                input.value = product.nom;
                                hiddenInput.value = product.id;
                                suggestions.innerHTML = '';
                            });

                            suggestions.appendChild(item);
                        });

                        if (data.length === 0) {
                            const noResult = document.createElement('div');
                            noResult.classList.add('list-group-item', 'text-muted');
                            noResult.textContent = 'Sense resultats';
                            suggestions.appendChild(noResult);
                        }
                    })
                    .catch(err => {
                        console.error('Error al buscar productes:', err);
                    });
            }, 300); // espera 300ms después de que el usuario deje de escribir
        });

        // Ocultar las sugerencias si se hace clic fuera
        document.addEventListener('click', (e) => {
            if (!suggestions.contains(e.target) && e.target !== input) {
                suggestions.innerHTML = '';
            }
        });
    });
    </script>

@endsection