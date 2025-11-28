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

        <div class="input-group mt-3">
            <input type="text" id="cercador-productes" class="form-control" placeholder="Afegeix un producte...">
            <button type="button" id="afegir-producte" class="btn btn-primary">Afegir</button>
        </div>

        <button type="submit" class="btn btn-success mt-4">Desar canvis</button>
        <a href="{{ route('llistes.index') }}" class="btn btn-secondary mt-4">Cancel·lar</a>
    </form>
</div>

<script>
    let index = {{ $llista->productes->count() }};
    const productesDiv = document.getElementById('productes-lista');
    const inputCercador = document.getElementById('cercador-productes');
    const botoAfegir = document.getElementById('afegir-producte');

    botoAfegir.addEventListener('click', async () => {
        const q = inputCercador.value.trim();
        if (!q) return;

        const res = await fetch(`/productes/search?q=${encodeURIComponent(q)}`);
        const productes = await res.json();

        if (productes.length === 0) {
            alert('No s’ha trobat cap producte amb aquest nom.');
            return;
        }

        const p = productes[0]; // Agafem el primer resultat

        const fila = document.createElement('div');
        fila.className = 'row mb-2 align-items-center';
        fila.innerHTML = `
            <div class="col-md-6">
                <input type="text" class="form-control" name="productes[${index}][nom]" value="${p.nom}" readonly>
                <input type="hidden" name="productes[${index}][id]" value="${p.id}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="productes[${index}][quantitat]" value="1" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger eliminar-producte">Eliminar</button>
            </div>
        `;
        productesDiv.appendChild(fila);
        index++;
        inputCercador.value = '';
    });

    document.addEventListener('click', e => {
        if (e.target.classList.contains('eliminar-producte')) {
            e.target.closest('.row').remove();
        }
    });
</script>
@endsection
