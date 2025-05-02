@extends('./Layouts.landing') {{-- O layout Auditor --}}
@section('title', 'Reportes de Auditoría')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Reportes de Auditoría</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item active">Reportes</li>
    </ol>


    <p>Seleccione el reporte que desea generar o exportar.</p>

    <div class="list-group">

        {{-- Conciliación de Ingresos --}}
        <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Conciliación de Ingresos</h5>
                <small class="text-muted">Pagos vs Contratos</small>
            </div>
            <p class="mb-1">Verifica los pagos marcados como 'Pagada' en un período.</p>
            <div class="mt-2">
                {{-- Formulario para ver HTML (con filtro de fechas opcional) --}}
                <form action="{{ route('auditor.reportes.ver.conciliacion') }}" method="GET" class="d-inline-block me-2">
                     {{-- Podrías añadir inputs date para fecha_inicio, fecha_fin --}}
                    <button type="submit" class="btn btn-info btn-sm"><i class="fas fa-eye me-1"></i> Ver Reporte</button>
                </form>
                 {{-- Botones Exportar --}}
                 <form action="{{ route('auditor.reportes.exportar.conciliacion', ['format' => 'excel']) }}" method="GET" class="d-inline-block me-1"> <button type="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i> Excel</button> </form>
                 <form action="{{ route('auditor.reportes.exportar.conciliacion', ['format' => 'pdf']) }}" method="GET" class="d-inline-block"> <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf me-1"></i> PDF</button> </form>
            </div>
        </div>

        {{-- Contratos Vencidos sin Acción --}}
         <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between"> <h5 class="mb-1">Contratos Vencidos sin Acción</h5> <small class="text-muted">Gracia expirada</small> </div>
            <p class="mb-1">Identifica contratos cuya fecha de gracia ya pasó pero siguen activos o sin acción registrada.</p>
             <div class="mt-2">
                 <a href="{{ route('auditor.reportes.ver.vencidosSinAccion') }}" class="btn btn-info btn-sm me-2"><i class="fas fa-eye me-1"></i> Ver Reporte</a>
                 <form action="{{ route('auditor.reportes.exportar.vencidosSinAccion', ['format' => 'excel']) }}" method="GET" class="d-inline-block me-1"> <button type="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i> Excel</button> </form>
                 <form action="{{ route('auditor.reportes.exportar.vencidosSinAccion', ['format' => 'pdf']) }}" method="GET" class="d-inline-block"> <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf me-1"></i> PDF</button> </form>
            </div>
        </div>

         {{-- Pagos Pendientes Antiguos --}}
         <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between"> <h5 class="mb-1">Pagos Pendientes Antiguos</h5> <small class="text-muted">Posibles morosos</small> </div>
            <p class="mb-1">Muestra boletas pendientes generadas hace más de X meses.</p>
             <div class="mt-2">
                 <form action="{{ route('auditor.reportes.ver.pagosAntiguos') }}" method="GET" class="d-inline-block me-2">
                     <label for="meses_pagos" class="me-1 small">Meses Antigüedad:</label>
                     <input type="number" name="meses" id="meses_pagos" value="6" min="1" max="60" class="form-control form-control-sm d-inline-block me-2" style="width: 70px;">
                     <button type="submit" class="btn btn-info btn-sm"><i class="fas fa-eye me-1"></i> Ver Reporte</button>
                 </form>
                 <form action="{{ route('auditor.reportes.exportar.pagosAntiguos', ['format' => 'excel']) }}" method="GET" class="d-inline-block me-1"> <input type="hidden" name="meses" value="6"> {{-- Pasar meses default --}} <button type="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i> Excel</button> </form>
                 <form action="{{ route('auditor.reportes.exportar.pagosAntiguos', ['format' => 'pdf']) }}" method="GET" class="d-inline-block"> <input type="hidden" name="meses" value="6"> <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf me-1"></i> PDF</button> </form>
            </div>
        </div>

        {{-- Añadir más items para otros reportes (Exhumaciones, Nichos Históricos, etc.) --}}

    </div>
</div>
@endsection