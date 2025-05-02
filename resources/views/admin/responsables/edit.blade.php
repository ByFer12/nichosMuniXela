@extends('./Layouts.landing')

@section('title', 'Editar Responsable: ' . $responsable->nombreCompleto) {{-- Asume accesor --}}

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Responsable</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.responsables.index') }}">Responsables</a></li>
        <li class="breadcrumb-item active">Editar: {{ $responsable->nombreCompleto }}</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-edit me-1"></i> Datos del Responsable</div>
        <div class="card-body">
            <form action="{{ route('admin.responsables.update', $responsable) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                {{-- Datos Personales --}}
                <h5 class="mb-3 text-primary">Información Personal</h5>
                <div class="row g-3 mb-4">
                     {{-- Inputs: nombres, apellidos, dpi, telefono, correo_electronico --}}
                     {{-- Prellenados con old() y $responsable->... --}}
                     <div class="col-md-6">
                        <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombres') is-invalid @enderror" id="nombres" name="nombres" value="{{ old('nombres', $responsable->nombres) }}" required>
                        @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                     </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellidos') is-invalid @enderror" id="apellidos" name="apellidos" value="{{ old('apellidos', $responsable->apellidos) }}" required>
                        @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="dpi" class="form-label">DPI <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('dpi') is-invalid @enderror" id="dpi" name="dpi" value="{{ old('dpi', $responsable->dpi) }}" required>
                         @error('dpi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $responsable->telefono) }}">
                         @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control @error('correo_electronico') is-invalid @enderror" id="correo_electronico" name="correo_electronico" value="{{ old('correo_electronico', $responsable->correo_electronico) }}">
                         @error('correo_electronico')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Dirección --}}
                <h5 class="mb-3 text-primary">Dirección</h5>
                 <div class="row g-3">
                     {{-- Reutilizar los mismos campos de dirección que en ocupantes/edit.blade.php --}}
                     {{-- Prellenados con old() y $responsable->direccion->... ?? '' --}}
                     @php
                         $direccionActual = $responsable->direccion;
                         $municipioActual = $direccionActual ? $direccionActual->municipio : null;
                         $departamentoActual = $municipioActual ? $municipioActual->departamento : null;
                     @endphp
                      <div class="col-md-6">
                        <label for="addr_calle_numero" class="form-label">Calle y Número</label>
                        <input type="text" class="form-control @error('addr_calle_numero') is-invalid @enderror" id="addr_calle_numero" name="addr_calle_numero" value="{{ old('addr_calle_numero', $direccionActual->calle_numero ?? '') }}">
                         @error('addr_calle_numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                     <div class="col-md-6">
                        <label for="addr_colonia_barrio" class="form-label">Colonia / Barrio / Zona</label>
                        <input type="text" class="form-control @error('addr_colonia_barrio') is-invalid @enderror" id="addr_colonia_barrio" name="addr_colonia_barrio" value="{{ old('addr_colonia_barrio', $direccionActual->colonia_barrio ?? '') }}">
                         @error('addr_colonia_barrio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                     </div>
                     <div class="col-md-4">
                         <label for="addr_codigo_postal" class="form-label">Código Postal</label>
                         <input type="text" class="form-control @error('addr_codigo_postal') is-invalid @enderror" id="addr_codigo_postal" name="addr_codigo_postal" value="{{ old('addr_codigo_postal', $direccionActual->codigo_postal ?? '') }}">
                         @error('addr_codigo_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                     </div>
                     {{-- Departamento --}}
                     <div class="col-md-4">
                         <label for="addr_departamento_id" class="form-label">Departamento</label>
                         <select class="form-select @error('addr_departamento_id') is-invalid @enderror" id="addr_departamento_id" name="addr_departamento_id">
                             <option value="">Seleccione...</option>
                             @foreach($departamentos ?? [] as $depto)
                                 <option value="{{ $depto->id }}" {{ old('addr_departamento_id', $departamentoActual->id ?? '') == $depto->id ? 'selected' : '' }}>
                                     {{ $depto->nombre }}
                                 </option>
                             @endforeach
                         </select>
                         @error('addr_departamento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                     </div>
                     {{-- Municipio --}}
                     <div class="col-md-4">
                         <label for="addr_municipio_id" class="form-label">Municipio</label>
                         <select class="form-select @error('addr_municipio_id') is-invalid @enderror" id="addr_municipio_id" name="addr_municipio_id" {{ $municipios->isEmpty() && !old('addr_municipio_id') ? 'disabled' : '' }}>
                             <option value="">Seleccione departamento...</option>
                              @foreach($municipios as $mun) {{-- $municipios viene del controlador --}}
                                 <option value="{{ $mun->id }}" {{ old('addr_municipio_id', $municipioActual->id ?? '') == $mun->id ? 'selected' : '' }}>
                                     {{ $mun->nombre }}
                                 </option>
                              @endforeach
                         </select>
                          @error('addr_municipio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                     </div>
                    {{-- Referencia --}}
                    <div class="col-12">
                        <label for="addr_referencia" class="form-label">Referencia Adicional</label>
                        <textarea class="form-control @error('addr_referencia') is-invalid @enderror" id="addr_referencia" name="addr_referencia" rows="2">{{ old('addr_referencia', $direccionActual->referencia_adicional ?? '') }}</textarea>
                         @error('addr_referencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                 </div>

                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.responsables.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Reutilizar el JS de selects dependientes --}}
@push('scripts')
 <script> /* ... Código validación Bootstrap ... */ </script>
 <script> /* ... Código selects dependientes ... */ </script>
@endpush