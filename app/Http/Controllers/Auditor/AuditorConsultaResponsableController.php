<?php

namespace App\Http\Controllers\Auditor; // Asegurar namespace correcto

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Responsable; // Importar modelos
use App\Models\Direccion;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB; // Para búsqueda CONCAT
use Illuminate\Support\Facades\Log;

class AuditorConsultaResponsableController extends Controller
{
    /**
     * Muestra la lista de responsables (solo lectura).
     */
    public function index(Request $request)
    {
        try {
            $query = Responsable::with(['direccion.municipio.departamento']) // Cargar dirección completa
                                ->orderBy('apellidos', 'asc')
                                ->orderBy('nombres', 'asc');

            // --- Filtros (Igual que en AdminResponsableController) ---
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm){
                    $q->where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'like', "%{$searchTerm}%")
                      ->orWhere('dpi', 'like', "%{$searchTerm}%")
                      ->orWhere('correo_electronico', 'like', "%{$searchTerm}%");
                });
            }
            // ... otros filtros si los necesitas ...

            $responsables = $query->paginate(20)->withQueryString(); // Define $responsables

            // Pasar a la vista específica del auditor
            return view('auditor.consultar.responsables.index', compact('responsables'));

        } catch (\Exception $e) {
            Log::error("Error auditor al listar responsables: " . $e->getMessage());
            return redirect()->route('auditor.consultar.dashboard')
                     ->with('error', 'No se pudieron cargar los responsables.');
        }
    }

    /**
     * Muestra los detalles de un responsable específico (solo lectura).
     */
    public function show(Responsable $responsable) // Route Model Binding
    {
        try {
            // Cargar todas las relaciones relevantes para auditoría
            $responsable->load([
                'direccion.municipio.departamento', // Dirección completa
                'usuario', // El usuario vinculado (si existe)
                'contratos' => function($q_cont) { // Contratos asociados
                    $q_cont->with(['nicho:id,codigo', 'ocupante:id,nombres,apellidos']) // Cargar info clave del contrato
                           ->orderBy('fecha_inicio', 'desc');
                }
            ]);

            return view('auditor.consultar.responsables.show', compact('responsable'));

        } catch (\Exception $e) {
            Log::error("Error auditor al ver responsable ID {$responsable->id}: " . $e->getMessage());
            return redirect()->route('auditor.consultar.responsables.index')
                     ->with('error', 'No se pudo cargar el detalle del responsable.');
        }
    }
}