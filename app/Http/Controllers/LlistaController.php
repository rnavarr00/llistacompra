<?php

namespace App\Http\Controllers;

use App\Models\Llista;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LlistaController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * Mètode de suport: Obté les llistes creades per l'usuari actual (Owner).
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function llistesPropies(User $user)
    {
        return Llista::where('usuari_id', $user->id)
            ->get()
            ->each(function ($llista) {
                $llista->es_compartida = false;
            });
    }

    private function llistesCompartides(User $user)
    {
        return $user->llistesCompartides()
            ->get()
            ->each(function ($llista) {
                $llista->es_compartida = true;
            });
    }

    public function index()
    {
        $user = Auth::user();

        $llistes_propies = $this->llistesPropies($user);
        $llistes_compartides = $this->llistesCompartides($user);

        $llistes = $llistes_propies
            ->merge($llistes_compartides)
            ->sortByDesc('created_at');

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
        // Validació de la llista
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:llistes,nom',
            'productes' => 'required|array',
            'productes.*.id' => 'required|exists:productes,id',
            'productes.*.quantitat' => 'required|integer|min:1',
        ], [
            'nom.unique' => 'Ja existeix una llista amb aquest nom.',
            'productes.required' => 'Has d’afegir almenys un producte.',
        ]);


        // Creació de la llista
        $llista = Llista::create([
            'nom'       => $validatedData['nom'],
            'usuari_id' => Auth::id(),
        ]);

        $syncData = [];
        foreach ($validatedData['productes'] as $producte) {
            // Construïm l'array de dades pivot
            $syncData[$producte['id']] = [
                'quantitat' => $producte['quantitat'],
                'comprat' => false,
            ];
        }

        // Utilitzem sync() per a una associació inicial més neta
        $llista->productes()->sync($syncData);

        return redirect()->route('llistes.index')
            ->with('success', '✅ Llista "' . $llista->nom . '" creada amb èxit!');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id) // Rep l'ID com a string
    {
        $llista = Llista::findOrFail($id);
        // Crida LlistaPolicy::view(). Això comprova:
        // a) Si és l'Owner (via before()).
        // b) Si té qualsevol rol compartit (viewer, editor, admin).
        $this->authorize('view', $llista);

        $llista->load('productes.categoria');

        // Agrupar productes per categoria
        $productesPerCategoria = $llista->productes->groupBy(function ($producte) {
            return $producte->categoria->nom ?? 'Sense categoria';
        });

        // Convertim a array i el partim en dues columnes
        $categoriesArray = $productesPerCategoria->toArray();
        $midpoint = ceil(count($categoriesArray) / 2);

        $columna1 = array_slice($categoriesArray, 0, $midpoint, true);
        $columna2 = array_slice($categoriesArray, $midpoint, null, true);

        $hasTwoColumns = count($columna2) > 0;

        return view('llistes.show', compact('llista', 'columna1', 'columna2', 'hasTwoColumns'));
    }


    /**
     * Show the form for editing the specified resource.
     */

    // Editar el nombre de una lista
    public function edit(string $id)
    {
        $llista = Llista::with('productes')->findOrFail($id);

        $this->authorize('update', $llista);

        return view('llistes.edit', compact('llista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $llista = Llista::with('productes')->findOrFail($id);

        $this->authorize('update', $llista);

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:llistes,nom,' . $id,
            'productes' => 'required|array|min:1',
            'productes.*.id' => 'required|exists:productes,id',
            'productes.*.quantitat' => 'required|integer|min:1|max:100',
        ], [
            'nom.required' => 'Has d’introduir un nom per a la llista.',
            'nom.unique' => 'Ja existeix una llista amb aquest nom.',
            'productes.required' => 'Has d’afegir almenys un producte.',
            'productes.*.quantitat.max' => 'La quantitat màxima permesa és de 100 unitats.',
        ]);

        $llista->update([
            'nom' => $validated['nom'],
        ]);

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
        // Si l'usuari no és l'owner (que és qui la Policy permet), Laravel llançarà un 403 Forbidden.
        $this->authorize('delete', $llista);

        $llista->delete();

        return redirect()->route('llistes.index')->with('success', 'Llista eliminada correctament.');
    }

    /**
     * Mostra el formulari per gestionar els permisos de compartició d'una llista.
     */
    public function share(string $id)
    {
        $llista = Llista::findOrFail($id);
        // Permet l'accés només a l'Owner i Admin.
        $this->authorize('share', $llista);
        // Carreguem els usuaris que comparteixen aquesta llista (inclòs el camp pivot 'rol').
        // També carreguem l'owner (usuari_id) per a la vista.
        $llista->load('usuaris'); // 'usuaris' és la relació inversa a llistesCompartides

        return view('llistes.share', compact('llista'));
    }

    /**
     * Processa la lògica d'afegir, modificar o eliminar permisos de compartició.
     */
    public function processShare(Request $request, string $id)
    {
        $llista = Llista::findOrFail($id);

        $this->authorize('share', $llista);

        $validated = $request->validate([
            // Pot ser un array buit si s'eliminen tots els usuaris
            'usuaris' => 'nullable|array',

            // Cada element de l'array 'usuaris' ha de ser un ID vàlid d'usuari que no sigui l'Owner, amb un rol vàlid.
            'usuaris.*.id' => 'required|exists:users,id|not_in:' . $llista->usuari_id,
            'usuaris.*.rol' => 'required|in:viewer,editor,admin',
        ]);

        $syncData = [];
        if (!empty($validated['usuaris'])) {
            foreach ($validated['usuaris'] as $userEntry) {
                // Clau: ID de l'usuari; Valor: Dades de la taula pivot
                $syncData[$userEntry['id']] = [
                    'rol' => $userEntry['rol']
                ];
            }
        }
        // Si un usuari compartit ja hi és, actualitza el seu rol.
        // Si hi ha un usuari nou a $syncData, l'afegeix.
        // Si un usuari compartit existia però no és a $syncData, l'ELIMINA.
        $llista->usuaris()->sync($syncData);

        return redirect()->route('llistes.show', $llista->id)
            ->with('success', 'Permisos de compartició actualitzats correctament.');
    }

    /**
     * Cerca usuaris per nom o correu electrònic (AJAX/JSON).
     * Exclou l'usuari autenticat.
     */
    public function searchUsers(Request $request)
    {
        $searchTerm = $request->get('query');

        if (!$searchTerm) {
            return response()->json([]);
        }
        // Excloure l'usuari actual
        $currentUserId = Auth::id();

        $usuaris = User::where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%");
        })
            ->where('id', '!=', $currentUserId)
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        // Retornar en format json
        return response()->json($usuaris);
    }
}
