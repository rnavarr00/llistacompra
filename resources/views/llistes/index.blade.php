@extends('layouts.master')

@section('title', 'Les meves llistes')

@section('content')

<div class="container">

    {{-- CAPÇALERA --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-1 mb-4">

        <div class="flex-grow-1 mx-md-4">
            <input
                type="text"
                class="form-control rounded-4 py-3 px-3 shadow-sm"
                id="cercadorLlistes"
                name="nom"
                placeholder="Cerca una llista o escriu-ne una de nova..."
                autocomplete="off"
                maxlength="255">
        </div>

        <button type="button" id="btnCrear"
                data-url="{{ route('llistes.store') }}"
                data-token="{{ csrf_token() }}"
                class="btn btn-primary btn-lg boto-crear d-flex align-items-center justify-content-center shadow-sm"
                aria-label="Crear una llista"
                title="Crear una llista amb aquest nom">
            <i class="bi bi-plus-lg fs-3"></i>
        </button>

    </div>

    {{-- LLISTES --}}
    <div class="row g-4" id="graellaLlistes">

        @forelse ($llistes as $llista)

            <div class="col-12 col-sm-6 col-md-4 llista-item" data-nom="{{ strtolower($llista->nom) }}">

                <div class="card h-100 text-center shadow-sm border-0 list-card
                    {{ $llista->es_compartida ? 'shared-bg' : '' }}"
                    style="
                        border-left: 5px solid {{ $llista->color ?? '#dee2e6' }} !important;
                        background-color: {{ $llista->color
                            ? $llista->color . '18'
                            : ($llista->es_compartida ? '#f8f9fa' : '#ffffff') }};
                    ">

                    {{-- DISTINTIU DE LLISTA COMPARTIDA --}}
                    @if ($llista->es_compartida)
                        <div class="shared-badge">
                            <span class="badge rounded-pill bg-info text-dark shadow-sm"
                                  title="Llista compartida"
                                  aria-label="Llista compartida">
                                <i class="bi bi-people-fill"></i>
                                <span class="shared-label">Compartida</span>
                            </span>
                        </div>
                    @endif

                    {{-- CONTINGUT CLICABLE PER OBRIR LA LLISTA --}}
                    <a href="{{ route('llistes.show', $llista->id) }}"
                       class="list-card-link text-decoration-none text-reset"
                       aria-label="Obrir la llista {{ $llista->nom }}">

                        <div class="card-body d-flex flex-column justify-content-center align-items-center">

                            <h5 class="card-title mb-0 nom-llista">
                                {{ $llista->nom }}
                            </h5>

                            @if ($llista->es_compartida)
                                <small class="text-muted mt-2">
                                    Compartida amb tu
                                </small>
                            @endif

                        </div>

                    </a>

                    {{-- ACCIONS A LES DUES CANTONADES SUPERIORS --}}
                    <div class="card-actions">

                        {{-- COMPARTIR: CANTONADA SUPERIOR ESQUERRA --}}
                        @can('share', $llista)
                            <a href="{{ route('llistes.share', $llista->id) }}"
                               class="btn btn-outline-primary action-btn"
                               aria-label="Compartir {{ $llista->nom }}"
                               title="Compartir llista">
                                <i class="bi bi-share-fill"></i>
                                <span class="action-label">Compartir</span>
                            </a>
                        @endcan

                        {{-- ELIMINAR: CANTONADA SUPERIOR DRETA --}}
                        @can('delete', $llista)
                            <form action="{{ route('llistes.destroy', $llista->id) }}"
                                  method="POST"
                                  class="form-eliminar"
                                  onsubmit="return confirm('Estàs segur que vols eliminar la llista «{{ addslashes($llista->nom) }}»? Aquesta acció no es pot desfer.');">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-outline-danger action-btn"
                                        aria-label="Eliminar {{ $llista->nom }}"
                                        title="Eliminar llista">
                                    <i class="bi bi-trash-fill"></i>
                                    <span class="action-label">Eliminar</span>
                                </button>

                            </form>
                        @endcan

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12 text-center mt-5" id="senseLlistesInicial">
                <p class="text-muted">
                    Encara no tens cap llista creada. Escriu un nom a dalt i prem el botó '+' per crear-ne una!
                </p>
            </div>

        @endforelse

    </div>

    {{-- Missatge quan la cerca no troba res --}}
    <div id="senseResultats" class="col-12 text-center mt-5 d-none">
        <p class="text-muted">No s'ha trobat cap llista amb aquest nom.</p>
    </div>

</div>

<style>
    /* Diferenciació visual de les llistes compartides */
    .shared-bg {
        background-color: #f8f9fa;
        border-left: 4px solid #0dcaf0 !important;
    }

    /* Targetes de les llistes */
    .list-card {
        position: relative;
        border-radius: 16px;
        padding-top: 42px;
        overflow: hidden;
        transition: box-shadow 0.2s ease-in-out,
                    transform 0.2s ease-in-out;
    }

    .list-card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-4px);
    }

    /* Enllaç que obre la llista */
    .list-card-link {
        display: flex;
        flex: 1;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
        padding: 0.4rem 0.75rem 0.6rem;
        color: inherit;
        text-decoration: none;
    }

    .list-card-link:focus-visible {
        outline: 3px solid #0d6efd;
        outline-offset: -4px;
        border-radius: 12px;
    }

    /* Botons a les cantonades superiors */
    .card-actions {
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        z-index: 3;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 0;
        margin: 0;
        pointer-events: none;
    }

    .card-actions > a,
    .card-actions > form {
        pointer-events: auto;
    }

    /* Botons més grans i fàcils de prémer */
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 10px 15px;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        white-space: nowrap;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease,
                    box-shadow 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }

    .action-btn i {
        font-size: 1.05rem;
    }

    .form-eliminar {
        margin: 0;
    }

    /* Distintiu de llista compartida */
    .shared-badge {
        position: absolute;
        top: 62px;
        left: 10px;
        z-index: 1;
    }

    .shared-label {
        margin-left: 4px;
    }

    /* Botó per crear una llista */
    .boto-crear {
        min-width: 56px;
        min-height: 56px;
        border-radius: 12px;
    }

    /* Adaptació a pantalles petites */
    @media (max-width: 576px) {
        .list-card {
            padding-top: 42px;
        }

        .card-actions {
            top: 8px;
            left: 8px;
            right: 8px;
            gap: 8px;
        }

        .action-btn {
            width: 44px;
            height: 44px;
            min-width: 44px;
            min-height: 44px;
            padding: 0;
            justify-content: center;
            border-radius: 10px;
        }

        .action-btn i {
            font-size: 1.15rem;
            margin: 0 !important;
        }

        .action-label {
            display: none;
        }

        .shared-badge {
            top: 62px;
            left: 8px;
        }

        .list-card-link {
            padding: 0.75rem 0.75rem 1rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cercador = document.getElementById('cercadorLlistes');
    const btnCrear = document.getElementById('btnCrear');
    const itemsLlista = document.querySelectorAll('.llista-item');
    const missatgeSenseResultats = document.getElementById('senseResultats');

    // ---------- 1. FILTRE EN TEMPS REAL (CERCA) ----------
    cercador.addEventListener('input', (e) => {
        const text = e.target.value.toLowerCase().trim();
        let comptadorVisibles = 0;

        itemsLlista.forEach(item => {
            const nomLlista = item.dataset.nom;
            if (nomLlista.includes(text)) {
                item.style.display = 'block';
                comptadorVisibles++;
            } else {
                item.style.display = 'none';
            }
        });

        // Mostrar o amagar avís de cap resultat
        if (comptadorVisibles === 0 && itemsLlista.length > 0) {
            missatgeSenseResultats.classList.remove('d-none');
        } else {
            missatgeSenseResultats.classList.add('d-none');
        }
    });

    // Permetre prémer "Enter" a l'input per crear directament
    cercador.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnCrear.click();
        }
    });

    // ---------- 2. CREAR LLISTA DES DE LA BARRA ----------
    btnCrear.addEventListener('click', async () => {
        const nomLlista = cercador.value.trim();
        const url = btnCrear.dataset.url;
        const token = btnCrear.dataset.token;

        if (nomLlista === '') {
            alert('Si us plau, introdueix un nom per a la nova llista a la barra de cerca.');
            cercador.focus();
            return;
        }

        if (nomLlista.length > 255) {
            alert('El nom és massa llarg (màxim 255 caràcters).');
            return;
        }

        btnCrear.disabled = true;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ nom: nomLlista })
            });

            const data = await res.json();

            if (res.ok) {
                window.location.href = data.redirect;
                return;
            }

            if (res.status === 422) {
                const errorMsg = data.errors?.nom?.[0] ?? 'Nom no vàlid.';
                alert(errorMsg);
                btnCrear.disabled = false;
                return;
            }

            throw new Error();
        } catch {
            alert('No s\'ha pogut crear la llista. Torna-ho a provar.');
            btnCrear.disabled = false;
        }
    });
});
</script>

@endsection