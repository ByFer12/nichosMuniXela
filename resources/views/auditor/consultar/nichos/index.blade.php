@extends('./Layouts.landing') {{-- O el layout que use el Auditor --}}
@section('title', 'Consulta de Nichos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Consulta de Nichos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item active">Nichos</li>
    </ol>



    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros y Listado de Nichos (Solo Lectura)
            {{-- SIN botón de Crear --}}
        </div>
        <div class="card-body">
            {{-- Formulario de Filtros (Igual que en admin) --}}
            <form method="GET" action="{{ route('auditor.consultar.nichos.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 {{-- ... inputs/selects de filtro ... --}}
                 <div class="col-auto"> <input type="text" name="search_codigo" class="form-control form-control-sm" placeholder="Código..." value="{{ request('search_codigo') }}"> </div>
                  <div class="col-auto"> <select name="tipo_nicho_id" class="form-select form-select-sm"> <option value="">-- Tipo --</option> @foreach($tiposNicho as $tipo) <option value="{{ $tipo->id }}" {{ request('tipo_nicho_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option> @endforeach </select> </div>
                  <div class="col-auto"> <select name="estado_nicho_id" class="form-select form-select-sm"> <option value="">-- Estado --</option> @foreach($estadosNicho as $estado) <option value="{{ $estado->id }}" {{ request('estado_nicho_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option> @endforeach </select> </div>
                 <div class="col-auto"> <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button> <a href="{{ route('auditor.consultar.nichos.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a> </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Ubicación</th>
                            <th>Histórico</th>
                            <th class="text-center">Acciones</th> {{-- Cambiado a text-center --}}
                        </tr>
                    </thead>
                     <tbody>
                        @forelse($nichos as $nicho)
                        <tr>
                            <td>{{ $nicho->codigo }}</td>
                            <td>{{ $nicho->tipoNicho->nombre ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $nicho->estadoNicho->nombre == 'Ocupado' ? 'warning' : ($nicho->estadoNicho->nombre == 'Disponible' ? 'success' : 'secondary') }} text-dark">
                                    {{ $nicho->estadoNicho->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $nicho->calle ?? 'S/C' }} y {{ $nicho->avenida ?? 'S/A' }}</td>
                            <td>{{ $nicho->es_historico ? 'Sí' : 'No' }}</td>
                            <td class="text-center"> {{-- Centrado --}}
                                {{-- Botón para ver detalles (única acción) --}}
                                <a href="{{ route('auditor.consultar.nichos.show', $nicho) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr> <td colspan="6" class="text-center">No se encontraron nichos.</td> </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            @if ($nichos->hasPages()) <div class="mt-3">{{ $nichos->links() }}</div> @endif
        </div>
    </div>
</div>
@endsection