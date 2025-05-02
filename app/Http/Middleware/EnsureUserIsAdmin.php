<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si el usuario está autenticado Y si su rol_id es 1 (Admin)
        // Asegúrate que tu modelo User tenga la relación 'rol' o acceso a 'rol_id'
        if (!Auth::check() || Auth::user()->rol_id !== 1) {
            // Puedes redirigir a donde quieras con un error
            // O simplemente abortar con 403 Prohibido
            // abort(403, 'Acceso no autorizado.');
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        return $next($request);
    }
}