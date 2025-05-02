@extends('./Layouts.landing') {{-- O el layout del auditor --}}
@section('title', 'Consulta de Pagos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Consulta de Pagos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.dashboard') }}">Consultar Datos</a></li>
        <li class="breadcrumb-item active">Pagos</li>
    </ol>


    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros y Listado de Pagos (Solo Lectura)
        </div>
        <div class="card-body">
            {{-- Formulario de Filtros --}}
            <form method="GET" action="{{ route('auditor.consultar.pagos.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 <div class="col-auto"> <input type="text" name="search_boleta" class="form-control form-control-sm" placeholder="No. Boleta..." value="{{ request('search_boleta') }}"> </div>
                 <div class="col-auto"> <input type="number" name="contrato_id" class="form-control form-control-sm" placeholder="ID Contrato..." value="{{ request('contrato_id') }}"> </div>
                 <div class="col-auto"> <select name="estado_pago_id" class="form-select form-select-sm"> <option value="">-- Estado Pago --</option> @foreach($estadosPago as $estado) <option value="{{ $estado->id }}" {{ request('estado_pago_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option> @endforeach </select> </div>
                 <div class="col-auto"> <label for="fecha_desde" class="col-form-label col-form-label-sm">Desde:</label> </div>
                 <div class="col-auto"> <input type="date" name="fecha_desde" id="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}"> </div>
                 <div class="col-auto"> <label for="fecha_hasta" class="col-form-label col-form-label-sm">Hasta:</label> </div>
                 <div class="col-auto"> <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}"> </div>
                 <div class="col-auto"> <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button> <a href="{{ route('auditor.consultar.pagos.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a> </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>No. Boleta</th>
                            <th>Contrato</th>
                            <th>Responsable</th>
                            <th>Monto</th>
                            <th>Emisión</th>
                            <th>Vencim.</th>
                            <th>Estado</th>
                            <th>F. Pago Reg.</th>
                            <th>Admin Reg.</th>
                            <th class="text-center">Comprob.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pagos as $pago)
                        <tr>
                            <td>{{ $pago->id }}</td>
                            <td>{{ $pago->numero_boleta }}</td>
                            <td>{{ $pago->contrato_id }} <a href="{{ route('auditor.consultar.contratos.show', $pago->contrato_id) }}" class="btn-link p-0"><i class="fas fa-eye fa-xs"></i></a></td>
                            <td>{{ $pago->contrato->responsable->nombreCompleto ?? 'N/A' }}</td>
                            <td class="text-end">Q {{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->fecha_emision ? $pago->fecha_emision->format('d/m/y') : '-' }}</td>
                            <td>{{ $pago->fecha_vencimiento ? $pago->fecha_vencimiento->format('d/m/y') : '-' }}</td>
                            <td><span class="badge bg-{{ $pago->estadoPago->nombre == 'Pagada' ? 'success' : ($pago->estadoPago->nombre == 'Pendiente' ? 'warning text-dark' : 'secondary') }}">{{ $pago->estadoPago->nombre ?? 'N/A' }}</span></td>
                            <td>{{ $pago->fecha_registro_pago ? $pago->fecha_registro_pago->format('d/m/y') : '-' }}</td>
                            <td>{{ $pago->registradorPago->nombre ?? '-' }}</td>
                            <td class="text-center">
                                @if($pago->comprobante_pago_ruta)
                                    <a href="{{ route('auditor.consultar.pagos.comprobante', $pago) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Ver Comprobante">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                @else - @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center">No se encontraron pagos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            @if ($pagos->hasPages()) <div class="mt-3">{{ $pagos->links() }}</div> @endif
        </div>
    </div>
</div>
@endsection