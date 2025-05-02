@extends('./Layouts.landing') {{-- Asume tu layout de admin --}}
@section('title', 'Gestión de Nichos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Nichos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Nichos</li>
    </ol>


    <div class="card mb-4">
         <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-archive me-1"></i>Lista de Nichos</span>
            <a href="{{ route('admin.nichos.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Registrar Nicho
            </a>
        </div>
         <div class="card-body">
             {{-- Formulario de Filtros --}}
              {{-- Formulario de Filtros (Layout Horizontal) --}}
              <form method="GET" action="{{ route('admin.nichos.index') }}" class="row gx-2 gy-2 align-items-center mb-4"> {{-- gx-2 para espaciado horizontal, gy-2 para vertical si se envuelve --}}

                {{-- Input Código --}}
                <div class="col-auto">
                    <label for="search_codigo_filter" class="visually-hidden">Código</label> {{-- Label oculto para accesibilidad --}}
                    <input type="text" name="search_codigo" id="search_codigo_filter" class="form-control form-control-sm" placeholder="Código..." value="{{ request('search_codigo') }}">
                </div>

                {{-- Select Tipo --}}
                <div class="col-auto">
                    <label for="tipo_nicho_filter" class="visually-hidden">Tipo</label>
                    <select name="tipo_nicho_id" id="tipo_nicho_filter" class="form-select form-select-sm">
                        <option value="">-- Tipo --</option>
                        @foreach($tiposNicho as $tipo)
                           <option value="{{ $tipo->id }}" {{ request('tipo_nicho_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                 {{-- Select Estado --}}
                 <div class="col-auto">
                    <label for="estado_nicho_filter" class="visually-hidden">Estado</label>
                    <select name="estado_nicho_id" id="estado_nicho_filter" class="form-select form-select-sm">
                        <option value="">-- Estado --</option>
                        @foreach($estadosNicho as $estado)
                           <option value="{{ $estado->id }}" {{ request('estado_nicho_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                 {{-- Input Calle --}}
                 <div class="col-auto">
                    <label for="calle_filter" class="visually-hidden">Calle</label>
                    <input type="text" name="calle" id="calle_filter" class="form-control form-control-sm" placeholder="Calle..." value="{{ request('calle') }}">
                </div>

                 {{-- Input Avenida --}}
                 <div class="col-auto">
                    <label for="avenida_filter" class="visually-hidden">Avenida</label>
                    <input type="text" name="avenida" id="avenida_filter" class="form-control form-control-sm" placeholder="Avenida..." value="{{ request('avenida') }}">
                </div>

                 {{-- Select Histórico --}}
                 <div class="col-auto">
                     <label for="es_historico_filter" class="visually-hidden">Histórico</label>
                     <select name="es_historico" id="es_historico_filter" class="form-select form-select-sm">
                        <option value="">-- Histórico --</option>
                        <option value="1" {{ request('es_historico') === '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('es_historico') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
                </div>
                <div class="col-auto">
                     <a href="{{ route('admin.nichos.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar"><i class="fas fa-broom"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Ubicación</th>
                            <th>Histórico</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
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
                            <td>{{ Str::limit($nicho->observaciones, 50) }}</td> {{-- Limitar texto largo --}}
                            <td>
                                <a href="{{ route('admin.nichos.edit', $nicho) }}" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Aquí podrías añadir botón para ver historial o contratos asociados --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron nichos con los filtros aplicados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             {{-- Paginación --}}
            @if ($nichos->hasPages())
            <div class="mt-3">
                {{ $nichos->links() }}
            </div>
            @endif
         </div>
    </div>
</div>
@endsection