<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Verifiquem que l'usuari està autenticat
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Verifiquem que l'usuari té com a nom 'admin'
        if (Auth::user()->name !== 'admin') {
            return redirect('/');
        }

        return $next($request);
    }
}
