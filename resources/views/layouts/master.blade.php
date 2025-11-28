<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LlistApp')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<link rel="icon" href="{{ asset('img1.png') }}" type="image/png">


<body class="bg-gray-100 text-gray-800 font-sans flex flex-col min-h-screen">

    {{-- NAVBAR --}}
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-8 py-4 flex justify-between items-center">

            {{-- LOGO --}}
            <a href="{{ route('llistes.index') }}"
                class="flex items-center space-x-2 text-blue-600 font-extrabold text-2xl hover:text-blue-700 transition">
                <img src="{{ asset('img.png') }}" alt="LlistApp" style="height:40px; width:auto;">
            </a>

            {{-- PERFIL / LOGOUT --}}
            <div class="flex items-center space-x-8">
                {{-- Perfil --}}
                <a href="{{ route('profile.edit') }}" class="flex items-center">

                    @auth
                    <div class="flex items-center 
                    space-x-4          
                    px-3 py-1.5        
                    bg-white 
                    text-gray-800
                    rounded-md 
                    shadow-md hover:shadow-lg 
                    border border-gray-200
                    transition cursor-pointer">

                        <i class="bi bi-person-circle text-xl"></i>

                        <span class="font-semibold">{{ auth()->user()->name }}</span>
                    </div>
                    @endauth

                </a>


                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="flex items-center space-x-2 text-red-600 hover:text-red-700 transition text-lg font-semibold">
                        <i class="bi bi-box-arrow-right text-2xl"></i>
                        <span class="hidden sm:inline font-medium">Tancar sessió</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="container mx-auto mt-6 px-6">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white shadow-inner border-t mt-auto">
        <div class="container mx-auto text-center py-6 text-gray-600 text-sm">
            &copy; {{ date('Y') }}
            <span class="font-semibold text-blue-600">LlistApp</span> —
            Fet per <span class="text-blue-500">Rebeca</span> i <span class="text-blue-500">Raúl</span> · Institut Baix Camp 🏫
        </div>
    </footer>

</body>

</html>