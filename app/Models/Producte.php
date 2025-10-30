<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producte extends Model
{

    protected $table = 'productes';

    use HasFactory;

    // Camps que poden ser omplerts amb assignació massiva
    protected $fillable = ['nom', 'categoria_id'];

    // Cada producte pertany a una categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relació N:N amb les llistes (taula pivot productes_llistes)
    public function llistes()
    {
        return $this->belongsToMany(Llista::class, 'productes_llistes')
            ->withPivot('comprat', 'quantitat')
            ->withTimestamps();
    }
}
