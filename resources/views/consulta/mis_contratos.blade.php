@extends('./Layouts.landing') {{-- Usa tu layout principal --}}

@section('title', 'Mis Contratos')

@section('content')
<div class="container mt-4 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Mis Contratos Asociados</h2>
        <a href="{{ route('consulta.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Portal
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if($contratos->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            No se encontraron contratos asociados a tu cuenta. Si crees que esto es un error, por favor contacta a la administración.
        </div>
    @else
        <div class="list-group">
            @foreach($contratos as $contrato)
                <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 shadow-sm border rounded">
                    <div class="row">
                        {{-- Columna Izquierda: Información del Nicho --}}
                        <div class="col-md-4 border-end-md mb-3 mb-md-0">
                            <h5 class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Nicho</h5>
                            <dl class="mb-0">
                                <dt>Código:</dt>
                                <dd>{{ $contrato->nicho->codigo ?? 'N/A' }}</dd>

                                <dt>Tipo:</dt>
                                {{-- Accede al nombre a través de la relación cargada --}}
                                <dd>{{ $contrato->nicho->tipoNicho->nombre ?? 'N/A' }}</dd>

                                <dt>Ubicación:</dt>
                                <dd>{{ $contrato->nicho->calle ?? 'S/C' }} y {{ $contrato->nicho->avenida ?? 'S/A' }}</dd>

                                <dt>Estado Actual:</dt>
                                <dd>
                                    <span class="badge bg-{{ $contrato->nicho->estadoNicho->nombre == 'Ocupado' ? 'warning' : ($contrato->nicho->estadoNicho->nombre == 'Disponible' ? 'success' : 'secondary') }}">
                                        {{ $contrato->nicho->estadoNicho->nombre ?? 'N/A' }}
                                    </span>
                                </dd>
                            </dl>
                        </div>

                        {{-- Columna Central: Información del Contrato --}}
                        <div class="col-md-4 border-end-md mb-3 mb-md-0">
                            <h5 class="mb-2"><i class="fas fa-file-contract me-2 text-success"></i>Contrato #{{ $contrato->id }}</h5>
                            <dl class="mb-0">
                                <dt>Estado:</dt>
                                <dd>
                                    <span class="badge bg-{{ $contrato->activo ? 'success' : 'danger' }}">
                                        {{ $contrato->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                    @if ($contrato->renovado)
                                        <span class="badge bg-info">Renovado</span>
                                    @endif
                                </dd>

                                <dt>Fechas:</dt>
                                <dd>Inicio: {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') ?? 'N/A' }}</dd>
                                <dd>Fin: {{ \Carbon\Carbon::parse($contrato->fecha_fin_original)->format('d/m/Y') ?? 'N/A' }}</dd>
                                <dd>Gracia: {{ \Carbon\Carbon::parse($contrato->fecha_fin_gracia)->format('d/m/Y') ?? 'N/A' }}</dd>

                                <dt>Costo Inicial:</dt>
                                <dd>Q {{ number_format($contrato->costo_inicial, 2) }}</dd>
                            </dl>
                        </div>

                        {{-- Columna Derecha: Información del Ocupante --}}
                        <div class="col-md-4">
                             <h5 class="mb-2"><i class="fas fa-user me-2 text-secondary"></i>Ocupante</h5>
                             <dl class="mb-0">
                                <dt>Nombre:</dt>
                                <dd>{{ $contrato->ocupante->nombres ?? 'N/A' }} {{ $contrato->ocupante->apellidos ?? '' }}</dd>

                                <dt>Fecha Fallecimiento:</dt>
                                <dd>{{ $contrato->ocupante->fecha_fallecimiento ? \Carbon\Carbon::parse($contrato->ocupante->fecha_fallecimiento)->format('d/m/Y') : 'N/A' }}</dd>

                                <dt>DPI:</dt>
                                <dd>{{ $contrato->ocupante->dpi ?? 'No registrado' }}</dd>
                            </dl>
                        </div>
                    </div>

                    {{-- (Opcional) Área de Acciones específicas para este contrato --}}
                    {{-- <div class="mt-3 pt-2 border-top">
                        <a href="#" class="btn btn-sm btn-outline-primary disabled">Solicitar Boleta para este contrato</a>
                        <a href="#" class="btn btn-sm btn-outline-warning disabled ms-2">Solicitar Exhumación</a>
                    </div> --}}
                </div>
            @endforeach
        </div>

        {{-- (Opcional) Paginación si esperas muchos contratos por responsable --}}
        {{-- {{ $contratos->links() }} --}}

    @endif

</div>
@endsection

@push('styles')
<style>
    /* Estilo para separar columnas en pantallas medianas o más grandes */
    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #dee2e6 !important;
        }
    }
    /* Ajustes menores a las listas de definición */
    dl dt {
        font-weight: 600;
        color: #555;
         float: left; /* Alinea término a la izquierda */
         width: 150px; /* Ancho fijo para término, ajusta según necesites */
         clear: left;
         margin-bottom: 0.3rem;
    }
    dl dd {
        margin-left: 160px; /* Espacio para el término + un poco más */
        margin-bottom: 0.3rem;
        word-wrap: break-word; /* Evita desbordamiento */
    }
    .list-group-item {
        overflow: hidden; /* Asegura que el float de dt no afecte al contenedor */
    }

</style>
@endpush