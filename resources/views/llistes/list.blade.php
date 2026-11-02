@extends('layouts.master')
@section('title', $llista->nom)
@section('content')

@php
    $potEditar = auth()->user()->can('update', $llista);
@endphp

<div class="container my-5 p-4 rounded-4" id="fonsLlista"
     data-csrf="{{ csrf_token() }}"
     data-nom-url="{{ route('llistes.nom', $llista->id) }}"
     data-color-url="{{ route('llistes.color', $llista->id) }}"
     data-add-url="{{ route('llistes.productes.afegir', $llista->id) }}"
     style="background-color: {{ $llista->color ? $llista->color . '18' : 'transparent' }};
            transition: background-color 0.25s ease;">

    {{-- Capçalera --}}
    <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center flex-grow-1">
            <i class="{{ $llista->icona ?? 'bi bi-list-check' }} text-primary fs-2 me-3"></i>
            @if ($potEditar)
                <input type="text" name="nom" id="listName"
                    class="form-control form-control-lg fw-bold text-primary @error('nom') is-invalid @enderror"
                    value="{{ old('nom', $llista->nom) }}"
                    data-valor="{{ $llista->nom }}"
                    maxlength="255"
                    placeholder="Introdueix el nom de la llista"
                    required pattern=".*\S.*"
                    title="El nom de la llista no pot estar buit ni tenir només espais.">
            @else
                <h2 class="fw-bold text-primary mb-0" style="font-size:1.7rem;">{{ $llista->nom }}</h2>
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
                <label class="opcio-color">
                    <input type="radio" name="color" value=""
                        {{ old('color', $llista->color ?? '') == '' ? 'checked' : '' }}>
                    <span class="mostra-color sin-color" title="Sense color">
                        <i class="bi bi-slash-circle"></i>
                    </span>
                    <span class="nombre-color">Sense color</span>
                </label>

                @foreach ($colors as $hex => $nombre)
                    <label class="opcio-color">
                        <input type="radio" name="color" value="{{ hex }}"
                            {{ old('color', $llista->color ?? '') === $hex ? 'checked' : '' }}>
                        <span class="mostra-color" style="background-color: {{ $hex }};" title="{{ $nombre }}"></span>
                        <span class="nombre-color">{{ $nombre }}</span>
                    </label>
                @endforeach
            </div>

            @error('color')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>
    @endif

    {{-- Afegir productes --}}
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
    @endif

    {{-- Productes, per categories --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            @if (count($columna1))
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" id="toggle-tots"
                            class="btn btn-outline-primary btn-sm px-2 py-1">
                        <i class="bi bi-check2-all me-1"></i>
                        <span>Marcar tots</span>
                    </button>
                </div>
            @endif

            <div id="buit" class="text-center py-5 text-muted {{ count($columna1) ? 'd-none' : '' }}">
                <i class="bi bi-clipboard-x fs-1 d-block mb-3"></i>
                <p class="mb-0">Encara no hi ha productes en aquesta llista.</p>
            </div>

            <div id="columnes">
                @if (count($columna1))
                <div class="row g-4">
                    @foreach ([$columna1, $columna2] as $columna)
                        @if (count($columna))
                        <div class="columna {{ $hasTwoColumns ? 'col-md-6' : 'col-12' }}">
                            <div class="card shadow-lg border-0 h-100">
                                <div class="card-body columna-body">
                                    @foreach ($columna as $categoria => $productes)
                                        <h5 class="fw-bold text-primary mt-3 mb-2 categoria-titol"
                                            data-categoria="{{ $categoria }}">
                                            <i class="{{ \App\Models\Categoria::imatgeCategoria($categoria) }} me-2"></i>{{ $categoria }}
                                        </h5>
                                        <ul class="list-group list-group-flush mb-3">
                                            @foreach ($productes as $producte)
                                                @php $comprat = $producte['pivot']['comprat']; @endphp
                                                <li class="list-group-item py-3 px-2 d-flex align-items-center border-0 border-bottom producte-row {{ $comprat ? 'comprat' : '' }}"
                                                    data-id="{{ $producte['id'] }}"
                                                    data-delete-url="{{ route('llistes.productes.eliminar', ['llista_id' => $llista->id, 'producte_id' => $producte['id']]) }}">

                                                    <input type="checkbox" class="form-check-input me-3 fs-5 toggle-comprat"
                                                        data-url="{{ route('productes.toggle', ['llista_id' => $llista->id, 'producte_id' => $producte['id']]) }}"
                                                        {{ $comprat ? 'checked' : '' }}>

                                                    <span class="fs-5 fw-medium nom">{{ $producte['nom'] }}</span>

                                                    @if ($potEditar)
                                                        <input type="number"
                                                            class="form-control form-control-sm ms-auto me-3 quantitat quantitat-input"
                                                            style="width:80px;"
                                                            value="{{ $producte['pivot']['quantitat'] }}"
                                                            data-valor="{{ $producte['pivot']['quantitat'] }}"
                                                            data-url="{{ route('llistes.productes.quantitat', ['llista_id' => $llista->id, 'producte_id' => $producte['id']]) }}"
                                                            min="1" max="100">

                                                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-producte" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @else
                                                        <span class="fs-5 fw-semibold ms-auto text-secondary quantitat">{{ $producte['pivot']['quantitat'] }}x</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

<style>
    .comprat .nom,
    .comprat .quantitat {
        text-decoration: line-through;
        color: #6c757d !important;
    }

    /* Selector de color de la llista */
    .selector-colors { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 10px; }
    .opcio-color {
        display: flex; flex-direction: column; align-items: center;
        gap: 5px; cursor: pointer; font-size: 0.85rem;
    }
    .opcio-color input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
    .mostra-color {
        width: 36px; height: 36px; border-radius: 50%;
        border: 2px solid rgba(0, 0, 0, 0.12);
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .sin-color { background-color: #ffffff; color: #697586; border: 2px dashed #adb5bd; }
    .opcio-color input:checked + .mostra-color {
        outline: 3px solid #212529; outline-offset: 3px; transform: scale(1.05);
    }
    .opcio-color input:focus-visible + .mostra-color {
        outline: 3px solid #0d6efd; outline-offset: 4px;
    }
    .nombre-color { color: #495057; }

    /* Adaptació per a pantalles petites */
    @media (max-width: 576px) {
        .selector-colors {
            display: grid;
            grid-template-columns: repeat(9, minmax(0, 1fr));
            gap: 4px;
            width: 100%;
        }

        .opcio-color {
            min-width: 0;
            width: 100%;
            gap: 4px;
            font-size: 0.65rem;
            text-align: center;
        }

        .mostra-color {
            width: clamp(20px, 6vw, 32px);
            height: clamp(20px, 6vw, 32px);
        }

        .opcio-color input:checked + .mostra-color {
            outline-offset: 2px;
        }

        .nombre-color {
            font-size: 9px;
            line-height: 1.1;
            overflow-wrap: anywhere;
            text-align: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .mostra-color { transition: none; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const contenidor = document.getElementById('fonsLlista');
    const csrf = contenidor.dataset.csrf;

    // ---------- Petició JSON genèrica ----------
    async function api(url, method, body) {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: body ? JSON.stringify(body) : undefined
        });
        let data = {};
        try { data = await res.json(); } catch {}
        if (!res.ok) {
            const primer = data.errors ? Object.values(data.errors)[0][0] : null;
            throw new Error(primer || data.message || 'Error del servidor.');
        }
        return data;
    }

    // Marca verda/vermella breu per indicar si s'ha desat
    function marca(el, ok) {
        el.classList.remove('is-valid', 'is-invalid');
        el.classList.add(ok ? 'is-valid' : 'is-invalid');
        setTimeout(() => el.classList.remove('is-valid', 'is-invalid'), 1200);
    }

    // Guarda l'estat d'un producte al servidor (AJAX)
    async function guardarEstatComprat(checkbox, nouEstat, estatAnterior) {
        const row = checkbox.closest('.producte-row');

        checkbox.checked = nouEstat;
        row.classList.toggle('comprat', nouEstat);

        try {
            const body = new FormData();
            body.append('_method', 'PATCH');
            body.append('_token', csrf);

            const res = await fetch(checkbox.dataset.url, {
                method: 'POST',
                body,
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) throw new Error();
            return true;
        } catch {
            checkbox.checked = estatAnterior;
            row.classList.toggle('comprat', estatAnterior);
            return false;
        }
    }

    // Actualitza el text del botó segons l'estat dels productes
    function actualitzarTextTots() {
        const boton = document.getElementById('toggle-tots');
        if (!boton) return;

        const checkboxes = [...document.querySelectorAll('.toggle-comprat')];
        const totsComprats = checkboxes.length > 0 &&
            checkboxes.every(checkbox => checkbox.checked);

        const span = boton.querySelector('span');
        if (span) {
            span.textContent = totsComprats ? 'Desmarcar tots' : 'Marcar tots';
        }
    }

    // Canvi individual de compra d'un producte
    document.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('toggle-comprat')) return;

        const checkbox = e.target;
        const nouEstat = checkbox.checked;
        const estatAnterior = !nouEstat;

        const correcte = await guardarEstatComprat(checkbox, nouEstat, estatAnterior);
        if (!correcte) {
            alert('No s\'ha pogut actualitzar el producte.');
        }

        actualitzarTextTots();
    });

    // Marcar o desmarcar tots els productes
    const botoTots = document.getElementById('toggle-tots');
    if (botoTots) {
        actualitzarTextTots();

        botoTots.addEventListener('click', async () => {
            const checkboxes = [...document.querySelectorAll('.toggle-comprat')];
            if (!checkboxes.length) return;

            const nouEstat = checkboxes.some(checkbox => !checkbox.checked);

            botoTots.disabled = true;
            checkboxes.forEach(checkbox => checkbox.disabled = true);

            const resultats = await Promise.all(
                checkboxes
                    .filter(checkbox => checkbox.checked !== nouEstat)
                    .map(checkbox => guardarEstatComprat(checkbox, nouEstat, checkbox.checked))
            );

            checkboxes.forEach(checkbox => checkbox.disabled = false);
            botoTots.disabled = false;

            actualitzarTextTots();

            if (resultats.some(resultat => !resultat)) {
                alert('Alguns productes no s\'han pogut actualitzar.');
            }
        });
    }

    // La resta només existeix si l'usuari pot editar
    const inputNom = document.getElementById('listName');
    if (!inputNom) return;

    // ---------- Nom de la llista (change) ----------
    inputNom.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); inputNom.blur(); }
    });

    inputNom.addEventListener('change', async () => {
        const nou = inputNom.value.trim();

        if (nou === '') {
            alert('El nom no pot estar buit.');
            inputNom.value = inputNom.dataset.valor;
            return;
        }
        if (nou === inputNom.dataset.valor) { inputNom.value = nou; return; }

        try {
            const data = await api(contenidor.dataset.nomUrl, 'PATCH', { nom: nou });
            inputNom.value = data.nom;
            inputNom.dataset.valor = data.nom;
            document.title = data.nom;
            marca(inputNom, true);
        } catch (err) {
            inputNom.value = inputNom.dataset.valor;
            marca(inputNom, false);
            alert(err.message);
        }
    });

    // ---------- Canvi de color ----------
    const radiosColor = document.querySelectorAll('input[name="color"]');
    let colorGuardat = document.querySelector('input[name="color"]:checked')?.value ?? '';

    const pintaFons = (hex) => {
        contenidor.style.backgroundColor = hex ? hex + '18' : 'transparent';
    };

    radiosColor.forEach(radio => radio.addEventListener('change', async () => {
        const nou = radio.value;
        pintaFons(nou);
        try {
            await api(contenidor.dataset.colorUrl, 'PATCH', { color: nou === '' ? null : nou });
            colorGuardat = nou;
        } catch {
            [...radiosColor].find(r => r.value === colorGuardat).checked = true;
            pintaFons(colorGuardat);
            alert('No s\'ha pogut canviar el color.');
        }
    }));

    // ---------- Quantitat d'un producte (change) ----------
    document.addEventListener('change', async (e) => {
        const input = e.target;
        if (!input.classList.contains('quantitat-input')) return;

        const valor = Number(input.value);
        if (!Number.isInteger(valor) || valor < 1 || valor > 100) {
            alert('Indica una quantitat vàlida (1-100).');
            input.value = input.dataset.valor;
            return;
        }
        if (String(valor) === input.dataset.valor) return;

        try {
            const data = await api(input.dataset.url, 'PATCH', { quantitat: valor });
            input.dataset.valor = data.quantitat;
            marca(input, true);
        } catch (err) {
            input.value = input.dataset.valor;
            marca(input, false);
            alert(err.message);
        }
    });

    // ---------- Estructura de productes ----------
    const columnes = document.getElementById('columnes');
    const buit = document.getElementById('buit');

    function trobaOCreaUl(categoria, icona) {
        const titol = [...document.querySelectorAll('.categoria-titol')]
            .find(h => h.dataset.categoria === categoria);
        if (titol) return titol.nextElementSibling;

        let cos = [...document.querySelectorAll('.columna-body')].pop();
        if (!cos) {
            columnes.innerHTML = `
                <div class="row g-4"><div class="columna col-12">
                    <div class="card shadow-lg border-0 h-100">
                        <div class="card-body columna-body"></div>
                    </div>
                </div></div>`;
            cos = columnes.querySelector('.columna-body');
        }

        const h5 = document.createElement('h5');
        h5.className = 'fw-bold text-primary mt-3 mb-2 categoria-titol';
        h5.dataset.categoria = categoria;
        const i = document.createElement('i');
        i.className = icona + ' me-2';
        h5.append(i, document.createTextNode(categoria));

        const ul = document.createElement('ul');
        ul.className = 'list-group list-group-flush mb-3';
        cos.append(h5, ul);
        return ul;
    }

    function pintaFila(p) {
        const ul = trobaOCreaUl(p.categoria, p.icona);
        const li = document.createElement('li');
        li.className = 'list-group-item py-3 px-2 d-flex align-items-center border-0 border-bottom producte-row';
        li.dataset.id = p.id;
        li.dataset.deleteUrl = p.delete_url;
        li.innerHTML = `
            <input type="checkbox" class="form-check-input me-3 fs-5 toggle-comprat">
            <span class="fs-5 fw-medium nom"></span>
            <input type="number" class="form-control form-control-sm ms-auto me-3 quantitat quantitat-input"
                   style="width:80px;" min="1" max="100">
            <button type="button" class="btn btn-sm btn-outline-danger eliminar-producte" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>`;
        li.querySelector('.toggle-comprat').dataset.url = p.toggle_url;
        li.querySelector('.nom').textContent = p.nom;
        const q = li.querySelector('.quantitat-input');
        q.value = p.quantitat;
        q.dataset.valor = p.quantitat;
        q.dataset.url = p.quantitat_url;
        ul.appendChild(li);
        buit.classList.add('d-none');
    }

    // ---------- Autocompletat ----------
    const cercador = document.getElementById('cercador-productes');
    const suggestions = document.getElementById('suggestions');
    const hiddenId = document.getElementById('producte_id_nou');
    const qtyNou = document.getElementById('quantitat_nou');
    const btnAfegir = document.getElementById('afegir-producte');
    const searchUrl = cercador.dataset.searchUrl;
    let timeout = null;

    const afegits = new Set(
        [...document.querySelectorAll('.producte-row')].map(r => r.dataset.id)
    );

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
                })
                .catch(() => { suggestions.innerHTML = ''; });
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!suggestions.contains(e.target) && e.target !== cercador) suggestions.innerHTML = '';
    });

    [cercador, qtyNou].forEach(el => el.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); btnAfegir.click(); }
    }));

    // ---------- Afegir producte ----------
    btnAfegir.addEventListener('click', async () => {
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

        btnAfegir.disabled = true;
        try {
            const data = await api(contenidor.dataset.addUrl, 'POST', { producte_id: id, quantitat });
            afegits.add(String(data.id));
            pintaFila(data);
        } catch (err) {
            alert(err.message);
            return;
        } finally {
            btnAfegir.disabled = false;
        }

        cercador.value = '';
        hiddenId.value = '';
        qtyNou.value = 1;
        cercador.focus();
    });

    // ---------- Eliminar producte ----------
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.eliminar-producte');
        if (!btn) return;
        const row = btn.closest('.producte-row');

        const nomProducte = row.querySelector('.nom').textContent.trim();
        if (!confirm(`Segur que vols eliminar "${nomProducte}" de la llista?`)) return;

        btn.disabled = true;
        try {
            await api(row.dataset.deleteUrl, 'DELETE');
        } catch {
            btn.disabled = false;
            alert('No s\'ha pogut eliminar el producte.');
            return;
        }

        afegits.delete(row.dataset.id);

        const ul = row.closest('ul');
        row.remove();

        if (!ul.querySelector('.producte-row')) {
            const cos = ul.closest('.columna-body');
            ul.previousElementSibling?.remove();
            ul.remove();
            if (!cos.querySelector('.categoria-titol')) cos.closest('.columna').remove();
        }

        if (!document.querySelector('.producte-row')) {
            columnes.innerHTML = '';
            buit.classList.remove('d-none');
        }
    });
});
</script>