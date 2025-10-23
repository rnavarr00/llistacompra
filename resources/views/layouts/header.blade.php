<header class="bg-white shadow-md">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <h1 class="text-xl font-bold text-green-600">
            <a href="#">INICI</a>
        </h1>
        <nav class="space-x-4">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-gray-700 hover:text-green-600 border-none bg-transparent cursor-pointer">
                    Tancar sessió
                </button>
            </form>
        </nav>
    </div>
</header>
