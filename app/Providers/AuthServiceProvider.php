<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Llista; // Importem el model Llista
use App\Policies\LlistaPolicy; // Importem la Policy

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // RELACIÓ CRUCIAL: Quan es faci una comprovació d'autorització sobre una Llista, utilitza LlistaPolicy.
        Llista::class => LlistaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Aquí podríeu definir Gates, però utilitzarem Policies.
    }
}