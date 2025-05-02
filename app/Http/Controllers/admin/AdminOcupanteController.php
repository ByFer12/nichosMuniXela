<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ocupante;
use App\Models\Direccion;
use App\Models\Departamento;
use App\Models\Municipio;
// Ya no necesitas Localidad si la quitaste de Direccion
// use App\Models\Localidad;
use App\Models\CatTipoGenero;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminOcupanteController extends Controller
{
    /**
     * Muestra la lista de ocupantes.
     */
    public function index(Request $request)
    {
        try {
            $query = Ocupante::with(['tipoGenero', 'direccion.municipio.departamento']) 
                            ->orderBy('apellidos', 'asc')
                            ->orderBy('nombres', 'asc');

            // --- Filtros ---
             if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm){
                    $q->where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'like', "%{$searchTerm}%")
                      ->orWhere('dpi', 'like', "%{$searchTerm}%");
                });
            }
             if ($request->filled('genero')) {
                $query->where('tipo_genero_id', $request->input('genero'));
            }
             if ($request->filled('fallecimiento_desde')) {
                $query->where('fecha_fallecimiento', '>=', $request->input('fallecimiento_desde'));
            }
            if ($request->filled('fallecimiento_hasta')) {
                $query->where('fecha_fallecimiento', '<=', $request->input('fallecimiento_hasta'));
            }
            // --- Fin Filtros ---

            $ocupantes = $query->paginate(20)->withQueryString();
            $generos = CatTipoGenero::orderBy('nombre')->get(); // Para filtro

            return view('admin.ocupantes.index', compact('ocupantes', 'generos'));

        } catch (\Exception $e) {
            Log::error("Error al listar ocupantes admin: " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'No se pudieron cargar los ocupantes.');
        }
    }

    /**
     * Muestra el formulario para crear un nuevo ocupante.
     */
    public function create()
    {
        try {
            $generos = CatTipoGenero::orderBy('nombre')->pluck('nombre', 'id');
            $departamentos = Departamento::orderBy('nombre')->get();
            // No pasamos municipios/localidades, se cargan con JS
            return view('admin.ocupantes.create', compact('generos', 'departamentos'));
        } catch (\Exception $e) {
             Log::error("Error al mostrar form crear ocupante admin: " . $e->getMessage());
            return redirect()->route('admin.ocupantes.index')->with('error', 'No se pudo abrir el formulario de creación.');
        }
    }

    /**
     * Almacena un nuevo ocupante y su dirección.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Ocupante
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'dpi' => ['nullable', 'string', 'max:20', Rule::unique('ocupantes', 'dpi')], // Único si se proporciona
            'fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'fecha_fallecimiento' => 'required|date|before_or_equal:today',
            'tipo_genero_id' => 'nullable|integer|exists:cat_tipos_genero,id',
            'causa_muerte' => 'nullable|string|max:255',

            // Dirección (Opcional general, pero requiere selección si se ingresan detalles)
            'addr_calle_numero' => 'nullable|string|max:255',
            'addr_colonia_barrio' => 'nullable|string|max:150',
            'addr_codigo_postal' => 'nullable|string|max:10',
            'addr_departamento_id' => 'nullable|required_with:addr_municipio_id|integer|exists:departamentos,id',
            'addr_municipio_id' => 'nullable|required_with:addr_departamento_id|integer|exists:municipios,id',
            'addr_referencia' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $direccionId = null;

            // Crear dirección SOLO si se proporcionó al menos un campo de dirección relevante
            // O si se seleccionó depto/municipio (lo que implica que se quiere guardar dirección)
            if ($request->filled('addr_calle_numero') || $request->filled('addr_colonia_barrio') || $request->filled('addr_departamento_id')) {
                 // Validar que si se empezó a llenar la dirección, se completen los selects
                 $request->validate([
                      'addr_departamento_id' => 'required|integer|exists:departamentos,id',
                      'addr_municipio_id' => 'required|integer|exists:municipios,id',
                 ], [
                      'addr_departamento_id.required' => 'Si ingresas detalles de dirección, debes seleccionar Departamento.',
                      'addr_municipio_id.required' => 'Si ingresas detalles de dirección, debes seleccionar Municipio.',
                 ]);

                $direccion = Direccion::create([
                    'calle_numero' => $validated['addr_calle_numero'],
                    'colonia_barrio' => $validated['addr_colonia_barrio'],
                    'codigo_postal' => $validated['addr_codigo_postal'],
                    'municipio_id' => $validated['addr_municipio_id'], // Usar municipio_id
                    'referencia_adicional' => $validated['addr_referencia'],
                    'pais' => 'Guatemala',
                ]);
                $direccionId = $direccion->id;
            }

            // Crear Ocupante
            Ocupante::create([
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'dpi' => $validated['dpi'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'fecha_fallecimiento' => $validated['fecha_fallecimiento'],
                'tipo_genero_id' => $validated['tipo_genero_id'],
                'causa_muerte' => $validated['causa_muerte'],
                'direccion_id' => $direccionId, // Asignar ID de dirección o null
            ]);

            DB::commit();
            return redirect()->route('admin.ocupantes.index')->with('success', 'Ocupante registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
             Log::error("Error al guardar ocupante admin: " . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->route('admin.ocupantes.create')
                             ->with('error', 'No se pudo registrar al ocupante: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un ocupante existente.
     */
    public function edit(Ocupante $ocupante) // Route Model Binding
    {
        try {
            // Cargar relaciones necesarias, incluyendo la dirección completa
             $ocupante->load(['tipoGenero', 'direccion.municipio.departamento']);

            $generos = CatTipoGenero::orderBy('nombre')->pluck('nombre', 'id');
            $departamentos = Departamento::orderBy('nombre')->get();
            $municipios = collect(); // Vacío por defecto

             // Si el ocupante tiene dirección y municipio, cargar los municipios de su depto
            if ($ocupante->direccion && $ocupante->direccion->municipio) {
                $municipios = Municipio::where('departamento_id', $ocupante->direccion->municipio->departamento_id)
                                        ->orderBy('nombre')->get();
            }

            return view('admin.ocupantes.edit', compact('ocupante', 'generos', 'departamentos', 'municipios'));
        } catch (\Exception $e) {
            Log::error("Error al mostrar form editar ocupante admin (ID: {$ocupante->id}): " . $e->getMessage());
            return redirect()->route('admin.ocupantes.index')->with('error', 'No se pudo abrir el formulario de edición.');
        }
    }

    /**
     * Actualiza un ocupante existente y su dirección.
     */
    public function update(Request $request, Ocupante $ocupante) // Route Model Binding
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'dpi' => ['nullable', 'string', 'max:20', Rule::unique('ocupantes', 'dpi')->ignore($ocupante->id)], // Ignorar actual
            'fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'fecha_fallecimiento' => 'required|date|before_or_equal:today',
            'tipo_genero_id' => 'nullable|integer|exists:cat_tipos_genero,id',
            'causa_muerte' => 'nullable|string|max:255',

            // Dirección
            'addr_calle_numero' => 'nullable|string|max:255',
            'addr_colonia_barrio' => 'nullable|string|max:150',
            'addr_codigo_postal' => 'nullable|string|max:10',
            'addr_departamento_id' => 'nullable|required_with:addr_municipio_id|integer|exists:departamentos,id',
            'addr_municipio_id' => 'nullable|required_with:addr_departamento_id|integer|exists:municipios,id',
            'addr_referencia' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $direccionId = $ocupante->direccion_id; // Mantener ID actual por defecto

             // Actualizar o Crear Dirección si se proporcionan datos relevantes
             if ($request->filled('addr_calle_numero') || $request->filled('addr_colonia_barrio') || $request->filled('addr_departamento_id')) {
                 // Validar selects si se empezó a llenar dirección
                 $request->validate([
                      'addr_departamento_id' => 'required|integer|exists:departamentos,id',
                      'addr_municipio_id' => 'required|integer|exists:municipios,id',
                 ], [
                      'addr_departamento_id.required' => 'Si editas detalles de dirección, debes seleccionar Departamento.',
                      'addr_municipio_id.required' => 'Si editas detalles de dirección, debes seleccionar Municipio.',
                 ]);

                 $direccionData = [
                    'calle_numero' => $validated['addr_calle_numero'],
                    'colonia_barrio' => $validated['addr_colonia_barrio'],
                    'codigo_postal' => $validated['addr_codigo_postal'],
                    'municipio_id' => $validated['addr_municipio_id'],
                    'referencia_adicional' => $validated['addr_referencia'],
                    'pais' => 'Guatemala',
                 ];

                 if ($direccionId) { // Si ya tenía dirección, actualizarla
                     Direccion::find($direccionId)->update($direccionData);
                 } else { // Si no tenía, crearla
                     $nuevaDireccion = Direccion::create($direccionData);
                     $direccionId = $nuevaDireccion->id; // Obtener el nuevo ID
                 }
             } else {
                 // Si se borraron todos los campos de dirección, podríamos desvincularla (opcional)
                 // $direccionId = null;
             }

             // Actualizar Ocupante
             $ocupante->update([
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'dpi' => $validated['dpi'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'fecha_fallecimiento' => $validated['fecha_fallecimiento'],
                'tipo_genero_id' => $validated['tipo_genero_id'],
                'causa_muerte' => $validated['causa_muerte'],
                'direccion_id' => $direccionId, // Asignar ID actualizado o null
            ]);

            DB::commit();
            return redirect()->route('admin.ocupantes.index')->with('success', 'Ocupante actualizado correctamente.');

        } catch (\Exception $e) {
             DB::rollBack();
             Log::error("Error al actualizar ocupante admin (ID: {$ocupante->id}): " . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->route('admin.ocupantes.edit', $ocupante)
                             ->with('error', 'No se pudo actualizar al ocupante: ' . $e->getMessage())
                             ->withInput();
        }
    }
}