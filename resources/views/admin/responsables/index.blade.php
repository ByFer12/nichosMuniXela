@extends('./Layouts.landing')
@section('title', 'Gestión de Responsables')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Responsables</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Responsables</li>
    </ol>



    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-address-book me-1"></i>Lista de Responsables
            {{-- NO hay botón de Crear --}}
        </div>
        <div class="card-body">
            {{-- Formulario de Filtros --}}
            <form method="GET" action="{{ route('admin.responsables.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 <div class="col-auto">
                     <input type="text" name="search" class="form-control form-control-sm" placeholder="Nombre, Apellido, DPI o Email..." value="{{ request('search') }}">
                 </div>
                 {{-- Añadir más filtros si es necesario --}}
                 <div class="col-auto">
                     <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
                      <a href="{{ route('admin.responsables.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                 </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>DPI</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                     <tbody>
                        @forelse($responsables as $responsable)
                        <tr>
                            <td>{{ $responsable->id }}</td>
                            <td>{{ $responsable->apellidos }}, {{ $responsable->nombres }}</td>
                            <td>{{ $responsable->dpi }}</td>
                            <td>{{ $responsable->telefono ?? '-' }}</td>
                            <td>{{ $responsable->correo_electronico ?? '-' }}</td>
                            <td>{{ $responsable->direccion ? $responsable->direccion->resumen : '-' }}</td> {{-- Asume accesor 'resumen' --}}
                            <td>
                                <a href="{{ route('admin.responsables.edit', $responsable) }}" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Aquí botón para ver contratos asociados --}}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No se encontraron responsables.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            @if ($responsables->hasPages()) <div class="mt-3">{{ $responsables->links() }}</div> @endif
        </div>
    </div>
</div>
@endsection