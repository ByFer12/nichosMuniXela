<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Responsable; // Importar Modelos
use App\Models\Direccion;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminResponsableController extends Controller
{
    /**
     * Muestra la lista de responsables.
     */
    public function index(Request $request)
    {
        try {
            $query = Responsable::with(['direccion.municipio.departamento']) // Cargar dirección completa
                                ->orderBy('apellidos', 'asc')
                                ->orderBy('nombres', 'asc');

            // --- Filtros ---
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm){
                    $q->where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'like', "%{$searchTerm}%")
                      ->orWhere('dpi', 'like', "%{$searchTerm}%")
                      ->orWhere('correo_electronico', 'like', "%{$searchTerm}%");
                });
            }
            // Puedes añadir más filtros si es necesario (por depto/municipio de dirección, etc.)
            // --- Fin Filtros ---

            $responsables = $query->paginate(20)->withQueryString();

            return view('admin.responsables.index', compact('responsables'));

        } catch (\Exception $e) {
            Log::error("Error al listar responsables admin: " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'No se pudieron cargar los responsables.');
        }
    }

    // NO HAY MÉTODO create() NI store()

    /**
     * Muestra el formulario para editar un responsable existente.
     */
    public function edit(Responsable $responsable) // Route Model Binding
    {
        try {
            // Cargar dirección completa
             $responsable->load(['direccion.municipio.departamento']);

            $departamentos = Departamento::orderBy('nombre')->get();
            $municipios = collect(); // Vacío por defecto

             // Si el responsable tiene dirección y municipio, cargar los municipios de su depto
            if ($responsable->direccion && $responsable->direccion->municipio) {
                $municipios = Municipio::where('departamento_id', $responsable->direccion->municipio->departamento_id)
                                        ->orderBy('nombre')->get();
            }

            return view('admin.responsables.edit', compact('responsable', 'departamentos', 'municipios'));
        } catch (\Exception $e) {
            Log::error("Error al mostrar form editar responsable admin (ID: {$responsable->id}): " . $e->getMessage());
            return redirect()->route('admin.responsables.index')->with('error', 'No se pudo abrir el formulario de edición.');
        }
    }

    /**
     * Actualiza un responsable existente y su dirección.
     */
    public function update(Request $request, Responsable $responsable) // Route Model Binding
    {
        $validated = $request->validate([
            // Responsable
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'dpi' => ['required', 'string', 'max:20', Rule::unique('responsables', 'dpi')->ignore($responsable->id)], // Único ignorando actual
            'telefono' => 'nullable|string|max:25',
            'correo_electronico' => ['nullable', 'string', 'email', 'max:191', Rule::unique('responsables', 'correo_electronico')->ignore($responsable->id)], // Único ignorando actual

            // Dirección (similar a ocupante)
            'addr_calle_numero' => 'nullable|string|max:255',
            'addr_colonia_barrio' => 'nullable|string|max:150',
            'addr_codigo_postal' => 'nullable|string|max:10',
            'addr_departamento_id' => 'nullable|required_with:addr_municipio_id|integer|exists:departamentos,id',
            'addr_municipio_id' => 'nullable|required_with:addr_departamento_id|integer|exists:municipios,id',
            'addr_referencia' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $direccionId = $responsable->direccion_id; // Mantener actual por defecto

            // Actualizar o Crear Dirección si se proporcionan datos
            if ($request->filled('addr_calle_numero') || $request->filled('addr_colonia_barrio') || $request->filled('addr_departamento_id')) {
                 // Validar selects si se empezó a llenar dirección
                 $request->validate([
                      'addr_departamento_id' => 'required|integer|exists:departamentos,id',
                      'addr_municipio_id' => 'required|integer|exists:municipios,id',
                 ], [ /* ... mensajes ... */ ]);

                 $direccionData = [
                    'calle_numero' => $validated['addr_calle_numero'],
                    'colonia_barrio' => $validated['addr_colonia_barrio'],
                    'codigo_postal' => $validated['addr_codigo_postal'],
                    'municipio_id' => $validated['addr_municipio_id'],
                    'referencia_adicional' => $validated['addr_referencia'],
                    'pais' => 'Guatemala',
                 ];

                 if ($direccionId) { // Si ya tenía, actualizar
                     Direccion::find($direccionId)->update($direccionData);
                 } else { // Si no tenía, crear
                     $nuevaDireccion = Direccion::create($direccionData);
                     $direccionId = $nuevaDireccion->id;
                 }
            }
            // Nota: No estamos eliminando la dirección si se borran los campos, solo la desvincularemos si es necesario.

            // Actualizar Responsable
            $responsable->update([
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'dpi' => $validated['dpi'],
                'telefono' => $validated['telefono'],
                'correo_electronico' => $validated['correo_electronico'],
                'direccion_id' => $direccionId, // Asignar ID (actualizado o nuevo) o mantener el anterior si no se tocó la dirección
            ]);

            DB::commit();
            return redirect()->route('admin.responsables.index')->with('success', 'Responsable actualizado correctamente.');

        } catch (\Exception $e) {
             DB::rollBack();
            Log::error("Error al actualizar responsable admin (ID: {$responsable->id}): " . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->route('admin.responsables.edit', $responsable)
                             ->with('error', 'No se pudo actualizar al responsable: ' . $e->getMessage())
                             ->withInput();
        }
    }

    // NO HAY MÉTODO destroy()
}