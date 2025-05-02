<?php

namespace App\Http\Controllers\Auditor; // Namespace Correcto

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Contrato;
use App\Models\Nicho;
use App\Models\Exhumacion;
use App\Models\User; // Para reporte actividad (si se implementa)
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
// Para Exportar
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
// Necesitarás crear clases de exportación específicas para auditoría si son diferentes
// use App\Exports\Auditoria\ConciliacionExport;
// use App\Exports\Auditoria\VencidosSinAccionExport;
// use App\Exports\Auditoria\PagosAntiguosExport;

class AuditorReportController extends Controller
{
    /**
     * Muestra la página principal de reportes de auditoría.
     */
    public function index()
    {
        // Simplemente muestra la vista con los enlaces a los reportes
        return view('auditor.reportes.index');
    }

    // --- Métodos para MOSTRAR Reportes en HTML ---

    public function verConciliacionIngresos(Request $request)
    {
        // Lógica para obtener pagos 'Pagada' en un rango de fechas (ej. último mes)
        // y comparar con contratos iniciados/renovados (más complejo)
        // Por ahora, un ejemplo simple: Listar pagos pagados del último mes
        try {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->startOfMonth()->toDateString());
            $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());

            $pagosPagados = Pago::with('contrato.nicho', 'contrato.responsable', 'registradorPago')
                                ->where('estado_pago_id', 2) // Asume ID 2 = Pagada
                                ->whereBetween('fecha_registro_pago', [$fechaInicio, $fechaFin])
                                ->orderBy('fecha_registro_pago', 'desc')
                                ->get();

            $totalRecaudado = $pagosPagados->sum('monto');

            return view('auditor.reportes.ver_conciliacion', compact('pagosPagados', 'totalRecaudado', 'fechaInicio', 'fechaFin'));

        } catch (\Exception $e) {
            Log::error("Error auditor - verConciliacionIngresos: " . $e->getMessage());
            return redirect()->route('auditor.reportes.index')->with('error', 'Error al generar reporte de conciliación.');
        }
    }

    public function verContratosVencidosSinAccion(Request $request)
    {
         // Lógica para encontrar contratos cuyo 'fecha_fin_gracia' ya pasó
         // y 'activo' sigue siendo true (o no tienen registro de renovación/exhumación)
        try {
            $fechaLimite = Carbon::today(); // Contratos cuya gracia terminó antes de hoy

            $contratosVencidos = Contrato::with('nicho', 'ocupante', 'responsable')
                                        ->where('activo', true) // Que aún figuren activos
                                        ->where('fecha_fin_gracia', '<', $fechaLimite)
                                        // Opcional: Excluir los que SÍ tienen un pago de renovación reciente
                                        // ->whereDoesntHave('pagos', function($q) use ($fechaLimite){
                                        //      $q->where('estado_pago_id', 2) // Pagado
                                        //        ->where('fecha_registro_pago', '>=', $fechaLimite->copy()->subMonths(6)); // Pagado en últimos 6 meses
                                        // })
                                        ->orderBy('fecha_fin_gracia', 'asc')
                                        ->get();

            return view('auditor.reportes.ver_vencidos_sin_accion', compact('contratosVencidos', 'fechaLimite'));

        } catch (\Exception $e) {
             Log::error("Error auditor - verContratosVencidosSinAccion: " . $e->getMessage());
             return redirect()->route('auditor.reportes.index')->with('error', 'Error al generar reporte de contratos vencidos.');
        }
    }

     public function verPagosPendientesAntiguos(Request $request)
     {
         // Lógica para encontrar pagos 'Pendiente' con fecha de emisión muy antigua
         try {
            $mesesAntiguedad = $request->input('meses', 6); // Por defecto 6 meses
            $fechaLimite = Carbon::now()->subMonths($mesesAntiguedad);

            $pagosAntiguos = Pago::with('contrato.nicho', 'contrato.responsable')
                                ->where('estado_pago_id', 1) // Pendiente
                                ->where('fecha_emision', '<=', $fechaLimite)
                                ->orderBy('fecha_emision', 'asc')
                                ->get();

            return view('auditor.reportes.ver_pagos_antiguos', compact('pagosAntiguos', 'mesesAntiguedad'));

        } catch (\Exception $e) {
             Log::error("Error auditor - verPagosPendientesAntiguos: " . $e->getMessage());
             return redirect()->route('auditor.reportes.index')->with('error', 'Error al generar reporte de pagos antiguos.');
        }
     }

    // --- Métodos para EXPORTAR Reportes ---

    // NOTA: La lógica de obtención de datos aquí debería ser la misma
    // que en los métodos 'ver*', pero pasando los datos a una Clase de Exportación
    // o a una vista PDF. Aquí solo pongo placeholders.

    public function exportarConciliacionIngresos(Request $request, $format = 'excel') {
         try {
             $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->startOfMonth()->toDateString());
             $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
             $filename = "conciliacion_ingresos_{$fechaInicio}_a_{$fechaFin}_" . now()->format('Ymd');

             // Obtener datos (misma lógica que en 'ver*')
             $pagosPagados = Pago::with(/*...*/) ->where('estado_pago_id', 2) ->whereBetween('fecha_registro_pago', [$fechaInicio, $fechaFin])->get();
             $totalRecaudado = $pagosPagados->sum('monto');

             if (strtolower($format) === 'pdf') {
                 $pdf = Pdf::loadView('auditor.reportes.pdf.conciliacion', compact('pagosPagados', 'totalRecaudado', 'fechaInicio', 'fechaFin')); // Crear esta vista
                 return $pdf->download($filename . '.pdf');
             } else {
                 // return Excel::download(new ConciliacionExport($pagosPagados, $totalRecaudado, $fechaInicio, $fechaFin), $filename . '.xlsx'); // Crear esta clase Export
                 return redirect()->route('auditor.reportes.index')->with('info', 'Exportación Excel de Conciliación (Pendiente de implementar Clase Export).'); // Placeholder
             }
         } catch (\Exception $e) { /* ... manejo error ... */ }
    }

    public function exportarContratosVencidosSinAccion(Request $request, $format = 'excel') {
         try {
             $fechaLimite = Carbon::today();
             $filename = "contratos_vencidos_sin_accion_" . now()->format('Ymd');
             // Obtener datos (misma lógica que en 'ver*')
             $contratosVencidos = Contrato::with(/*...*/) ->where('activo', true)->where('fecha_fin_gracia', '<', $fechaLimite)->get();

             if (strtolower($format) === 'pdf') {
                 $pdf = Pdf::loadView('auditor.reportes.pdf.vencidos_sin_accion', compact('contratosVencidos', 'fechaLimite')); // Crear esta vista
                 return $pdf->download($filename . '.pdf');
             } else {
                 // return Excel::download(new VencidosSinAccionExport($contratosVencidos), $filename . '.xlsx'); // Crear esta clase Export
                  return redirect()->route('auditor.reportes.index')->with('info', 'Exportación Excel de Vencidos sin Acción (Pendiente de implementar Clase Export).'); // Placeholder
             }
         } catch (\Exception $e) { /* ... manejo error ... */ }
    }

    public function exportarPagosPendientesAntiguos(Request $request, $format = 'excel') {
        try {
             $mesesAntiguedad = $request->input('meses', 6);
             $fechaLimite = Carbon::now()->subMonths($mesesAntiguedad);
             $filename = "pagos_pendientes_{$mesesAntiguedad}meses_" . now()->format('Ymd');
             // Obtener datos (misma lógica que en 'ver*')
             $pagosAntiguos = Pago::with(/*...*/) ->where('estado_pago_id', 1)->where('fecha_emision', '<=', $fechaLimite)->get();

             if (strtolower($format) === 'pdf') {
                  $pdf = Pdf::loadView('auditor.reportes.pdf.pagos_antiguos', compact('pagosAntiguos', 'mesesAntiguedad')); // Crear esta vista
                 return $pdf->download($filename . '.pdf');
             } else {
                 // return Excel::download(new PagosAntiguosExport($pagosAntiguos), $filename . '.xlsx'); // Crear esta clase Export
                  return redirect()->route('auditor.reportes.index')->with('info', 'Exportación Excel de Pagos Antiguos (Pendiente de implementar Clase Export).'); // Placeholder
             }
         } catch (\Exception $e) { /* ... manejo error ... */ }
    }
}