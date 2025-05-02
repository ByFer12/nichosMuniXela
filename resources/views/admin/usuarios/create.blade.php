@extends('./Layouts.landing')
@section('title', 'Crear Nuevo Usuario')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Nuevo Usuario</h1>
    <ol class="breadcrumb mb-4">
        {{-- ... breadcrumbs ... --}}
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Crear</li>
    </ol>

  
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i> Datos del Nuevo Usuario
        </div>
        {{-- Mostrar TODOS los errores de validación juntos --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h5 class="alert-heading">¡Error de Validación!</h5>
    <p>Por favor, corrige los siguientes errores:</p>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

        <div class="card-body">
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row g-3">

                    {{-- 1. Selección de Rol (Siempre visible) --}}
                    <div class="col-12">
                         <label for="rol_id" class="form-label">Seleccione el Rol del Usuario <span class="text-danger">*</span></label>
                         {{-- ID: rol_id --}}
                         <select class="form-select @error('rol_id') is-invalid @enderror" id="rol_id" name="rol_id" required>
                            <option value="" selected disabled>-- Elige un rol --</option>
                            @foreach($roles as $id => $nombre)
                                <option value="{{ $id }}" {{ old('rol_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                         </select>
                         @error('rol_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Contenedor dinámico --}}
                    <div id="camposDinamicos" class="contents">

                        {{-- CAMPOS PARA ROLES NO-CONSULTA --}}
                        {{-- ID: camposNormales --}}
                        <div class="row g-3 mt-2" id="camposNormales">
                             <hr>
                             <h5 class="text-secondary">Datos del Usuario</h5>
                            <div class="col-md-6">
                                <label for="nombre_normal" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                {{-- ID: nombre_normal --}}
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre_normal" name="nombre" value="{{ old('nombre') }}">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                             <div class="col-md-6">
                                <label for="username_normal" class="form-label">Username (Opcional)</label>
                                {{-- ID: username_normal --}}
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username_normal" name="username" value="{{ old('username') }}">
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email_normal" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                {{-- ID: email_normal --}}
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email_normal" name="email" value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6"></div> {{-- Espacio --}}
                            <div class="col-md-6">
                                <label for="password_normal" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                {{-- ID: password_normal --}}
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password_normal" name="password" aria-describedby="passwordHelpNormal">
                                <div id="passwordHelpNormal" class="form-text">Mínimo 8 caracteres, letras y números.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation_normal" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                {{-- ID: password_confirmation_normal --}}
                                <input type="password" class="form-control" id="password_confirmation_normal" name="password_confirmation">
                            </div>
                        </div>

                        {{-- CAMPOS EXTRA PARA ROL CONSULTA --}}
                        {{-- ID: camposConsulta --}}
                        <div class="row g-3 mt-2" id="camposConsulta" style="display: none;">
                             <hr>
                             <h5 class="text-primary">Datos Generales Cuenta Consulta</h5>
                             <div class="col-md-6">
                                {{-- ID: nombre_consulta --}}
                                <label for="nombre_consulta" class="form-label">Nombre Familia/General <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre_consulta" name="nombre" value="{{ old('nombre') }}">
                                <div class="form-text">Nombre general para identificar la cuenta (Ej: Familia Pérez López).</div>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                {{-- ID: username_consulta --}}
                                <label for="username_consulta" class="form-label">Username (Login) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username_consulta" name="username" value="{{ old('username') }}">
                                <div class="form-text">Obligatorio para el inicio de sesión del usuario consulta.</div>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                             <div class="col-md-6">
                                 {{-- ID: email_consulta --}}
                                <label for="email_consulta" class="form-label">Correo Electrónico (Contacto/Login) <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email_consulta" name="email" value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                             <div class="col-md-6"></div> {{-- Espacio --}}
                             <div class="col-md-6">
                                 {{-- ID: password_consulta --}}
                                <label for="password_consulta" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password_consulta" name="password" aria-describedby="passwordHelpConsulta">
                                <div id="passwordHelpConsulta" class="form-text">Mínimo 8 caracteres, letras y números.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                {{-- ID: password_confirmation_consulta --}}
                                <label for="password_confirmation_consulta" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation_consulta" name="password_confirmation">
                            </div>

                             {{-- DATOS DEL RESPONSABLE VINCULADO --}}
                             {{-- ID: datosResponsable (para la sección completa) --}}
                             <div id="datosResponsable" class="mt-4 pt-3 border-top col-12">
                                <h5 class="mb-3 text-primary">Datos del Responsable <span class="text-muted small">(Requerido)</span></h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="resp_nombres" class="form-label">Nombres Responsable</label>
                                        {{-- ID: resp_nombres --}}
                                        <span class="text-danger responsable-required-indicator" style="display: none;">*</span>
                                        <input type="text" class="form-control @error('resp_nombres') is-invalid @enderror" id="resp_nombres" name="resp_nombres" value="{{ old('resp_nombres') }}">
                                        @error('resp_nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="resp_apellidos" class="form-label">Apellidos Responsable</label>
                                        {{-- ID: resp_apellidos --}}
                                        <span class="text-danger responsable-required-indicator" style="display: none;">*</span>
                                        <input type="text" class="form-control @error('resp_apellidos') is-invalid @enderror" id="resp_apellidos" name="resp_apellidos" value="{{ old('resp_apellidos') }}">
                                        @error('resp_apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="resp_dpi" class="form-label">DPI Responsable</label>
                                        {{-- ID: resp_dpi --}}
                                        <span class="text-danger responsable-required-indicator" style="display: none;">*</span>
                                        <input type="text" class="form-control @error('resp_dpi') is-invalid @enderror" id="resp_dpi" name="resp_dpi" value="{{ old('resp_dpi') }}">
                                        @error('resp_dpi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="resp_telefono" class="form-label">Teléfono Responsable</label>
                                        {{-- ID: resp_telefono --}}
                                        <input type="tel" class="form-control @error('resp_telefono') is-invalid @enderror" id="resp_telefono" name="resp_telefono" value="{{ old('resp_telefono') }}">
                                        @error('resp_telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="resp_correo_electronico" class="form-label">Correo Electrónico Responsable</label>
                                         {{-- ID: resp_correo_electronico --}}
                                        <input type="email" class="form-control @error('resp_correo_electronico') is-invalid @enderror" id="resp_correo_electronico" name="resp_correo_electronico" value="{{ old('resp_correo_electronico') }}">
                                        @error('resp_correo_electronico')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                     {{-- SECCIÓN DIRECCIÓN --}}
                                     <div class="col-12 mt-3"> <h6 class="text-secondary">Dirección del Responsable</h6> </div>
                                    <div class="col-md-6">
                                        <label for="resp_addr_calle_numero" class="form-label">Calle y Número</label>
                                        {{-- ID: resp_addr_calle_numero --}}
                                        <input type="text" class="form-control @error('resp_addr_calle_numero') is-invalid @enderror" id="resp_addr_calle_numero" name="resp_addr_calle_numero" value="{{ old('resp_addr_calle_numero') }}">
                                        @error('resp_addr_calle_numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="resp_addr_colonia_barrio" class="form-label">Colonia / Barrio / Zona</label>
                                         {{-- ID: resp_addr_colonia_barrio --}}
                                        <input type="text" class="form-control @error('resp_addr_colonia_barrio') is-invalid @enderror" id="resp_addr_colonia_barrio" name="resp_addr_colonia_barrio" value="{{ old('resp_addr_colonia_barrio') }}">
                                        @error('resp_addr_colonia_barrio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="resp_addr_codigo_postal" class="form-label">Código Postal</label>
                                         {{-- ID: resp_addr_codigo_postal --}}
                                        <input type="text" class="form-control @error('resp_addr_codigo_postal') is-invalid @enderror" id="resp_addr_codigo_postal" name="resp_addr_codigo_postal" value="{{ old('resp_addr_codigo_postal') }}">
                                        @error('resp_addr_codigo_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    {{-- Departamento --}}
                                    <div class="col-md-4"> {{-- Ajustado --}}
                                        <label for="resp_addr_departamento_id" class="form-label">Departamento</label>
                                        <span class="text-danger responsable-required-indicator" style="display: none;">*</span>
                                         {{-- ID: resp_addr_departamento_id --}}
                                        <select class="form-select @error('resp_addr_departamento_id') is-invalid @enderror" id="resp_addr_departamento_id" name="resp_addr_departamento_id">
                                            <option value="">Seleccione...</option>
                                            @foreach($departamentos ?? [] as $depto)
                                                <option value="{{ $depto->id }}" {{ old('resp_addr_departamento_id') == $depto->id ? 'selected' : '' }}>
                                                    {{ $depto->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('resp_addr_departamento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    {{-- Municipio --}}
                                    <div class="col-md-4"> {{-- Ajustado --}}
                                        <label for="resp_addr_municipio_id" class="form-label">Municipio</label>
                                        <span class="text-danger responsable-required-indicator" style="display: none;">*</span>
                                         {{-- *** ID CORREGIDO *** --}}
                                        <select class="form-select @error('resp_addr_municipio_id') is-invalid @enderror" id="resp_addr_municipio_id" name="resp_addr_municipio_id" disabled>
                                            <option value="">Seleccione departamento...</option>
                                        </select>
                                        @error('resp_addr_municipio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    {{-- Referencia --}}
                                    <div class="col-12">
                                        <label for="resp_addr_referencia" class="form-label">Referencia Adicional</label>
                                         {{-- ID: resp_addr_referencia --}}
                                        <textarea class="form-control @error('resp_addr_referencia') is-invalid @enderror" id="resp_addr_referencia" name="resp_addr_referencia" rows="2">{{ old('resp_addr_referencia') }}</textarea>
                                        @error('resp_addr_referencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                             </div> {{-- Fin #datosResponsable --}}
                        </div> {{-- Fin #camposConsulta --}}

                    </div>{{-- Fin #camposDinamicos --}}

                </div> {{-- Fin row Principal --}}

                <div class="mt-4 text-end">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

 <script>
 document.addEventListener('DOMContentLoaded', function() {
    // --- Elementos del DOM ---
    const rolSelect = document.getElementById('rol_id');
    const camposNormalesDiv = document.getElementById('camposNormales');
    const camposConsultaDiv = document.getElementById('camposConsulta');

    // Inputs Normales
    const nombreNormalInput = document.getElementById('nombre_normal');
    const usernameNormalInput = document.getElementById('username_normal');
    const emailNormalInput = document.getElementById('email_normal');
    const passwordNormalInput = document.getElementById('password_normal');
    const passwordConfirmationNormalInput = document.getElementById('password_confirmation_normal');

    // Inputs Consulta (Usuario)
    const nombreConsultaInput = document.getElementById('nombre_consulta');
    const usernameConsultaInput = document.getElementById('username_consulta');
    const emailConsultaInput = document.getElementById('email_consulta');
    const passwordConsultaInput = document.getElementById('password_consulta');
    const passwordConfirmationConsultaInput = document.getElementById('password_confirmation_consulta');

     // Inputs Consulta (Responsable)
    const respNombresInput = document.getElementById('resp_nombres');
    const respApellidosInput = document.getElementById('resp_apellidos');
    const respDpiInput = document.getElementById('resp_dpi');
    const respTelefonoInput = document.getElementById('resp_telefono');
    const respCorreoInput = document.getElementById('resp_correo_electronico');

    // Inputs Consulta (Dirección)
    const respAddrDeptoSelect = document.getElementById('resp_addr_departamento_id');
    const respAddrMuniSelect = document.getElementById('resp_addr_municipio_id'); // *** VERIFICAR ESTE ID ***
    const respAddrCalleInput = document.getElementById('resp_addr_calle_numero');
    const respAddrColoniaInput = document.getElementById('resp_addr_colonia_barrio');
    const respAddrCPInput = document.getElementById('resp_addr_codigo_postal');
    const respAddrRefTextarea = document.getElementById('resp_addr_referencia');

    // Indicadores Required
    const usernameRequiredIndicator = document.getElementById('username_required_indicator'); // *** ASEGURARSE QUE ESTE SPAN EXISTA EN EL HTML ***
    const responsableRequiredIndicators = document.querySelectorAll('.responsable-required-indicator'); // *** ASEGURARSE QUE ESTOS SPANS EXISTAN ***

    // --- Función Principal handleRolChange ---
    function handleRolChange() {
        const esConsulta = rolSelect.value == '4';

        // Fallback si algún elemento no se encuentra (para depurar)
        if (!camposConsultaDiv || !camposNormalesDiv) {
            console.error("Error: No se encontraron los divs principales de campos.");
            return;
        }

        camposConsultaDiv.style.display = esConsulta ? 'block' : 'none';
        camposNormalesDiv.style.display = esConsulta ? 'none' : 'block';

        // --- Ajustar Campos Normales ---
        setElementState(nombreNormalInput, !esConsulta, !esConsulta);
        setElementState(usernameNormalInput, !esConsulta, false); // username normal no es requerido
        setElementState(emailNormalInput, !esConsulta, !esConsulta);
        setElementState(passwordNormalInput, !esConsulta, !esConsulta);
        setElementState(passwordConfirmationNormalInput, !esConsulta, !esConsulta);

        // --- Ajustar Campos Consulta (Usuario) ---
        setElementState(nombreConsultaInput, esConsulta, esConsulta);
        setElementState(usernameConsultaInput, esConsulta, esConsulta);
        if (usernameRequiredIndicator) usernameRequiredIndicator.style.display = esConsulta ? 'inline' : 'none';
        setElementState(emailConsultaInput, esConsulta, esConsulta);
        setElementState(passwordConsultaInput, esConsulta, esConsulta);
        setElementState(passwordConfirmationConsultaInput, esConsulta, esConsulta);

        // --- Ajustar Campos Consulta (Responsable) ---
        setElementState(respNombresInput, esConsulta, esConsulta);
        setElementState(respApellidosInput, esConsulta, esConsulta);
        setElementState(respDpiInput, esConsulta, esConsulta);
        setElementState(respTelefonoInput, esConsulta, false); // Teléfono no requerido
        setElementState(respCorreoInput, esConsulta, false); // Correo no requerido
        responsableRequiredIndicators.forEach(span => span.style.display = esConsulta ? 'inline' : 'none');

         // --- Ajustar Campos Consulta (Dirección) ---
        setElementState(respAddrDeptoSelect, esConsulta, esConsulta);
        setElementState(respAddrMuniSelect, esConsulta, esConsulta);
        setElementState(respAddrCalleInput, esConsulta, false);
        setElementState(respAddrColoniaInput, esConsulta, false);
        setElementState(respAddrCPInput, esConsulta, false);
        setElementState(respAddrRefTextarea, esConsulta, false);

        // Resetear selects dependientes si se oculta la sección
        if (!esConsulta) {
             resetSelect(respAddrMuniSelect, 'Seleccione departamento...');
        } else {
             // Si se selecciona Consulta y el depto ya tiene valor, cargar municipios
             if (respAddrDeptoSelect && respAddrDeptoSelect.value) {
                cargarMunicipios(respAddrDeptoSelect.value, '{{ old('resp_addr_municipio_id') }}');
             } else {
                 resetSelect(respAddrMuniSelect, 'Seleccione departamento...');
             }
        }
    }

    // Función helper para habilitar/deshabilitar y poner required
    function setElementState(element, enabled, required) {
        if (element) { // Solo si el elemento existe
            element.disabled = !enabled;
            element.required = required;
        } else {
            // console.warn("Elemento no encontrado en setElementState:", element); // Ayuda a depurar
        }
    }


    // --- Funciones Helper Selects Dependientes (sin cambios en lógica fetch) ---
    function resetSelect(selectElement, placeholder) {
         if (selectElement) { // Verificar si existe
            selectElement.innerHTML = `<option value="">${placeholder}</option>`;
            selectElement.disabled = true;
         }
    }

    function cargarMunicipios(departamentoId, municipioSeleccionadoId = null) {
        if (!respAddrMuniSelect) return; // No hacer nada si el select no existe
        resetSelect(respAddrMuniSelect, 'Cargando...');

        if (!departamentoId) { resetSelect(respAddrMuniSelect, 'Seleccione departamento...'); return; }

        // Asegúrate que la ruta es correcta y accesible
        fetch(`/get/departamentos/${departamentoId}/municipios`)
            .then(response => response.ok ? response.json() : Promise.reject('Error carga municipios'))
            .then(data => {
                resetSelect(respAddrMuniSelect, 'Seleccione...');
                if(data && data.length > 0){
                    data.forEach(municipio => {
                        const option = new Option(municipio.nombre, municipio.id);
                        if (municipioSeleccionadoId && municipio.id == parseInt(municipioSeleccionadoId, 10)) {
                            option.selected = true;
                        }
                        respAddrMuniSelect.add(option);
                    });
                    respAddrMuniSelect.disabled = false; // Habilitar solo si hay datos
                } else {
                     resetSelect(respAddrMuniSelect, 'No hay municipios');
                }
            })
            .catch(error => { console.error(error); resetSelect(respAddrMuniSelect, 'Error al cargar'); });
    }


    // --- Event Listeners ---
    if (rolSelect) { // Verificar si existe
        rolSelect.addEventListener('change', handleRolChange);
    } else {
        console.error("Elemento #rol_id no encontrado.");
    }
     if (respAddrDeptoSelect) { // Verificar si existe
        respAddrDeptoSelect.addEventListener('change', function() {
            cargarMunicipios(this.value);
        });
     } else {
         console.error("Elemento #resp_addr_departamento_id no encontrado.");
     }


    // --- Ejecución Inicial ---
    handleRolChange(); // Aplicar estado inicial
    const initialDeptoId = '{{ old('resp_addr_departamento_id') }}';
    const initialMuniId = '{{ old('resp_addr_municipio_id') }}';
     if (rolSelect.value == '4' && initialDeptoId) { // Solo cargar si el rol inicial es Consulta Y hay depto viejo
         cargarMunicipios(initialDeptoId, initialMuniId);
     }

 });
 </script>
