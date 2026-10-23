@extends('layouts.master')
@section('title', $llista->nom )
@section('content') 

@php
    $potEditar =auth()->user()->can('update', $llista);
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

    <form id="llistaForm" method="POST" action="{{ route('llistes.update', $llista->id) }}">
        @csrf
        @method('PUT')

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
                    <h2 class="fw-bold text-primary mb-0" style="font-size:1.7rem;">{{($llista->nom) }}
                    </h2>
                @endif
            </div>

            <a href="{{ route('llistes.index') }}"
            class="btn btn-outline-primary d-flex align-items-center">
                <i class="bi bi-arrow-left me-2"></i> Tornar
            </a>
        </div>

        {{-- Selector de color de la llista --}}
        @if ($potEditar)
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-palette me-2"></i>Color de la llista
                </label>

                <div class="selector-colors">
                    {{-- Sense color --}}
                    <label class="opcion-color">
                        <input
                            type="radio"
                            name="color"
                            value=""
                            {{ old('color', $llista->color ?? '') == '' ? 'checked' : '' }}
                        >
                        <span class="muestra-color sin-color" title="Sense color">
                            <i class="bi bi-slash-circle"></i>
                        </span>
                        <span class="nombre-color">Sense color</span>
                    </label>

                    {{-- Colors de la paleta --}}
                    @foreach ($colors as $hex => $nombre)
                        <label class="opcion-color">
                            <input
                                type="radio"
                                name="color"
                                value="{{ $hex }}"
                                {{ old('color', $llista->color ?? '') === $hex ? 'checked' : '' }}
                            >
                            <span
                                class="muestra-color"
                                style="background-color: {{ $hex }};"
                                title="{{ $nombre }}"
                            ></span>
                            <span class="nombre-color">{{ $nombre }}</span>
                        </label>
                    @endforeach
                </div>

                @error('color')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        @endif

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

                                            <span class="fs-5 fw-medium nom">{{ $producte['nom'] }}</span>

                                            @if ($potEditar)
                                                <input type="number" class="form-control form-control-sm ms-auto me-3 quantitat"
                                                    style="width:80px;" name="productes[{{ $i }}][quantitat]"
                                                    value="{{ $producte['pivot']['quantitat'] }}" min="1" max="100" required>

                                                <button type="button" class="btn btn-sm btn-outline-danger eliminar-producte" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <span class="fs-5 fw-semibold ms-auto text-secondary quantitat">{{ $producte['pivot']['quantitat'] }}x</span>
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
                <i class="bi bi-save"></i>Desar canvis
            </button>
        </div>
        @endif
    </form>
</div>

<style>
    .comprat .nom, .comprat .quantitat { text-decoration: line-through; color: #6c757d !important; }

    /* Selector de color de la llista */
    .selector-colors {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 10px;
    }

    .opcion-color {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .opcion-color input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .muestra-color {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .sin-color {
        background-color: #ffffff;
        color: #697586;
        border: 2px dashed #adb5bd;
    }

    .opcion-color input:checked + .muestra-color {
        outline: 3px solid #212529;
        outline-offset: 3px;
        transform: scale(1.05);
    }

    .opcion-color input:focus-visible + .muestra-color {
        outline: 3px solid #0d6efd;
        outline-offset: 4px;
    }

    .nombre-color {
        color: #495057;
    }

    @media (prefers-reduced-motion: reduce) {
        .muestra-color {
            transition: none;
        }
    }
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