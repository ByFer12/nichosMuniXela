@extends('./Layouts.landing') {{-- O el layout del auditor --}}
@section('title', 'Reporte: Conciliación de Ingresos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Reporte: Conciliación de Ingresos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Conciliación</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-dollar-sign me-1"></i> Pagos Registrados como 'Pagada' entre {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} y {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            {{-- Botones Exportar --}}
            <div class="float-end">
                 <form action="{{ route('auditor.reportes.exportar.conciliacion', ['format' => 'excel', 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" method="GET" class="d-inline-block me-1"> <button type="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i> Excel</button> </form>
                 <form action="{{ route('auditor.reportes.exportar.conciliacion', ['format' => 'pdf', 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" method="GET" class="d-inline-block"> <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf me-1"></i> PDF</button> </form>
            </div>
        </div>
        <div class="card-body">
            @if($pagosPagados->isEmpty())
                 <div class="alert alert-info">No se encontraron pagos registrados en este período.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr><th>ID Pago</th><th>Boleta</th><th>Contrato</th><th>Responsable</th><th>Fecha Pago</th><th>Monto</th><th>Registró</th></tr>
                    </thead>
                    <tbody>
                        @foreach($pagosPagados as $pago)
                        <tr>
                            <td>{{ $pago->id }}</td>
                            <td>{{ $pago->numero_boleta }}</td>
                            <td>{{ $pago->contrato_id }}</td>
                            <td>{{ $pago->contrato->responsable->nombreCompleto ?? 'N/A' }}</td>
                            <td>{{ $pago->fecha_registro_pago ? $pago->fecha_registro_pago->format('d/m/Y H:i') : '-'}}</td>
                            <td class="text-end">Q {{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->registradorPago->nombre ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total Recaudado:</th>
                            <th class="text-end">Q {{ number_format($totalRecaudado, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection