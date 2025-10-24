<?php

namespace App\Http\Controllers;

use App\Models\Llista;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LlistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // Mostrar todas las listas que tenga el usuario logueado
    public function index()
    {
        $user = Auth::user();

        // Només les llistes creades per l’usuari loguejat
        $llistes = Llista::where('usuari_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('llistes.index', compact('llistes'));
    }


    /**
     * Show the form for creating a new resource.
     */

    // Crear una lista
    public function create()
    {
        return view('llistes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Confirmem que el nom de la llista sigui valid
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:llistes,nom',
        ], [
            // Mensaje personalizado para la regla unique (opcional)
            'nom.unique' => 'Ja existeix una llista amb aquest nom.',
        ]);

        // 3. CREACIÓN Y GUARDADO EN LA BASE DE DATOS
        // Se utiliza el modelo Llista para crear un nuevo registro.
        $llista = Llista::create([
            'nom'       => $validatedData['nom'], // Asigna el dato validado (el nombre de la lista) a la columna 'nom'.
            'usuari_id' => Auth::id(),             // Asigna el ID del usuario actualmente autenticado a la columna 'usuari_id'.
        ]);

        // 4. REDIRECCIÓN Y MENSAJE DE ÉXITO
        // Redirige al usuario a la página de índice o a la que desees tras la acción exitosa.
        return redirect()->route('llistes.index')
            // 'with' adjunta una variable de sesión temporal (flash) que se usa para mostrar 
            // un mensaje de éxito en la siguiente página.
            ->with('success', '✅ Llista "' . $llista->nom . '" creada amb èxit!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $llista = Llista::findOrFail($id);

        return view('llistes.show', compact('llista'));
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
