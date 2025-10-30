<?php

namespace App\Http\Controllers;

use App\Models\Llista;
use Illuminate\Http\Request;

class ProducteController extends Controller
{
    public function toggle($llista_id, $producte_id)
    {
        // Buscar la llista i el producte dins la relació
        $llista = Llista::findOrFail($llista_id);
        $producte = $llista->productes()->where('producte_id', $producte_id)->firstOrFail();

        // Canviar l’estat de "comprat" (si era true, passa a false i viceversa)
        $nouEstat = !$producte->pivot->comprat;

        // Actualitzar el valor a la taula pivot
        $llista->productes()->updateExistingPivot($producte_id, [
            'comprat' => $nouEstat
        ]);

        // Tornar enrere per mostrar els canvis
        return back();
    }
}
