<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    public function productes() {
    return $this->hasMany(Producte::class);
}

}
