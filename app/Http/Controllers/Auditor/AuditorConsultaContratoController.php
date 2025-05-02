<?php

namespace App\Http\Controllers\Auditor; // Asegurar namespace correcto

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contrato; // Importar modelos necesarios
use App\Models\Nicho;
use App\Models\Ocupante;
use App\Models\Responsable;
use Illuminate\Support\Facades\DB; // Para búsquedas CONCAT
use Illuminate\Support\Facades\Log;

class AuditorConsultaContratoController extends Controller
{
    /**
     * Muestra la lista de contratos (solo lectura).
     */
    public function index(Request $request)
    {
        try {
            // Reutilizar lógica de filtrado del Admin Controller
            $query = Contrato::with(['nicho', 'ocupante', 'responsable'])
                             ->orderBy('fecha_inicio', 'desc');

            // --- Filtros (Igual que en AdminContratoController) ---
            if ($request->filled('search_nicho')) {
                $query->whereHas('nicho', function ($q) use ($request) {
                    $q->where('codigo', 'like', '%' . $request->input('search_nicho') . '%');
                });
            }
             if ($request->filled('search_ocupante')) {
                $searchTerm = $request->input('search_ocupante');
                $query->whereHas('ocupante', function ($q) use ($searchTerm) {
                     $q->where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'like', "%{$searchTerm}%")
                       ->orWhere('dpi', 'like', "%{$searchTerm}%");
                });
            }
             if ($request->filled('search_responsable')) {
                 $searchTerm = $request->input('search_responsable');
                 $query->whereHas('responsable', function ($q) use ($searchTerm) {
                     $q->where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'like', "%{$searchTerm}%")
                       ->orWhere('dpi', 'like', "%{$searchTerm}%");
                 });
            }
             if ($request->filled('estado_contrato')) {
                $activoValue = $request->input('estado_contrato') === '1' ? 1 : 0;
                $query->where('activo', $activoValue);
            }
            // --- Fin Filtros ---

            $contratos = $query->paginate(15)->withQueryString(); // Define $contratos

            // Pasar a la vista específica del auditor
            return view('auditor.consultar.contratos.index', compact('contratos'));

        } catch (\Exception $e) {
            Log::error("Error auditor al listar contratos: " . $e->getMessage());
            return redirect()->route('auditor.consultar.dashboard') // Redirige al dashboard de consulta
                     ->with('error', 'No se pudieron cargar los contratos.');
        }
    }

    /**
     * Muestra los detalles de un contrato específico (solo lectura).
     */
    public function show(Contrato $contrato) // Route Model Binding
    {
        try {
            // Cargar todas las relaciones relevantes para auditoría
            $contrato->load([
                'nicho.tipoNicho',
                'nicho.estadoNicho',
                'ocupante.tipoGenero',
                'ocupante.direccion.municipio.departamento', // Dirección completa ocupante
                'responsable.direccion.municipio.departamento', // Dirección completa responsable
                'pagos' => function($q) { // Cargar pagos asociados
                    $q->with('estadoPago', 'registradorPago')->orderBy('fecha_emision', 'desc'); // Cargar estado y quién registró
                },
                'contratoAnterior', // Para ver historial de renovación
                // Podrías cargar exhumaciones si estuvieran vinculadas directamente al contrato
            ]);

            return view('auditor.consultar.contratos.show', compact('contrato'));

        } catch (\Exception $e) {
            Log::error("Error auditor al ver contrato ID {$contrato->id}: " . $e->getMessage());
            return redirect()->route('auditor.consultar.contratos.index')
                     ->with('error', 'No se pudo cargar el detalle del contrato.');
        }
    }
}