@extends('layouts.master')
@section('title', 'Compartir')
@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto bg-white shadow-xl rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Gestió de Permisos de: "{{ $llista->nom }}"
        </h1>
        
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- EL FORMULARI APUNTA AL MÈTODE POST processShare --}}
        <form method="POST" action="{{ route('llistes.processShare', $llista->id) }}">
            @csrf
            
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Usuaris Compartits</h2>

            <div id="users-container" class="space-y-4">
                
                {{-- 1. L'OWNER (NO MODIFICABLE) --}}
                <div class="flex items-center space-x-4 border-b pb-3 mb-3 bg-gray-50 p-3 rounded">
                    <div class="flex-grow">
                        <p class="font-medium text-gray-900">{{ $llista->owner->name }} ({{ $llista->owner->email }})</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-200 rounded-full">
                        Owner
                    </span>
                </div>

                {{-- 2. LLISTA D'USUARIS COMPARTITS --}}
                {{-- Iterem pels usuaris a través de la relació 'usuaris' (excloent l'owner) --}}
                @foreach ($llista->usuaris as $user)
                    @if ($user->id !== $llista->usuari_id)
                        <div class="flex items-center space-x-4 border-b pb-3 user-entry" data-user-id="{{ $user->id }}">
                            {{-- ID DE L'USUARI AMAGAT (NECESSARI PER AL SYNC) --}}
                            <input type="hidden" name="usuaris[{{ $user->id }}][id]" value="{{ $user->id }}">

                            <div class="flex-grow">
                                <p class="font-medium text-gray-900">{{ $user->name }} ({{ $user->email }})</p>
                            </div>

                            <select name="usuaris[{{ $user->id }}][rol]" class="border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 w-32">
                                <option value="viewer" {{ $user->pivot->rol === 'viewer' ? 'selected' : '' }}>Viewer</option>
                                <option value="editor" {{ $user->pivot->rol === 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="admin" {{ $user->pivot->rol === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>

                            {{-- BOTÓ D'ELIMINAR (ELIMINARÀ AQUEST USUARI DEL FORMULARI ABANS D'ENVIAR) --}}
                            <button type="button" onclick="removeUserEntry(this)" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out p-2 rounded-full hover:bg-red-50" title="Deixar de compartir">
                                ❌
                            </button>
                        </div>
                    @endif
                @endforeach
            </div>

            <hr class="my-6">
            
            {{-- 3. AFegir Nou Usuari (IMPLEMENTACIÓ REAL DE L'AUTOCOMPLETAT) --}}
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Afegir Nou Col·laborador</h2>

            <div class="mb-4 relative">
                <label for="user-search" class="block text-sm font-medium text-gray-700">Cercar per nom o correu</label>
                <input type="text" id="user-search" placeholder="Escriu per cercar usuaris..."
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                
                {{-- CONTENIDOR PELS RESULTATS DE LA CERCA --}}
                <div id="search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 hidden">
                    {{-- Aquí s'injectaran els resultats amb JavaScript --}}
                </div>
            </div>

            <div class="mt-8 pt-4 border-t">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-black font-bold py-2 px-6 rounded-lg transition duration-150 ease-in-out shadow-md">
                    Guardar Canvis de Permisos
                </button>
                <a href="{{ route('llistes.show', $llista->id) }}" class="ml-4 text-gray-600 hover:text-gray-900 transition duration-150 ease-in-out">
                    Tornar a la Llista
                </a>
            </div>
        </form>
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
        
        if (query.length < 3) { 
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