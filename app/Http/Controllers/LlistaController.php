<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Llista;
use App\Models\Producte;
use App\Models\User;
use App\Support\ColorLlista;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LlistaController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Llistes creades per l'usuari actual (owner).
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

        $llistes = $this->llistesPropies($user)
            ->merge($this->llistesCompartides($user))
            ->sortByDesc('created_at');

        return view('llistes.index', compact('llistes'));
    }

    /**
     * Crea una llista nova (només nom i, opcionalment, color). Respon JSON.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'nom' => [
                    'required', 'string', 'max:255',
                    Rule::unique('llistes', 'nom')->where('usuari_id', Auth::id()),
                ],
                'color' => [
                    'nullable',
                    'string',
                    Rule::in(array_keys(ColorLlista::PALETA)),
                ],
            ],
            [
                'nom.required' => 'Has d’introduir un nom per a la llista.',
                'nom.unique'   => 'Ja tens una llista amb aquest nom.',
                'nom.max'      => 'El nom no pot passar de 255 caràcters.',
            ]
        );

        $llista = Llista::create([
            'nom'       => trim($validated['nom']),
            'usuari_id' => Auth::id(),
            'color'     => $validated['color'] ?? null,
        ]);

        return response()->json(
            ['redirect' => route('llistes.show', $llista->id)],
            201
        );
    }

    public function show(string $id)
    {
        $llista = Llista::findOrFail($id);
        $this->authorize('view', $llista);

        return view('llistes.list', $this->dadesVista($llista));
    }

    // Es manté perquè no es trenquin enllaços antics: l'edició es fa a show.
    public function edit(string $id)
    {
        return redirect()->route('llistes.show', $id);
    }

    /**
     * Canvi de nom (AJAX).
     */
    public function actualitzarNom(Request $request, string $id)
    {
        $llista = Llista::findOrFail($id);
        $this->authorize('update', $llista);

        $validated = $request->validate(
            [
                'nom' => [
                    'required', 'string', 'max:255',
                    Rule::unique('llistes', 'nom')
                        ->where('usuari_id', $llista->usuari_id)
                        ->ignore($llista->id),
                ],
            ],
            [
                'nom.required' => 'Has d’introduir un nom per a la llista.',
                'nom.unique'   => 'El propietari ja té una llista amb aquest nom.',
                'nom.max'      => 'El nom no pot passar de 255 caràcters.',
            ]
        );

        $llista->update(['nom' => trim($validated['nom'])]);

        return response()->json(['nom' => $llista->nom]);
    }

    /**
     * Canvi de color (AJAX).
     */
    public function actualitzarColor(Request $request, string $id)
    {
        $llista = Llista::findOrFail($id);
        $this->authorize('update', $llista);

        $validated = $request->validate([
            'color' => ['nullable', 'string', Rule::in(array_keys(ColorLlista::PALETA))],
        ]);

        $llista->update(['color' => $validated['color'] ?? null]);

        return response()->json(['ok' => true]);
    }
    /**
     * Afegeix un producte a la llista (AJAX).
     * Accepta un producte existent (producte_id) o un nom nou (nom).
     * Retorna les dades per pintar la fila.
     */
    public function afegirProducte(Request $request, string $id)
    {
        $llista = Llista::findOrFail($id);
        $this->authorize('update', $llista);

        $validated = $request->validate(
            [
                'producte_id' => 'nullable|required_without:nom|exists:productes,id',
                'nom'         => 'nullable|required_without:producte_id|string|max:255',
                'quantitat'   => 'required|integer|min:1|max:100',
            ],
            [
                'producte_id.required_without' => 'Has de seleccionar un producte.',
                'nom.max' => 'El nom del producte no pot passar de 255 caràcters.',
            ]
        );

        if (!empty($validated['producte_id'])) {
            // Producte existent
            $producte = Producte::findOrFail($validated['producte_id']);
        } else {
            // Nom nou: netegem espais repetits
            $nom = trim(preg_replace('/\s+/', ' ', $validated['nom']));

            if ($nom === '') {
                return response()->json(['message' => 'El nom del producte no pot estar buit.'], 422);
            }

            // Si ja existeix (sense distingir majúscules ni accents), el reutilitzem
            $nomNormalitzat = Producte::normalitzar($nom);
            $producte = Producte::get(['id', 'nom', 'categoria_id'])
                ->first(fn ($p) => Producte::normalitzar($p->nom) === $nomNormalitzat);

            // Si no existeix, el creem a la categoria 1 (Sense Categoria)
            if (!$producte) {
                $producte = Producte::create([
                    'nom'          => $nom,
                    'categoria_id' => 1,
                ]);
            }
        }

        if ($llista->productes()->where('productes.id', $producte->id)->exists()) {
            return response()->json(['message' => 'Aquest producte ja és a la llista.'], 422);
        }

        $llista->productes()->attach($producte->id, [
            'quantitat' => $validated['quantitat'],
            'comprat'   => false,
        ]);

        $producte->load('categoria');
        $nomCategoria = $producte->categoria->nom ?? 'Sense categoria';

        return response()->json([
            'id'            => $producte->id,
            'nom'           => $producte->nom,
            'categoria'     => $nomCategoria,
            'icona'         => Categoria::imatgeCategoria($nomCategoria),
            'quantitat'     => (int) $validated['quantitat'],
            'toggle_url'    => route('productes.toggle', [
                'llista_id' => $llista->id, 'producte_id' => $producte->id,
            ]),
            'delete_url'    => route('llistes.productes.eliminar', [
                'llista_id' => $llista->id, 'producte_id' => $producte->id,
            ]),
            'quantitat_url' => route('llistes.productes.quantitat', [
                'llista_id' => $llista->id, 'producte_id' => $producte->id,
            ]),
        ]);
    }

    /**
     * Canvi de quantitat d'un producte (AJAX).
     */
    public function actualitzarQuantitat(Request $request, string $llista_id, string $producte_id)
    {
        $llista = Llista::findOrFail($llista_id);
        $this->authorize('update', $llista);

        $validated = $request->validate(
            ['quantitat' => 'required|integer|min:1|max:100'],
            ['quantitat.max' => 'La quantitat màxima permesa és de 100 unitats.']
        );

        $llista->productes()->updateExistingPivot($producte_id, [
            'quantitat' => $validated['quantitat'],
        ]);

        return response()->json(['quantitat' => (int) $validated['quantitat']]);
    }

    /**
     * Treu un producte de la llista (AJAX). No l'esborra de la taula productes.
     */
    public function eliminarProducte(string $llista_id, string $producte_id)
    {
        $llista = Llista::findOrFail($llista_id);
        $this->authorize('update', $llista);

        $llista->productes()->detach($producte_id);

        return response()->json(['ok' => true]);
    }

    /**
     * Eliminar una llista.
     */
    public function destroy(string $id)
    {
        $llista = Llista::findOrFail($id);
        // Si l'usuari no és l'owner, la Policy llança un 403.
        $this->authorize('delete', $llista);

        $llista->delete();

        return redirect()->route('llistes.index')->with('success', 'Llista eliminada correctament.');
    }

    /**
     * Formulari per gestionar els permisos de compartició.
     */
    public function share(string $id)
    {
        $llista = Llista::findOrFail($id);
        $this->authorize('share', $llista);
        $llista->load('usuaris');

        return view('llistes.share', compact('llista'));
    }

    /**
     * Afegeix, modifica o elimina permisos de compartició.
     */
    public function processShare(Request $request, string $id)
    {
        $llista = Llista::findOrFail($id);

        $this->authorize('share', $llista);

        $validated = $request->validate([
            'usuaris' => 'nullable|array',
            'usuaris.*.id' => 'required|exists:users,id|not_in:' . $llista->usuari_id,
            'usuaris.*.rol' => 'required|in:viewer,editor,admin',
        ]);

        $syncData = [];
        if (!empty($validated['usuaris'])) {
            foreach ($validated['usuaris'] as $userEntry) {
                $syncData[$userEntry['id']] = [
                    'rol' => $userEntry['rol']
                ];
            }
        }
        // Actualitza rols, afegeix usuaris nous i ELIMINA els que ja no hi són.
        $llista->usuaris()->sync($syncData);

        return redirect()->route('llistes.show', $llista->id)
            ->with('success', 'Permisos de compartició actualitzats correctament.');
    }

    /**
     * Cerca usuaris per nom o correu (JSON). Exclou l'usuari autenticat.
     */
    public function searchUsers(Request $request)
    {
        $searchTerm = $request->get('query');

        if (!$searchTerm) {
            return response()->json([]);
        }

        $usuaris = User::where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%");
        })
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($usuaris);
    }

    /**
     * Prepara les dades de la vista de la llista.
     */
    private function dadesVista(Llista $llista): array
    {
        $productes = $llista->load('productes.categoria')->productes;

        $perCategoria = $productes->groupBy(
            fn ($producte) => $producte->categoria->nom ?? 'Sense categoria'
        )->toArray();

        $midpoint = ceil(count($perCategoria) / 2);
        $columna1 = array_slice($perCategoria, 0, $midpoint, true);
        $columna2 = array_slice($perCategoria, $midpoint, null, true);
        $hasTwoColumns = count($columna2) > 0;

        $colors = ColorLlista::PALETA;

        return compact(
            'llista',
            'columna1',
            'columna2',
            'hasTwoColumns',
            'colors'
        );
    }
}