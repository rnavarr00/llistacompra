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
            'productes' => 'required|array',
            'productes.*.id' => 'required|exists:productes,id',
            'productes.*.quantitat' => 'required|integer|min:1',
        ], [
            'nom.unique' => 'Ja existeix una llista amb aquest nom.',
            'productes.required' => 'Has d’afegir almenys un producte.',
        ]);


        // 2️⃣ CREACIÓN DE LA LISTA
        $llista = Llista::create([
            'nom'       => $validatedData['nom'],
            'usuari_id' => Auth::id(),
        ]);

        // 3️⃣ ASOCIAR EL PRODUCTO
        foreach ($validatedData['productes'] as $producte) {
            $llista->productes()->syncWithoutDetaching([
                $producte['id'] => [
                    'quantitat' => $producte['quantitat'],
                    'comprat' => false,
                ]
            ]);
        }

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
        $llista = Llista::with('productes')->findOrFail($id);

        if ($llista->usuari_id !== Auth::id()) {
            abort(403, 'No tens permís per editar aquesta llista.');
        }

        return view('llistes.edit', compact('llista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $llista = Llista::with('productes')->findOrFail($id);

        if ($llista->usuari_id !== Auth::id()) {
            abort(403, 'No tens permís per modificar aquesta llista.');
        }

        // Validación directa
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:llistes,nom,' . $id,
            'productes' => 'required|array|min:1',
            'productes.*.id' => 'required|exists:productes,id',
            'productes.*.quantitat' => 'required|integer|min:1',
        ], [
            'nom.required' => 'Has d’introduir un nom per a la llista.',
            'nom.unique' => 'Ja existeix una llista amb aquest nom.',
            'productes.required' => 'Has d’afegir almenys un producte.',
        ]);

        // Actualizar nombre
        $llista->update([
            'nom' => $validated['nom'],
        ]);

        // Sincronizar productos con cantidades
        $syncData = [];
        foreach ($validated['productes'] as $producte) {
            $syncData[$producte['id']] = ['quantitat' => $producte['quantitat']];
        }
        $llista->productes()->sync($syncData);

        return redirect()->route('llistes.index')->with('success', 'Llista actualitzada correctament.');
    }


    /**
     * Remove the specified resource from storage.
     */

    // Eliminar una lista
    public function destroy(string $id)
    {
        $llista = Llista::findOrFail($id);
        $llista->delete();

        return redirect()->route('llistes.index')->with('success', 'Llista eliminada correctament');
    }
}
