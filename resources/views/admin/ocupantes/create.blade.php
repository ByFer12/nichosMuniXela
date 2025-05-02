@extends('./Layouts.landing')
@section('title', 'Registrar Nuevo Ocupante')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Registrar Nuevo Ocupante</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.ocupantes.index') }}">Ocupantes</a></li>
        <li class="breadcrumb-item active">Registrar</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-user-plus me-1"></i> Datos del Ocupante</div>
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
                     </div>
                     <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellidos') is-invalid @enderror" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required>
                        @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            @foreach($generos as $id => $nombre)
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
                    </div>
                     <div class="col-12">
                        <label for="causa_muerte" class="form-label">Causa de Muerte</label>
                        <input type="text" class="form-control @error('causa_muerte') is-invalid @enderror" id="causa_muerte" name="causa_muerte" value="{{ old('causa_muerte') }}">
                         @error('causa_muerte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                 </div>

                {{-- Dirección (Opcional) --}}
                 <h5 class="mb-3 text-primary">Dirección Registrada (Opcional)</h5>
                 <div class="row g-3">
                    {{-- Inputs addr_calle_numero, addr_colonia_barrio, addr_codigo_postal --}}
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
                        <select class="form-select @error('addr_departamento_id') is-invalid @enderror" id="addr_departamento_id" name="addr_departamento_id">
                            <option value="">Seleccione...</option>
                            @foreach($departamentos ?? [] as $depto)
                                <option value="{{ $depto->id }}" {{ old('addr_departamento_id') == $depto->id ? 'selected' : '' }}>{{ $depto->nombre }}</option>
                            @endforeach
                        </select>
                         @error('addr_departamento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="addr_municipio_id" class="form-label">Municipio</label>
                        <select class="form-select @error('addr_municipio_id') is-invalid @enderror" id="addr_municipio_id" name="addr_municipio_id" disabled>
                            <option value="">Seleccione departamento...</option>
                            {{-- Llenar con JS --}}
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

                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.ocupantes.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Registrar Ocupante</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
 {{-- Script validación Bootstrap --}}
 <script> /* ... */ </script>
 {{-- Script selects dependientes (¡REUTILIZADO!) --}}
 <script>
 document.addEventListener('DOMContentLoaded', function () {
    // *** USA LOS MISMOS IDs que en el form de usuario ***
    const deptoSelect = document.getElementById('addr_departamento_id'); // ID del select depto
    const muniSelect = document.getElementById('addr_municipio_id');   // ID del select municipio

    function resetSelect(selectElement, placeholder) { /* ... (misma función reset) ... */ }
    function cargarMunicipios(departamentoId, municipioSeleccionadoId = null) { /* ... (misma función cargarMunicipios) ... */ }

    // --- Event Listeners ---
    if(deptoSelect){ deptoSelect.addEventListener('change', function() { cargarMunicipios(this.value); }); }

    // --- Carga Inicial ---
    const initialDeptoId = '{{ old('addr_departamento_id') }}';
    const initialMuniId = '{{ old('addr_municipio_id') }}';
     if (initialDeptoId) { cargarMunicipios(initialDeptoId, initialMuniId); }
 });
 </script>
@endpush