@extends('layouts.master')

@section('title', 'Administrador')

@section('content')

<div class="container mx-auto py-6 px-4">

    <!-- Header con nombre del usuario -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Panel de Administración</h1>

        <div class="flex items-center gap-4">
            <span class="text-gray-600 font-medium">👤 {{ Auth::user()->name }}</span>
            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">+</a>
        </div>
    </div>

    <!-- Botonera de acceso rápido -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <a href="#" class="bg-blue-500 text-white text-center py-6 rounded-lg shadow hover:bg-blue-600 transition">
            Productos
        </a>
        <a href="#" class="bg-purple-500 text-white text-center py-6 rounded-lg shadow hover:bg-purple-600 transition">
            Categorías
        </a>
        <a href="#" class="bg-indigo-500 text-white text-center py-6 rounded-lg shadow hover:bg-indigo-600 transition">
            Usuarios
        </a>
    </div>

</div>

@endsection

