<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ocupante;
use App\Models\CatTipoGenero;
use Illuminate\Support\Facades\DB; // Para búsqueda CONCAT
use Illuminate\Support\Facades\Log;

class AuditorConsultaOcupanteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Ocupante::with(['tipoGenero', 'direccion.municipio.departamento'])
                            ->orderBy('apellidos', 'asc')
                            ->orderBy('nombres', 'asc');

            // --- Copiar/Adaptar Filtros del AdminOcupanteController ---
             if ($request->filled('search')) {
                 $searchTerm = $request->input('search');
                 $query->where(function($q) use ($searchTerm){
                     $q->where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'like', "%{$searchTerm}%")
                       ->orWhere('dpi', 'like', "%{$searchTerm}%");
                 });
             }
            if ($request->filled('genero')) { $query->where('tipo_genero_id', $request->input('genero')); }
            // ... otros filtros ...

            $ocupantes = $query->paginate(20)->withQueryString(); // Define $ocupantes
            $generos = CatTipoGenero::orderBy('nombre')->get();    // Define $generos

            // Pasar a la vista de auditor
            return view('auditor.consultar.ocupantes.index', compact('ocupantes', 'generos'));

        } catch (\Exception $e) {
            Log::error("Error auditor al listar ocupantes: " . $e->getMessage());
            return redirect()->route('auditor.consultar.dashboard')->with('error', 'No se pudieron cargar los ocupantes.');
        }
    }

    // También necesitarás implementar el método show() si tienes la ruta definida
     public function show(Ocupante $ocupante)
     {
         try {
             $ocupante->load(['tipoGenero', 'direccion.municipio.departamento', 'contratos.nicho']); // Cargar relaciones
             return view('auditor.consultar.ocupantes.show', compact('ocupante'));
         } catch (\Exception $e) {
             Log::error("Error auditor al ver ocupante ID {$ocupante->id}: " . $e->getMessage());
             return redirect()->route('auditor.consultar.ocupantes.index')->with('error', 'No se pudo cargar el detalle del ocupante.');
         }
     }
}