@extends('layouts.master')

@section('title', 'Les meves llistes')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Les meves llistes</h1>

        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary">👤 {{ Auth::user()->name }}</span>
            <a href="#" class="btn btn-primary">+</a>
        </div>
    </div>

    {{-- Aquí després vindrà el llistat de llistes --}}
</div>

@endsection
