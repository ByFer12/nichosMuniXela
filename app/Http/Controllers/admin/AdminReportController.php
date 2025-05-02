<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nicho;
use App\Models\Contrato;
use App\Models\Pago;
use App\Models\Exhumacion;
use App\Models\CatEstadoNicho;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
// Para Exportar
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PagosPendientesExport; // <-- Crear esta clase de exportación
use App\Exports\OcupacionExport;       // <-- Crear esta clase de exportación
use Barryvdh\DomPDF\Facade\Pdf;        // Para exportar a PDF

class AdminReportController extends Controller
{
    /**
     * Muestra el dashboard de reportes con estadísticas y gráficos.
     */
 
public function dashboard()
{
    try {
        // --- Estadísticas Rápidas ---
        $totalNichos = Nicho::count();
        $estadoOcupadoId = CatEstadoNicho::where('nombre', 'Ocupado')->value('id');
        $estadoDisponibleId = CatEstadoNicho::where('nombre', 'Disponible')->value('id');
        $nichosOcupados = $estadoOcupadoId ? Nicho::where('estado_nicho_id', $estadoOcupadoId)->count() : 0;
        $nichosDisponibles = $estadoDisponibleId ? Nicho::where('estado_nicho_id', $estadoDisponibleId)->count() : 0;
        $contratosActivos = Contrato::where('activo', true)->count();
        $fechaLimiteVencer = Carbon::today()->addDays(30);
        $contratosPorVencer = Contrato::where('activo', true)
                                     ->where('fecha_fin_original', '<=', $fechaLimiteVencer)
                                     ->where('fecha_fin_original', '>=', Carbon::today())
                                     ->count();
        $pagosPendientesCount = Pago::where('estado_pago_id', 1)->count(); // Asumiendo ID 1 = Pendiente

        // --- Datos para Gráficos ---
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $ingresosMensuales = Pago::where('estado_pago_id', 2)
            ->whereBetween('fecha_registro_pago', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(fecha_registro_pago, "%Y-%m") as mes, SUM(monto) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get()
            ->keyBy('mes'); // Clave: mes (ej: '2024-01')
        
        // Rellenar meses faltantes con 0
        $chartIngresosLabels = [];
        $chartIngresosData = [];
        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $startDate->copy()->addMonths($i)->format('Y-m');
            $chartIngresosLabels[] = $currentMonth;
            $chartIngresosData[] = $ingresosMensuales->has($currentMonth) ? $ingresosMensuales[$currentMonth]->total : 0;
        }
        // 1. Ocupación por Tipo de Nicho
        $ocupacionPorTipo = Nicho::join('cat_tipos_nicho', 'nichos.tipo_nicho_id', '=', 'cat_tipos_nicho.id')
                                 ->join('cat_estados_nicho', 'nichos.estado_nicho_id', '=', 'cat_estados_nicho.id')
                                 ->select('cat_tipos_nicho.nombre as tipo', 'cat_estados_nicho.nombre as estado', DB::raw('count(*) as total'))
                                 ->groupBy('tipo', 'estado') // Asegúrate que los nombres de columna coincidan con los alias
                                 ->get();

        // Formatear para Chart.js (Gráfico de Barras Apiladas)
        $chartOcupacionLabels = $ocupacionPorTipo->pluck('tipo')->unique()->values()->toArray();
        
        // Asegúrate de definir colores para cada estado
        $coloresEstados = [
            'Disponible' => '#28a745', // Verde
            'Ocupado' => '#ffc107',    // Amarillo
            'Reservado' => '#17a2b8',  // Azul
            'Mantenimiento' => '#6c757d', // Gris
            // Añade más estados según sea necesario
        ];
        
        // Formar datasets para el gráfico de barras apiladas
        $chartOcupacionDatasets = [];
        $estados = $ocupacionPorTipo->pluck('estado')->unique()->values();
        
        foreach($estados as $estado) {
            $data = [];
            foreach($chartOcupacionLabels as $tipo) {
                $item = $ocupacionPorTipo->where('tipo', $tipo)->where('estado', $estado)->first();
                $data[] = $item ? $item->total : 0;
            }
            
            // Usar el color definido para el estado, o un color por defecto
            $color = $coloresEstados[$estado] ?? '#000000';
            
            $chartOcupacionDatasets[] = [
                'label' => $estado,
                'data' => $data,
                'backgroundColor' => $color,
            ];
        }

        // 2. Ingresos Mensuales (Últimos 12 meses)
        $fechaInicio = Carbon::now()->subMonths(11)->startOfMonth();
        
        $ingresosMensuales = Pago::where('estado_pago_id', 2) // Asumiendo ID 2 = Pagado
                                ->where('fecha_registro_pago', '>=', $fechaInicio)
                                ->select(
                                    DB::raw('SUM(monto) as total'),
                                    DB::raw("DATE_FORMAT(fecha_registro_pago, '%Y-%m') as mes")
                                )
                                ->groupBy('mes')
                                ->orderBy('mes', 'asc')
                                ->get();

        // Crear un arreglo con todos los meses de los últimos 12 meses
        $mesesCompletos = [];
        $chartIngresosLabels = [];
        $chartIngresosData = [];
        
        // Generar los últimos 12 meses
        for ($i = 0; $i < 12; $i++) {
            $mes = Carbon::now()->subMonths(11 - $i)->format('Y-m');
            $mesesCompletos[$mes] = 0; // Inicializar con cero
            $chartIngresosLabels[] = $mes;
        }
        
        // Rellenar con datos reales donde existan
        foreach ($ingresosMensuales as $ingreso) {
            $mesesCompletos[$ingreso->mes] = (float)$ingreso->total;
        }
        
        // Convertir a array para el gráfico
        $chartIngresosData = array_values($mesesCompletos);

        // Log para depurar
        Log::info('Datos para gráficos:', [
            'ocupacionLabels' => $chartOcupacionLabels,
            'ocupacionDatasets' => $chartOcupacionDatasets,
            'ingresosLabels' => $chartIngresosLabels,
            'ingresosData' => $chartIngresosData
        ]);

        return view('admin.reportes.dashboard', compact(
            'totalNichos', 'nichosOcupados', 'nichosDisponibles', 'contratosActivos', 
            'contratosPorVencer', 'pagosPendientesCount',
            'chartOcupacionLabels', 'chartOcupacionDatasets',
            'chartIngresosLabels', 'chartIngresosData'
        ));

    } catch (\Exception $e) {
        Log::error("Error al cargar dashboard de reportes: " . $e->getMessage());
        return redirect()->route('admin.dashboard')->with('error', 'No se pudo cargar la sección de reportes. Error: ' . $e->getMessage());
    }
}

    /**
     * Muestra/Genera el reporte de contratos por vencer.
     */
    public function reporteContratosPorVencer(Request $request)
    {
        try {
            $diasLimite = $request->input('dias', 30); // Por defecto 30 días
            $fechaLimite = Carbon::today()->addDays($diasLimite);

            $contratos = Contrato::with(['nicho', 'ocupante', 'responsable'])
                                 ->where('activo', true)
                                 ->where('fecha_fin_original', '<=', $fechaLimite)
                                 ->where('fecha_fin_original', '>=', Carbon::today()) // Solo futuros
                                 ->orderBy('fecha_fin_original', 'asc')
                                 ->get();

            // Podrías pasar $contratos a una vista HTML para mostrarlos
             return view('admin.reportes.contratos_vencer', compact('contratos', 'diasLimite'));

            // O exportar directamente (ejemplo PDF)
            // $pdf = Pdf::loadView('pdf.reporte_contratos_vencer', compact('contratos', 'diasLimite'));
            // return $pdf->download("contratos_por_vencer_{$diasLimite}d.pdf");

        } catch (\Exception $e) {
             Log::error("Error generando reporte contratos por vencer: " . $e->getMessage());
            return back()->with('error', 'Error al generar el reporte.');
        }
    }

    /**
     * Exporta el reporte de pagos pendientes a PDF o Excel.
     */
    public function exportarPagosPendientes(Request $request, $format = 'excel')
    {
        try {
            $filename = "pagos_pendientes_" . now()->format('Ymd_His');
    
            if (strtolower($format) === 'pdf') {
                // Obtener datos para el PDF
                $pagos = Pago::with(['contrato.nicho', 'contrato.responsable'])
                            ->where('estado_pago_id', 1) // Asumiendo ID 1 = Pendiente
                            ->orderBy('fecha_vencimiento')
                            ->get();
                            
                // Verificar que tenemos datos
                if ($pagos->isEmpty()) {
                    return back()->with('info', 'No hay pagos pendientes para exportar.');
                }
                
                // Generar PDF
                $pdf = PDF::loadView('pdf.reporte_pagos_pendientes', compact('pagos'));
                return $pdf->download($filename . '.pdf');
            } else { 
                // Exportar a Excel
                return Excel::download(new PagosPendientesExport(), $filename . '.xlsx');
            }
        } catch (\Exception $e) {
            Log::error("Error exportando pagos pendientes: " . $e->getMessage());
            return back()->with('error', 'Error al exportar el reporte: ' . $e->getMessage());
        }
    }

     /**
     * Exporta el reporte de ocupación a PDF o Excel.
     */
    public function exportarOcupacion(Request $request, $format = 'excel')
    {
        try {
            $filename = "reporte_ocupacion_" . now()->format('Ymd_His');
    
            if (strtolower($format) === 'pdf') {
                // Obtener datos para el PDF
                $nichos = Nicho::with(['tipoNicho', 'estadoNicho'])
                            ->orderBy('codigo')
                            ->get();
                            
                // Añadir info de contratos activos donde existan
                foreach ($nichos as $nicho) {
                    // Buscar contrato activo para este nicho
                    $nicho->contratoActivo = Contrato::where('nicho_id', $nicho->id)
                                                   ->where('activo', true)
                                                   ->with('ocupante')
                                                   ->first();
                }
                
                // Generar PDF
                $pdf = PDF::loadView('pdf.reporte_ocupacion', compact('nichos'));
                return $pdf->download($filename . '.pdf');
            } else {
                // Exportar a Excel
                return Excel::download(new OcupacionExport(), $filename . '.xlsx');
            }
        } catch (\Exception $e) {
            Log::error("Error exportando reporte ocupación: " . $e->getMessage());
            return back()->with('error', 'Error al exportar el reporte: ' . $e->getMessage());
        }
    }

    // Puedes añadir más métodos para otros reportes (exhumaciones, ingresos detallados, etc.)
}