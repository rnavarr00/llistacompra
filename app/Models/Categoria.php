<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categories';
    
    public function productes() {
        return $this->hasMany(Producte::class);
    }

    // Assignem un icona per cada Categoria, com les categories no es poden modificar, no és necessari
    // afegir aquestes icones com un camp de la BD, es poden assignar directament al model
    public static function imatgeCategoria(string $nom):string {
        return match ($nom) { 
            'Sense categoria'     => 'fa-solid fa-tag',
            'Fruites i verdures'  => 'fa-solid fa-carrot',
            'Carn, peix i ous'    => 'fa-solid fa-drumstick-bite',
            'Làctics i formatges' => 'fa-solid fa-cheese',
            'Pa, pasta i cereals' => 'fa-solid fa-bread-slice',
            'Conserves i llegums' => 'fa-solid fa-jar',
            'Begudes'             => 'fa-solid fa-glass-water',
            'Snacks i dolços'     => 'fa-solid fa-cookie-bite',
            'Neteja de la llar'   => 'fa-solid fa-broom',
            'Higiene personal'    => 'fa-solid fa-pump-soap',
            'Mascotes'            => 'fa-solid fa-paw',
            'Bebès i infants'     => 'fa-solid fa-baby',
            'Farmàcia i benestar' => 'fa-solid fa-kit-medical',
            default               => 'fa-solid fa-tag',
        };
    }
}
