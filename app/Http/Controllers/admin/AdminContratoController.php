<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contrato;
use App\Models\Nicho;
use App\Models\Ocupante;
use App\Models\Responsable;
use App\Models\CatEstadoNicho;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Para cálculo de fechas

class AdminContratoController extends Controller
{
    /**
     * Muestra la lista de contratos.
     */
    public function index(Request $request)
    {
        try {
            $query = Contrato::with(['nicho', 'ocupante', 'responsable'])
                             ->orderBy('fecha_inicio', 'desc');

            // --- Filtros ---
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

            $contratos = $query->paginate(15)->withQueryString();

            return view('admin.contratos.index', compact('contratos'));

        } catch (\Exception $e) {
            Log::error("Error al listar contratos admin: " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'No se pudieron cargar los contratos.');
        }
    }

    /**
     * Muestra el formulario para crear un nuevo contrato.
     */
    public function create()
    {
         try {
             // 1. Buscar el ID del estado "Disponible"
             $estadoDisponible = CatEstadoNicho::where('nombre', 'Disponible')->first();
             if (!$estadoDisponible) {
                 throw new \Exception('Estado "Disponible" no encontrado en catálogo.');
             }

             // 2. Obtener Nichos Disponibles
             $nichosDisponibles = Nicho::where('estado_nicho_id', $estadoDisponible->id)
             ->orderBy('codigo')
             ->with('tipoNicho') // Carga la relación
             ->get();

             // 3. Obtener Ocupantes (potenciales o todos) y Responsables
             //    Considera limitar o usar búsqueda si son muchos
             $ocupantes = Ocupante::orderBy('apellidos')->orderBy('nombres')->get(['id', 'nombres', 'apellidos', 'dpi']);
             $responsables = Responsable::orderBy('apellidos')->orderBy('nombres')->get(['id', 'nombres', 'apellidos', 'dpi']);

             if ($nichosDisponibles->isEmpty()){
                  return redirect()->route('admin.contratos.index')->with('warning', 'No hay nichos disponibles para asignar en este momento.');
             }

            return view('admin.contratos.create', compact('nichosDisponibles', 'ocupantes', 'responsables'));

        } catch (\Exception $e) {
            Log::error("Error al mostrar form crear contrato admin: " . $e->getMessage());
            return redirect()->route('admin.contratos.index')->with('error', 'No se pudo abrir el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Almacena un nuevo contrato y actualiza estado del nicho.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nicho_id' => 'required|integer|exists:nichos,id',
            'ocupante_id' => 'required|integer|exists:ocupantes,id',
            'responsable_id' => 'required|integer|exists:responsables,id',
            'fecha_inicio' => 'required|date',
            'costo_inicial' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Re-verificar que el Nicho esté Disponible ANTES de crear
            $nicho = Nicho::findOrFail($validated['nicho_id']);
            $estadoDisponible = CatEstadoNicho::where('nombre', 'Disponible')->firstOrFail();
            $estadoOcupado = CatEstadoNicho::where('nombre', 'Ocupado')->firstOrFail();

            if ($nicho->estado_nicho_id != $estadoDisponible->id) {
                throw new \Exception("El nicho seleccionado (ID: {$nicho->id}) ya no está disponible.");
            }

            // 2. Calcular fechas
            $fechaInicio = Carbon::parse($validated['fecha_inicio']);
            $fechaFinOriginal = $fechaInicio->copy()->addYears(6);
            $fechaFinGracia = $fechaFinOriginal->copy()->addYear();

            // 3. Crear el Contrato
            Contrato::create([
                'nicho_id' => $validated['nicho_id'],
                'ocupante_id' => $validated['ocupante_id'],
                'responsable_id' => $validated['responsable_id'],
                'fecha_inicio' => $fechaInicio->toDateString(),
                'fecha_fin_original' => $fechaFinOriginal->toDateString(),
                'fecha_fin_gracia' => $fechaFinGracia->toDateString(),
                'costo_inicial' => $validated['costo_inicial'],
                'activo' => false,
                'renovado' => false,
                'contrato_anterior_id' => null, 
            ]);

            // 4. Actualizar Estado del Nicho a Ocupado
            $nicho->estado_nicho_id = $estadoOcupado->id;
            $nicho->save();

            DB::commit();
            return redirect()->route('admin.contratos.index')->with('success', 'Contrato creado y nicho actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al guardar contrato admin: " . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->route('admin.contratos.create')
                             ->with('error', 'No se pudo crear el contrato: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un contrato existente.
     */
    public function edit(Contrato $contrato) // Route Model Binding
    {
         try {
             // Cargar relaciones necesarias
             $contrato->load(['nicho', 'ocupante', 'responsable']);

             // Obtener listas para selects (podrían ser todos o filtrados)
             $nichos = Nicho::orderBy('codigo')->get(['id', 'codigo']);
             $ocupantes = Ocupante::orderBy('apellidos')->orderBy('nombres')->get(['id', 'nombres', 'apellidos', 'dpi']);
             $responsables = Responsable::orderBy('apellidos')->orderBy('nombres')->get(['id', 'nombres', 'apellidos', 'dpi']);
             

            return view('admin.contratos.edit', compact('contrato', 'nichos', 'ocupantes', 'responsables'));
        } catch (\Exception $e) {
             Log::error("Error al mostrar form editar contrato admin (ID: {$contrato->id}): " . $e->getMessage());
            return redirect()->route('admin.contratos.index')->with('error', 'No se pudo abrir el formulario de edición.');
        }
    }

    /**
     * Actualiza un contrato existente.
     */
    public function update(Request $request, Contrato $contrato) // Route Model Binding
    {
         // Validar SOLO los campos que permitimos editar
         $validated = $request->validate([
            // Generalmente no se debería cambiar nicho u ocupante una vez creado,
            // pero sí el responsable o las fechas/costos si hubo error.
            'responsable_id' => 'required|integer|exists:responsables,id',
            'fecha_inicio' => 'required|date',
            'costo_inicial' => 'required|numeric|min:0',
            'activo' => 'required|boolean', // Permitir marcar como inactivo
            // Añadir 'observaciones' si tuvieras ese campo
        ]);

         DB::beginTransaction();
         try {
             // Recalcular fechas FIN si cambia la fecha de INICIO
             $fechaInicio = Carbon::parse($validated['fecha_inicio']);
             $fechaFinOriginal = $fechaInicio->copy()->addYears(6);
             $fechaFinGracia = $fechaFinOriginal->copy()->addYear();

             $updateData = [
                'responsable_id' => $validated['responsable_id'],
                'fecha_inicio' => $fechaInicio->toDateString(),
                'fecha_fin_original' => $fechaFinOriginal->toDateString(),
                'fecha_fin_gracia' => $fechaFinGracia->toDateString(),
                'costo_inicial' => $validated['costo_inicial'],
                'activo' => $validated['activo'],
             ];

            // Lógica adicional si se desactiva el contrato: ¿liberar el nicho?
            // Esto depende de tus reglas de negocio. Podría hacerse aquí o en un proceso separado.
            // if (!$validated['activo'] && $contrato->activo) { // Si se está desactivando
            //     $estadoDisponible = CatEstadoNicho::where('nombre', 'Disponible')->firstOrFail();
            //     $nicho = Nicho::find($contrato->nicho_id);
            //     if ($nicho) {
            //         $nicho->estado_nicho_id = $estadoDisponible->id;
            //         $nicho->save();
            //     }
            // }

            $contrato->update($updateData);

            DB::commit();
            return redirect()->route('admin.contratos.index')->with('success', 'Contrato actualizado correctamente.');

         } catch (\Exception $e) {
             DB::rollBack();
            Log::error("Error al actualizar contrato admin (ID: {$contrato->id}): " . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->route('admin.contratos.edit', $contrato)
                             ->with('error', 'No se pudo actualizar el contrato: ' . $e->getMessage())
                             ->withInput();
        }
    }
}