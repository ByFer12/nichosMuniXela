@extends('./Layouts.landing') {{-- O el layout que use el Auditor --}}
@section('title', 'Consulta de Ocupantes')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Consulta de Ocupantes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item active">Ocupantes</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros y Listado de Ocupantes (Solo Lectura)
             {{-- SIN botón de Crear --}}
        </div>
        <div class="card-body">
            {{-- Formulario de Filtros (Copiar/Adaptar de admin/ocupantes/index) --}}
            <form method="GET" action="{{ route('auditor.consultar.ocupantes.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 <div class="col-auto"> <input type="text" name="search" class="form-control form-control-sm" placeholder="Nombre, Apellido o DPI..." value="{{ request('search') }}"> </div>
                 <div class="col-auto"> <select name="genero" class="form-select form-select-sm"> <option value="">-- Género --</option> @foreach($generos as $genero) <option value="{{ $genero->id }}" {{ request('genero') == $genero->id ? 'selected' : '' }}>{{ $genero->nombre }}</option> @endforeach </select> </div>
                 {{-- ... otros filtros si los tenías ... --}}
                 <div class="col-auto"> <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button> <a href="{{ route('auditor.consultar.ocupantes.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a> </div>
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
                            <th class="text-center">Acciones</th>
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
                            <td>{{ $ocupante->direccion ? $ocupante->direccion->resumen : '-' }}</td>
                            <td class="text-center">
                                {{-- Botón para ver detalles --}}
                                <a href="{{ route('auditor.consultar.ocupantes.show', $ocupante) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
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