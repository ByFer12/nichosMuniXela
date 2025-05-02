<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAyudante {
    public function handle(Request $request, Closure $next): Response {
        // Permitir si es Ayudante (ID 2) O si es Admin (ID 1)
        // El Admin puede acceder a las secciones del Ayudante (opcional, pero común)
        if (!Auth::check() || !in_array(Auth::user()->rol_id, [1, 2])) { // <-- PERMITE ADMIN (1) y AYUDANTE (2)
             return redirect('/')->with('error', 'Acceso no autorizado para esta sección.');
        }
        // Si necesitas que SOLO el Ayudante acceda, usa: Auth::user()->rol_id !== 2
        return $next($request);
    }
}