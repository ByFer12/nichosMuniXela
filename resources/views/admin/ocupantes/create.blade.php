@extends('./Layouts.landing')
@section('title', 'Registrar Nuevo Ocupante')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Registrar Nuevo Ocupante</h1>
    <ol class="breadcrumb mb-4">
        {{-- Ajusta estas rutas si quieres una genérica para admin/ayudante --}}
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.ocupantes.index') }}">Ocupantes</a></li>
        <li class="breadcrumb-item active">Registrar</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i> Datos del Nuevo Ocupante
        </div>

        {{-- Mostrar TODOS los errores de validación juntos (Estilo User Form) --}}
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert"> {{-- Añadido margen m-3 --}}
            <h5 class="alert-heading">¡Error de Validación!</h5>
            <p>Por favor, corrija los siguientes errores:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card-body">
            <form action="{{ route('admin.ocupantes.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                {{-- Datos Personales --}}
                <h5 class="mb-3 text-primary">Información Personal</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                       <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                       <input type="text" class="form-control @error('nombres') is-invalid @enderror" id="nombres" name="nombres" value="{{ old('nombres') }}" required>
                       @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                       <div class="valid-feedback">Correcto.</div>
                    </div>
                    <div class="col-md-6">
                       <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                       <input type="text" class="form-control @error('apellidos') is-invalid @enderror" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required>
                       @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="valid-feedback">Correcto.</div>
                    </div>
                    <div class="col-md-4">
                       <label for="dpi" class="form-label">DPI</label>
                       <input type="text" class="form-control @error('dpi') is-invalid @enderror" id="dpi" name="dpi" value="{{ old('dpi') }}">
                        @error('dpi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_genero_id" class="form-label">Género</label>
                        <select class="form-select @error('tipo_genero_id') is-invalid @enderror" id="tipo_genero_id" name="tipo_genero_id">
                           <option value="">Seleccione...</option>
                           @foreach($generos ?? [] as $id => $nombre)
                               <option value="{{ $id }}" {{ old('tipo_genero_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                           @endforeach
                        </select>
                        @error('tipo_genero_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

               {{-- Fechas y Causa --}}
               <h5 class="mb-3 text-primary">Información de Defunción</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                       <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                       <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" max="{{ date('Y-m-d') }}">
                        @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                   </div>
                   <div class="col-md-6">
                       <label for="fecha_fallecimiento" class="form-label">Fecha de Fallecimiento <span class="text-danger">*</span></label>
                       <input type="date" class="form-control @error('fecha_fallecimiento') is-invalid @enderror" id="fecha_fallecimiento" name="fecha_fallecimiento" value="{{ old('fecha_fallecimiento') }}" required max="{{ date('Y-m-d') }}">
                       @error('fecha_fallecimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="valid-feedback">Correcto.</div>
                   </div>
                    <div class="col-12">
                       <label for="causa_muerte" class="form-label">Causa de Muerte</label>
                       <input type="text" class="form-control @error('causa_muerte') is-invalid @enderror" id="causa_muerte" name="causa_muerte" value="{{ old('causa_muerte') }}">
                        @error('causa_muerte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                   </div>
                </div>

               {{-- Dirección (Opcional) --}}
                <h5 class="mb-3 text-primary">Dirección Registrada (Opcional)</h5>
                <div class="row g-3 mb-4"> {{-- Separación --}}
                    <div class="col-md-6">
                       <label for="addr_calle_numero" class="form-label">Calle y Número</label>
                       <input type="text" class="form-control @error('addr_calle_numero') is-invalid @enderror" id="addr_calle_numero" name="addr_calle_numero" value="{{ old('addr_calle_numero') }}">
                       @error('addr_calle_numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                       <label for="addr_colonia_barrio" class="form-label">Colonia / Barrio / Zona</label>
                       <input type="text" class="form-control @error('addr_colonia_barrio') is-invalid @enderror" id="addr_colonia_barrio" name="addr_colonia_barrio" value="{{ old('addr_colonia_barrio') }}">
                        @error('addr_colonia_barrio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                   <div class="col-md-4">
                       <label for="addr_codigo_postal" class="form-label">Código Postal</label>
                       <input type="text" class="form-control @error('addr_codigo_postal') is-invalid @enderror" id="addr_codigo_postal" name="addr_codigo_postal" value="{{ old('addr_codigo_postal') }}">
                       @error('addr_codigo_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                   </div>
                   {{-- Selects addr_departamento_id, addr_municipio_id --}}
                   <div class="col-md-4">
                       <label for="addr_departamento_id" class="form-label">Departamento</label>
                       {{-- ID: addr_departamento_id --}}
                       <select class="form-select @error('addr_departamento_id') is-invalid @enderror" id="addr_departamento_id" name="addr_departamento_id">
                           <option value="">Seleccione Departamento...</option>
                           @foreach($departamentos ?? [] as $depto)
                               <option value="{{ $depto->id }}" {{ old('addr_departamento_id') == $depto->id ? 'selected' : '' }}>
                                   {{ $depto->nombre }}
                               </option>
                           @endforeach
                       </select>
                        @error('addr_departamento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                   </div>
                   <div class="col-md-4">
                       <label for="addr_municipio_id" class="form-label">Municipio</label>
                       {{-- ID: addr_municipio_id --}}
                       <select class="form-select @error('addr_municipio_id') is-invalid @enderror" id="addr_municipio_id" name="addr_municipio_id" disabled> {{-- Empieza deshabilitado --}}
                           <option value="">Seleccione Departamento primero...</option>
                           {{-- Se llenará con JavaScript --}}
                       </select>
                        @error('addr_municipio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                   </div>
                   {{-- Textarea addr_referencia --}}
                    <div class="col-12">
                       <label for="addr_referencia" class="form-label">Referencia Adicional</label>
                       <textarea class="form-control @error('addr_referencia') is-invalid @enderror" id="addr_referencia" name="addr_referencia" rows="2">{{ old('addr_referencia') }}</textarea>
                        @error('addr_referencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                   </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end"> {{-- Separador visual --}}
                    <a href="{{ route('admin.ocupantes.index') }}" class="btn btn-secondary me-2"><i class="fas fa-times me-1"></i>Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Registrar Ocupante</button>
               </div>
           </form>
        </div>
    </div>
</div>
@endsection

 <script>
 // Example starter JavaScript for disabling form submissions if there are invalid fields
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

 {{-- Script selects dependientes (Estilo User Form) --}}
 <script>
 document.addEventListener('DOMContentLoaded', function() {
    // --- Elementos del DOM (sección Dirección Ocupante) ---
    const deptoSelect = document.getElementById('addr_departamento_id');
    const muniSelect = document.getElementById('addr_municipio_id');
    // URL Template para obtener municipios (Asegúrate que la ruta 'web.location.municipios' existe y funciona)
    const municipiosUrlTemplate = "{{ route('web.location.municipios', ['departamento' => ':id']) }}";

    // --- Funciones Helper Selects Dependientes ---

    // Función para resetear el select de municipios
    function resetSelect(selectElement, placeholder) {
         if (selectElement) { // Verificar si el elemento existe
            selectElement.innerHTML = `<option value="">${placeholder}</option>`; // Limpia y pone placeholder
            selectElement.disabled = true; // Lo deshabilita
         } else {
             console.warn("Intento de resetear un select que no existe:", selectElement);
         }
    }

    // Función ASÍNCRONA para cargar municipios usando Fetch API
    async function cargarMunicipios(departamentoId, municipioSeleccionadoId = null) {
        // Si el select de municipio no existe en el DOM, no continuar
        if (!muniSelect) {
            console.error("Error: El select de municipios ('addr_municipio_id') no se encontró.");
            return;
        }

        // Muestra estado de carga y deshabilita mientras se hace la petición
        resetSelect(muniSelect, 'Cargando municipios...');

        // Si no se proporciona un ID de departamento válido, resetear y salir
        if (!departamentoId || departamentoId === '') {
            resetSelect(muniSelect, 'Seleccione Departamento primero...');
            return;
        }

        // Construye la URL final para la petición AJAX
        const url = municipiosUrlTemplate.replace(':id', departamentoId);

        try {
            // Realiza la petición GET
            const response = await fetch(url);

            // Verifica si la respuesta de la red fue exitosa (status 2xx)
            if (!response.ok) {
                // Lanza un error para ser capturado por el bloque catch
                throw new Error(`Error de red: ${response.status} ${response.statusText}`);
            }

            // Intenta convertir la respuesta a JSON
            const data = await response.json();

            // Limpia el select antes de añadir nuevas opciones y define placeholder
            resetSelect(muniSelect, 'Seleccione Municipio...');

            // Verifica si se recibieron datos y si es un array con elementos
            if (data && Array.isArray(data) && data.length > 0) {
                // Itera sobre los municipios recibidos
                data.forEach(municipio => {
                    // **IMPORTANTE**: Asegúrate que tu controlador devuelve objetos con 'id' y 'nombre'
                    const option = new Option(municipio.nombre, municipio.id);
                    // Si se pasó un ID para preseleccionar (manejo de old())
                    if (municipioSeleccionadoId && municipio.id == parseInt(municipioSeleccionadoId, 10)) {
                        option.selected = true; // Marca esta opción como seleccionada
                    }
                    muniSelect.add(option); // Añade la opción al select
                });
                muniSelect.disabled = false; // Habilita el select porque tiene opciones
            } else {
                 // Si no hay datos o el array está vacío, muestra mensaje y mantiene deshabilitado
                 resetSelect(muniSelect, 'No hay municipios para este Depto.');
            }

        } catch (error) {
            // Captura cualquier error (red, parseo JSON, etc.)
            console.error('Error al cargar municipios:', error);
            // Muestra un mensaje de error en el select y lo mantiene deshabilitado
            resetSelect(muniSelect, 'Error al cargar municipios');
        }
    }

    // --- Event Listeners ---
    if (deptoSelect) { // Asegurarse que el select de departamento existe
        deptoSelect.addEventListener('change', function() {
            // Cuando cambia el departamento, llama a cargarMunicipios con el nuevo ID
            cargarMunicipios(this.value);
        });
    } else {
        console.error("Elemento #addr_departamento_id no encontrado. El script de selects dependientes no funcionará.");
    }

    // --- Ejecución Inicial (al cargar la página) ---
    // Obtener el valor inicial del departamento (podría venir de old())
    const initialDeptoId = deptoSelect ? deptoSelect.value : null;
    // Obtener el valor old() del municipio (si existe)
    const initialMuniId = '{{ old('addr_municipio_id') }}';

    // Si al cargar la página ya hay un departamento seleccionado
    if (initialDeptoId) {
        // Llama a cargarMunicipios para poblar el select y potencialmente preseleccionar el municipio
        cargarMunicipios(initialDeptoId, initialMuniId);
    } else {
        // Si no hay departamento inicial, solo asegura que el select de municipio esté reseteado/deshabilitado
        resetSelect(muniSelect, 'Seleccione Departamento primero...');
    }

 });
 </script>
