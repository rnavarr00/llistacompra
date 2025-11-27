@extends('layouts.welcomeMaster')

@section('title', 'Benvingut a ListApp')

@section('content')

<link rel="icon" href="{{ asset('img1.png') }}" type="image/png">

<body 
    style="
        background: linear-gradient(135deg, #4f46e5, #06b6d4);
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 40px 20px;
    "
>

    <div class="container">

        <div class="row align-items-center">

            <!-- TEXT PRINCIPAL -->
            <div class="col-lg-6 mb-5 mb-lg-0"
                style="color: white;">

                <h1 class="fw-bold mb-3"
                    style="font-size: 3rem; line-height: 1.2;">
                    Organitza la teva vida<br>
                    amb <span style="color: #ffe066;">ListApp</span>
                </h1>

                <p class="mb-4"
                    style="font-size: 1.25rem; max-width: 500px;">
                    Crea, gestiona i comparteix llistes de manera ràpida, visual i intuïtiva.
                    Perfecte per a la compra.
                </p>

                @guest
                <div class="d-flex flex-column flex-sm-row gap-3">

                    <a href="{{ route('register') }}"
                        class="btn btn-light btn-lg"
                        style="
                            border-radius: 14px;
                            padding: 12px 28px;
                            font-size: 1.1rem;
                            color: #4f46e5;
                        ">
                        <i class="bi bi-person-plus me-2"></i>
                        Crear un compte
                    </a>

                    <a href="{{ route('login') }}"
                        class="btn btn-outline-light btn-lg"
                        style="
                            border-radius: 14px;
                            padding: 12px 28px;
                            font-size: 1.1rem;
                            background: khaki;
                            color: blue;
                            transition: box-shadow .15s ease-in-out;
                        "
                        onmouseover="this.style.boxShadow='0 0 10px blue';"
                        onmouseout="this.style.boxShadow='none';"
                        >
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Ja tinc un compte
                    </a>

                </div>

                @else

                <a href="{{ url('/llistes') }}"
                    class="btn btn-light btn-lg mt-3"
                    style="
                        border-radius: 14px;
                        padding: 12px 28px;
                        font-size: 1.1rem;
                        color: #4f46e5;
                    ">
                    Accedeix a les teves llistes
                </a>

                @endguest
            </div>

            <!-- IMATGE / TARGETA -->
            <div class="col-lg-6 d-flex justify-content-center">

                <div class="card shadow-lg"
                     style="
                        border-radius: 25px;
                        overflow: hidden;
                        width: 100%;
                        max-width: 420px;
                        background: rgba(255,255,255,0.9);
                        backdrop-filter: blur(6px);
                        border: none;
                     ">

                    <div style="background: #fff; padding: 40px; text-align: center;">

                        <img src="{{ asset('img.png') }}" 
                             alt="LlistApp"
                             style="height: 80px; margin-bottom: 25px;">

                        <h4 class="fw-bold mb-2" style="color:#1a1a2e;">Tot sota control</h4>

                        <p style="color:#444;">
                            Llistes organitzades, compartides i accessibles des de qualsevol dispositiu.
                        </p>

                    </d
