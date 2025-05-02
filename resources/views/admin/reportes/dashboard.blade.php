@extends('./Layouts.landing')

@section('title', 'Reportes y Estadísticas')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Reportes y Estadísticas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Reportes</li>
    </ol>



    {{-- 1. Estadísticas Rápidas --}}
    <div class="row mb-4">
        {{-- ... (cards de estadísticas como las tenías) ... --}}
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4"> <div class="card-body">Nichos Ocupados: {{ $nichosOcupados ?? 0 }} / {{ $totalNichos ?? 0 }}</div> </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4"> <div class="card-body">Nichos Disponibles: {{ $nichosDisponibles ?? 0 }}</div> </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark mb-4"> <div class="card-body">Contratos por Vencer (<30d): {{ $contratosPorVencer ?? 0 }}</div> </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4"> <div class="card-body">Pagos Pendientes: {{ $pagosPendientesCount ?? 0 }}</div> </div>
        </div>
    </div>

    {{-- 2. Gráficos --}}
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i> Ocupación por Tipo/Estado</div>
                {{-- *** ASEGÚRATE QUE EL ID ES CORRECTO *** --}}
                <div class="card-body"><canvas id="ocupacionChart" width="100%" height="50"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                 <div class="card-header"><i class="fas fa-chart-line me-1"></i> Ingresos Registrados (Últimos 12 Meses)</div>
                 {{-- *** ASEGÚRATE QUE EL ID ES CORRECTO *** --}}
                 <div class="card-body"><canvas id="ingresosChart" width="100%" height="50"></canvas></div>
            </div>
        </div>
    </div>

    {{-- 3. Generación de Reportes Específicos --}}
    <div class="card shadow-sm mb-4">
       {{-- ... (sección de generación de reportes como la tenías) ... --}}
        <div class="card-header"><i class="fas fa-file-download me-1"></i>Generar Reportes</div>
        <div class="card-body">
            <p>Seleccione el reporte que desea generar o exportar:</p>
            <ul>
                <li>
                    <strong>Contratos por Vencer:</strong>
                    <form action="{{ route('admin.reportes.contratosVencer') }}" method="GET" class="d-inline-flex align-items-center ms-2">
                        <label for="dias_vencer" class="me-1 small">Días:</label>
                        <input type="number" name="dias" id="dias_vencer" value="30" min="1" max="365" class="form-control form-control-sm me-2" style="width: 70px;">
                        <button type="submit" class="btn btn-outline-info btn-sm">Ver Reporte</button>
                    </form>
                </li>
                 <li class="mt-2">
                    <strong>Pagos Pendientes:</strong>
                     <a href="{{ route('admin.reportes.pagosPendientes.export', ['format' => 'excel']) }}" class="btn btn-outline-success btn-sm ms-2"><i class="fas fa-file-excel me-1"></i> Exportar Excel</a>
                     <a href="{{ route('admin.reportes.pagosPendientes.export', ['format' => 'pdf']) }}" class="btn btn-outline-danger btn-sm ms-2"><i class="fas fa-file-pdf me-1"></i> Exportar PDF</a>
                 </li>
                 <li class="mt-2">
                     <strong>Reporte de Ocupación General:</strong>
                     <a href="{{ route('admin.reportes.ocupacion.export', ['format' => 'excel']) }}" class="btn btn-outline-success btn-sm ms-2"><i class="fas fa-file-excel me-1"></i> Exportar Excel</a>
                     <a href="{{ route('admin.reportes.ocupacion.export', ['format' => 'pdf']) }}" class="btn btn-outline-danger btn-sm ms-2"><i class="fas fa-file-pdf me-1"></i> Exportar PDF</a>
                 </li>
                 <li class="mt-2">
                     <strong>Historial de Exhumaciones:</strong> <span class="text-muted">(Próximamente)</span>
                 </li>
            </ul>
        </div>
    </div>

</div>
@endsection

@push('styles')
{{-- Puedes añadir estilos si es necesario --}}
@endpush

@push('scripts')
{{-- *** PASO 1: INCLUIR CHART.JS (ANTES de tu script) *** --}}
{{-- Es crucial que esta línea esté ANTES del script que usa 'new Chart' --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

{{-- *** PASO 2: TU SCRIPT DE INICIALIZACIÓN *** --}}
<script>
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function () {

        // --- Depuración: Verificar datos recibidos ---
        console.log("Datos Ocupación Labels:", @json($chartOcupacionLabels ?? []));
        console.log("Datos Ocupación Datasets:", @json($chartOcupacionDatasets ?? []));
        console.log("Datos Ingresos Labels:", @json($chartIngresosLabels ?? []));
        console.log("Datos Ingresos Data:", @json($chartIngresosData ?? []));

        // --- Gráfico Ocupación ---
        const ctxOcupacion = document.getElementById('ocupacionChart').getContext('2d');
        // Verificar si el canvas existe antes de intentar dibujar
        if (ctxOcupacion) {
            try { // Añadir try-catch para capturar errores específicos del gráfico
                new Chart(ctxOcupacion, {
                    // *** Elegir UN tipo de gráfico ***
                    // type: 'doughnut', // Para Dona/Pie simple
                    type: 'bar',    // Para Barras Apiladas (si formateaste datasets así)

                    data: {
                        // Labels (Tipos de Nicho: Adulto, Niño)
                        labels: @json($chartOcupacionLabels ?? []),

                        // Datasets (Un dataset por cada Estado: Disponible, Ocupado, etc.)
                        // Asegúrate que $chartOcupacionDatasets sea un array de objetos
                        // donde cada objeto tiene 'label' (nombre del estado) y 'data' (array de totales por tipo)
                        datasets: @json($chartOcupacionDatasets ?? [])

                        /* --- Alternativa para Dona/Pie Simple (Ocupado vs Disponible) ---
                        labels: ['Ocupados', 'Disponibles', 'Otros'],
                        datasets: [{
                            label: 'Estado de Nichos',
                            data: [{{ $nichosOcupados ?? 0 }}, {{ $nichosDisponibles ?? 0 }}, {{ $totalNichos - $nichosOcupados - $nichosDisponibles }} ],
                            backgroundColor: ['#ffc107', '#28a745', '#6c757d'],
                            hoverOffset: 4
                        }]
                        */
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Distribución de Nichos por Tipo y Estado' }
                        },
                        // Opciones específicas para barras apiladas (si usas type: 'bar')
                         scales: {
                             x: { stacked: true }, // Apilar en eje X
                             y: { stacked: true, beginAtZero: true } // Apilar en eje Y
                         }
                    }
                });
                console.log("Gráfico Ocupación inicializado.");
             } catch (error) {
                console.error("Error al inicializar Gráfico Ocupación:", error);
             }
        } else {
            console.warn("Elemento canvas 'ocupacionChart' no encontrado.");
        }

        // --- Gráfico Ingresos ---
        const ctxIngresos = document.getElementById('ingresosChart').getContext('2d');

         // Verificar si el canvas existe
        if (ctxIngresos) {
             try { // Añadir try-catch
                new Chart(ctxIngresos, {
                    type: 'line',
                    data: {
                         // Labels (Meses: '2023-10', '2023-11', ...)
                         labels: @json($chartIngresosLabels ?? []),
                         datasets: [{
                            label: 'Ingresos Registrados (Q)',
                             // Data (Array de totales por mes)
                            data: @json($chartIngresosData ?? []),
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                         }]
                    },
                    options: {
                        responsive: true,
                         scales: { y: { beginAtZero: true } },
                        plugins: { legend: { display: false } } // Ocultar leyenda para gráfico de línea simple
                    }
                });
                 console.log("Gráfico Ingresos inicializado.");
             } catch (error) {
                 console.error("Error al inicializar Gráfico Ingresos:", error);
             }
        } else {
             console.warn("Elemento canvas 'ingresosChart' no encontrado.");
        }
    });
</script>
@endpush