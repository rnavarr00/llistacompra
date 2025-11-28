@extends('layouts.master')
@section('title', 'Compartir')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h1 class="card-title h3 mb-4 d-flex align-items-center text-dark border-bottom pb-3">
                        {{-- Icona canviada a Bootstrap Icons --}}
                        <i class="bi bi-person-lock me-3 text-primary"></i>
                        Gestió de permisos de la llista: "<strong>{{ $llista->nom }}</strong>"
                    </h1>
                    
                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            {{-- Icona canviada a Bootstrap Icons --}}
                            <i class="bi bi-check-circle me-3 fs-5"></i>
                            <span class="d-block">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('llistes.processShare', $llista->id) }}">
                        @csrf
                        
                        <h2 class="h5 mt-4 mb-3 d-flex align-items-center text-secondary">
                            {{-- Icona canviada a Bootstrap Icons --}}
                            <i class="bi bi-people me-2"></i>
                            USUARIS COMPARTITS
                        </h2>

                        <div id="users-container" class="list-group list-group-flush mb-4">
                            
                            {{-- 1. L'OWNER (NO MODIFICABLE) --}}
                            <div class="list-group-item d-flex align-items-center bg-light border-start border-5 border-success rounded mb-2 shadow-sm p-3">
                                {{-- Icona canviada a Bootstrap Icons --}}
                                <i class="bi bi-gem text-warning fs-5 me-3"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-medium text-dark">{{ $llista->owner->name }} (<span class="text-muted">{{ $llista->owner->email }}</span>)</p>
                                </div>
                                <span class="badge rounded-pill bg-success text-white fw-bold">
                                    CREADOR
                                </span>
                            </div>

                            {{-- 2. LLISTA D'USUARIS COMPARTITS --}}
                            @foreach ($llista->usuaris as $user)
                                @if ($user->id !== $llista->usuari_id)
                                    <div class="list-group-item d-flex align-items-center user-entry py-3 border-bottom" data-user-id="{{ $user->id }}">
                                        {{-- Icona canviada a Bootstrap Icons --}}
                                        <i class="bi bi-person me-3 text-secondary"></i>
                                        
                                        <input type="hidden" name="usuaris[{{ $user->id }}][id]" value="{{ $user->id }}">

                                        <div class="flex-grow-1 me-3">
                                            <p class="mb-0 fw-medium text-dark">{{ $user->name }} (<span class="text-muted">{{ $user->email }}</span>)</p>
                                        </div>

                                        {{-- SELECTOR DE ROLS: Aquí NO podem posar icons de cap mena de manera nativa --}}
                                        <select name="usuaris[{{ $user->id }}][rol]" class="form-select w-auto me-3">
                                            <option value="viewer" {{ $user->pivot->rol === 'viewer' ? 'selected' : '' }}>Només visualitzar</option>
                                            <option value="editor" {{ $user->pivot->rol === 'editor' ? 'selected' : '' }}>Editor</option>
                                            <option value="admin" {{ $user->pivot->rol === 'admin' ? 'selected' : '' }}>Adminstrador</option>
                                        </select>

                                        <button type="button" onclick="removeUserEntry(this)" class="btn btn-outline-danger btn-sm rounded-circle" title="Deixar de compartir">
                                            {{-- Icona canviada a Bootstrap Icons --}}
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <hr class="my-4">
                        
                        {{-- 3. Afegir Nou Usuari --}}
                        <h2 class="h5 mb-3 d-flex align-items-center text-secondary">
                            {{-- Icona canviada a Bootstrap Icons --}}
                            <i class="bi bi-person-add me-2"></i>
                            Afegir nou col·laborador
                        </h2>

                        <div class="mb-4 position-relative">
                            <label for="user-search" class="form-label">Cercar per nom o correu</label>
                            <div class="input-group">
                                {{-- Icona canviada a Bootstrap Icons --}}
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="user-search" placeholder="Escriu per cercar usuaris..."
                                        class="form-control">
                            </div>
                            
                            <div id="search-results" class="position-absolute z-100 w-100 bg-white border rounded shadow mt-1" style="display: none;">
                                {{-- Aquí s'injectaran els resultats amb JavaScript --}}
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end align-items-center">
                            <a href="{{ route('llistes.show', $llista->id) }}" class="btn btn-link text-secondary me-3 d-flex align-items-center">
                                {{-- Icona canviada a Bootstrap Icons --}}
                                <i class="bi bi-arrow-left-circle me-2"></i>
                                Tornar a la Llista
                            </a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                {{-- Icona canviada a Bootstrap Icons --}}
                                <i class="bi bi-save me-2"></i>
                                Guardar els canvis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // FUNCIÓ JAVASCRIPT PER ELIMINAR UN USUARI COMPARTIT DEL FORMULARI
    function removeUserEntry(button) {
        if (confirm('Estàs segur que vols deixar de compartir aquesta llista amb aquest usuari?')) {
            const entry = button.closest('.user-entry');
            
            // També l'eliminem del Set de IDs existents per si vol afegir-lo de nou sense recarregar
            const userId = entry.dataset.userId;
            existingUserIds.delete(userId);
            
            entry.remove();
        }
    }
    
    // ----------------------------------------------------------------------------------
    // LÒGICA D'AUTOCOMPLETAT D'USUARIS
    // ----------------------------------------------------------------------------------
    
    const userSearchInput = document.getElementById('user-search');
    const searchResultsDiv = document.getElementById('search-results');
    const usersContainer = document.getElementById('users-container');
    
    // Inicialitzem un Set per guardar els IDs dels usuaris actuals i evitar duplicats
    const existingUserIds = new Set(
        Array.from(usersContainer.querySelectorAll('.user-entry'))
             .map(el => el.dataset.userId)
    );
    
    let debounceTimeout;

    // 1. Escolta l'escriptura a l'input de cerca
    userSearchInput.addEventListener('input', function() {
        clearTimeout(debounceTimeout);
        const query = this.value;
        
        if (query.length < 2) { 
            searchResultsDiv.innerHTML = '';
            searchResultsDiv.classList.add('hidden');
            return;
        }

        // Debounce: espera 300ms abans de llançar la petició AJAX
        debounceTimeout = setTimeout(() => {
            fetchUsers(query);
        }, 300);
    });

    // 2. Funció per fer la petició AJAX
    function fetchUsers(query) {
        fetch('{{ route('usuaris.search') }}?query=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(users => {
                searchResultsDiv.innerHTML = '';
                
                if (users.length === 0) {
                    searchResultsDiv.innerHTML = '<div class="p-2 text-gray-500">No s\'ha trobat cap usuari.</div>';
                    searchResultsDiv.classList.remove('hidden');
                    return;
                }

                users.forEach(user => {
                    // 3. Excloure usuaris que ja estan compartint la llista
                    if (existingUserIds.has(String(user.id))) {
                        return;
                    }

                    const resultItem = document.createElement('div');
                    resultItem.classList.add('p-2', 'hover:bg-indigo-100', 'cursor-pointer', 'text-gray-900', 'border-b', 'last:border-b-0');
                    resultItem.innerHTML = `<span class="font-semibold">${user.name}</span> (${user.email})`;
                    
                    // 4. Afegir l'usuari al fer clic
                    resultItem.addEventListener('click', () => addUserToShare(user));
                    
                    searchResultsDiv.appendChild(resultItem);
                });
                
                searchResultsDiv.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error en la cerca d\'usuaris:', error);
                searchResultsDiv.innerHTML = '<div class="p-2 text-red-500">Error en la cerca.</div>';
            });
    }

    // 5. Funció per afegir l'usuari al formulari de compartició
    function addUserToShare(user) {
        // Ocultar resultats i netejar input
        searchResultsDiv.classList.add('hidden');
        userSearchInput.value = '';
        
        // Evitar duplicats
        if (existingUserIds.has(String(user.id))) {
            return;
        }

        // Marcar com afegit
        existingUserIds.add(String(user.id));
        
        // Crear la nova línia HTML (similar a les existents)
        const newEntry = document.createElement('div');
        newEntry.classList.add('flex', 'items-center', 'space-x-4', 'border-b', 'pb-3', 'user-entry', 'bg-yellow-50', 'p-2', 'rounded');
        newEntry.dataset.userId = user.id;

        const html = `
            {{-- ID DE L'USUARI AMAGAT (NECESSARI PER AL SYNC) --}}
            <input type="hidden" name="usuaris[${user.id}][id]" value="${user.id}">

            <div class="flex-grow">
                <p class="font-medium text-gray-900">${user.name} (${user.email})</p>
            </div>

            <select name="usuaris[${user.id}][rol]" class="border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 w-32">
                <option value="viewer" selected>Viewer</option>
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
            </select>

            <button type="button" onclick="removeUserEntry(this)" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out p-2 rounded-full hover:bg-red-50" title="Deixar de compartir">
                ❌
            </button>
        `;
        
        newEntry.innerHTML = html;
        usersContainer.appendChild(newEntry);
    }
    
    // Ocultar resultats si l'usuari fa clic fora de la caixa de cerca
    document.addEventListener('click', function(event) {
        if (!userSearchInput.contains(event.target) && !searchResultsDiv.contains(event.target)) {
            searchResultsDiv.classList.add('hidden');
        }
    });

</script>
@endsection