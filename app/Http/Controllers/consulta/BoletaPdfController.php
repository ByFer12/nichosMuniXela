<?php

namespace App\Http\Controllers\Consulta;

use App\Http\Controllers\Controller;
use App\Models\Pago; // Importar Pago
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Importar Facade PDF
use Illuminate\Support\Facades\Log;


class BoletaPdfController extends Controller
{
    public function download(Pago $pago) // Route Model Binding para Pago
    {
        try {
            // 1. Autorización: Verificar que el PAGO pertenece a un CONTRATO del USUARIO actual
            // Cargar relaciones necesarias para la verificación y el PDF
            $pago->load(['contrato.responsable', 'contrato.nicho', 'contrato.ocupante']);

            if (!$pago->contrato || !$pago->contrato->responsable || $pago->contrato->responsable_id !== Auth::user()->responsable_id) {
                 Log::warning("Intento no autorizado descarga boleta PDF. Usuario: ".Auth::id().", Pago ID: {$pago->id}");
                 abort(403, 'No autorizado para descargar esta boleta.');
            }

            // 2. Preparar datos para la vista PDF
            $data = [
                'pago' => $pago,
                'contrato' => $pago->contrato,
                'responsable' => $pago->contrato->responsable,
                'nicho' => $pago->contrato->nicho,
                'ocupante' => $pago->contrato->ocupante,
                // Puedes añadir más datos si necesitas (ej. datos municipales)
            ];

            // 3. Generar el PDF usando una vista Blade
            $pdf = Pdf::loadView('pdf.boleta_pago', $data); // Nombre de la vista Blade para el PDF

            // 4. Devolver el PDF para descarga
            // return $pdf->stream('boleta-'.$pago->numero_boleta.'.pdf'); // Ver en navegador
            return $pdf->download('boleta-'.$pago->numero_boleta.'.pdf'); // Forzar descarga

        } catch (\Exception $e) {
            Log::error("Error al generar PDF boleta (Pago ID: {$pago->id}): " . $e->getMessage());
            // Redirigir a la página anterior con un error genérico
             return back()->with('error', 'No se pudo generar el PDF de la boleta.');
        }
    }
}