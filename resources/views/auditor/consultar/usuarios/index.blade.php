@extends('./Layouts.landing') {{-- O el layout del Auditor --}}
@section('title', 'Consulta de Usuarios')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Consulta de Usuarios</h1>
     <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.dashboard') }}">Consultar Datos</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>



    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros y Listado de Usuarios (Solo Lectura)
        </div>
        <div class="card-body">
            {{-- Formulario de Filtros (copiado/adaptado de admin) --}}
             <form method="GET" action="{{ route('auditor.consultar.usuarios.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 <div class="col-auto"> <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre, email..." value="{{ request('search') }}"> </div>
                 <div class="col-auto"> <select name="rol_id" class="form-select form-select-sm"> <option value="">-- Todos los Roles --</option> @foreach($roles as $rol) <option value="{{ $rol->id }}" {{ request('rol_id') == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option> @endforeach </select> </div>
                 <div class="col-auto"> <select name="activo" class="form-select form-select-sm"> <option value="">-- Cualquier Estado --</option> <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activo</option> <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivo</option> </select> </div>
                 <div class="col-auto"> <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button> <a href="{{ route('auditor.consultar.usuarios.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a> </div>
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
                            <th class="text-center">Acciones</th>
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
                            <td> @if($user->activo) <span class="badge bg-success">Activo</span> @else <span class="badge bg-danger">Inactivo</span> @endif </td>
                            <td class="text-center">
                                <a href="{{ route('auditor.consultar.usuarios.show', $user) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr> <td colspan="7" class="text-center">No se encontraron usuarios.</td> </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            @if ($users->hasPages()) <div class="mt-3">{{ $users->links() }}</div> @endif
        </div>
    </div>
</div>
@endsection