@extends('./Layouts.landing')
@section('title', 'Editar Usuario: ' . $user->nombre)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar: {{ $user->nombre }}</li>
    </ol>


    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i> Datos del Usuario
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


<div class="col-md-4">
<label for="resp_dpi" class="form-label">DPI Responsable</label>
<span class="text-danger responsable-required-indicator" style="display: none;">*</span>
{{-- ID: resp_dpi --}}
<input type="text" class="form-control @error('resp_dpi') is-invalid @enderror" id="resp_dpi" name="resp_dpi" value="{{ old('resp_dpi') }}">
{{-- Mostrar error específico del campo --}}
@error('resp_dpi')
   <div class="invalid-feedback d-block">{{-- d-block para forzar visibilidad --}}
       {{ $message }}
   </div>
@enderror
</div>
        <div class="card-body">
            <form action="{{ route('admin.usuarios.update', $user) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- Nombre --}}
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $user->nombre) }}" required>
                    </div>

                    {{-- Username --}}
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username (Opcional)</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}">
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                     {{-- Rol --}}
                    <div class="col-md-6">
                         <label for="rol_id" class="form-label">Rol <span class="text-danger">*</span></label>
                         <select class="form-select" id="rol_id" name="rol_id" required>
                            <option value="" disabled>Seleccione un rol...</option>
                            @foreach($roles as $id => $nombre)
                                <option value="{{ $id }}" {{ old('rol_id', $user->rol_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                         </select>
                    </div>

                     {{-- Estado Activo --}}
                     <div class="col-md-6">
                        <label for="activo" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="activo" name="activo" required>
                            <option value="1" {{ old('activo', $user->activo) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('activo', $user->activo) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                     {{-- Vincular Responsable (Opcional, si rol es Consulta) --}}
                      {{-- <div class="col-md-6" id="campoResponsableEdit" style="display: {{ old('rol_id', $user->rol_id) == 4 ? 'block' : 'none' }};">
                          <label for="responsable_id" class="form-label">Responsable Asociado (ID)</label>
                          <input type="number" class="form-control" id="responsable_id" name="responsable_id" value="{{ old('responsable_id', $user->responsable_id) }}">
                           <div class="form-text">Necesario si el rol es "Consulta".</div>
                      </div> --}}

                    {{-- Nueva Contraseña (Opcional) --}}
                    <div class="col-12 mt-4">
                        <p class="text-muted"><strong>Cambiar Contraseña (dejar en blanco para no modificar)</strong></p>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelpEdit">
                        <div id="passwordHelpEdit" class="form-text">Mínimo 8 caracteres, letras y números.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                </div> {{-- Fin row --}}

                <div class="mt-4 text-end">
                     <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- @push('scripts')
 <script>
 // Script para mostrar/ocultar campo responsable según rol (si lo implementas)
 document.getElementById('rol_id')?.addEventListener('change', function() {
    document.getElementById('campoResponsableEdit').style.display = this.value == '4' ? 'block' : 'none';
    // document.getElementById('responsable_id').required = this.value == '4'; // Cuidado con required aquí
 });
 </script>
 @endpush --}}