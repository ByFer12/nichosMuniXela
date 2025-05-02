<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Contrato;
use App\Models\Pago;
use App\Models\Nicho;
use App\Models\Exhumacion; // Podríamos usarlo si aprobar crea registro
use App\Models\CatEstadoNicho;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminSolicitudController extends Controller
{
    /**
     * Muestra la lista de solicitudes pendientes.
     */
    public function index(Request $request)
    {
        try {
            $query = Solicitud::with(['contrato.nicho', 'contrato.ocupante', 'solicitante'])
                             ->whereIn('estado', ['pendiente', 'en_proceso']); // Mostrar pendientes y en proceso

            // --- Filtros (Ejemplos) ---
            if ($request->filled('tipo_solicitud')) {
                $query->where('tipo_solicitud', $request->input('tipo_solicitud'));
            }
            if ($request->filled('search_contrato')) {
                 $query->whereHas('contrato', function($q) use ($request) {
                     $q->where('id', $request->input('search_contrato'));
                 });
            }
            // ... otros filtros si necesitas ...

            $solicitudes = $query->orderBy('fecha_solicitud', 'asc')->paginate(15); // Más antiguas primero

            return view('admin.solicitudes.index', compact('solicitudes'));

        } catch (\Exception $e) {
            Log::error("Error al listar solicitudes admin: " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'No se pudieron cargar las solicitudes.');
        }
    }

    /**
     * Procesa una solicitud de BOLETA: Crea el registro de pago y actualiza la solicitud.
     */
    public function processBoleta(Request $request, Solicitud $solicitud)
    {
        // ... (Validaciones iniciales) ...

        DB::beginTransaction();
        try {
            $contrato = Contrato::findOrFail($solicitud->contrato_id);
            $numeroBoleta = 'BOL-' . time() . '-' . $contrato->id;
            $montoRenovacion = 600.00;
            $estadoPagoPendienteId = 1; // Asumiendo ID 1 = Pendiente (VERIFICAR ESTO)

            // 1. Crear el registro de PAGO (Boleta)
            $nuevoPago = Pago::create([ // Guardamos la instancia creada
                'contrato_id' => $contrato->id,
                'numero_boleta' => $numeroBoleta,
                'monto' => $montoRenovacion,
                'fecha_emision' => Carbon::today()->toDateString(),
                'fecha_vencimiento' => Carbon::today()->addDays(30)->toDateString(),
                'estado_pago_id' => $estadoPagoPendienteId, // <-- Estado Pendiente
                // ... (otros campos de pago null por ahora) ...
            ]);

            // 2. Actualizar la SOLICITUD (solo estado y datos de procesamiento)
            $solicitud->update([
                'estado' => 'procesada', // Indica que la solicitud se atendió
                'fecha_procesamiento' => now(),
                'usuario_procesador_id' => Auth::id(),
                'observaciones_admin' => $request->input('observaciones_admin') ?? "Boleta #{$nuevoPago->numero_boleta} generada.",
                // 'pago_id' => $nuevoPago->id, // <-- ELIMINADO
            ]);

            DB::commit();
            // ... (Notificación opcional) ...
            return redirect()->route('admin.solicitudes.index')->with('success', "Boleta #{$nuevoPago->numero_boleta} generada y solicitud #{$solicitud->id} marcada como procesada.");

        } catch (\Exception $e) {
            // ... (Manejo de error) ...
        }
    }
    /**
     * Aprueba una solicitud de EXHUMACIÓN (solo actualiza estado de solicitud).
     * La creación del registro en 'exhumaciones' y el cambio de estado del nicho
     * podrían ser pasos posteriores en otra interfaz.
     */
    public function approveExhumacion(Request $request, Solicitud $solicitud)
    {
         if ($solicitud->tipo_solicitud !== 'exhumacion' || $solicitud->estado !== 'pendiente') {
            return redirect()->route('admin.solicitudes.index')->with('error', 'Solicitud no válida o ya procesada.');
        }

        // Re-validar si el nicho es histórico (por si acaso)
        $contrato = Contrato::with('nicho')->find($solicitud->contrato_id);
        if ($contrato && $contrato->nicho && $contrato->nicho->es_historico) {
             return redirect()->route('admin.solicitudes.index')->with('error', "No se puede aprobar exhumación para nicho histórico (Solicitud #{$solicitud->id}).");
        }

        $request->validate(['observaciones_admin' => 'nullable|string|max:1000']);

        try {
            $solicitud->update([
                'estado' => 'aprobada', // Nuevo estado
                'fecha_procesamiento' => now(),
                'usuario_procesador_id' => Auth::id(),
                'observaciones_admin' => $request->input('observaciones_admin') ?? 'Solicitud de exhumación aprobada.',
            ]);

             // TODO: Opcional - Notificar al usuario consulta

            return redirect()->route('admin.solicitudes.index')->with('success', "Solicitud de exhumación #{$solicitud->id} aprobada.");

        } catch (\Exception $e) {
             Log::error("Error al aprobar solicitud exhumación admin (ID: {$solicitud->id}): " . $e->getMessage(), ['request' => $request->all()]);
             return redirect()->route('admin.solicitudes.index')->with('error', 'Error al aprobar la solicitud de exhumación.');
        }
    }

    /**
     * Rechaza cualquier tipo de solicitud pendiente.
     */
    public function reject(Request $request, Solicitud $solicitud)
    {
         if ($solicitud->estado !== 'pendiente') {
            return redirect()->route('admin.solicitudes.index')->with('error', 'La solicitud ya ha sido procesada.');
        }

        $request->validate([
            'observaciones_admin' => 'required|string|max:1000', // Motivo del rechazo es obligatorio
        ], ['observaciones_admin.required' => 'Debe indicar el motivo del rechazo.']);

        try {
             $solicitud->update([
                'estado' => 'rechazada',
                'fecha_procesamiento' => now(),
                'usuario_procesador_id' => Auth::id(),
                'observaciones_admin' => $request->input('observaciones_admin'),
            ]);

             // TODO: Opcional - Notificar al usuario consulta

             return redirect()->route('admin.solicitudes.index')->with('success', "Solicitud #{$solicitud->id} rechazada.");

        } catch (\Exception $e) {
             Log::error("Error al rechazar solicitud admin (ID: {$solicitud->id}): " . $e->getMessage(), ['request' => $request->all()]);
             return redirect()->route('admin.solicitudes.index')->with('error', 'Error al rechazar la solicitud.');
        }
    }
}