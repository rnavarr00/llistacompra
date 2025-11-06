@extends('layouts.welcomeMaster')

@section('title', 'Benvingut a ListaCompra')

@section('content')
<body class="bg-primary bg-gradient">

    <!-- Botons superior dret -->
    <div class="position-absolute top-0 end-0 p-3">
        @guest
            <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Inicia sessió</a>
            <a href="{{ route('register') }}" class="btn btn-light text-primary fw-semibold">Registra’t</a>
        @else
            <a href="{{ url('/llistes') }}" class="btn btn-light text-primary fw-semibold">Les meves llistes</a>
        @endguest
    </div>

    <!-- Contenidor central -->
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 p-md-5 text-center" style="max-width: 650px;">
            <h1 class="text-primary fw-bold mb-3">Benvingut/da a llista compra 📝</h1>
            <p class="mb-3">
                Aquesta aplicació t’ajuda a <strong>crear, organitzar i compartir llistes</strong> de forma fàcil i ràpida.  
                Pots fer llistes de la compra, tasques o idees, i compartir-les amb els teus amics o companys per col·laborar-hi en temps real.
            </p>
            <p class="mb-4">
                <strong>Comença ara</strong> i descobreix com de fàcil pot ser mantenir-te organitzat i connectat!
            </p>

            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg mb-2">
                <i class="bi bi-person-plus me-2"></i>Crea un compte</a>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ja tinc compte</a>
            @else
                <a href="{{ url('/llistes') }}" class="btn btn-primary btn-lg">
                Accedeix a les teves llistes</a>
            @endguest
        </div>
    </div>
</body>
@endsection
