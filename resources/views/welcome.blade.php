@extends('layouts.welcomeMaster')

@section('title', 'Benvingut a ListApp')

@section('content')

<style>
    .welcome-page {
        background: linear-gradient(135deg, #4f46e5, #06b6d4);
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 40px 20px;
        box-sizing: border-box;
    }

    .titol-principal {
        font-size: clamp(1.8rem, 4vw, 3rem);
        line-height: 1.2;
        overflow-wrap: break-word;
    }

    .text-principal {
        font-size: 1.25rem;
        max-width: 500px;
    }

    .welcome-btn {
        border-radius: 14px;
        padding: 12px 28px;
        font-size: 1.1rem;
        text-align: center;
    }

    .welcome-btn-register,
    .welcome-btn-access {
        color: #4f46e5;
    }

    .welcome-btn-login {
        background: khaki;
        color: blue;
        transition: box-shadow .15s ease-in-out;
    }

    .welcome-btn-login:hover {
        background: khaki;
        color: blue;
        box-shadow: 0 0 10px blue;
    }

    .welcome-card {
        border-radius: 25px;
        overflow: hidden;
        width: 100%;
        max-width: 420px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(6px);
        border: none;
    }

    .welcome-card-content {
        background: #fff;
        padding: 40px;
        text-align: center;
    }

    .welcome-logo {
        height: 80px;
        max-width: 100%;
        object-fit: contain;
        margin-bottom: 25px;
    }

    @media (max-width: 576px) {
        .welcome-page {
            padding: 30px 16px;
        }

        .titol-principal {
            font-size: clamp(1.8rem, 7vw, 2.4rem);
        }

        .salt-escriptori {
            display: none;
        }

        .text-principal {
            font-size: 1.05rem;
        }

        .welcome-buttons {
            width: 100%;
        }

        .welcome-btn {
            width: 100%;
            white-space: normal;
        }

        .welcome-card-content {
            padding: 28px 20px;
        }
    }
</style>

<div class="welcome-page">
    <div class="container">

        <div class="row align-items-center">

            {{-- TEXT PRINCIPAL --}}
            <div class="col-lg-6 mb-5 mb-lg-0 text-white">

                <h1 class="fw-bold mb-3 titol-principal">
                    Organitza la teva vida
                    <br class="salt-escriptori">
                    amb <span style="color: #ffe066;">LlistApp</span>
                </h1>

                <p class="mb-4 text-principal">
                    Crea, gestiona i comparteix llistes de manera ràpida,
                    visual i intuïtiva. Perfecte per a la compra.
                </p>

                @guest
                    <div class="d-flex flex-column flex-sm-row gap-3 welcome-buttons">

                        <a href="{{ route('register') }}"
                           class="btn btn-light btn-lg welcome-btn welcome-btn-register">
                            <i class="bi bi-person-plus me-2"></i>
                            Crear un compte
                        </a>

                        <a href="{{ route('login') }}"
                           class="btn btn-outline-light btn-lg welcome-btn welcome-btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Ja tinc un compte
                        </a>

                    </div>
                @else
                    <a href="{{ url('/llistes') }}"
                       class="btn btn-light btn-lg mt-3 welcome-btn welcome-btn-access">
                        Accedeix a les teves llistes
                    </a>
                @endguest

            </div>
            {{-- FI TEXT PRINCIPAL --}}

            {{-- IMATGE / TARGETA --}}
            <div class="col-lg-6 d-flex justify-content-center">

                <div class="card shadow-lg welcome-card">

                    <div class="welcome-card-content">

                        <img src="{{ asset('img.png') }}"
                             alt="LlistApp"
                             class="welcome-logo">

                        <h4 class="fw-bold mb-2" style="color: #1a1a2e;">
                            Tot sota control
                        </h4>

                        <p class="mb-0" style="color: #444;">
                            Llistes organitzades, compartides i accessibles
                            des de qualsevol dispositiu.
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

@endsection
