@extends('layouts.master')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Editar llista</h1>

    <form method="POST" action="{{ route('llistes.update', $llista->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la llista</label>
            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                   id="nom" name="nom" value="{{ old('nom', $llista->nom) }}" required>
            @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h4 class="mt-4">Productes</h4>

        <div id="productes-lista">
            @foreach ($llista->productes as $producte)
                <div class="row mb-2 align-items-center">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="productes[{{ $loop->index }}][nom]"
                               value="{{ $producte->nom }}" readonly>
                        <input type="hidden" name="productes[{{ $loop->index }}][id]" value="{{ $producte->id }}">
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="productes[{{ $loop->index }}][quantitat]"
                               value="{{ $producte->pivot->quantitat }}" min="1" max="100" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger eliminar-producte">Eliminar</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3 position-relative"> 
            <label for="cercador-productes" class="form-label">Afegir nous productes</label>
            <div class="input-group">
                <input type="hidden" id="producte_id_nou" value="">

                <input type="text" id="cercador-productes" class="form-control" placeholder="Cerca i selecciona un producte..." 
                       data-search-url="{{ url('/productes/search') }}">
                
                <input type="number" id="quantitat_nou" class="form-control" placeholder="Qtat" value="1" min="1" style="max-width: 80px;" required>

                <button type="button" id="afegir-producte" class="btn btn-primary">Afegir</button>
            </div>
            
            <div id="suggestions" class="list-group position-absolute w-100" style="z-index: 100;"></div>
        </div>

        <button type="submit" class="btn btn-success mt-4">Desar canvis</button>
        <a href="{{ route('llistes.index') }}" class="btn btn-secondary mt-4">Cancel·lar</a>
    </form>
</div>

<script>
    // Inicialització de l'índex per als nous productes
    let index = {{ $llista->productes->count() }}; 
    
    const productesDiv = document.getElementById('productes-lista');
    const botoAfegir = document.getElementById('afegir-producte');
    const inputQuantitatNou = document.getElementById('quantitat_nou'); // <--- NOU INPUT

    const inputCercador = document.getElementById('cercador-productes');
    const suggestions = document.getElementById('suggestions');
    const hiddenInputId = document.getElementById('producte_id_nou');
    const searchUrl = inputCercador.dataset.searchUrl;
    let timeout = null;

    // Control de Duplicats
    const productesAfegits = new Set();
    
    // Omplir el Set amb els IDs dels productes ja existents a la llista
    document.querySelectorAll('#productes-lista input[type="hidden"][name$="[id]"]').forEach(input => {
        productesAfegits.add(input.value);
    });

    inputCercador.addEventListener('input', function() {
        const query = this.value.trim();
        hiddenInputId.value = ''; // Netejar l'ID al començar a escriure
        suggestions.innerHTML = '';

        if (query.length === 0) return;

        clearTimeout(timeout);
        timeout = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    
                    data.forEach(product => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.classList.add('list-group-item', 'list-group-item-action'); 
                        item.textContent = product.nom;
                        
                        item.addEventListener('click', () => {
                            // Al fer clic en una suggerència, actualitzem els inputs
                            inputCercador.value = product.nom;
                            hiddenInputId.value = String(product.id);
                            suggestions.innerHTML = '';
                            inputQuantitatNou.focus(); // Enfocar la quantitat per introduir-la
                        });
                        suggestions.appendChild(item);
                    });
                });
        }, 300); 
    });

    // Amagar suggeriments al fer clic fora dels elements
    document.addEventListener('click', (e) => {
        if (!suggestions.contains(e.target) && e.target !== inputCercador) {
            suggestions.innerHTML = '';
        }
    });

    botoAfegir.addEventListener('click', function() {
        const producteId = hiddenInputId.value;
        const producteName = inputCercador.value.trim();
        // guardem la quantitat abans d'afegir
        const quantitat = inputQuantitatNou.value; 

        // Validem la quantitat
        if (!quantitat || parseInt(quantitat) < 1) {
            alert('Indica una quantitat vàlida (mínim 1).');
            return;
        }

        // Validem el producte
        if (!producteId || !producteName) {
            alert('Has de seleccionar un producte de la llista de suggeriments abans d\'afegir.');
            return;
        }

        // Evitem productes duplicats
        if (productesAfegits.has(producteId)) {
            alert('Aquest producte ja ha estat afegit a la llista.');
            return;
        }

        productesAfegits.add(producteId);

        // Creació de la nova fila
        const fila = document.createElement('div');
        fila.className = 'row mb-2 align-items-center';
        fila.innerHTML = `
            <div class="col-md-6">
                <input type="text" class="form-control" name="productes[${index}][nom]" value="${producteName}" readonly>
                <input type="hidden" name="productes[${index}][id]" value="${producteId}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="productes[${index}][quantitat]" value="${quantitat}" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger eliminar-producte">Eliminar</button>
            </div>
        `;
        
        productesDiv.appendChild(fila);
        index++;
        
        // Netejar inputs
        inputCercador.value = '';
        hiddenInputId.value = '';
        suggestions.innerHTML = '';
        inputQuantitatNou.value = '1';
    });

    document.addEventListener('click', e => {
        if (e.target.classList.contains('eliminar-producte')) {
            const rowElement = e.target.closest('.row');
            
            // Trobar l'ID del producte per eliminar-lo
            const producteIdEliminar = rowElement.querySelector('input[name$="[id]"]').value;
            
            if (productesAfegits.has(producteIdEliminar)) {
                 productesAfegits.delete(producteIdEliminar);
            }
            
            rowElement.remove();
        }
    });
</script>
@endsection