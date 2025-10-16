<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Camps que es poden assignar massivament.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Camps que s’oculten quan es serialitza l’objecte.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversió automàtica de tipus.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* =====================================================
     | RELACIONS ELOQUENT
     ===================================================== */

    /**
     * Llistes creades per l’usuari (1:N)
     * → Un usuari pot crear moltes llistes.
     */
    public function llistesCreades()
    {
        return $this->hasMany(Llista::class, 'usuari_id');
    }

    /**
     * Llistes compartides amb l’usuari (N:N)
     * → Un usuari pot tenir accés a moltes llistes compartides.
     */
    public function llistesCompartides()
    {
        return $this->belongsToMany(Llista::class, 'usuaris_llistes')
                    ->withTimestamps();
    }
}
