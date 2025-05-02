<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AllowAdminOrAyudante
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user(); // Obtener el usuario autenticado

        // 2. Verificar si el rol es Admin (1) o Ayudante (2)
        if (in_array($user->rol_id, [1, 2])) {
            // Si es Admin o Ayudante, permite continuar con la solicitud
            return $next($request);
        }

        // 3. Si no es ninguno de los roles permitidos, denegar acceso
        // Puedes redirigir a su dashboard o mostrar un error 403 (Prohibido)
        // abort(403, 'Acción no autorizada.');
        // O redirigir al login o a una página de "acceso denegado"
        return redirect('/login')->with('error', 'No tienes permiso para acceder a esta sección.');
    }
}