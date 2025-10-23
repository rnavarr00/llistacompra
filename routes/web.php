<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LlistaController;
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
});

// Rutes de llistes (totes les accions: index, create, show, edit, etc.)
Route::resource('llistes', App\Http\Controllers\LlistaController::class);

// 1. Ruta para mostrar el formulario (GET)
Route::get('/llistes/crear', [LlistaController::class, 'create'])->name('llistes.create');

// 2. Ruta para procesar y guardar el formulario (POST)
Route::post('/llistes', [LlistaController::class, 'store'])->name('llistes.store');


// VISTES ADMIN
// Pàgina d'inici per l'administrador 
Route::middleware([AdminMiddleware::class])->group(function () {
        Route::get('/admin', [AdminController::class, 'index']);
    });

require __DIR__.'/auth.php';
