@extends('./Layouts.landing')
@section('title', 'Gestión de Catálogos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Catálogos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Catálogos</li>
    </ol>



    <p>Seleccione el catálogo que desea administrar:</p>

    <div class="list-group">
        @forelse($listaCatalogos as $catalogo)
            <a href="{{ route('admin.catalogos.index', $catalogo['slug']) }}" class="list-group-item list-group-item-action">
                <i class="fas fa-tag me-2"></i> {{ $catalogo['title'] }}
            </a>
        @empty
            <p class="text-muted">No hay catálogos configurados para administrar.</p>
        @endforelse
    </div>

</div>
@endsection