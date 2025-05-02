@extends('./Layouts.landing')
@section('title', 'Gestión de: ' . $catalogoInfo['title'])

@section('content')
<div class="container-fluid px-4">
    {{-- Título y Breadcrumbs Dinámicos --}}
    <h1 class="mt-4">Gestión de: {{ $catalogoInfo['title'] }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.catalogos.dashboard') }}">Catálogos</a></li>
        <li class="breadcrumb-item active">{{ $catalogoInfo['title'] }}</li>
    </ol>



    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-1"></i>Items de {{ $catalogoInfo['title'] }}</span>
            {{-- Botón Crear Dinámico --}}
            <a href="{{ route('admin.catalogos.create', $catalogoInfo['slug']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Crear Nuevo Item
            </a>
        </div>
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                {{-- Botón Editar Dinámico --}}
                                <a href="{{ route('admin.catalogos.edit', [$catalogoInfo['slug'], $item->id]) }}" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Botón Eliminar Dinámico --}}
                                <form action="{{ route('admin.catalogos.destroy', [$catalogoInfo['slug'], $item->id]) }}" method="POST" class="d-inline needs-confirmation" data-confirm-message="¿Estás seguro de eliminar '{{ $item->nombre }}'? Esta acción no se puede deshacer y podría afectar registros existentes si está en uso.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr> <td colspan="4" class="text-center">No hay items en este catálogo.</td> </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>
              {{-- Paginación --}}
            @if ($items->hasPages()) <div class="mt-3">{{ $items->links() }}</div> @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script para confirmación antes de eliminar --}}
<script> /* ... (mismo script needs-confirmation que en usuarios/index) ... */ </script>
@endpush