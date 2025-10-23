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
        $llistes = collect([
            (object)['id' => 1, 'nom' => 'Compra setmanal', 'icona' => 'bi-cart'],
            (object)['id' => 2, 'nom' => 'Sopar de Nadal', 'icona' => 'bi-gift'],
            (object)['id' => 3, 'nom' => 'Coses per la platja', 'icona' => 'bi-sun'],
        ]);

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
        $list = Llista::create([
            'nom'       => $validatedData['nom'], // Asigna el dato validado (el nombre de la lista) a la columna 'nom'.
            'usuari_id' => Auth::id(),             // Asigna el ID del usuario actualmente autenticado a la columna 'usuari_id'.
        ]);

        // 4. REDIRECCIÓN Y MENSAJE DE ÉXITO
        // Redirige al usuario a la página de índice o a la que desees tras la acción exitosa.
        return redirect()->route('llistes.index') 
                        // 'with' adjunta una variable de sesión temporal (flash) que se usa para mostrar 
                        // un mensaje de éxito en la siguiente página.
                        ->with('success', '✅ Llista "' . $list->nom . '" creada amb èxit!');
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
