@extends('layouts.master')

@section('title', 'Crear llista')

@section('content')
<div class="container py-4"> 
    <h1 class="mb-4 fw-bold text-decoration-underline">CREAR LLISTA</h1> 

    <form action="{{ route('llistes.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3"> 

            <label for="listName" class="mb-2">Nom de la llista:</label>
            <div class="row">
                <div class="col-md-6">
                    <input
                        type="text"
                        class="form-control"
                        id="listName"
                        name="nom"
                        placeholder="Introdueix el nom de la llista"
                        required
                        pattern=".*\S.*"
                        title="El nom de la llista no pot estar buit ni tenir només espais.">

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
                        data-search-url="{{ route('productes.search') }}">

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
            <div id="productesContainer">
            </div>
        </div>

        <div class="mb-2">
            <button type="button" class="btn btn-primary d-block" id="afegirProducte">
                <i class="bi bi-plus-circle"></i> Afegir producte
            </button>
        </div>

        <div id="llistaVisual" class="mt-3 mb-3">
            <h5>Productes afegits:</h5>
            <ul class="list-group" id="productesList"></ul>
        </div>

        <div class="mb-2">
            <button type="submit" class="btn btn-success d-block">
                <i class="bi bi-save"></i> Guardar llista
            </button>
        </div>
    </form>
</div>

{{-- Script que ens ajudarà a autocompletar el que l'usuari escrigui al producte --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('nomProducte');
        const suggestions = document.getElementById('suggestions');
        const hiddenInput = document.getElementById('producte_id');
        const searchUrl = input.dataset.searchUrl;
        let timeout = null;

        // S'activa cada cop que l'usuari escriu (o borra) qualsevol tecla del teclat
        input.addEventListener('input', function() {
            const query = this.value.trim(); // borrem els espais en blanc
            hiddenInput.value = '';
            suggestions.innerHTML = '';

            if (query.length === 0) return;

            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetch(`${searchUrl}?q=${encodeURIComponent(query)}`) // encodeUri el que fa es no tindre en compte accents, dieresis i demés
                    .then(res => res.json())
                    .then(data => {
                        suggestions.innerHTML = '';
                        data.forEach(product => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.textContent = product.nom;
                            item.addEventListener('click', () => {
                                input.value = product.nom;
                                hiddenInput.value = product.id;
                                suggestions.innerHTML = '';
                            });
                            suggestions.appendChild(item);
                        });
                    });
            // Buscarem quan hagi passat un temps (0.3 segons), per evitar fer una petició per cada lletra
            // que escrigui o borri l'usuari
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!suggestions.contains(e.target) && e.target !== input) {
                suggestions.innerHTML = '';
            }
        });

        let productIndex = 0;
        let productesAfegits = new Set(); //Control de duplicats

        const productesContainer = document.getElementById('productesContainer');
        const productesList = document.getElementById('productesList');
        const afegirBtn = document.getElementById('afegirProducte');

        afegirBtn.addEventListener('click', function() {
            const producteId = hiddenInput.value;
            const producteName = input.value.trim();
            const quantitat = document.getElementById('quantitat').value;

            if (!producteId || !producteName || !quantitat) {
                alert('Selecciona un producte i indica una quantitat vàlida.');
                return;
            }

            // Evitar productes duplicats
            if (productesAfegits.has(producteId)) {
                alert('Aquest producte ja ha estat afegit a la llista.');
                return;
            }

            // Afegim producte 
            productesAfegits.add(producteId);

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = `productes[${productIndex}][id]`;
            inputId.value = producteId;

            const inputQuantitat = document.createElement('input');
            inputQuantitat.type = 'hidden';
            inputQuantitat.name = `productes[${productIndex}][quantitat]`;
            inputQuantitat.value = quantitat;

            productesContainer.appendChild(inputId);
            productesContainer.appendChild(inputQuantitat);

            // Llista visual
            const li = document.createElement('li');
            li.classList.add('list-group-item', 'd-flex', 'justify-content-between');
            li.textContent = `${producteName} — Quantitat: ${quantitat}`;
            productesList.appendChild(li);

            productIndex++;

            // Netejar input un cop s'ha afegit el producte
            input.value = '';
            hiddenInput.value = '';
            document.getElementById('quantitat').value = 1;
        });

    });
</script>

@endsection