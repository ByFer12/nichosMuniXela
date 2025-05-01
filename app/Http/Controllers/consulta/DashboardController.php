<?php

namespace App\Http\Controllers\Consulta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Obtiene el usuario autenticado

        // Verificar si el usuario tiene el rol_id de Consulta (ej: 4)
        if ($user->rol_id !== 4) { // Ajusta el ID si es diferente
             // Si no es consulta, redirigir o mostrar un error
            return redirect('/')->with('error', 'No tienes permisos para acceder a esta área.');
        }

        // Si es consulta, cargar la vista del dashboard
        return view('consulta.dashboard'); // Asegúrate de que esta vista exista
    }
}
