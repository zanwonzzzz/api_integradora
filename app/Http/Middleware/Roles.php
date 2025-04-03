<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        //dd($user->cuenta_activa, $user->cuenta_activa_Admin);


        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        if ($user->cuenta_activa==0 || $user->cuenta_activa_Admin == 0) {
            return response()->json(['error' => 'Tu cuenta ha sido desactivada por un administrador'], 403);
        }

        return $next($request);
    }
}
