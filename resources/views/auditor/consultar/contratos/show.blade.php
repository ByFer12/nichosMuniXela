@extends('./Layouts.landing') {{-- O el layout que use el Auditor --}}
@section('title', 'Detalle Contrato #' . $contrato->id)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalle Contrato #{{ $contrato->id }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
         <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.dashboard') }}">Consultar Datos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.contratos.index') }}">Contratos</a></li>
        <li class="breadcrumb-item active">Detalle</li>
    </ol>

   

    <div class="row">
        {{-- Información del Contrato --}}
        <div class="col-lg-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><i class="fas fa-file-contract me-1"></i> Información del Contrato</div>
                <div class="card-body">
                     <dl class="row">
                        <dt class="col-sm-4">ID Contrato:</dt><dd class="col-sm-8">{{ $contrato->id }}</dd>
                        <dt class="col-sm-4">Estado:</dt><dd class="col-sm-8">@if($contrato->activo) <span class="badge bg-success">Activo</span> @else <span class="badge bg-danger">Inactivo</span> @endif @if($contrato->renovado) <span class="badge bg-info">Renovado</span> @endif</dd>
                        <dt class="col-sm-4">Fecha Inicio:</dt><dd class="col-sm-8">{{ $contrato->fecha_inicio ? $contrato->fecha_inicio->format('d/m/Y') : '-' }}</dd>
                        <dt class="col-sm-4">Fecha Fin Original:</dt><dd class="col-sm-8">{{ $contrato->fecha_fin_original ? $contrato->fecha_fin_original->format('d/m/Y') : '-' }}</dd>
                        <dt class="col-sm-4">Fecha Fin Gracia:</dt><dd class="col-sm-8">{{ $contrato->fecha_fin_gracia ? $contrato->fecha_fin_gracia->format('d/m/Y') : '-' }}</dd>
                        <dt class="col-sm-4">Costo Inicial:</dt><dd class="col-sm-8">Q {{ number_format($contrato->costo_inicial, 2) }}</dd>
                        <dt class="col-sm-4">Contrato Anterior:</dt><dd class="col-sm-8">{{ $contrato->contrato_anterior_id ?? 'N/A' }} @if($contrato->contratoAnterior) <a href="{{ route('auditor.consultar.contratos.show', $contrato->contratoAnterior) }}" class="btn btn-link btn-sm p-0"><i class="fas fa-eye fa-xs"></i></a> @endif</dd>
                        <dt class="col-sm-4">Creado:</dt><dd class="col-sm-8">{{ $contrato->created_at ? $contrato->created_at->format('d/m/Y H:i') : '-' }}</dd>
                        <dt class="col-sm-4">Actualizado:</dt><dd class="col-sm-8">{{ $contrato->updated_at ? $contrato->updated_at->format('d/m/Y H:i') : '-' }}</dd>
                    </dl>
                </div>
            </div>
             {{-- Información del Nicho --}}
            <div class="card mb-4 shadow-sm">
                 <div class="card-header"><i class="fas fa-archive me-1"></i> Nicho Asociado</div>
                 <div class="card-body">
                     @if($contrato->nicho)
                     <dl class="row">
                        <dt class="col-sm-4">Código:</dt><dd class="col-sm-8">{{ $contrato->nicho->codigo }} <a href="{{ route('auditor.consultar.nichos.show', $contrato->nicho_id) }}" class="btn btn-link btn-sm p-0"><i class="fas fa-eye fa-xs"></i></a></dd>
                        <dt class="col-sm-4">Tipo:</dt><dd class="col-sm-8">{{ $contrato->nicho->tipoNicho->nombre ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Estado Actual:</dt><dd class="col-sm-8"><span class="badge bg-{{ $contrato->nicho->estadoNicho->nombre == 'Ocupado' ? 'warning' : ($contrato->nicho->estadoNicho->nombre == 'Disponible' ? 'success' : 'secondary') }} text-dark">{{ $contrato->nicho->estadoNicho->nombre ?? 'N/A' }}</span></dd>
                        <dt class="col-sm-4">Ubicación:</dt><dd class="col-sm-8">{{ $contrato->nicho->calle ?? 'S/C' }} y {{ $contrato->nicho->avenida ?? 'S/A' }}</dd>
                     </dl>
                     @else <p class="text-muted">No hay nicho asociado.</p> @endif
                </div>
            </div>
        </div>

        {{-- Información Ocupante y Responsable --}}
         <div class="col-lg-6">
             {{-- Ocupante --}}
             <div class="card mb-4 shadow-sm">
                 <div class="card-header"><i class="fas fa-user-clock me-1"></i> Ocupante</div>
                 <div class="card-body">
                      @if($contrato->ocupante)
                     <dl class="row">
                        <dt class="col-sm-4">Nombre:</dt><dd class="col-sm-8">{{ $contrato->ocupante->nombreCompleto }} <a href="{{ route('auditor.consultar.ocupantes.show', $contrato->ocupante_id) }}" class="btn btn-link btn-sm p-0"><i class="fas fa-eye fa-xs"></i></a></dd>
                        <dt class="col-sm-4">DPI:</dt><dd class="col-sm-8">{{ $contrato->ocupante->dpi ?? '-' }}</dd>
                        <dt class="col-sm-4">F. Fallec.:</dt><dd class="col-sm-8">{{ $contrato->ocupante->fecha_fallecimiento ? $contrato->ocupante->fecha_fallecimiento->format('d/m/Y') : '-' }}</dd>
                        {{-- Podrías mostrar más detalles del ocupante aquí --}}
                     </dl>
                     @else <p class="text-muted">No hay ocupante asociado.</p> @endif
                </div>
            </div>
             {{-- Responsable --}}
             <div class="card mb-4 shadow-sm">
                 <div class="card-header"><i class="fas fa-address-book me-1"></i> Responsable</div>
                 <div class="card-body">
                      @if($contrato->responsable)
                     <dl class="row">
                        <dt class="col-sm-4">Nombre:</dt><dd class="col-sm-8">{{ $contrato->responsable->nombreCompleto }} <a href="{{ route('auditor.consultar.responsables.show', $contrato->responsable_id) }}" class="btn btn-link btn-sm p-0"><i class="fas fa-eye fa-xs"></i></a></dd>
                        <dt class="col-sm-4">DPI:</dt><dd class="col-sm-8">{{ $contrato->responsable->dpi }}</dd>
                        <dt class="col-sm-4">Teléfono:</dt><dd class="col-sm-8">{{ $contrato->responsable->telefono ?? '-' }}</dd>
                        <dt class="col-sm-4">Email:</dt><dd class="col-sm-8">{{ $contrato->responsable->correo_electronico ?? '-' }}</dd>
                         {{-- Podrías mostrar la dirección resumida --}}
                        <dt class="col-sm-4">Dirección:</dt><dd class="col-sm-8">{{ $contrato->responsable->direccion->resumen ?? '-' }}</dd>
                     </dl>
                     @else <p class="text-muted">No hay responsable asociado.</p> @endif
                </div>
            </div>
         </div>
    </div> {{-- Fin .row principal --}}

    {{-- Historial de Pagos Asociados --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header"><i class="fas fa-history me-1"></i> Historial de Pagos Asociados a este Contrato</div>
        <div class="card-body">
             @if($contrato->pagos->isEmpty())
                 <p class="text-muted">No hay pagos registrados para este contrato.</p>
             @else
             <div class="table-responsive">
                 <table class="table table-sm table-bordered">
                     <thead class="table-light">
                         <tr><th>ID Pago</th><th>No. Boleta</th><th>Monto</th><th>Emisión</th><th>Vencimiento</th><th>Estado</th><th>F. Pago</th><th>Registró</th><th>Comprobante</th></tr>
                     </thead>
                     <tbody>
                         @foreach($contrato->pagos as $pago)
                         <tr>
                            <td>{{ $pago->id }}</td>
                            <td>{{ $pago->numero_boleta }}</td>
                            <td class="text-end">{{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->fecha_emision ? $pago->fecha_emision->format('d/m/y') : '-' }}</td>
                            <td>{{ $pago->fecha_vencimiento ? $pago->fecha_vencimiento->format('d/m/y') : '-' }}</td>
                            <td><span class="badge bg-{{ $pago->estadoPago->nombre == 'Pagada' ? 'success' : ($pago->estadoPago->nombre == 'Pendiente' ? 'warning text-dark' : 'secondary') }}">{{ $pago->estadoPago->nombre ?? 'N/A' }}</span></td>
                            <td>{{ $pago->fecha_registro_pago ? $pago->fecha_registro_pago->format('d/m/y') : '-' }}</td>
                            <td>{{ $pago->registradorPago->nombre ?? '-' }}</td>
                             <td class="text-center">
                                 @if($pago->comprobante_pago_ruta)
                                     <a href="{{ route('auditor.consultar.pagos.comprobante', $pago) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Ver Comprobante">
                                        <i class="fas fa-receipt"></i> Ver
                                     </a>
                                 @else
                                     -
                                 @endif
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
         <a href="{{ route('auditor.consultar.contratos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Listado de Contratos
        </a>
    </div>

</div>
@endsection

@push('styles')
<style>
    /* Estilos para las listas de definición */
    dl dt { font-weight: 600; color: #555; padding-right: 5px;}
    dl dd { margin-bottom: 0.5rem; }
</style>
@endpush