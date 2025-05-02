@extends('./Layouts.landing') {{-- Usa tu layout principal --}}

@section('title', 'Seguimiento de Mis Solicitudes')

@section('content')
<div class="container mt-4 mb-5">

    {{-- Encabezado de la página --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Seguimiento de Mis Solicitudes</h2>
        <a href="{{ route('consulta.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Portal
        </a>
    </div>

    {{-- Mostrar mensajes de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabla de Solicitudes --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Historial de Solicitudes</h5>
        </div>
        <div class="card-body p-0">
            @if($solicitudes->isEmpty())
                <div class="alert alert-info m-3 text-center">
                    <i class="fas fa-info-circle me-2"></i> Aún no has realizado ninguna solicitud.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Fecha Sol.</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Contrato / Nicho</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Observaciones Admin</th>
                                {{-- ***** NUEVA CABECERA ***** --}}
                                <th scope="col" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                                <tr>
                                    {{-- Fecha Solicitud --}}
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y H:i') }}</td>

                                    {{-- Tipo de Solicitud --}}
                                    <td>
                                        @if($solicitud->tipo_solicitud == 'boleta')
                                            <i class="fas fa-file-invoice-dollar text-success me-1" title="Solicitud de Boleta"></i> Boleta
                                        @elseif($solicitud->tipo_solicitud == 'exhumacion')
                                            <i class="fas fa-skull-crossbones text-warning me-1" title="Solicitud de Exhumación"></i> Exhumación
                                        @else
                                            <i class="fas fa-question-circle text-secondary me-1" title="Otro Tipo"></i> Otro
                                        @endif
                                    </td>

                                    {{-- Contrato / Nicho / Ocupante --}}
                                    <td>
                                        @if($solicitud->contrato)
                                            Contrato #{{ $solicitud->contrato->id }}
                                            @if($solicitud->contrato->nicho)
                                                <br><small class="text-muted">(Nicho: {{ $solicitud->contrato->nicho->codigo ?? 'S/C' }})</small>
                                            @endif
                                            @if($solicitud->tipo_solicitud == 'exhumacion' && $solicitud->contrato->relationLoaded('ocupante') && $solicitud->contrato->ocupante)
                                                 <br><small class="text-muted">Ocup: {{ $solicitud->contrato->ocupante->nombres ?? '' }} {{ $solicitud->contrato->ocupante->apellidos ?? '' }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        @php
                                            $badgeClass = 'bg-secondary'; // Default
                                            // *** AJUSTE: Usar 'aprobada' para exhumación si es el término que usaste en el controller ***
                                            if ($solicitud->estado == 'procesada' || $solicitud->estado == 'aprobada') $badgeClass = 'bg-success';
                                            else if ($solicitud->estado == 'rechazada') $badgeClass = 'bg-danger';
                                            else if ($solicitud->estado == 'en_proceso') $badgeClass = 'bg-info';
                                            else if ($solicitud->estado == 'cancelada') $badgeClass = 'bg-dark';
                                            else if ($solicitud->estado == 'pendiente') $badgeClass = 'bg-warning text-dark';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($solicitud->estado) }}</span>
                                    </td>

                                     {{-- Observaciones del Administrador --}}
                                    <td>
                                        {{ $solicitud->observaciones_admin ?? '-' }}
                                        @if($solicitud->fecha_procesamiento)
                                            <br><small class="text-muted">({{ \Carbon\Carbon::parse($solicitud->fecha_procesamiento)->format('d/m/Y') }})</small>
                                        @endif
                                    </td>

  
                                    <td class="text-center">
                                        {{-- Bloque PHP para determinar si hay un pago pendiente asociado al contrato --}}
                                        @php
                                            // Inicializa la variable a null por defecto
                                            $pagoPendiente = null;
                        
                                            // 1. Verifica si la solicitud tiene un contrato asociado Y
                                            // 2. si la relación 'pagos' de ESE contrato fue cargada (Eager Loading desde el controller)
                                            if ($solicitud->contrato && $solicitud->contrato->relationLoaded('pagos')) {
                        
                                                // 3. Busca el PRIMER pago dentro de la colección '$solicitud->contrato->pagos'
                                                //    Recordemos que el controlador YA FILTRÓ esta colección para que solo contenga
                                                //    pagos con estado_pago_id = 1 (Pendiente)
                                                $pagoPendiente = $solicitud->contrato->pagos->first();
                        
                                                // Alternativa (más explícita pero redundante si el controller filtró bien):
                                                // $pagoPendiente = $solicitud->contrato->pagos->firstWhere('estado_pago_id', 1); // Asume ID 1 = Pendiente
                                            }
                                        @endphp
                        
                                        {{-- Ahora, usa la variable $pagoPendiente en la condición --}}
                        
                                        {{-- Condición 1: Mostrar botón PDF --}}
                                        {{-- ¿Es tipo 'boleta'? Y ¿El estado de la *solicitud* es 'procesada'? Y ¿Encontramos un pago *pendiente* ($pagoPendiente no es null)? --}}
                                        @if($solicitud->tipo_solicitud == 'boleta' && $solicitud->estado == 'procesada' && $pagoPendiente)
                                            {{-- Si todo es sí, muestra el enlace para descargar el PDF de ESE pago pendiente --}}
                                           <a href="{{ route('consulta.boleta.pdf.download', ['pago' => $pagoPendiente->id]) }}"
                                               class="btn btn-danger btn-sm"
                                               title="Descargar Boleta PDF (Pago #{{ $pagoPendiente->id }})"
                                               target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                        
                                        {{-- Condición 2: Mostrar icono de Exhumación Aprobada --}}
                                         @elseif($solicitud->tipo_solicitud == 'exhumacion' && $solicitud->estado == 'aprobada')
                                             <i class="fas fa-check-circle text-success" title="Aprobada. Contactar administración para programar."></i>
                        
                                        {{-- Condición 3: Mostrar icono de Solicitud Rechazada --}}
                                         @elseif($solicitud->estado == 'rechazada')
                                             <i class="fas fa-times-circle text-danger" title="Rechazada. Ver observaciones."></i>
                        
                                        {{-- Condición 4: Mostrar icono de Solicitud Pendiente --}}
                                         @elseif($solicitud->estado == 'pendiente')
                                             <i class="fas fa-hourglass-half text-warning" title="Pendiente de revisión"></i>
                        
                                        {{-- Caso por defecto: No hay acción específica --}}
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
        </div> {{-- Fin card-body --}}

        {{-- Paginación --}}
        @if($solicitudes->hasPages())
            <div class="card-footer bg-light">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div> {{-- Fin card --}}

</div> {{-- Fin container --}}
@endsection

@push('styles')
{{-- Los estilos pueden permanecer iguales --}}
<style> /* ... */ </style>
@endpush

{{-- No se necesitan scripts adicionales para esto --}}