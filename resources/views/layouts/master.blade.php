<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Lista de Compras')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    {{-- Header modular para usuarios normales --}}
    @include('layouts.header')

    <!-- Main Content -->
    <main class="container mx-auto mt-6 px-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner mt-12">
        <div class="container mx-auto text-center py-6 text-gray-600">
            &copy; {{ date('Y') }} Projecte de llistes fet per Rebeca i Raúl, de l'Institut Baix Camp. 
        </div>
    </footer>

    <!-- Scripts opcionales -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
