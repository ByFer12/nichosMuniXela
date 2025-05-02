@extends('./Layouts.landing') {{-- Asume tu layout de admin --}}
@section('title', 'Registrar Nuevo Nicho')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Registrar Nuevo Nicho</h1>
     <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.nichos.index') }}">Nichos</a></li>
        <li class="breadcrumb-item active">Registrar</li>
    </ol>


    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-plus me-1"></i> Datos del Nicho</div>
        <div class="card-body">
            <form action="{{ route('admin.nichos.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label">Código Único <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                         @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                     <div class="col-md-6">
                         <label for="tipo_nicho_id" class="form-label">Tipo de Nicho <span class="text-danger">*</span></label>
                         <select class="form-select @error('tipo_nicho_id') is-invalid @enderror" id="tipo_nicho_id" name="tipo_nicho_id" required>
                            <option value="" selected disabled>Seleccione...</option>
                             @foreach($tiposNicho as $id => $nombre)
                                <option value="{{ $id }}" {{ old('tipo_nicho_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                         </select>
                         @error('tipo_nicho_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                     <div class="col-md-6">
                        <label for="calle" class="form-label">Calle</label>
                        <input type="text" class="form-control @error('calle') is-invalid @enderror" id="calle" name="calle" value="{{ old('calle') }}">
                         @error('calle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                     <div class="col-md-6">
                        <label for="avenida" class="form-label">Avenida</label>
                        <input type="text" class="form-control @error('avenida') is-invalid @enderror" id="avenida" name="avenida" value="{{ old('avenida') }}">
                         @error('avenida')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                     <div class="col-md-6">
                        <label for="es_historico" class="form-label">¿Es Histórico? <span class="text-danger">*</span></label>
                        <select class="form-select @error('es_historico') is-invalid @enderror" id="es_historico" name="es_historico" required>
                            <option value="0" {{ old('es_historico', '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('es_historico') == '1' ? 'selected' : '' }}>Sí</option>
                        </select>
                         @error('es_historico')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                      <div class="col-12">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                         @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                     <div class="col-12 form-text">
                         El estado inicial del nicho se establecerá como "Disponible".
                     </div>
                </div>
                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.nichos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Nicho</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection