@extends('./Layouts.landing')
@section('title', 'Gestión de Ocupantes')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Ocupantes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Ocupantes</li>
    </ol>

 
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-user-clock me-1"></i>Lista de Ocupantes (Fallecidos)</span>
            <a href="{{ route('admin.ocupantes.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Registrar Ocupante
            </a>
        </div>
        <div class="card-body">
            {{-- Formulario de Filtros --}}
            <form method="GET" action="{{ route('admin.ocupantes.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 <div class="col-auto">
                     <input type="text" name="search" class="form-control form-control-sm" placeholder="Nombre, Apellido o DPI..." value="{{ request('search') }}">
                 </div>
                 <div class="col-auto">
                     <select name="genero" class="form-select form-select-sm">
                         <option value="">-- Género --</option>
                         @foreach($generos as $genero)
                             <option value="{{ $genero->id }}" {{ request('genero') == $genero->id ? 'selected' : '' }}>{{ $genero->nombre }}</option>
                         @endforeach
                     </select>
                 </div>
                 <div class="col-auto">
                      <label for="fallecimiento_desde" class="col-form-label col-form-label-sm">Fallec. Desde:</label>
                 </div>
                  <div class="col-auto">
                     <input type="date" name="fallecimiento_desde" id="fallecimiento_desde" class="form-control form-control-sm" value="{{ request('fallecimiento_desde') }}">
                 </div>
                 <div class="col-auto">
                       <label for="fallecimiento_hasta" class="col-form-label col-form-label-sm">Fallec. Hasta:</label>
                 </div>
                 <div class="col-auto">
                     <input type="date" name="fallecimiento_hasta" id="fallecimiento_hasta" class="form-control form-control-sm" value="{{ request('fallecimiento_hasta') }}">
                 </div>
                 <div class="col-auto">
                     <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
                      <a href="{{ route('admin.ocupantes.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                 </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                     <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>DPI</th>
                            <th>F. Nacimiento</th>
                            <th>F. Fallecimiento</th>
                            <th>Género</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                     <tbody>
                         @forelse($ocupantes as $ocupante)
                        <tr>
                            <td>{{ $ocupante->id }}</td>
                            <td>{{ $ocupante->apellidos }}, {{ $ocupante->nombres }}</td>
                            <td>{{ $ocupante->dpi ?? '-' }}</td>
                            <td>{{ $ocupante->fecha_nacimiento ? \Carbon\Carbon::parse($ocupante->fecha_nacimiento)->format('d/m/Y') : '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($ocupante->fecha_fallecimiento)->format('d/m/Y') }}</td>
                            <td>{{ $ocupante->tipoGenero->nombre ?? '-' }}</td>
                            <td>{{ $ocupante->direccion ? $ocupante->direccion->resumen : '-' }}</td> {{-- Asume accesor 'resumen' en Direccion --}}
                            <td>
                                <a href="{{ route('admin.ocupantes.edit', $ocupante) }}" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Aquí botón para "Declarar Histórico" si lo implementas --}}
                            </td>
                        </tr>
                         @empty
                         <tr><td colspan="8" class="text-center">No se encontraron ocupantes.</td></tr>
                         @endforelse
                    </tbody>
                </table>
            </div>
             {{-- Paginación --}}
            @if ($ocupantes->hasPages()) <div class="mt-3">{{ $ocupantes->links() }}</div> @endif
        </div>
    </div>
</div>
@endsection