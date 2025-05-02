<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Para pluralizar nombres
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model; // Para type hinting dinámico
use Illuminate\Support\Facades\DB;
class AdminCatalogoController extends Controller
{
    // Mapeo de slugs de URL a información del modelo
    private $catalogos = [
        'tipos-nicho' => [
            'model' => \App\Models\CatTipoNicho::class,
            'title' => 'Tipos de Nicho',
            'related_table' => 'nichos', // Tabla donde se usa esta FK
            'related_column' => 'tipo_nicho_id' // Columna FK
        ],
        'estados-nicho' => [
            'model' => \App\Models\CatEstadoNicho::class,
            'title' => 'Estados de Nicho',
             'related_table' => 'nichos',
            'related_column' => 'estado_nicho_id'
        ],
        'tipos-genero' => [
            'model' => \App\Models\CatTipoGenero::class,
            'title' => 'Tipos de Género',
             'related_table' => 'ocupantes',
            'related_column' => 'tipo_genero_id'
        ],
        'estados-pago' => [
            'model' => \App\Models\CatEstadoPago::class,
            'title' => 'Estados de Pago',
             'related_table' => 'pagos',
            'related_column' => 'estado_pago_id'
        ],
        'estados-exhumacion' => [
            'model' => \App\Models\CatEstadoExhumacion::class,
            'title' => 'Estados de Exhumación',
             'related_table' => 'exhumaciones',
            'related_column' => 'estado_exhumacion_id'
        ],
        'destinos-restos' => [
            'model' => \App\Models\CatDestinoResto::class,
            'title' => 'Destinos de Restos',
             'related_table' => 'exhumaciones',
            'related_column' => 'destino_resto_id'
        ],
    ];

    /**
     * Helper para obtener la información del catálogo basado en el slug.
     * Aborta con 404 si el slug no es válido.
     */
    private function getCatalogoInfo(string $slug): array
    {
        if (!array_key_exists($slug, $this->catalogos)) {
            abort(404, "Catálogo no encontrado.");
        }
        // Añadir el slug al array para pasarlo fácilmente a las vistas
        $info = $this->catalogos[$slug];
        $info['slug'] = $slug;
        return $info;
    }

     /**
     * Muestra la página índice con la lista de catálogos gestionables.
     */
    public function catalogIndex()
    {
        // Simplemente pasamos la lista de catálogos (títulos y slugs) a la vista
        $listaCatalogos = collect($this->catalogos)->map(function ($item, $key) {
            return ['slug' => $key, 'title' => $item['title']];
        })->sortBy('title');

        return view('admin.catalogos.dashboard', compact('listaCatalogos'));
    }


    /**
     * Muestra la lista de items para un catálogo específico.
     */
    public function index(string $catalogoSlug)
    {
        $catalogoInfo = $this->getCatalogoInfo($catalogoSlug);
        $modelClass = $catalogoInfo['model'];

        try {
            $items = $modelClass::orderBy('nombre', 'asc')->paginate(20);
            return view('admin.catalogos.index', compact('items', 'catalogoInfo'));
        } catch (\Exception $e) {
             Log::error("Error al listar catalogo {$catalogoSlug} admin: " . $e->getMessage());
            return redirect()->route('admin.catalogos.dashboard')->with('error', "No se pudieron cargar los items de {$catalogoInfo['title']}.");
        }
    }

    /**
     * Muestra el formulario para crear un nuevo item en un catálogo.
     */
    public function create(string $catalogoSlug)
    {
        $catalogoInfo = $this->getCatalogoInfo($catalogoSlug);
        return view('admin.catalogos.create', compact('catalogoInfo'));
    }

    /**
     * Almacena un nuevo item en un catálogo.
     */
    public function store(Request $request, string $catalogoSlug)
    {
        $catalogoInfo = $this->getCatalogoInfo($catalogoSlug);
        /** @var Model $modelClass */
        $modelClass = $catalogoInfo['model'];
        $tableName = (new $modelClass)->getTable(); // Obtener nombre de tabla dinámicamente

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique($tableName, 'nombre')],
            // Podrías añadir 'descripcion' si la tuvieras
        ]);

        try {
            $modelClass::create($validated);
             return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('success', 'Item creado correctamente.');
        } catch (\Exception $e) {
             Log::error("Error al guardar item catalogo {$catalogoSlug} admin: " . $e->getMessage());
            return redirect()->route('admin.catalogos.create', $catalogoSlug)->with('error', 'No se pudo crear el item.')->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un item existente.
     */
    public function edit(string $catalogoSlug, int $id)
    {
        $catalogoInfo = $this->getCatalogoInfo($catalogoSlug);
        /** @var Model $modelClass */
        $modelClass = $catalogoInfo['model'];

        try {
            $item = $modelClass::findOrFail($id);
            return view('admin.catalogos.edit', compact('item', 'catalogoInfo'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('error', 'Item no encontrado.');
        } catch (\Exception $e) {
             Log::error("Error al mostrar form editar item catalogo {$catalogoSlug} admin (ID: {$id}): " . $e->getMessage());
             return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('error', 'No se pudo abrir el formulario de edición.');
        }
    }

    /**
     * Actualiza un item existente en un catálogo.
     */
    public function update(Request $request, string $catalogoSlug, int $id)
    {
        $catalogoInfo = $this->getCatalogoInfo($catalogoSlug);
         /** @var Model $modelClass */
        $modelClass = $catalogoInfo['model'];
        $tableName = (new $modelClass)->getTable();

         $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique($tableName, 'nombre')->ignore($id)],
        ]);

        try {
            $item = $modelClass::findOrFail($id);
            $item->update($validated);
            return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('success', 'Item actualizado correctamente.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('error', 'Item no encontrado.');
        } catch (\Exception $e) {
             Log::error("Error al actualizar item catalogo {$catalogoSlug} admin (ID: {$id}): " . $e->getMessage());
             return redirect()->route('admin.catalogos.edit', [$catalogoSlug, $id])->with('error', 'No se pudo actualizar el item.')->withInput();
        }
    }

    /**
     * Elimina un item de un catálogo (con verificación de uso).
     */
    public function destroy(string $catalogoSlug, int $id)
    {
        $catalogoInfo = $this->getCatalogoInfo($catalogoSlug);
        /** @var Model $modelClass */
        $modelClass = $catalogoInfo['model'];

        try {
            $item = $modelClass::findOrFail($id);

            // --- Verificación de Uso ---
            $relatedTable = $catalogoInfo['related_table'] ?? null;
            $relatedColumn = $catalogoInfo['related_column'] ?? null;
            $isInUse = false;
            if ($relatedTable && $relatedColumn) {
                $isInUse = DB::table($relatedTable)->where($relatedColumn, $id)->exists();
            }
            if ($isInUse) {
                 return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('error', "No se puede eliminar '{$item->nombre}' porque está siendo utilizado en la tabla '{$relatedTable}'.");
            }
            // --- Fin Verificación ---

            $itemName = $item->nombre; // Guardar nombre para mensaje
            $item->delete();

            return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('success', "Item '{$itemName}' eliminado correctamente.");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('error', 'Item no encontrado.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar item catalogo {$catalogoSlug} admin (ID: {$id}): " . $e->getMessage());
            return redirect()->route('admin.catalogos.index', $catalogoSlug)->with('error', 'No se pudo eliminar el item.');
        }
    }
}