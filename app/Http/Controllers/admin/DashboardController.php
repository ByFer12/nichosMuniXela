<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
 public function index()
    {
        $user = Auth::user(); // Obtiene el usuario autenticado (el objeto de DB::table si usas driver database)

        // Verificar si el usuario tiene el rol_id de Administrador (ej: 1)
        if ($user->rol_id !== 1) { // Ajusta el ID si es diferente
            // Si no es administrador, redirigir o mostrar un error
            return redirect('/')->with('error', 'No tienes permisos para acceder a esta área.');
        }

        // Si es administrador, cargar la vista del dashboard
        return view('admin.dashboard'); // Asegúrate de que esta vista exista
    }
}
