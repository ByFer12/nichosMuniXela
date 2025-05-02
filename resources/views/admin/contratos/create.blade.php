@extends('./Layouts.landing') 
@section('title', 'Crear Nuevo Contrato')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Nuevo Contrato de Ocupación</h1>
     <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.contratos.index') }}">Contratos</a></li>
        <li class="breadcrumb-item active">Crear</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-plus me-1"></i> Datos del Contrato</div>
        <div class="card-body">
            <form action="{{ route('admin.contratos.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row g-3">

                    {{-- Nicho Disponible --}}
                    <div class="col-md-6">
                         <label for="nicho_id" class="form-label">Nicho Disponible <span class="text-danger">*</span></label>
                         <select class="form-select @error('nicho_id') is-invalid @enderror" id="nicho_id" name="nicho_id" required>
                            <option value="" selected disabled>Seleccione un nicho...</option>
                            @foreach($nichosDisponibles as $nicho)
                                <option value="{{ $nicho->id }}" {{ old('nicho_id') == $nicho->id ? 'selected' : '' }}>
                                    {{ $nicho->codigo }} ({{ $nicho->calle ?? 'S/C' }} y {{ $nicho->avenida ?? 'S/A' }})  para {{ $nicho->tipoNicho->nombre ?? 'N/A' }}s
                                </option>
                            @endforeach
                         </select>
                         @error('nicho_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                         <div class="form-text">Solo se muestran los nichos marcados como "Disponibles".</div>
                    </div>
                    <div class="col-md-6"></div> {{-- Espacio --}}

                     {{-- Ocupante --}}
                    <div class="col-md-6">
                         <label for="ocupante_id" class="form-label">Ocupante (Persona Fallecida) <span class="text-danger">*</span></label>
                         <select class="form-select select2-enable @error('ocupante_id') is-invalid @enderror" id="ocupante_id" name="ocupante_id" required>
                            <option value="" selected disabled>Busque o seleccione...</option>
                            @foreach($ocupantes as $ocupante)
                                <option value="{{ $ocupante->id }}" {{ old('ocupante_id') == $ocupante->id ? 'selected' : '' }}>
                                    {{ $ocupante->apellidos }}, {{ $ocupante->nombres }} {{ $ocupante->dpi ? '('.$ocupante->dpi.')' : '' }}
                                </option>
                            @endforeach
                         </select>
                         @error('ocupante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                         {{-- Aquí podrías añadir un botón "+ Crear Ocupante" que abra un modal --}}
                    </div>

                     {{-- Responsable --}}
                     <div class="col-md-6">
                         <label for="responsable_id" class="form-label">Responsable del Contrato <span class="text-danger">*</span></label>
                         <select class="form-select select2-enable @error('responsable_id') is-invalid @enderror" id="responsable_id" name="responsable_id" required>
                             <option value="" selected disabled>Busque o seleccione...</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id }}" {{ old('responsable_id') == $responsable->id ? 'selected' : '' }}>
                                     {{ $responsable->apellidos }}, {{ $responsable->nombres }} ({{ $responsable->dpi }})
                                </option>
                            @endforeach
                         </select>
                          @error('responsable_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                          {{-- Aquí podrías añadir un botón "+ Crear Responsable" que abra un modal --}}
                    </div>

                    {{-- Fecha Inicio --}}
                     <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio del Contrato <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                        @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Las fechas de fin y gracia (6+1 años) se calcularán automáticamente.</div>
                    </div>

                     {{-- Costo Inicial --}}
                     <div class="col-md-6">
                        <label for="costo_inicial" class="form-label">Costo Inicial Q <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('costo_inicial') is-invalid @enderror" id="costo_inicial" name="costo_inicial" value="{{ old('costo_inicial', '600.00') }}" required> {{-- Valor por defecto --}}
                         @error('costo_inicial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>
                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.contratos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Contrato</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Si usas Select2 u otra librería para los selects --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
 {{-- Script validación Bootstrap --}}
 <script> /* ... (código validación) ... */ </script>
 {{-- Si usas Select2 --}}
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> {{-- Select2 requiere jQuery --}}
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <script>
 $(document).ready(function() {
    $('.select2-enable').select2({
        theme: 'bootstrap-5',
        placeholder: $(this).data('placeholder') || 'Busque o seleccione...'
    });
 });
 </script>
@endpush