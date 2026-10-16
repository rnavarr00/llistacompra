@extends('layouts.master')
@section('title', $llista->exists ? $llista->nom : 'Crear llista')
@section('content') 

@php
    $esNova    = !$llista->exists;
    $potEditar = $esNova || auth()->user()->can('update', $llista);
    $i = 0; // índex dels inputs productes[i][...]
@endphp

<div class="container my-5">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="llistaForm" method="POST"
          action="{{ $esNova ? route('llistes.store') : route('llistes.update', $llista->id) }}">
        @csrf
        @unless ($esNova) @method('PUT') @endunless

        {{-- Capçalera --}}
        <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex align-items-center flex-grow-1">
                <i class="{{ $llista->icona ?? 'bi bi-list-check' }} text-primary fs-2 me-3"></i>
                @if ($potEditar)
                    <input type="text" name="nom" id="listName"
                           class="form-control form-control-lg fw-bold text-primary @error('nom') is-invalid @enderror"
                           value="{{ old('nom', $llista->nom) }}"
                           placeholder="Introdueix el nom de la llista"
                           required pattern=".*\S.*"
                           title="El nom de la llista no pot estar buit ni tenir només espais.">
                @else
                    <h2 class="fw-bold text-primary mb-0" style="font-size:1.7rem;">{{ ucfirst($llista->nom) }}</h2>
                @endif
            </div>
            <a href="{{ route('llistes.index') }}" class="btn btn-outline-primary d-flex align-items-center">
                <i class="bi bi-arrow-left me-2"></i> Tornar
            </a>
        </div>

        {{-- Afegir productes (només si pot editar) --}}
        @if ($potEditar)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body position-relative">
                <label for="cercador-productes" class="form-label fw-semibold">Afegir productes</label>
                <div class="input-group">
                    <input type="hidden" id="producte_id_nou">
                    <input type="text" id="cercador-productes" class="form-control"
                           placeholder="Cerca i selecciona un producte..." autocomplete="off"
                           data-search-url="{{ url('/productes/search') }}">
                    <input type="number" id="quantitat_nou" class="form-control" value="1"
                           min="1" max="100" style="max-width:90px;">
                    <button type="button" id="afegir-producte" class="btn btn-primary">Afegir</button>
                </div>
                <div id="suggestions" class="list-group position-absolute w-100" style="z-index:100;"></div>
            </div>
        </div>

        {{-- Productes nous encara sense desar --}}
        <div id="nousCard" class="card shadow-sm border-0 mb-4 d-none">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-2">
                    <i class="bi bi-hourglass-split me-2"></i> Pendents de desar
                </h5>
                <ul id="nousProductes" class="list-group list-group-flush"></ul>
            </div>
        </div>
        @endif

        {{-- Productes existents, per categories --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                @if (count($columna1))
                <div class="row g-4">
                    @foreach ([$columna1, $columna2] as $columna)
                        @if (count($columna))
                        <div class="{{ $hasTwoColumns ? 'col-md-6' : 'col-12' }}">
                            <div class="card shadow-lg border-0 h-100">
                                <div class="card-body">
                                    @foreach ($columna as $categoria => $productes)
                                        <h5 class="fw-bold text-primary mt-3 mb-2">
                                            <i class="{{ \App\Models\Categoria::imatgeCategoria($categoria) }} me-2"></i>
                                            </i>{{ $categoria }}
                                        </h5>
                                        <ul class="list-group list-group-flush mb-3">
                                            @foreach ($productes as $producte)
                                            @php $comprat = $producte['pivot']['comprat']; @endphp
                                            <li class="list-group-item py-3 px-2 d-flex align-items-center border-0 border-bottom producte-row {{ $comprat ? 'comprat' : '' }}"
                                                data-id="{{ $producte['id'] }}">

                                                <input type="checkbox" class="form-check-input me-3 fs-5 toggle-comprat"
                                                       data-url="{{ route('productes.toggle', ['llista_id' => $llista->id, 'producte_id' => $producte['id']]) }}"
                                                       {{ $comprat ? 'checked' : '' }}>

                                                <input type="hidden" name="productes[{{ $i }}][id]" value="{{ $producte['id'] }}">

                                                @if ($potEditar)
                                                    <input type="number" class="form-control form-control-sm me-3 quantitat"
                                                           style="width:80px;" name="productes[{{ $i }}][quantitat]"
                                                           value="{{ $producte['pivot']['quantitat'] }}" min="1" max="100" required>
                                                @else
                                                    <span class="fs-5 fw-semibold me-3 text-secondary quantitat">{{ $producte['pivot']['quantitat'] }}x</span>
                                                @endif

                                                <span class="fs-5 fw-medium nom">{{ $producte['nom'] }}</span>

                                                @if ($potEditar)
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto eliminar-producte" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </li>
                                            @php $i++; @endphp
                                            @endforeach
                                        </ul>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x fs-1 d-block mb-3"></i>
                    <p class="mb-0">Encara no hi ha productes en aquesta llista.</p>
                </div>
                @endif
            </div>
        </div>

        @if ($potEditar)
        <div class="mt-4">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> {{ $esNova ? 'Guardar llista' : 'Desar canvis' }}
            </button>
        </div>
        @endif
    </form>
</div>

<style>
    .comprat .nom, .comprat .quantitat { text-decoration: line-through; color: #6c757d !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
              || document.querySelector('#llistaForm input[name="_token"]').value;

    // ---------- Marcar com a comprat (AJAX) ----------
    document.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('toggle-comprat')) return;
        const checkbox = e.target;
        const row = checkbox.closest('.producte-row');
        row.classList.toggle('comprat', checkbox.checked);

        try {
            const body = new FormData();
            body.append('_method', 'PATCH');
            body.append('_token', csrf);
            const res = await fetch(checkbox.dataset.url, {
                method: 'POST', body, headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error();
        } catch {
            // Si falla, desfem el canvi visual
            checkbox.checked = !checkbox.checked;
            row.classList.toggle('comprat', checkbox.checked);
            alert('No s\'ha pogut actualitzar el producte.');
        }
    });

    // La resta només existeix si l'usuari pot editar
    const cercador = document.getElementById('cercador-productes');
    if (!cercador) return;

    const suggestions = document.getElementById('suggestions');
    const hiddenId = document.getElementById('producte_id_nou');
    const qtyNou = document.getElementById('quantitat_nou');
    const btnAfegir = document.getElementById('afegir-producte');
    const nousCard = document.getElementById('nousCard');
    const nousUl = document.getElementById('nousProductes');
    const searchUrl = cercador.dataset.searchUrl;

    let index = document.querySelectorAll('.producte-row').length;
    let timeout = null;

    // Control de duplicats
    const afegits = new Set(
        [...document.querySelectorAll('.producte-row')].map(r => r.dataset.id)
    );

    // ---------- Autocompletat ----------
    cercador.addEventListener('input', function () {
        const query = this.value.trim();
        hiddenId.value = '';
        suggestions.innerHTML = '';
        if (!query) return;

        clearTimeout(timeout);
        timeout = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    data.forEach(product => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = product.nom;
                        item.addEventListener('click', () => {
                            cercador.value = product.nom;
                            hiddenId.value = String(product.id);
                            suggestions.innerHTML = '';
                            qtyNou.focus();
                        });
                        suggestions.appendChild(item);
                    });
                });
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!suggestions.contains(e.target) && e.target !== cercador) suggestions.innerHTML = '';
    });

    // Enter als camps d'afegir no ha d'enviar tot el formulari
    [cercador, qtyNou].forEach(el => el.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); btnAfegir.click(); }
    }));

    // ---------- Afegir producte ----------
    btnAfegir.addEventListener('click', () => {
        const id = hiddenId.value;
        const nom = cercador.value.trim();
        const quantitat = parseInt(qtyNou.value);

        if (!quantitat || quantitat < 1 || quantitat > 100) {
            alert('Indica una quantitat vàlida (1-100).');
            return;
        }
        if (!id || !nom) {
            alert('Has de seleccionar un producte dels suggeriments.');
            return;
        }
        if (afegits.has(id)) {
            alert('Aquest producte ja és a la llista.');
            return;
        }
        afegits.add(id);

        const li = document.createElement('li');
        li.className = 'list-group-item py-3 px-2 d-flex align-items-center border-0 border-bottom producte-row';
        li.dataset.id = id;
        li.innerHTML = `
            <input type="hidden" name="productes[${index}][id]" value="${id}">
            <input type="number" class="form-control form-control-sm me-3 quantitat" style="width:80px;"
                   name="productes[${index}][quantitat]" value="${quantitat}" min="1" max="100" required>
            <span class="fs-5 fw-medium nom"></span>
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto eliminar-producte" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>`;
        li.querySelector('.nom').textContent = nom; // textContent: evita injectar HTML

        nousUl.appendChild(li);
        nousCard.classList.remove('d-none');
        index++;

        cercador.value = '';
        hiddenId.value = '';
        qtyNou.value = 1;
        cercador.focus();
    });

    // ---------- Eliminar producte ----------
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.eliminar-producte');
        if (!btn) return;
        const row = btn.closest('.producte-row');
        afegits.delete(row.dataset.id);
        row.remove();
        if (!nousUl.children.length) nousCard.classList.add('d-none');
    });
});
</script>
@endsection