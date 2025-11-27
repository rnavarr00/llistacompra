<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LlistApp')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Estils personalitzats opcionals --}}
    <style>
        body {
            background: linear-gradient(135deg, #007bff 0%, #4fc3f7 100%);
            min-height: 100vh;
            color: #fff;
        }
        .card {
            border: none;
            border-radius: 1rem;
        }
    </style>
</head>
<body>

    {{-- Contingut de cada pàgina --}}
    @yield('content')

    {{-- Scripts Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
