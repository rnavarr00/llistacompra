<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producte;

class ProducteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // Dentro de una lista, mostrar todos los productos que hay en ella
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */

    // Crear un producto, su categoría será -> SIN CATEGORÍA
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

    // Eliminar un producto de la lista seleccionada
    public function destroy(string $id)
    {
        //
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

        // Busquem per l'inici de paraula (case-insensitive)
        $products = Producte::query()
            ->where('nom', 'like', $q . '%')
            ->orderBy('nom')
            ->limit(10)
            ->get(['id', 'nom']);

        return response()->json($products);
    }
}
