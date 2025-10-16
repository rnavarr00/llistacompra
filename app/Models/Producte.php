<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producte extends Model
{
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function llistes()
    {
        return $this->belongsToMany(Llista::class, 'productes_llistes')
            ->withPivot('comprat', 'quantitat')
            ->withTimestamps();
    }
}
