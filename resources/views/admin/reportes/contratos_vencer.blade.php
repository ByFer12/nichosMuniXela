@extends('./Layouts.landing')
@section('title', 'Reporte: Contratos por Vencer (Próximos ' . $diasLimite . ' días)')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Reporte: Contratos por Vencer</h1>
     <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.reportes.dashboard') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Contratos por Vencer</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
           <i class="fas fa-file-alt me-1"></i> Contratos Venciendo en los Próximos {{ $diasLimite }} Días (hasta {{ \Carbon\Carbon::today()->addDays($diasLimite)->format('d/m/Y') }})
           {{-- Aquí podrías añadir botones para exportar ESTA vista filtrada --}}
        </div>
        <div class="card-body">
             @if($contratos->isEmpty())
                 <div class="alert alert-info">No hay contratos activos que venzan en este período.</div>
             @else
             <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                     <thead class="table-light">
                        <tr>
                            <th>ID Contrato</th>
                            <th>Fecha Fin</th>
                            <th>Fecha Gracia</th>
                            <th>Nicho</th>
                            <th>Ocupante</th>
                            <th>Responsable</th>
                            <th>Tel. Resp.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contratos as $contrato)
                        <tr>
                            <td>{{ $contrato->id }}</td>
                            <td>{{ $contrato->fecha_fin_original->format('d/m/Y') }}</td>
                            <td>{{ $contrato->fecha_fin_gracia->format('d/m/Y') }}</td>
                            <td>{{ $contrato->nicho->codigo ?? 'N/A' }}</td>
                            <td>{{ $contrato->ocupante->nombreCompleto ?? 'N/A' }}</td>
                            <td>{{ $contrato->responsable->nombreCompleto ?? 'N/A' }}</td>
                            <td>{{ $contrato->responsable->telefono ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
             </div>
             @endif
        </div>
    </div>
</div>
@endsection