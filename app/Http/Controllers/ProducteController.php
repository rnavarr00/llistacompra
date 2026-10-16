<?php

namespace App\Http\Controllers;

use App\Models\Llista;
use Illuminate\Http\Request;
use App\Models\Producte;

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

    public function search(Request $request)
    {
        $q = $request->query('q', '');
        // Borrem els espais i limitem el número de caràcters
        $q = trim(substr($q, 0, 100)); 

        // Si l'usuari no escriu res, no recomanem cap producte
        if ($q === '') {
            return response()->json([]);
        }

        // Passem el producte per la funció normalitzar, explicada al Model
        $qNormalitzada = Producte::normalitzar($q);

        // Busquem per l'inici de paraula (case-insensitive)
        $products = Producte::query()
            ->orderBy('nom') 
            ->get(['id', 'nom']) 
            ->filter(function ($product) use ($qNormalitzada) { 
                return str_starts_with( 
                    Producte::normalitzar($product->nom), $qNormalitzada ); }) 
                    ->take(10) ->values();

        return response()->json($products);
    }
}
