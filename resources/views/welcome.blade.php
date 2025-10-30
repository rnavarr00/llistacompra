<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvingut a ListaCompra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
            <h1 class="text-primary fw-bold mb-3">Benvingut/da a ListaCompra 📝</h1>
            <p class="mb-3">
                Aquesta aplicació t’ajuda a <strong>crear, organitzar i compartir llistes</strong> de forma fàcil i ràpida.  
                Pots fer llistes de la compra, tasques o idees, i compartir-les amb els teus amics o companys per col·laborar-hi en temps real.
            </p>
            <p class="mb-4">
                <strong>Comença ara</strong> i descobreix com de fàcil pot ser mantenir-te organitzat i connectat!
            </p>

            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-2">Crea un compte</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Ja tinc compte</a>
            @else
                <a href="{{ url('/llistes') }}" class="btn btn-primary btn-lg">Accedeix a les teves llistes</a>
            @endguest
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
