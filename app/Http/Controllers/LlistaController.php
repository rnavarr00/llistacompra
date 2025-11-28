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
        // 1. UTILITZEM LA RELACIÓ llistesCreades() del model User (1:N)
        // Podríem usar $user->llistesCreades, però la query simple és igual de ràpida.
        return Llista::where('usuari_id', $user->id)
                     ->get();
    }

    /**
     * Mètode de suport: Obté les llistes compartides amb l'usuari actual.
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function llistesCompartides(User $user)
    {
        // 1. OBTENIR ELS ID DE LES LLISTES COMPARTIDES (Via relació Many-to-Many amb Eloquent)
        // Això és més net que usar DB::table().
        $llistes_compartides_ids = $user->llistesCompartides()->pluck('llista_id');

        // 2. RETORNAR ELS OBJECTES LLISTA
        return Llista::whereIn('id', $llistes_compartides_ids)
                     ->get();
    }

    /**
     * Display a listing of the resource.
     * Mostrar totes les llistes (pròpies i compartides) que tingui l'usuari loguejat.
     */
    public function index()
    {
        // 1. OBTENIR L'USUARI AUTENTICAT
        $user = Auth::user();

        // 2. RECOLLIR LES DUES COLLECTIONS
        $llistes_propies = $this->llistesPropies($user);
        $llistes_compartides = $this->llistesCompartides($user);

        // 3. FUSIONAR I ORDENAR
        // Utilitzem merge() i sortByDesc() de Laravel Collections.
        $llistes = $llistes_propies
            ->merge($llistes_compartides)
            ->sortByDesc('created_at');

        // 4. RETORNAR LA VISTA
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
        $syncData = [];
        foreach ($validatedData['productes'] as $producte) {
            // Construïm l'array de dades pivot.
            $syncData[$producte['id']] = [
                'quantitat' => $producte['quantitat'],
                'comprat' => false,
            ];
        }

        // Utilitzem sync() per a una associació inicial més neta
        $llista->productes()->sync($syncData); 


        // 4️⃣ REDIRECCIÓ
        return redirect()->route('llistes.index')
            ->with('success', '✅ Llista "' . $llista->nom . '" creada amb èxit!');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id) // Rep l'ID com a string
    {
        // 1. RECUPERACIÓ DEL MODEL
        // Busquem la llista, llançant 404 si no existeix.
        $llista = Llista::findOrFail($id);
        
        // 2. AUTORITZACIÓ EXPLÍCITA
        // Crida LlistaPolicy::view(). Això comprova:
        // a) Si és l'Owner (via before()).
        // b) Si té qualsevol rol compartit (viewer, editor, admin).
        $this->authorize('view', $llista);

        // Si la comprovació passa:
        $llista->load('productes'); 

        return view('llistes.show', compact('llista'));
    }


    /**
     * Show the form for editing the specified resource.
     */

    // Editar el nombre de una lista
    public function edit(string $id)
    {
        // 1. RECUPERACIÓ DEL MODEL
        $llista = Llista::with('productes')->findOrFail($id);
        
        // 2. AUTORITZACIÓ EXPLÍCITA: Crida LlistaPolicy::update()
        // La Policy decidirà si l'usuari té el rol 'editor', 'admin' o si és l'owner.
        $this->authorize('update', $llista); 
        
        return view('llistes.edit', compact('llista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $llista = Llista::with('productes')->findOrFail($id);
    
        // 1. AUTORITZACIÓ EXPLÍCITA: Crida LlistaPolicy::update()
        $this->authorize('update', $llista); 

        // 2. Validació (mantinguda del vostre codi original)
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

        // 3. Actualitzar nom i productes
        $llista->update([
            'nom' => $validated['nom'],
        ]);

        // Sincronitzar productes amb quantitats
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
        // 1. RECUPERACIÓ DEL MODEL
        $llista = Llista::findOrFail($id);
        
        // 2. AUTORITZACIÓ EXPLÍCITA: Crida LlistaPolicy::delete()
        // Si l'usuari no és l'owner (que és qui la Policy permet), 
        // Laravel llançarà un 403 Forbidden.
        $this->authorize('delete', $llista);

        // 3. ELIMINACIÓ (Si la comprovació passa)
        $llista->delete();

        return redirect()->route('llistes.index')->with('success', 'Llista eliminada correctament.');
    }

    /**
    * Mostra el formulari per gestionar els permisos de compartició d'una llista.
    */
    public function share(string $id)
    {
        // 1. RECUPERACIÓ DEL MODEL
        $llista = Llista::findOrFail($id);

        // 2. AUTORITZACIÓ EXPLÍCITA: Crida LlistaPolicy::share()
        // Permet l'accés només a l'Owner i Admin.
        $this->authorize('share', $llista);

        // 3. CARREGAR LA RELACIÓ D'USUARIS COMPARTITS
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
        // 1. RECUPERACIÓ DEL MODEL
        $llista = Llista::findOrFail($id);
        
        // 2. AUTORITZACIÓ EXPLÍCITA: Crida LlistaPolicy::share()
        // Requerit abans de qualsevol modificació de permisos.
        $this->authorize('share', $llista);

        // 3. VALIDACIÓ
        $validated = $request->validate([
            // Pot ser un array buit si s'eliminen tots els usuaris
            'usuaris' => 'nullable|array', 
            
            // Cada element de l'array 'usuaris' ha de ser un ID vàlid d'usuari
            // que no sigui l'Owner, amb un rol vàlid.
            'usuaris.*.id' => 'required|exists:users,id|not_in:' . $llista->usuari_id,
            'usuaris.*.rol' => 'required|in:viewer,editor,admin',
        ]);

        // 4. PREPARACIÓ DE DADES PER AL SYNC
        $syncData = [];
        if (!empty($validated['usuaris'])) {
            foreach ($validated['usuaris'] as $userEntry) {
                // Clau: ID de l'usuari; Valor: Dades de la taula pivot
                $syncData[$userEntry['id']] = [
                    'rol' => $userEntry['rol']
                ];
            }
        }
        
        // 5. SINCRONITZACIÓ DE LA RELACIÓ
        // El mètode sync() sincronitza la relació N:N:
        // - Si un usuari compartit ja hi és, actualitza el seu rol.
        // - Si hi ha un usuari nou a $syncData, l'afegeix.
        // - Si un usuari compartit existia però no és a $syncData, l'ELIMINA.
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
        // 1. OBTENIR EL TERME DE CERCA
        $searchTerm = $request->get('query');

        if (!$searchTerm) {
            return response()->json([]);
        }

        // 2. EXCLOURE L'USUARI ACTUAL (L'OWNER QUE ESTÀ COMPARTINT)
        $currentUserId = Auth::id();

        // 3. EXECUTAR LA CERCA
        $usuaris = User::where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                        ->where('id', '!=', $currentUserId) // No es pot compartir amb un mateix
                        ->select('id', 'name', 'email') // Seleccionar només els camps necessaris
                        ->limit(10)
                        ->get();

        // 4. RETORNAR EN FORMAT JSON
        return response()->json($usuaris);
    }
}
