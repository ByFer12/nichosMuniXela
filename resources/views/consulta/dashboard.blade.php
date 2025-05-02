@extends('./Layouts.landing') 

@section('title', 'Portal de Consulta')

@section('content')
<div class="container-fluid mt-4 mb-5" style="max-width: 1200px;">


    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded">
        <div>
            <h2 class="h4 mb-0">Portal de Consulta</h2>
            @php
                $nombreUsuario = Auth::user()->nombre ?? Auth::user()->username ?? Auth::user()->email ?? 'Usuario';
            @endphp
            <p class="mb-0 text-muted">Bienvenido, {{ $nombreUsuario }}!</p>
        </div>
        <div>
   
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div style="max-width: 1140px; margin: 0 auto 1.5rem auto;">
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
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
         {{-- Mensaje específico si hubo error cargando contratos para el modal --}}
         @if(session('error_interno'))
            <div class="alert alert-danger" role="alert">
                {{ session('error_interno') }}
            </div>
        @endif
    </div>

    <div class="row g-4">

        {{-- Card: Consultar Nicho/Contrato --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-search me-2"></i>Consultar Nicho/Contrato</h5>
                    <p class="card-text text-muted">Busca y visualiza la información detallada del nicho y el contrato asociado a tu responsabilidad.</p>
                    <a href="{{ route('consulta.contratos.index') }}" class="btn btn-primary mt-auto">Ver mis Contratos</a>
                </div>
            </div>
        </div>

        {{-- Card: Solicitar Boleta de Pago --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-file-invoice-dollar me-2"></i>Solicitar Boleta de Pago</h5>
                    <p class="card-text text-muted">Genera una solicitud para recibir la boleta necesaria para la renovación o pago de tu contrato.</p>
                    {{-- Botón que abre el modal --}}
                    <button type="button" class="btn btn-success mt-auto" data-bs-toggle="modal" data-bs-target="#modalSolicitarBoleta">
                        Seleccionar Contrato
                    </button>
                </div>
            </div>
        </div>

{{-- Card: Solicitar Exhumación (Modificar botón) --}}
<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-skull-crossbones me-2"></i>Solicitar Exhumación</h5>
            <p class="card-text text-muted">Inicia el proceso de solicitud para la exhumación de los restos del ocupante asociado a tu contrato.</p>
            {{-- ***** CAMBIO AQUÍ: Botón que abre el nuevo modal ***** --}}
            <button type="button" class="btn btn-warning mt-auto" data-bs-toggle="modal" data-bs-target="#modalSolicitarExhumacion">
                Iniciar Solicitud
            </button>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>
{{-- Card: Seguimiento de Solicitudes --}}
<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-tasks me-2"></i>Seguimiento de Solicitudes</h5>
            <p class="card-text text-muted">Consulta el estado actual de tus solicitudes de boletas de pago o exhumaciones.</p>
            {{-- ***** CAMBIO AQUÍ: Enlace a la nueva ruta y quitar disabled ***** --}}
            <a href="{{ route('consulta.solicitudes.index') }}" class="btn btn-info mt-auto">Ver mis Solicitudes</a>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>

{{-- Card: Mi Perfil --}}
<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-user-edit me-2"></i>Mi Perfil</h5>
            <p class="card-text text-muted">Visualiza y actualiza tu información de contacto registrada en el sistema (teléfono, correo, dirección).</p>
            {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('perfil.edit') }}" class="btn btn-secondary mt-auto">Editar mi Información</a>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>


    </div> {{-- Fin de .row --}}

</div> 

<div class="modal fade" id="modalSolicitarBoleta" tabindex="-1" aria-labelledby="modalSolicitarBoletaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSolicitarBoletaLabel">Solicitar Boleta de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSolicitarBoletaModal" action="{{ route('consulta.boleta.request.modal') }}" method="POST" class="needs-validation" novalidate onsubmit="return confirm('¿Confirmas la solicitud de boleta para el contrato seleccionado?');">
                @csrf
                <div class="modal-body">
                    <p>Selecciona el contrato para el cual deseas solicitar una boleta para la renovacion o pago de primer contrato:</p>
                    <select class="form-select" name="contrato_id_modal" id="contratoSelectModal" required>
                        <option value="" selected disabled>-- Elige un contrato --</option>

                        {{-- ***** MODIFICADO: Iterar sobre $contratosParaSelect y mostrar estado ***** --}}
                        @if(isset($contratosParaSelect) && !$contratosParaSelect->isEmpty())
                            @foreach($contratosParaSelect as $contratoModal)
                                <option value="{{ $contratoModal->id }}">
                                    Contrato #{{ $contratoModal->id }}
                                    {{-- Indicador Visual si está INACTIVO --}}
                                    {!! !$contratoModal->activo ? '<span class="text-danger fw-bold"> (Inactivo)</span>' : '' !!}
                                    {{-- Mostrar Nicho si la relación cargó --}}
                                    @if($contratoModal->relationLoaded('nicho') && $contratoModal->nicho)
                                        (Nicho: {{ $contratoModal->nicho->codigo ?? 'S/C' }})
                                    @endif
                                     {{-- Mostrar Ocupante si la relación cargó --}}
                                     @if($contratoModal->relationLoaded('ocupante') && $contratoModal->ocupante)
                                         - {{ $contratoModal->ocupante->nombres ?? '' }} {{ $contratoModal->ocupante->apellidos ?? '' }}
                                     @endif
                                     - Fin: {{ \Carbon\Carbon::parse($contratoModal->fecha_fin_original)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No se encontraron contratos asociados a tu cuenta.</option>
                        @endif
                        {{-- ***** FIN MODIFICADO ***** --}}
                    </select>
                    <div class="invalid-feedback">
                        Por favor, selecciona un contrato de la lista.
                    </div>

                    <div class="mt-3">
                         <label for="observacionesModal" class="form-label">Observaciones (Opcional):</label>
                         <textarea class="form-control" id="observacionesModal" name="observaciones_usuario_modal" rows="2"></textarea>
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    {{-- Deshabilitar el botón si no hay contratos para seleccionar --}}
                    <button type="submit" class="btn btn-success" {{ !(isset($contratosParaSelect) && !$contratosParaSelect->isEmpty()) ? 'disabled' : '' }}>
                        Confirmar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- ================ NUEVO MODAL SOLICITAR EXHUMACIÓN =============== --}}
{{-- =============================================================== --}}
<div class="modal fade" id="modalSolicitarExhumacion" tabindex="-1" aria-labelledby="modalSolicitarExhumacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSolicitarExhumacionLabel">Solicitar Exhumación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSolicitarExhumacionModal" action="{{ route('consulta.exhumacion.request.modal') }}" method="POST" class="needs-validation" novalidate onsubmit="return confirm('ADVERTENCIA: La solicitud de exhumación es un proceso formal. ¿Estás seguro de que deseas continuar?');">
                @csrf
                <div class="modal-body">
                    <p>Selecciona el contrato/ocupante para el cual deseas iniciar la solicitud de exhumación:</p>
                    <div class="mb-3">
                        <label for="contratoSelectExhModal" class="form-label">Contrato Asociado <span class="text-danger">*</span></label>
                        <select class="form-select" name="contrato_id_exh_modal" id="contratoSelectExhModal" required>
                            <option value="" selected disabled>-- Elige un contrato --</option>
                            {{-- Poblar con TODOS los contratos (la validación backend filtrará históricos/sin ocupante etc) --}}
                            @if(isset($contratosParaSelect) && !$contratosParaSelect->isEmpty())
                                @foreach($contratosParaSelect as $contratoExhModal)
                                    {{-- Solo mostrar opción si el contrato tiene un ocupante asociado --}}
                                    @if($contratoExhModal->ocupante_id)
                                        <option value="{{ $contratoExhModal->id }}">
                                            Contrato #{{ $contratoExhModal->id }}
                                            {{-- Ocupante --}}
                                            @if($contratoExhModal->relationLoaded('ocupante') && $contratoExhModal->ocupante)
                                                (Ocupante: {{ $contratoExhModal->ocupante->nombres ?? '' }} {{ $contratoExhModal->ocupante->apellidos ?? '' }})
                                            @endif
                                            {{-- Nicho --}}
                                            @if($contratoExhModal->relationLoaded('nicho') && $contratoExhModal->nicho)
                                                - Nicho: {{ $contratoExhModal->nicho->codigo ?? 'S/C' }}
                                                {{-- Indicador Histórico --}}
                                                {!! $contratoExhModal->nicho->es_historico ? '<span class="text-danger fw-bold"> (Histórico)</span>' : '' !!}
                                            @endif
                                             {!! !$contratoExhModal->activo ? '<span class="text-danger fw-bold"> (Inactivo)</span>' : '' !!}
                                        </option>
                                    @endif
                                @endforeach
                            @else
                                <option value="" disabled>No se encontraron contratos asociados.</option>
                            @endif
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecciona el contrato correspondiente.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="motivoExhumacionModal" class="form-label">Motivo de la Solicitud <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivoExhumacionModal" name="motivo_exhumacion_modal" rows="3" required placeholder="Ej: Traslado a otro cementerio, Cremación, Vencimiento de contrato..."></textarea>
                        <div class="invalid-feedback">
                            Por favor, ingresa el motivo de la solicitud.
                        </div>
                    </div>

                     <div class="alert alert-warning small">
                        <strong>Nota:</strong> Esta solicitud será revisada por la administración. Podrían contactarte para solicitar documentación adicional o coordinar detalles. La aprobación no está garantizada y está sujeta a normativas vigentes.
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    {{-- Deshabilitar si no hay contratos elegibles --}}
                    <button type="submit" class="btn btn-warning" {{ !(isset($contratosParaSelect) && $contratosParaSelect->contains('ocupante_id', '!=', null)) ? 'disabled' : '' }}>
                        Enviar Solicitud de Exhumación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection {{-- Fin de @section('content') --}}


{{-- Estilos específicos (pueden ir aquí o en el layout) --}}
@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card-title i {
        color: #6c757d;
        margin-right: 0.5rem;
    }
     .fa-search { color: #0d6efd; }
     .fa-file-invoice-dollar { color: #198754; }
     .fa-skull-crossbones { color: #ffc107; }
     .fa-tasks { color: #0dcaf0; }
     .fa-user-edit { color: #6c757d; }
</style>
@endpush

{{-- Scripts específicos (asegúrate que Bootstrap JS esté en el layout) --}}
@push('scripts')
<script>
// Script para validación de Bootstrap
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>
@endpush