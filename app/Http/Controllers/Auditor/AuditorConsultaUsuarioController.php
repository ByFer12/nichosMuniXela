<?php

namespace App\Http\Controllers\Auditor; // Namespace Correcto

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Importar modelos
use App\Models\Rol;
use Illuminate\Support\Facades\Log;

class AuditorConsultaUsuarioController extends Controller
{
    /**
     * Muestra la lista de usuarios (solo lectura).
     */
    public function index(Request $request)
    {
        try {
             // Reutilizar lógica de filtrado de AdminUserController
            $query = User::with('rol')->orderBy('nombre');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }
             if ($request->filled('rol_id')) {
                $query->where('rol_id', $request->input('rol_id'));
            }
             if ($request->filled('activo')) {
                 $activoValue = $request->input('activo') === '1' ? 1 : 0;
                 $query->where('activo', $activoValue);
             }

            $users = $query->paginate(15)->withQueryString();
            $roles = Rol::orderBy('nombre')->get(); // Para el filtro

            // Pasar a la vista específica del auditor
            return view('auditor.consultar.usuarios.index', compact('users', 'roles'));

        } catch (\Exception $e) {
            Log::error("Error auditor al listar usuarios: " . $e->getMessage());
            return redirect()->route('auditor.consultar.dashboard')
                     ->with('error', 'No se pudieron cargar los usuarios.');
        }
    }

     /**
     * Muestra los detalles de un usuario específico (solo lectura).
     */
    public function show(User $user) // Route Model Binding
    {
        try {
            // Cargar relaciones relevantes
            $user->load(['rol', 'responsable']); // Cargar responsable si es consulta

            return view('auditor.consultar.usuarios.show', compact('user'));

        } catch (\Exception $e) {
            Log::error("Error auditor al ver usuario ID {$user->id}: " . $e->getMessage());
            return redirect()->route('auditor.consultar.usuarios.index')
                     ->with('error', 'No se pudo cargar el detalle del usuario.');
        }
    }
}