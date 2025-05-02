<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuditor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si está logueado y si su rol_id es 3 (Auditor)
        if (!Auth::check() || Auth::user()->rol_id !== 3) { // <-- Cambiar a 3 (o el ID correcto)
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        return $next($request);
    }
}