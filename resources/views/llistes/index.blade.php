@extends('layouts.master')
@section('title', 'Les meves llistes')
@section('content')

<link rel="icon" href="{{ asset('img1.png') }}" type="image/png">

<div class="container">

    {{-- ENCAPÇALAMENT AMB BOTÓ DE CREAR LLISTA --}}
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2 class="titol-dash text-primary fw-bold">
            Les meves llistes
        </h2>
        <a href="{{ route('llistes.create') }}"
            class="btn btn-primary btn-lg px-4 d-flex align-items-center justify-content-center shadow-sm">
            <i class="bi bi-plus-lg fs-3"></i>
        </a>
    </div>

    {{-- GRAELLA DE LLISTES --}}
    <div class="row g-4">

        @forelse ($llistes as $llista)
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card h-100 text-center shadow-sm border-0 position-relative list-card {{ $llista->es_compartida ? 'shared-bg' : '' }}"
                onclick="window.location=`{{ route('llistes.show', $llista->id) }}`"
                style="cursor:pointer; transition:all 0.2s ease-in-out;">

                {{-- INDICADOR DE LLISTA COMPARTIDA (Superior Esquerra) --}}
                @if($llista->es_compartida)
                <div class="position-absolute top-0 start-0 m-2">
                    <span class="badge rounded-pill bg-info text-dark shadow-sm">
                        <i class="bi bi-people-fill"></i>
                    </span>
                </div>
                @endif

                {{-- MENÚ DE TRES PUNTETS --}}
                <div class="dropdown position-absolute top-0 end-0 m-2">
                    <button class="btn btn-light btn-sm" type="button" id="menu{{ $llista->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menu{{ $llista->id }}">

                        @can('update', $llista)
                        <li><a class="dropdown-item" href="{{ route('llistes.edit', $llista->id) }}">Editar</a></li>
                        @endcan

                        @can('share', $llista)
                        <li>
                            <a class="dropdown-item" href="{{ route('llistes.share', $llista->id) }}">
                                <i class="bi bi-share-fill me-2"></i> Compartir
                            </a>
                        </li>
                        @endcan

                        @if(Gate::check('update', $llista) || Gate::check('share', $llista))
                        <li><hr class="dropdown-divider"></li>
                        @endif

                        @can('delete', $llista)
                        <li>
                            <form action="{{ route('llistes.destroy', $llista->id) }}" method="POST" onsubmit="return confirm('Estàs segur que vols eliminar aquesta llista?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                                    <i class="bi bi-trash-fill me-2"></i> Eliminar
                                </button>
                            </form>
                        </li>
                        @endcan
                    </ul>
                </div>

                {{-- COS DE LA TARGETA --}}
                <div class="card-body d-flex flex-column justify-content-center align-items-center mt-3">
                    <div class="fs-1 mb-3 text-secondary">
                        <i class="{{ $llista->icona ?? 'bi bi-list-check' }}"></i>
                    </div>
                    <h5 class="card-title mb-0 text-capitalize">{{ $llista->nom }}</h5>
                    @if($llista->es_compartida)
                        <small class="text-muted mt-2">Compartida amb tu</small>
                    @endif
                </div>
            </div>
        </div>

        @empty
        <div class="col-12 text-center mt-5">
            <p class="text-muted">Encara no tens cap llista creada.</p>
        </div>
        @endforelse
    </div>

    <style>
        /* Diferenciació visual per compartides */
        .shared-bg {
            background-color: #f8f9fa; /* Gris molt clar */
            border-left: 4px solid #0dcaf0 !important; /* Bordó lateral informatiu */
        }

        .list-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-5px);
        }

        .titol-dash {
            font-size: 2.6rem;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 6px 0;
            border-bottom: 4px solid rgba(13, 110, 253, 0.4);
            display: inline-block;
            opacity: 0;
            transform: translateY(12px);
            animation: dashFade 0.6s ease-out forwards;
        }

        @keyframes dashFade {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dropdown, .dropdown-item, .btn, form').forEach(el => {
                el.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
        });
    </script>
</div>
@endsection