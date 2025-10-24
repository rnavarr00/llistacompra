<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categories';
    
    public function productes() {
    return $this->hasMany(Producte::class);
}

}
