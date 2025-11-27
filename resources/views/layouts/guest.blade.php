<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="font-sans antialiased"
      style="background: linear-gradient(135deg, #4f46e5, #06b6d4); 
             animation: gradientMove 10s ease infinite;
             min-height: 100vh;">


    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" 
         style="background: transparent;">

            <div>
                <a href="/">
                    <img src="{{ asset('img.png') }}" alt="LlistApp" style="height:70px; width:auto; padding-top: 20px;">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
                <a href="/google-auth/redirect" class="text-sm text-gray-600 underline">SSO amb Google</a>
            </div>
        </div>
    </body>
</html>
