<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Les meves llistes</h2>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline text-gray-600">👤 {{ Auth::user()->name }}</span>
                <a href="#"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    +
                </a>
            </div>
        </div>
    </x-slot>


    </div>
</x-app-layout>