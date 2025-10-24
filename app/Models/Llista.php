<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Llista extends Model
{
    protected $table = 'llistes';

    protected $fillable = [
        'nom',
        'usuari_id',
        'productes_llistes'
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'usuari_id');
    }

    public function usuaris()
    {
        return $this->belongsToMany(User::class, 'usuaris_llistes');
    }

    public function productes()
    {
        return $this->belongsToMany(Producte::class, 'productes_llistes')
            ->withPivot('comprat', 'quantitat')
            ->withTimestamps();
    }
}
