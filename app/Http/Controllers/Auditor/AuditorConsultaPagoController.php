<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago; // Importar modelos
use App\Models\Contrato;
use App\Models\CatEstadoPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Para mostrar comprobante

class AuditorConsultaPagoController extends Controller
{
    /**
     * Muestra la lista de pagos (solo lectura).
     */
    public function index(Request $request)
    {
        try {
            $query = Pago::with(['contrato.nicho', 'contrato.responsable', 'estadoPago', 'usuarioRegistrador'])
             ->orderBy('fecha_emision', 'desc');

            // --- Filtros (Ejemplos) ---
            if ($request->filled('search_boleta')) {
                $query->where('numero_boleta', 'like', '%' . $request->input('search_boleta') . '%');
            }
            if ($request->filled('contrato_id')) {
                $query->where('contrato_id', $request->input('contrato_id'));
            }
             if ($request->filled('estado_pago_id')) {
                $query->where('estado_pago_id', $request->input('estado_pago_id'));
            }
             if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_emision', '>=', $request->input('fecha_desde'));
            }
             if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_emision', '<=', $request->input('fecha_hasta'));
            }
            // --- Fin Filtros ---

            $pagos = $query->paginate(20)->withQueryString();
            $estadosPago = CatEstadoPago::orderBy('nombre')->get(); // Para filtro

            return view('auditor.consultar.pagos.index', compact('pagos', 'estadosPago'));

        } catch (\Exception $e) {
            Log::error("Error auditor al listar pagos: " . $e->getMessage());
            return redirect()->route('auditor.consultar.dashboard')
                     ->with('error', 'No se pudieron cargar los pagos.');
        }
    }

    /**
     * Muestra el comprobante de pago (si existe).
     */
    public function showComprobante(Pago $pago) // Route Model Binding
    {
        // Se podría añadir validación extra si el auditor solo puede ver pagos de ciertos periodos, etc.
        if (!$pago->comprobante_pago_ruta) {
            abort(404, 'No hay comprobante asociado a este pago.');
        }

        // Asumiendo que guardaste los comprobantes en storage/app/public/comprobantes
        // ¡Asegúrate que el enlace simbólico 'php artisan storage:link' esté creado!
        $path = storage_path('app/public/' . $pago->comprobante_pago_ruta);

        if (!Storage::disk('public')->exists($pago->comprobante_pago_ruta)) {
             Log::error("Archivo comprobante no encontrado en storage: {$pago->comprobante_pago_ruta} (Pago ID: {$pago->id})");
             abort(404, 'Archivo de comprobante no encontrado.');
        }

        try {
            // Devuelve el archivo directamente al navegador
            return response()->file($path);
            // O si quieres forzar descarga:
            // return response()->download($path, $pago->comprobante_pago_nombre_original ?? basename($path));
        } catch (\Exception $e) {
             Log::error("Error al servir comprobante (Pago ID: {$pago->id}): " . $e->getMessage());
             abort(500, 'Error al mostrar el comprobante.');
        }
    }

    // Implementa show() si necesitas una vista detallada separada para el pago
    // public function show(Pago $pago) { ... }
}