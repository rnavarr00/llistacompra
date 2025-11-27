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
            
            {{-- 3. AFegir Nou Usuari (Aquí s'utilitzaria JavaScript / Ajax per la funcionalitat real) --}}
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Afegir Nou Col·laborador</h2>
            
            <p class="text-sm text-gray-500 mb-4">
                Per a la implementació simple, hauràs de gestionar l'afegiment de nous usuaris manualment o implementar una funció de cerca amb JavaScript.
            </p>

            {{-- Aquí hi aniria l'input d'autocompletat amb JS --}}
            {{-- Per a la prova, afegim un botó d'exemple que requerirà JS per funcionar --}}
            {{-- <button type="button" id="add-user-btn" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                Afegir Usuari (Requereix JS)
            </button> --}}

            <div class="mt-8 pt-4 border-t">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150 ease-in-out shadow-md">
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
            entry.remove();
        }
    }
    
    // NOTA: Caldria implementar una lògica amb AJAX i JavaScript per afegir nous usuaris 
    // buscant-los per email, creant una nova 'user-entry' amb l'ID i el Rol per defecte.
</script>

@endsection