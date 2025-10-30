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
                <label for="quantitat" class="mb-2 mt-3">Quantitat:</label>
                <div class="row">
                    <div class="col-md-3">
                        <input 
                            type="number" 
                            class="form-control" 
                            id="quantitat" 
                            name="quantitat" 
                            placeholder="Ex: 2" 
                            min="1" 
                            required>
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
        // El que escriu l'usuari
        const input = document.getElementById('nomProducte');
        // El que sugereix el buscador
        const suggestions = document.getElementById('suggestions');
        // El que escriu l'usuari, es transforma en id (el del producte)
        const hiddenInput = document.getElementById('producte_id');

        const searchUrl = input.dataset.searchUrl;
        // Evitem mostrar productes si l'usuari encara està escrivint
        let timeout = null; 

        input.addEventListener('input', function() {
            const query = this.value.trim(); // Borrem espais abans i després
            hiddenInput.value = ''; // Netejem id si el text canvia
            suggestions.innerHTML = ''; //Eliminem les sugerències

            // Si no hi ha text, no busca res
            if (query.length === 0) return;

            clearTimeout(timeout);
            timeout = setTimeout(() => {
                // Amb encodeURI fem que caràcters especials no corrompin la búsqueda
                fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions.innerHTML = ''; // Netejar anteriors sugerències
                        data.forEach(product => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.textContent = product.nom;

                            // Al fer click en el producte, omplim l'input visible i el que no veiem (id)
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
            }, 300); // espera 300ms després de que l'usuari deixi d'escriure abans de buscar
        });

        // Deixem d'ensenyar les sugerències si l'usuari fa click fora del requadre
        document.addEventListener('click', (e) => {
            if (!suggestions.contains(e.target) && e.target !== input) {
                suggestions.innerHTML = '';
            }
        });
    });
    </script>

@endsection