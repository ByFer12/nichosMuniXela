@extends('./Layouts.landing')
@section('title', 'Editar Nicho: ' . $nicho->codigo)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Nicho</h1>
     <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.nichos.index') }}">Nichos</a></li>
        <li class="breadcrumb-item active">Editar: {{ $nicho->codigo }}</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-edit me-1"></i> Datos del Nicho</div>
        <div class="card-body">
            <form action="{{ route('admin.nichos.update', $nicho) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label">Código Único <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo', $nicho->codigo) }}" required>
                         @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                     <div class="col-md-6">
                         <label for="tipo_nicho_id" class="form-label">Tipo de Nicho <span class="text-danger">*</span></label>
                         <select class="form-select @error('tipo_nicho_id') is-invalid @enderror" id="tipo_nicho_id" name="tipo_nicho_id" required>
                            <option value="" disabled>Seleccione...</option>
                             @foreach($tiposNicho as $id => $nombre)
                                <option value="{{ $id }}" {{ old('tipo_nicho_id', $nicho->tipo_nicho_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                         </select>
                         @error('tipo_nicho_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="calle" class="form-label">Calle</label>
                        <input type="text" class="form-control @error('calle') is-invalid @enderror" id="calle" name="calle" value="{{ old('calle', $nicho->calle) }}">
                         @error('calle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                     <div class="col-md-6">
                        <label for="avenida" class="form-label">Avenida</label>
                        <input type="text" class="form-control @error('avenida') is-invalid @enderror" id="avenida" name="avenida" value="{{ old('avenida', $nicho->avenida) }}">
                         @error('avenida')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="estado_nicho_id" class="form-label">Estado Actual <span class="text-danger">*</span></label>
                         <select class="form-select @error('estado_nicho_id') is-invalid @enderror" id="estado_nicho_id" name="estado_nicho_id" required aria-describedby="estadoHelp">
                             <option value="" disabled>Seleccione...</option>
                             @foreach($estadosNicho as $id => $nombre)
                                <option value="{{ $id }}" {{ old('estado_nicho_id', $nicho->estado_nicho_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                         </select>
                         <div id="estadoHelp" class="form-text text-warning">Precaución: Cambiar el estado manualmente aquí puede interferir con los procesos automáticos de Contratos y Exhumaciones.</div>
                         @error('estado_nicho_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                     <div class="col-md-6">
                        <label for="es_historico" class="form-label">¿Es Histórico? <span class="text-danger">*</span></label>
                        <select class="form-select @error('es_historico') is-invalid @enderror" id="es_historico" name="es_historico" required>
                            <option value="0" {{ old('es_historico', $nicho->es_historico) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('es_historico', $nicho->es_historico) == 1 ? 'selected' : '' }}>Sí</option>
                        </select>
                         @error('es_historico')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                      <div class="col-12">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $nicho->observaciones) }}</textarea>
                         @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>
                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.nichos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection