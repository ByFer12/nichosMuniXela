@extends('./Layouts.landing') {{-- Asume que tienes un layout para admin --}}

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>

    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-users me-1"></i>Lista de Usuarios</span>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Crear Usuario
            </a>
        </div>
        <div class="card-body">
            {{-- Formulario de Filtros (Opcional) --}}
             <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-3 mb-4">
                 <div class="col-md-4">
                     <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre, email..." value="{{ request('search') }}">
                 </div>
                 <div class="col-md-3">
                     <select name="rol_id" class="form-select form-select-sm">
                         <option value="">-- Todos los Roles --</option>
                         @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ request('rol_id') == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                         @endforeach
                     </select>
                 </div>
                 <div class="col-md-3">
                      <select name="activo" class="form-select form-select-sm">
                         <option value="">-- Cualquier Estado --</option>
                         <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activo</option>
                         <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivo</option>
                     </select>
                 </div>
                 <div class="col-md-2">
                     <button type="submit" class="btn btn-secondary btn-sm w-100">Filtrar</button>
                 </div>
             </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->nombre }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username ?? '-' }}</td>
                            <td>{{ $user->rol->nombre ?? 'N/A' }}</td>
                            <td>
                                @if($user->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.usuarios.edit', $user) }}" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Formulario para Activar/Desactivar --}}
                                <form action="{{ route('admin.usuarios.toggleStatus', $user) }}" method="POST" class="d-inline needs-confirmation" data-confirm-message="¿Estás seguro de {{ $user->activo ? 'DESACTIVAR' : 'ACTIVAR' }} este usuario?">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-{{ $user->activo ? 'danger' : 'success' }} btn-sm" title="{{ $user->activo ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $user->activo ? 'toggle-off' : 'toggle-on' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron usuarios.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($users->hasPages())
            <div class="mt-3">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script para confirmación antes de enviar forms --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const confirmationForms = document.querySelectorAll('.needs-confirmation');
    confirmationForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            const message = this.getAttribute('data-confirm-message') || '¿Estás seguro de realizar esta acción?';
            if (!confirm(message)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
});
</script>
@endpush