<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si el usuario está autenticado, redirigimos según su rol_id
                $user = Auth::guard($guard)->user(); // Obtenemos el usuario autenticado
                
                // Verificamos si el usuario existe y tiene rol_id (debería tenerlo si la autenticación fue exitosa)
                if ($user && property_exists($user, 'rol_id')) {
                     switch ($user->rol_id) {
                        case 1: // Administrador
                            return redirect()->intended(route('admin.dashboard'));
                        case 4: // Consulta
                            return redirect()->intended(route('consulta.dashboard'));
                        default:
                    
                            return redirect('/'); // Redirigir a inicio o a una página de error
                     }
                }

                // Si por alguna razón no se pudo obtener el usuario o el rol_id, redirigir a una ruta por defecto
                // Esto es un fallback, idealmente no debería ocurrir si la autenticación funciona bien
                // return redirect(RouteServiceProvider::HOME); // RouteServiceProvider::HOME por defecto es '/home'
            }
        }

        return $next($request);
    }
}
