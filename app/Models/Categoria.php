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
            'Sense categoria'     => 'bi-tag',
            'Fruites i verdures'  => 'bi-basket',
            'Carn, peix i ous'    => 'bi-egg',
            'Làctics i formatges' => 'bi-cup-hot',
            'Pa, pasta i cereals' => 'bi-box',
            'Conserves i llegums' => 'bi-box-seam',
            'Begudes'             => 'bi-cup-straw',
            'Snacks i dolços'     => 'bi-gift',
            'Neteja de la llar'   => 'bi-house',
            'Higiene personal'    => 'bi-droplet',
            'Mascotes'            => 'bi-heart',
            'Bebès i infants'     => 'bi-balloon',
            'Farmàcia i benestar' => 'bi-bandaid',
            default               => 'bi-tag',
};
    }

}
