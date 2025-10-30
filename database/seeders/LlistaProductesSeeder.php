<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Llista;
use App\Models\Producte;

class LlistaProductesSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Crea una llista de prova
        $llista = Llista::create([
            'nom' => 'Compra setmanal',
            'usuari_id' => 1, // assegura't que existeixi un usuari amb ID 1
        ]);

        // 🔹 Selecciona 8 productes aleatoris de la BD (ja creats pel ProducteSeeder)
        $productes = Producte::inRandomOrder()->take(8)->get();

        // 🔹 Associa els productes a la llista amb valors pivot
        foreach ($productes as $producte) {
            $llista->productes()->attach($producte->id, [
                'comprat' => fake()->boolean(40), // 40% de probabilitat de comprat
                'quantitat' => fake()->numberBetween(1, 5),
            ]);
        }
    }
}
