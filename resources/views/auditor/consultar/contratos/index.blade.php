@extends('./Layouts.landing') {{-- O layout del auditor --}}
@section('title', 'Consulta de Contratos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Consulta de Contratos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
         <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.dashboard') }}">Consultar Datos</a></li>
        <li class="breadcrumb-item active">Contratos</li>
    </ol>

   

    <div class="card mb-4">
         <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros y Listado de Contratos (Solo Lectura)
             {{-- Sin botón Crear --}}
        </div>
         <div class="card-body">
             {{-- Formulario de Filtros (copiado/adaptado de admin) --}}
             <form method="GET" action="{{ route('auditor.consultar.contratos.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 <div class="col-auto"> <input type="text" name="search_nicho" class="form-control form-control-sm" placeholder="Código Nicho..." value="{{ request('search_nicho') }}"> </div>
                 <div class="col-auto"> <input type="text" name="search_ocupante" class="form-control form-control-sm" placeholder="Ocupante (Nombre/DPI)..." value="{{ request('search_ocupante') }}"> </div>
                 <div class="col-auto"> <input type="text" name="search_responsable" class="form-control form-control-sm" placeholder="Responsable (Nombre/DPI)..." value="{{ request('search_responsable') }}"> </div>
                 <div class="col-auto"> <select name="estado_contrato" class="form-select form-select-sm"> <option value="">-- Estado Contrato --</option> <option value="1" {{ request('estado_contrato') === '1' ? 'selected' : '' }}>Activo</option> <option value="0" {{ request('estado_contrato') === '0' ? 'selected' : '' }}>Inactivo</option> </select> </div>
                 <div class="col-auto"> <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button> <a href="{{ route('auditor.consultar.contratos.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a> </div>
             </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nicho</th>
                            <th>Ocupante</th>
                            <th>Responsable</th>
                            <th>Fechas (Inicio/Fin/Gracia)</th>
                            <th>Costo</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                     <tbody>
                        @forelse($contratos as $contrato)
                        <tr>
                            <td>{{ $contrato->id }}</td>
                            <td>{{ $contrato->nicho->codigo ?? 'N/A' }}</td>
                            <td>{{ $contrato->ocupante->nombreCompleto ?? $contrato->ocupante->dpi ?? 'N/A' }}</td>
                            <td>{{ $contrato->responsable->nombreCompleto ?? $contrato->responsable->dpi ?? 'N/A' }}</td>
                            <td>
                                {{ $contrato->fecha_inicio ? $contrato->fecha_inicio->format('d/m/y') : '-' }} /
                                {{ $contrato->fecha_fin_original ? $contrato->fecha_fin_original->format('d/m/y') : '-' }} /
                                {{ $contrato->fecha_fin_gracia ? $contrato->fecha_fin_gracia->format('d/m/y') : '-' }}
                            </td>
                             <td class="text-end">Q {{ number_format($contrato->costo_inicial, 2) }}</td>
                            <td>
                                @if($contrato->activo) <span class="badge bg-success">Activo</span>
                                @else <span class="badge bg-danger">Inactivo</span> @endif
                                @if($contrato->renovado) <span class="badge bg-info">Renovado</span> @endif
                            </td>
                            <td class="text-center">
                                {{-- Botón Ver Detalles --}}
                                <a href="{{ route('auditor.consultar.contratos.show', $contrato) }}" class="btn btn-info btn-sm" title="Ver Detalles Completos">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr> <td colspan="8" class="text-center">No se encontraron contratos.</td> </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             {{-- Paginación --}}
            @if ($contratos->hasPages()) <div class="mt-3">{{ $contratos->links() }}</div> @endif
         </div>
    </div>
</div>
@endsection