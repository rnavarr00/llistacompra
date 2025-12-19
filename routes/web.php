<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LlistaController; 
use App\Http\Controllers\ProducteController;
use App\Http\Middleware\AdminMiddleware;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/google-auth/callback', function () {
    $user_google = Socialite::driver('google')->user();

    $user = User::updateOrCreate([
        'email' => $user_google->email,
    ], [
        'name' => $user_google->name,
        'email' => $user_google->email,
    ]);

    Auth::login($user, true);

    return redirect()->route('llistes.index');
});

Route::get('/dashboard', function () {
    return redirect()->route('llistes.index');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutes de llistes (totes les accions: index, create, show, edit, etc.)
    Route::resource('llistes', LlistaController::class);

    // 1. Ruta per mostrar el formulari de compartició (GET)
    Route::get('llistes/{id}/share', [LlistaController::class, 'share'])
        ->name('llistes.share');

    // 2. Ruta per processar els canvis de compartició (POST)
    Route::post('llistes/{id}/share', [LlistaController::class, 'processShare'])
        ->name('llistes.processShare');

    // Ruta que retorna un json amb els productes que coincideixen, l'usuari no hauria d'accedir
    // aquí, només és una ruta que retornarem al CREATE en format json.
    Route::get('/productes/search', [ProducteController::class, 'search'])->name('productes.search');

    // Cerca usuaris per nom o correu electrònic en format JSON.
    Route::get('/usuaris/search', [LlistaController::class, 'searchUsers'])->name('usuaris.search');
    });


// VISTES ADMIN
// Pàgina d'inici per l'administrador 
Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

Route::patch(
    '/llistes/{llista_id}/productes/{producte_id}/toggle',
    [ProducteController::class, 'toggle']
)->name('productes.toggle')->middleware('auth');

require __DIR__ . '/auth.php';