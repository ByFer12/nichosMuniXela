@extends('./Layouts.landing')
@section('title', 'Consultar Catálogo: ' . $catalogoInfo['title'])

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Consultar Catálogo: {{ $catalogoInfo['title'] }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.dashboard') }}">Consultar Datos</a></li>
        <li class="breadcrumb-item active">{{ $catalogoInfo['title'] }}</li>
    </ol>



    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-tags me-1"></i> Items del Catálogo (Solo Lectura)
        </div>
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @empty
                        <tr> <td colspan="3" class="text-center">No hay items en este catálogo.</td> </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>
              {{-- Paginación --}}
            @if ($items->hasPages()) <div class="mt-3">{{ $items->links() }}</div> @endif
        </div>
         <div class="card-footer text-end">
             <a href="{{ route('auditor.consultar.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list me-1"></i> Ver Otros Catálogos/Datos
            </a>
        </div>
    </div>
</div>
@endsection