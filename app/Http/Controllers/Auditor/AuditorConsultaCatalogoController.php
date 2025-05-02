<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Aunque no se use para filtros aquí
use Illuminate\Support\Facades\Log;
use App\Models; // Importar namespace de modelos para acceso dinámico

class AuditorConsultaCatalogoController extends Controller
{
     // Reutilizar el mismo mapeo que en AdminCatalogoController
     private $catalogos = [
        'tipos-nicho' => ['model' => Models\CatTipoNicho::class, 'title' => 'Tipos de Nicho'],
        'estados-nicho' => ['model' => Models\CatEstadoNicho::class, 'title' => 'Estados de Nicho'],
        'tipos-genero' => ['model' => Models\CatTipoGenero::class, 'title' => 'Tipos de Género'],
        'estados-pago' => ['model' => Models\CatEstadoPago::class, 'title' => 'Estados de Pago'],
        'estados-exhumacion' => ['model' => Models\CatEstadoExhumacion::class, 'title' => 'Estados de Exhumación'],
        'destinos-restos' => ['model' => Models\CatDestinoResto::class, 'title' => 'Destinos de Restos'],
         // Podrías añadir roles si quieres que el auditor los vea
         // 'roles' => ['model' => Models\Rol::class, 'title' => 'Roles de Usuario'],
    ];

    private function getCatalogoInfo(string $slug): array {
        if (!array_key_exists($slug, $this->catalogos)) { abort(404, "Catálogo no encontrado."); }
        $info = $this->catalogos[$slug];
        $info['slug'] = $slug;
        return $info;
    }

    /**
     * Muestra la lista de items para un catálogo específico (solo lectura).
     */
    public function index(string $catalogoSlug)
    {
        $catalogoInfo = $this->getCatalogoInfo($catalogoSlug);
        $modelClass = $catalogoInfo['model'];

        try {
            // Obtener items, quizás con paginación si son muchos
            $items = $modelClass::orderBy('nombre', 'asc')->paginate(25); // o ->get() si son pocos
            return view('auditor.consultar.catalogos.index', compact('items', 'catalogoInfo'));
        } catch (\Exception $e) {
             Log::error("Error auditor al listar catalogo {$catalogoSlug}: " . $e->getMessage());
            // Redirigir al dashboard de consulta del auditor
            return redirect()->route('auditor.consultar.dashboard')->with('error', "No se pudieron cargar los items de {$catalogoInfo['title']}.");
        }
    }
}