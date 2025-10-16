<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nom' => 'Sense categoria'],
            ['nom' => 'Fruites i verdures'],
            ['nom' => 'Carn, peix i ous'],
            ['nom' => 'Làctics i formatges'],
            ['nom' => 'Pa, pasta i cereals'],
            ['nom' => 'Conserves i llegums'],
            ['nom' => 'Begudes'],
            ['nom' => 'Snacks i dolços'],
            ['nom' => 'Neteja de la llar'],
            ['nom' => 'Higiene personal'],
            ['nom' => 'Mascotes'],
            ['nom' => 'Bebès i infants'],
            ['nom' => 'Farmàcia i benestar'],
        ];

        DB::table('categories')->insert($categories);
    }
}
