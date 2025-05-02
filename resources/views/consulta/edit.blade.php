@extends('./Layouts.landing') {{-- Usa tu layout principal --}}

@section('title', 'Editar Mi Perfil')

@section('content')
<div class="container mt-4 mb-5">

    {{-- Encabezado y Botón Volver --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Editar Mi Perfil</h2>
        <a href="{{ route('consulta.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Portal
        </a>
    </div>

    {{-- Mostrar Mensajes Flash --}}
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
    {{-- Mostrar todos los errores de validación al principio (opcional) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('perfil.update') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT') {{-- O @method('POST') si cambiaste la ruta --}}

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                 <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Información de Usuario</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Nombre --}}
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $user->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                         @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
             <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-address-book me-2"></i>Información de Contacto (Responsable)</h5>
             </div>
            <div class="card-body">
                 <div class="row g-3">
                    {{-- Teléfono --}}
                    <div class="col-md-6">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $user->responsable->telefono ?? '') }}" placeholder="Ej: 5555-1234">
                         @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                 </div>
            </div>
        </div>

         <div class="card shadow-sm mb-4">
             <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Dirección</h5>
             </div>
            <div class="card-body">
                {{-- Obtener dirección actual para pre-seleccionar --}}
                @php
                    $direccionActual = $user->responsable->direccion ?? null;
                    $localidadActual = $direccionActual ? $direccionActual->localidad : null;
                    $municipioActual = $localidadActual ? $localidadActual->municipio : null;
                    $departamentoActual = $municipioActual ? $municipioActual->departamento : null;
                @endphp

                 <div class="row g-3">
                     {{-- Calle y Número --}}
                    <div class="col-md-6">
                        <label for="calle_numero" class="form-label">Calle y Número</label>
                        <input type="text" class="form-control @error('calle_numero') is-invalid @enderror" id="calle_numero" name="calle_numero" value="{{ old('calle_numero', $direccionActual->calle_numero ?? '') }}">
                         @error('calle_numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     {{-- Colonia / Barrio --}}
                    <div class="col-md-6">
                        <label for="colonia_barrio" class="form-label">Colonia / Barrio / Zona</label>
                        <input type="text" class="form-control @error('colonia_barrio') is-invalid @enderror" id="colonia_barrio" name="colonia_barrio" value="{{ old('colonia_barrio', $direccionActual->colonia_barrio ?? '') }}">
                         @error('colonia_barrio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     {{-- Código Postal --}}
                    <div class="col-md-4">
                        <label for="codigo_postal" class="form-label">Código Postal</label>
                        <input type="text" class="form-control @error('codigo_postal') is-invalid @enderror" id="codigo_postal" name="codigo_postal" value="{{ old('codigo_postal', $direccionActual->codigo_postal ?? '') }}">
                         @error('codigo_postal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Departamento --}}
                    <div class="col-md-4">
                         <label for="departamento_id" class="form-label">Departamento <span class="text-danger">*</span></label>
                         <select class="form-select @error('departamento_id') is-invalid @enderror" id="departamento_id" name="departamento_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto->id }}" {{ old('departamento_id', $departamentoActual->id ?? '') == $depto->id ? 'selected' : '' }}>
                                    {{ $depto->nombre }}
                                </option>
                            @endforeach
                         </select>
                         @error('departamento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                    </div>

                    {{-- Municipio (Dependiente) --}}
                    <div class="col-md-4">
                         <label for="municipio_id" class="form-label">Municipio <span class="text-danger">*</span></label>
                         <select class="form-select @error('municipio_id') is-invalid @enderror" id="municipio_id" name="municipio_id" required>
                             <option value="">Seleccione departamento primero...</option>
                             {{-- Llenar con JS o cargar los del depto actual si existe --}}
                             @if($municipios->isNotEmpty())
                                @foreach($municipios as $mun)
                                <option value="{{ $mun->id }}" {{ old('municipio_id', $municipioActual->id ?? '') == $mun->id ? 'selected' : '' }}>
                                    {{ $mun->nombre }}
                                </option>
                                @endforeach
                             @endif
                         </select>
                         @error('municipio_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Localidad (Dependiente) --}}
                     <div class="col-md-4">
                         <label for="localidad_id" class="form-label">Localidad/Aldea <span class="text-danger">*</span></label>
                         <select class="form-select @error('localidad_id') is-invalid @enderror" id="localidad_id" name="localidad_id" required>
                            <option value="">Seleccione municipio primero...</option>
                             {{-- Llenar con JS o cargar las del municipio actual si existe --}}
                             @if($localidades->isNotEmpty())
                                @foreach($localidades as $loc)
                                <option value="{{ $loc->id }}" {{ old('localidad_id', $localidadActual->id ?? '') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->nombre }}
                                </option>
                                @endforeach
                             @endif
                         </select>
                         @error('localidad_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>

                    {{-- Referencia Adicional --}}
                    <div class="col-12">
                         <label for="referencia_adicional" class="form-label">Referencia Adicional</label>
                         <textarea class="form-control @error('referencia_adicional') is-invalid @enderror" id="referencia_adicional" name="referencia_adicional" rows="2">{{ old('referencia_adicional', $direccionActual->referencia_adicional ?? '') }}</textarea>
                         @error('referencia_adicional')
                            <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                    </div>
                 </div>
            </div>
        </div>


        <div class="card shadow-sm mb-4">
             <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Cambiar Contraseña (Opcional)</h5>
             </div>
            <div class="card-body">
                 <div class="row g-3">
                    {{-- Contraseña Actual --}}
                    <div class="col-md-4">
                         <label for="current_password" class="form-label">Contraseña Actual</label>
                         <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                         @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     {{-- Nueva Contraseña --}}
                    <div class="col-md-4">
                         <label for="new_password" class="form-label">Nueva Contraseña</label>
                         <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                         @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     {{-- Confirmar Nueva Contraseña --}}
                    <div class="col-md-4">
                         <label for="new_password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                         <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                         {{-- El error de confirmación usualmente se muestra en el campo 'new_password' --}}
                    </div>
                 </div>
            </div>
        </div>


        <div class="text-end">
            <a href="{{ route('consulta.dashboard') }}" class="btn btn-outline-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
{{-- Script básico para validación de Bootstrap --}}
<script>
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

{{-- Script para Selects Dependientes (Municipio/Localidad) --}}
{{-- Requiere jQuery o puedes adaptarlo a Vanilla JS --}}
{{-- Asume que tienes rutas API para obtener municipios y localidades --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deptoSelect = document.getElementById('departamento_id');
    const muniSelect = document.getElementById('municipio_id');
    const locSelect = document.getElementById('localidad_id');

    // --- Función para cargar Municipios ---
    function cargarMunicipios(departamentoId, municipioSeleccionadoId = null) {
        // ... (código para limpiar selects, poner 'Cargando...') ...

        if (!departamentoId) { /* ... */ return; }

        // ***** CAMBIO EN LA URL *****
        fetch(`/get/departamentos/${departamentoId}/municipios`) // <-- Usa la ruta de web.php
            .then(response => { /* ... manejo de respuesta ... */ })
            .then(data => { /* ... poblar select municipios ... */ })
            .catch(error => { /* ... manejo de error ... */ });
    }

     // --- Función para cargar Localidades ---
    function cargarLocalidades(municipioId, localidadSeleccionadaId = null) {
         // ... (código para limpiar select, poner 'Cargando...') ...

        if (!municipioId) { /* ... */ return; }

         // ***** CAMBIO EN LA URL *****
        fetch(`/get/municipios/${municipioId}/localidades`) // <-- Usa la ruta de web.php
            .then(response => { /* ... manejo de respuesta ... */ })
            .then(data => { /* ... poblar select localidades ... */ })
            .catch(error => { /* ... manejo de error ... */ });
    }

    // ... (Event Listeners y Carga Inicial - sin cambios aquí) ...

});
</script>
@endpush