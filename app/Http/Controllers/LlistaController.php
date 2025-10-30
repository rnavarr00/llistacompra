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
        // 1️⃣ VALIDACIÓN
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:llistes,nom',
            'producte_id' => 'required|exists:productes,id',
            'quantitat' => 'required|integer|min:1',
        ], [
            'nom.unique' => 'Ja existeix una llista amb aquest nom.',
        ]);

        // 2️⃣ CREACIÓN DE LA LISTA
        $llista = Llista::create([
            'nom'       => $validatedData['nom'],
            'usuari_id' => Auth::id(),
        ]);

        // 3️⃣ ASOCIAR EL PRODUCTO
        $llista->productes()->syncWithoutDetaching([
            $validatedData['producte_id'] => [
                'quantitat' => $validatedData['quantitat'],
                'comprat' => false
            ]
        ]);

        // 4️⃣ REDIRECCIÓN
        return redirect()->route('llistes.index')
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
