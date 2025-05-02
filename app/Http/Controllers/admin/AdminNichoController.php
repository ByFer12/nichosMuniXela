<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nicho; // Importar Modelo Nicho
use App\Models\CatTipoNicho; // Para select de tipo
use App\Models\CatEstadoNicho; // Para select de estado
use Illuminate\Validation\Rule; // Para unique código
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Para transacciones o buscar estado

class AdminNichoController extends Controller
{
    /**
     * Muestra la lista de nichos.
     */
    public function index(Request $request)
    {
        try {
            $query = Nicho::with(['tipoNicho', 'estadoNicho'])->orderBy('codigo');

            // --- Filtros (Ejemplos) ---
            if ($request->filled('search_codigo')) {
                $query->where('codigo', 'like', '%' . $request->input('search_codigo') . '%');
            }
            if ($request->filled('tipo_nicho_id')) {
                $query->where('tipo_nicho_id', $request->input('tipo_nicho_id'));
            }
            if ($request->filled('estado_nicho_id')) {
                $query->where('estado_nicho_id', $request->input('estado_nicho_id'));
            }
            if ($request->filled('calle')) {
                 $query->where('calle', 'like', '%' . $request->input('calle') . '%');
            }
             if ($request->filled('avenida')) {
                 $query->where('avenida', 'like', '%' . $request->input('avenida') . '%');
            }
            if ($request->filled('es_historico')) {
                 $query->where('es_historico', $request->input('es_historico') == '1');
            }
            // --- Fin Filtros ---

            $nichos = $query->paginate(20)->withQueryString();
            $tiposNicho = CatTipoNicho::orderBy('nombre')->get();
            $estadosNicho = CatEstadoNicho::orderBy('nombre')->get();

            return view('admin.nichos.index', compact('nichos', 'tiposNicho', 'estadosNicho'));

        } catch (\Exception $e) {
            Log::error("Error al listar nichos admin: " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'No se pudieron cargar los nichos.');
        }
    }

    /**
     * Muestra el formulario para crear un nuevo nicho.
     */
    public function create()
    {
         try {
            $tiposNicho = CatTipoNicho::orderBy('nombre')->pluck('nombre', 'id');
            // Opcional: Podrías pasar estados, pero al crear casi siempre será 'Disponible'
            // $estadosNicho = CatEstadoNicho::orderBy('nombre')->pluck('nombre', 'id');
            return view('admin.nichos.create', compact('tiposNicho'));
        } catch (\Exception $e) {
            Log::error("Error al mostrar form crear nicho admin: " . $e->getMessage());
            return redirect()->route('admin.nichos.index')->with('error', 'No se pudo abrir el formulario de creación.');
        }
    }

    /**
     * Almacena un nuevo nicho en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:nichos,codigo',
            'tipo_nicho_id' => 'required|integer|exists:cat_tipos_nicho,id',
            'calle' => 'nullable|string|max:100',
            'avenida' => 'nullable|string|max:100',
            'es_historico' => 'required|boolean', // Asegurar que llegue 0 o 1
            'observaciones' => 'nullable|string',
            // 'estado_nicho_id' NO se valida aquí, se asigna por defecto
        ]);

        try {
            // Buscar el ID del estado "Disponible" (asumiendo que existe)
            $estadoDisponible = CatEstadoNicho::where('nombre', 'Disponible')->first();
            if (!$estadoDisponible) {
                // ¡Importante! Manejar el caso si no existe el estado 'Disponible'
                return redirect()->back()->with('error', 'Estado "Disponible" no encontrado en catálogo. Contacta al desarrollador.')->withInput();
            }

            Nicho::create([
                'codigo' => $validated['codigo'],
                'tipo_nicho_id' => $validated['tipo_nicho_id'],
                'estado_nicho_id' => $estadoDisponible->id, // <-- Asignar estado Disponible
                'calle' => $validated['calle'],
                'avenida' => $validated['avenida'],
                'es_historico' => $validated['es_historico'],
                'observaciones' => $validated['observaciones'],
            ]);

            return redirect()->route('admin.nichos.index')->with('success', 'Nicho creado correctamente.');

        } catch (\Exception $e) {
             Log::error("Error al guardar nicho admin: " . $e->getMessage());
            return redirect()->route('admin.nichos.create')->with('error', 'No se pudo crear el nicho.')->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un nicho existente.
     */
    public function edit(Nicho $nicho) // Route Model Binding
    {
         try {
            $tiposNicho = CatTipoNicho::orderBy('nombre')->pluck('nombre', 'id');
            $estadosNicho = CatEstadoNicho::orderBy('nombre')->pluck('nombre', 'id');
            return view('admin.nichos.edit', compact('nicho', 'tiposNicho', 'estadosNicho'));
        } catch (\Exception $e) {
             Log::error("Error al mostrar form editar nicho admin (ID: {$nicho->id}): " . $e->getMessage());
            return redirect()->route('admin.nichos.index')->with('error', 'No se pudo abrir el formulario de edición.');
        }
    }

    /**
     * Actualiza un nicho existente en la base de datos.
     */
    public function update(Request $request, Nicho $nicho) // Route Model Binding
    {
         $validated = $request->validate([
            'codigo' => ['required','string','max:50', Rule::unique('nichos', 'codigo')->ignore($nicho->id)],
            'tipo_nicho_id' => 'required|integer|exists:cat_tipos_nicho,id',
            'estado_nicho_id' => 'required|integer|exists:cat_estados_nicho,id', // Permitir cambiar estado (con precaución)
            'calle' => 'nullable|string|max:100',
            'avenida' => 'nullable|string|max:100',
            'es_historico' => 'required|boolean',
            'observaciones' => 'nullable|string',
        ]);

         try {
            // Nota: Cambiar el estado aquí podría entrar en conflicto con
            // la lógica automática de contratos/exhumaciones. Usar con cuidado.
            $nicho->update($validated);
            return redirect()->route('admin.nichos.index')->with('success', 'Nicho actualizado correctamente.');
         } catch (\Exception $e) {
            Log::error("Error al actualizar nicho admin (ID: {$nicho->id}): " . $e->getMessage());
            return redirect()->route('admin.nichos.edit', $nicho)->with('error', 'No se pudo actualizar el nicho.')->withInput();
        }
    }

    // No implementamos destroy() porque los nichos físicos no se borran.
    // Podríamos implementar toggleStatus si hubiera un estado 'Deshabilitado' o 'En Reparación'.

}