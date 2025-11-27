<?php

namespace App\Policies;

use App\Models\Llista;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LlistaPolicy
{
    /**
     * Defineix l'accés total per al Creador (Owner) abans de qualsevol altra comprovació.
     */
    public function before(User $user, string $ability, Llista $llista): ?bool
    {
        // 1. COMPROVACIÓ DEL OWNER
        // Si l'usuari és el creador (owner), sempre té permís total.
        if ($user->id === $llista->usuari_id) {
            return true;
        }
        return null; // Continuem amb els mètodes específics (view, update, etc.) si no és l'owner.
    }

    /**
     * Determina si l'usuari pot veure la llista (view).
     * Requisit: Estar a la taula pivot amb QUALSEVOL rol.
     */
    public function view(User $user, Llista $llista): bool
    {
        // Comprova si la llista ha estat compartida amb aquest usuari.
        return $llista->usuaris()->where('usuari_id', $user->id)->exists();
    }

    /**
     * Determina si l'usuari pot actualitzar/editar la llista i els seus productes (update).
     * Requisit: Tenir el rol 'editor' o 'admin'.
     */
    public function update(User $user, Llista $llista): bool
    {
        // Obtenim el rol de l'usuari des de la taula pivot.
        $rol = $llista->usuaris()->where('usuari_id', $user->id)->first()?->pivot->rol;
        
        // Només permetem l'actualització si el rol és 'admin' o 'editor'.
        return in_array($rol, ['admin', 'editor']);
    }

    /**
     * Determina si l'usuari pot compartir o des-compartir la llista amb altres (share).
     * Requisit: Tenir el rol 'admin'.
     */
    public function share(User $user, Llista $llista): bool
    {
        // Obtenim el rol de l'usuari des de la taula pivot.
        $rol = $llista->usuaris()->where('usuari_id', $user->id)->first()?->pivot->rol;
        
        // Només permetem compartir si el rol és 'admin'.
        return $rol === 'admin';
    }

    /**
     * Determina si l'usuari pot eliminar la llista (delete).
     * Requisit: Exclusiu del Owner (ja gestionat per before()).
     */
    public function delete(User $user, Llista $llista): bool
    {
        // Sempre false per als usuaris compartits. El Owner té permís per 'before()'.
        return false; 
    }
}
