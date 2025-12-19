<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversió automàtica de tipus.
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function llistesCreades()
    {
        return $this->hasMany(Llista::class, 'usuari_id');
    }

    public function llistesCompartides()
    {
        return $this->belongsToMany(Llista::class, 'usuaris_llistes', 'usuari_id', 'llista_id')
                    ->withPivot('rol')
                    ->withTimestamps();
    }
}
