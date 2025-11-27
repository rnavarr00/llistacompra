<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LlistApp') }}</title>

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
            <a href="/google-auth/redirect"
                style="
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    background: #ffffff;
                    color: #3c4043;
                    border: 1px solid #dadce0;
                    border-radius: 6px;
                    padding: 10px 16px;
                    font-size: 0.95rem;
                    font-weight: 500;
                    text-decoration: none;
                    width: 100%;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    transition: all 0.2s ease;
            "
                onmouseover="this.style.background='#f7f8f8'"
                onmouseout="this.style.background='#ffffff'">

                <!-- ICONA GOOGLE -->
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                    alt="Google"
                    style="width: 20px; height: 20px;">

                <span>Iniciar sessió amb Google</span>
            </a>

        </div>
    </div>
</body>

</html>