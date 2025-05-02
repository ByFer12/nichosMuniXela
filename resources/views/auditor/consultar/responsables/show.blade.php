@extends('./Layouts.landing') {{-- O el layout del auditor --}}
{{-- Asume que el controlador pasa un objeto $responsable con la información del responsable --}}
{{-- También asume que el controlador maneja la autorización y la carga de datos correctamente --}}
@section('title', 'Detalle Responsable: ' . $responsable->nombreCompleto) {{-- Asume accesor --}}

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalle Responsable: {{ $responsable->nombreCompleto }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.dashboard') }}">Consultar Datos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.responsables.index') }}">Responsables</a></li>
        <li class="breadcrumb-item active">Detalle #{{ $responsable->id }}</li>
    </ol>
    <div class="row">
        {{-- Columna Información Personal y Contacto --}}
        <div class="col-lg-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><i class="fas fa-address-book me-1"></i> Información Personal y Contacto</div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt><dd class="col-sm-8">{{ $responsable->id }}</dd>
                        <dt class="col-sm-4">Nombres:</dt><dd class="col-sm-8">{{ $responsable->nombres }}</dd>
                        <dt class="col-sm-4">Apellidos:</dt><dd class="col-sm-8">{{ $responsable->apellidos }}</dd>
                        <dt class="col-sm-4">DPI:</dt><dd class="col-sm-8">{{ $responsable->dpi }}</dd>
                        <dt class="col-sm-4">Teléfono:</dt><dd class="col-sm-8">{{ $responsable->telefono ?? '-' }}</dd>
                        <dt class="col-sm-4">Email:</dt><dd class="col-sm-8">{{ $responsable->correo_electronico ?? '-' }}</dd>
                        <dt class="col-sm-4">Usuario Vinculado:</dt>
                        <dd class="col-sm-8">
                            @if($responsable->usuario)
                                ID: {{ $responsable->usuario->id }} ({{ $responsable->usuario->email }})
                                {{-- <a href="{{ route('auditor.consultar.usuarios.show', $responsable->usuario_id) }}" class="btn btn-link btn-sm p-0"><i class="fas fa-eye fa-xs"></i></a> --}}
                            @else
                                No vinculado
                            @endif
                        </dd>
                        <dt class="col-sm-4">Creado:</dt><dd class="col-sm-8">{{ $responsable->created_at ? $responsable->created_at->format('d/m/Y H:i') : '-' }}</dd>
                        <dt class="col-sm-4">Actualizado:</dt><dd class="col-sm-8">{{ $responsable->updated_at ? $responsable->updated_at->format('d/m/Y H:i') : '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Columna Dirección --}}
        <div class="col-lg-6">
            <div class="card mb-4 shadow-sm">
                 <div class="card-header"><i class="fas fa-map-marked-alt me-1"></i> Dirección Registrada</div>
                 <div class="card-body">
                     @if($responsable->direccion)
                        @php $direccion = $responsable->direccion; @endphp
                         <dl class="row">
                            <dt class="col-sm-4">Calle/Número:</dt><dd class="col-sm-8">{{ $direccion->calle_numero ?: '-' }}</dd>
                            <dt class="col-sm-4">Colonia/Barrio:</dt><dd class="col-sm-8">{{ $direccion->colonia_barrio ?: '-' }}</dd>
                            <dt class="col-sm-4">Código Postal:</dt><dd class="col-sm-8">{{ $direccion->codigo_postal ?: '-' }}</dd>
                            <dt class="col-sm-4">Municipio:</dt><dd class="col-sm-8">{{ $direccion->municipio->nombre ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Departamento:</dt><dd class="col-sm-8">{{ $direccion->municipio->departamento->nombre ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Referencia:</dt><dd class="col-sm-8">{{ $direccion->referencia_adicional ?: '-' }}</dd>
                            <dt class="col-sm-4">País:</dt><dd class="col-sm-8">{{ $direccion->pais }}</dd>
                         </dl>
                     @else
                         <p class="text-muted">No hay dirección registrada para este responsable.</p>
                     @endif
                </div>
            </div>
        </div>
    </div> {{-- Fin .row --}}

    {{-- Contratos Asociados --}}
     <div class="card mb-4 shadow-sm">
        <div class="card-header"><i class="fas fa-file-contract me-1"></i> Contratos Asociados a este Responsable</div>
        <div class="card-body">
             @if($responsable->contratos->isEmpty())
                 <p class="text-muted">Este responsable no tiene contratos asociados.</p>
             @else
             <div class="table-responsive">
                 <table class="table table-sm table-bordered">
                     <thead class="table-light">
                         <tr><th>ID Contrato</th><th>Nicho</th><th>Ocupante</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Acción</th></tr>
                     </thead>
                     <tbody>
                         @foreach($responsable->contratos as $contrato)
                         <tr>
                            <td>{{ $contrato->id }}</td>
                            <td>{{ $contrato->nicho->codigo ?? 'N/A' }}</td>
                            <td>{{ $contrato->ocupante->nombreCompleto ?? 'N/A' }}</td>
                            <td>{{ $contrato->fecha_inicio ? $contrato->fecha_inicio->format('d/m/y') : '-' }}</td>
                            <td>{{ $contrato->fecha_fin_original ? $contrato->fecha_fin_original->format('d/m/y') : '-' }}</td>
                             <td>@if($contrato->activo) <span class="badge bg-success">Activo</span> @else <span class="badge bg-secondary">Inactivo</span> @endif</td>
                            <td class="text-center">
                                <a href="{{ route('auditor.consultar.contratos.show', $contrato->id) }}" class="btn btn-outline-info btn-sm" title="Ver Contrato">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                         </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
             @endif
        </div>
    </div>


    <div class="text-center mt-3">
         <a href="{{ route('auditor.consultar.responsables.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Listado de Responsables
        </a>
    </div>

</div>
@endsection

@push('styles')
<style>
    dl dt { font-weight: 600; color: #555; padding-right: 5px;}
    dl dd { margin-bottom: 0.5rem; }
</style>
@endpush