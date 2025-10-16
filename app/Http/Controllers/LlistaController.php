<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LlistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // Mostrar todas las listas que tenga el usuario logueado
    public function index()
    {
        return view('llistes.index');
    }

    /**
     * Show the form for creating a new resource.
     */

    // Crear una lista
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    // Editar el nombre de una lista
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    // Eliminar una lista
    public function destroy(string $id)
    {
        //
    }
}
