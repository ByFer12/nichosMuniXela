<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nicho;
use App\Models\CatTipoNicho;
use App\Models\CatEstadoNicho;
use Illuminate\Support\Facades\Log;

class AuditorConsultaNichoController extends Controller
{
    /**
     * Muestra la lista de nichos (solo lectura).
     */
    public function index(Request $request) // <--- Asegúrate que Request esté importado si usas filtros
    {
        try {
            $query = Nicho::with(['tipoNicho', 'estadoNicho'])
                         ->orderBy('codigo');

            // --- Filtros (Copiar/Pegar de la implementación anterior si los tienes) ---
            if ($request->filled('search_codigo')) {
                $query->where('codigo', 'like', '%' . $request->input('search_codigo') . '%');
            }
            if ($request->filled('tipo_nicho_id')) {
                $query->where('tipo_nicho_id', $request->input('tipo_nicho_id'));
            }
            if ($request->filled('estado_nicho_id')) {
                $query->where('estado_nicho_id', $request->input('estado_nicho_id'));
            }
            // ... otros filtros ...

            // ***** LÍNEAS IMPORTANTES QUE DEFINEN LAS VARIABLES *****
            $nichos = $query->paginate(20)->withQueryString(); // Define $nichos
            $tiposNicho = CatTipoNicho::orderBy('nombre')->get();  // Define $tiposNicho
            $estadosNicho = CatEstadoNicho::orderBy('nombre')->get(); // Define $estadosNicho
            // ***** FIN LÍNEAS IMPORTANTES *****

            // Ahora las variables existen y compact() funcionará
            return view('auditor.consultar.nichos.index', compact('nichos', 'tiposNicho', 'estadosNicho'));

        } catch (\Exception $e) {
            Log::error("Error auditor al listar nichos: " . $e->getMessage());
            return redirect()->route('auditor.dashboard')->with('error', 'No se pudieron cargar los nichos.');
        }
    }

    /**
     * Muestra los detalles de un nicho específico (solo lectura).
     */
    public function show(Nicho $nicho)
    {
        try {
            $nicho->load([ /* ... relaciones ... */ ]);
            return view('auditor.consultar.nichos.show', compact('nicho'));
        } catch (\Exception $e) {
             Log::error("Error auditor al ver nicho ID {$nicho->id}: " . $e->getMessage());
             return redirect()->route('auditor.consultar.nichos.index')->with('error', 'No se pudo cargar el detalle del nicho.');
        }
    }
}