@extends('layouts.master')

@section('title', 'Les meves llistes')

@section('content')

<div class="container">

    {{-- CAPÇALERA --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 my-4">

        <h2 class="titol-dash text-primary fw-bold mb-0">
            Les meves llistes
        </h2>

        <div class="flex-grow-1 mx-md-4">
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

        <a href="{{ route('llistes.create') }}"
           class="btn btn-primary btn-lg boto-crear d-flex align-items-center justify-content-center shadow-sm"
           aria-label="Crear una llista"
           title="Crear una llista">
            <i class="bi bi-plus-lg fs-3"></i>
        </a>

    </div>

    {{-- LLISTES --}}
    <div class="row g-4">

        @forelse ($llistes as $llista)

            <div class="col-12 col-sm-6 col-md-4">

                <div class="card h-100 text-center shadow-sm border-0 list-card
                    {{ $llista->es_compartida ? 'shared-bg' : '' }}">

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

                            <div class="fs-1 mb-3 text-secondary">
                                <i class="{{ $llista->icona ?? 'bi bi-list-check' }}"></i>
                            </div>

                            <h5 class="card-title mb-0 text-capitalize">
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

            <div class="col-12 text-center mt-5">
                <p class="text-muted">
                    Encara no tens cap llista creada.
                </p>
            </div>

        @endforelse

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
        padding-top: 58px;
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
        padding: 0.75rem 1rem 1rem;
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

    /* Títol del menú principal */
    .titol-dash {
        font-size: 2.6rem;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 6px 0;
        border-bottom: 4px solid rgba(13, 110, 253, 0.4);
        display: inline-block;
        animation: dashFade 0.6s ease-out forwards;
    }

    @keyframes dashFade {
        from {
            opacity: 0;
            transform: translateY(18px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Adaptació a pantalles petites */
    @media (max-width: 576px) {
        .titol-dash {
            font-size: 2rem;
        }

        .list-card {
            padding-top: 58px;
        }

        .card-actions {
            top: 8px;
            left: 8px;
            right: 8px;
            gap: 8px;
        }

        /* Botones grandes, solo con iconos */
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

        /* Ocultar el texto de los botones en móvil */
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

    /* Accessibilitat: respectar la preferència de menys moviment */
    @media (prefers-reduced-motion: reduce) {
        .list-card,
        .action-btn,
        .titol-dash {
            animation: none;
            transition: none;
        }
    }
</style>

@endsection
