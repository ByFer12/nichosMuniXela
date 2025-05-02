@extends('./Layouts.landing') 
@section('title', 'Editar Contrato #' . $contrato->id)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Contrato</h1>
     <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.contratos.index') }}">Contratos</a></li>
        <li class="breadcrumb-item active">Editar #{{ $contrato->id }}</li>
    </ol>


    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-edit me-1"></i> Datos del Contrato</div>
        <div class="card-body">
            <form action="{{ route('admin.contratos.update', $contrato) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="alert alert-info small">
                    <strong>Información Original:</strong><br>
                    Nicho: {{ $contrato->nicho->codigo ?? 'N/A' }} <br>
                    Ocupante: {{ $contrato->ocupante->nombreCompleto ?? $contrato->ocupante->dpi ?? 'N/A' }}
                </div>

                <div class="row g-3">

                     {{-- Responsable (Editable) --}}
                     <div class="col-md-6">
                         <label for="responsable_id" class="form-label">Responsable del Contrato <span class="text-danger">*</span></label>
                         <select class="form-select select2-enable @error('responsable_id') is-invalid @enderror" id="responsable_id" name="responsable_id" required>
                             <option value="" disabled>Busque o seleccione...</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id }}" {{ old('responsable_id', $contrato->responsable_id) == $responsable->id ? 'selected' : '' }}>
                                     {{ $responsable->apellidos }}, {{ $responsable->nombres }} ({{ $responsable->dpi }})
                                </option>
                            @endforeach
                         </select>
                          @error('responsable_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Fecha Inicio (Editable, recalcula fin) --}}
                     <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $contrato->fecha_inicio) }}" required>
                        @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                         <div class="form-text">Cambiar esta fecha recalculará las fechas de fin y gracia.</div>
                    </div>

                     {{-- Costo Inicial (Editable) --}}
                     <div class="col-md-6">
                        <label for="costo_inicial" class="form-label">Costo Inicial Q <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('costo_inicial') is-invalid @enderror" id="costo_inicial" name="costo_inicial" value="{{ old('costo_inicial', $contrato->costo_inicial) }}" required>
                         @error('costo_inicial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Estado Activo (Editable) --}}
                     <div class="col-md-6">
                        <label for="activo" class="form-label">Estado del Contrato <span class="text-danger">*</span></label>
                        <select class="form-select @error('activo') is-invalid @enderror" id="activo" name="activo" required>
                            <option value="1" {{ old('activo', $contrato->activo) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('activo', $contrato->activo) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                         @error('activo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text text-danger">Precaución: Marcar como inactivo no libera el nicho automáticamente.</div>
                    </div>

                    {{-- Campos No Editables (Mostrar Informativo) --}}
                    <div class="col-md-6">
                        <label class="form-label">Fecha Fin Original (Calculada)</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($contrato->fecha_fin_original)->format('d/m/Y') }}" readonly disabled>
                    </div>
                     <div class="col-md-6">
                        <label class="form-label">Fecha Fin Gracia (Calculada)</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($contrato->fecha_fin_gracia)->format('d/m/Y') }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                         <label class="form-label">Contrato Anterior (Si aplica)</label>
                         <input type="text" class="form-control" value="{{ $contrato->contrato_anterior_id ?? 'N/A' }}" readonly disabled>
                    </div>
                     <div class="col-md-6">
                        <label class="form-label">¿Renovado?</label>
                        <input type="text" class="form-control" value="{{ $contrato->renovado ? 'Sí' : 'No' }}" readonly disabled>
                    </div>

                </div>
                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.contratos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Los mismos push de styles y scripts que en create (para Select2) --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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