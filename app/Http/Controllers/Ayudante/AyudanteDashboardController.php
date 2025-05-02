<?php

namespace App\Http\Controllers\Ayudante; // <-- Namespace correcto

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Quitar si no se usa
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AyudanteDashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Doble verificación (Middleware ya protege)
        if (!$user || !in_array($user->rol_id, [1, 2])) { // Permite Admin o Ayudante
             Auth::logout();
             return redirect('/login')->with('error', 'Acceso no autorizado.');
        }

        Log::info("Acceso al dashboard de ayudante por usuario: {$user->nombre} (ID: {$user->id})");

        return view('ayudante.dashboard', compact('user'));
    }
}