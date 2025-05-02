<?php

namespace App\Http\Controllers\Auditor; // <-- Namespace correcto

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Quitar si no se usa
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditorDashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Doble verificación (Middleware ya protege, pero es buena práctica)
        if (!$user || $user->rol_id !== 3) { // <-- Asegurar ID 3
            Auth::logout();
            return redirect('/login')->with('error', 'Acceso no autorizado.');
        }

        Log::info("Acceso al dashboard de auditor por usuario: {$user->nombre} (ID: {$user->id})");

        // Pasar el usuario a la vista para el saludo, etc.
        return view('auditor.dashboard', compact('user')); // <-- Vista específica del auditor
    }
}