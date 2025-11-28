<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LlistApp')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Paleta de blaus personalitzada */
        :root {
            --blue-deep: #1e3a8a;
            --blue-primary: #2563eb;
            --blue-bright: #3b82f6;
            --blue-light: #60a5fa;
            --blue-sky: #93c5fd;
            --blue-pale: #dbeafe;
            --blue-ultra-light: #eff6ff;
            --text-dark: #0f172a;
            --text-mid: #475569;
            --danger: #ef4444;
        }

        /* Fons blau amb formes orgàniques */
        body {
            background: #f0f7ff;
            background-image: 
                radial-gradient(ellipse at 10% 20%, rgba(37, 99, 235, 0.12) 0%, transparent 45%),
                radial-gradient(ellipse at 90% 70%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 100%, rgba(147, 197, 253, 0.1) 0%, transparent 60%);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-dark);
        }

        /* Header amb degradat blau */
        .custom-header {
            background: linear-gradient(135deg, var(--blue-bright) 0%, var(--blue-primary) 100%);
            border: none;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.25);
            position: relative;
        }

        .custom-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, 
                var(--blue-sky) 0%, 
                var(--blue-light) 50%, 
                var(--blue-sky) 100%);
            opacity: 0.6;
        }

        /* Logo amb hover dinàmic */
        .logo-link {
            display: inline-block;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .logo-link:hover {
            transform: scale(1.1) translateY(-3px);
            filter: drop-shadow(0 5px 15px rgba(255, 255, 255, 0.4));
        }

        /* Badge de perfil modern amb blau */
        .profile-container {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 30px;
            padding: 6px 16px;
            transition: all 0.35s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .profile-container:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
        }

        .profile-icon {
            color: white;
            font-size: 1.3rem;
            transition: all 0.35s ease;
        }

        .profile-container:hover .profile-icon {
            color: var(--blue-primary);
            transform: scale(1.15);
        }

        .profile-name {
            font-weight: 600;
            color: white;
            font-size: 0.9rem;
            letter-spacing: -0.01em;
            transition: color 0.35s ease;
        }

        .profile-container:hover .profile-name {
            color: var(--blue-deep);
        }

        /* Botó logout amb estil modern */
        .logout-button {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 6px 14px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .logout-button:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.5);
            color: #fee;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        .logout-icon {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .logout-button:hover .logout-icon {
            transform: translateX(5px) rotate(5deg);
        }

        /* Container principal amb padding asimètric */
        .main-wrapper {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 200px);
            padding: 48px 0;
        }

        .main-wrapper .container {
            max-width: 1100px;
        }

        /* Footer amb degradat blau invertit */
        .custom-footer {
            background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-bright) 100%);
            border: none;
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        .custom-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 200%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.05) 50%, 
                transparent 100%);
            animation: shimmer 8s infinite linear;
        }

        @keyframes shimmer {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(50%); }
        }

        .footer-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            line-height: 1.8;
            position: relative;
            z-index: 1;
        }

        .footer-brand {
            color: white;
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .footer-brand:hover {
            color: var(--blue-sky);
            text-shadow: 0 0 20px rgba(147, 197, 253, 0.8);
        }

        .footer-author {
            color: var(--blue-pale);
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .footer-author:hover {
            color: white;
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
        }

        .footer-heart {
            color: #fca5a5;
            display: inline-block;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            10%, 30% { transform: scale(1.1); }
            20% { transform: scale(1.15); }
        }

        /* Separator del footer */
        .footer-separator {
            color: rgba(255, 255, 255, 0.4);
            margin: 0 12px;
        }

        /* Animació d'entrada */
        .container {
            animation: slideUp 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Override Bootstrap */
        .shadow, .shadow-sm, .shadow-md, .shadow-lg {
            box-shadow: none !important;
        }

        /* Scroll suau */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<link rel="icon" href="{{ asset('img1.png') }}" type="image/png">
<body class="d-flex flex-column min-vh-100">
    
    {{-- NAVBAR --}}
    <header class="custom-header sticky-top">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                {{-- LOGO --}}
                <a href="{{ route('llistes.index') }}" class="logo-link">
                    <img src="{{ asset('img.png') }}" alt="LlistApp" style="height: 46px; width: auto;">
                </a>
                
                {{-- PERFIL / LOGOUT --}}
                <div class="d-flex align-items-center gap-3">
                    {{-- Perfil --}}
                    <a href="{{ route('profile.edit') }}" class="text-decoration-none">
                        @auth
                        <div class="profile-container d-flex align-items-center gap-3">
                            <i class="bi bi-person-circle profile-icon"></i>
                            <span class="profile-name d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </div>
                        @endauth
                    </a>
                    
                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="logout-button btn">
                            <i class="bi bi-box-arrow-right logout-icon"></i>
                            <span class="d-none d-sm-inline">Sortir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    
    {{-- MAIN CONTENT --}}
    <main class="main-wrapper">
        <div class="container">
            @yield('content')
        </div>
    </main>
    
    {{-- FOOTER --}}
    <footer class="custom-footer mt-auto">
        <div class="container py-4">
            <div class="footer-text text-center">
                <p class="mb-0">
                    © {{ date('Y') }} <span class="footer-brand">LlistApp</span> 
                    <span class="footer-separator">·</span>
                    Creat amb <span class="footer-heart">♥</span> per 
                    <span class="footer-author">Rebeca</span> i 
                    <span class="footer-author">Raúl</span>
                    <span class="footer-separator">·</span>
                    Institut Baix Camp
                </p>
            </div>
        </div>
    </footer>
    
</body>
</html>