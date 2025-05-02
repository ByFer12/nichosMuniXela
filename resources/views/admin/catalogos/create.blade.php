@extends('./Layouts.landing')
@section('title', 'Crear Item - ' . $catalogoInfo['title'])

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Nuevo Item en: {{ $catalogoInfo['title'] }}</h1>
     <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.catalogos.dashboard') }}">Catálogos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.catalogos.index', $catalogoInfo['slug']) }}">{{ $catalogoInfo['title'] }}</a></li>
        <li class="breadcrumb-item active">Crear</li>
    </ol>

    <div class="card mb-4">
         <div class="card-header"><i class="fas fa-plus me-1"></i> Nuevo Item</div>
         <div class="card-body">
            {{-- Formulario Dinámico --}}
            <form action="{{ route('admin.catalogos.store', $catalogoInfo['slug']) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                 <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                     @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                 </div>
                {{-- Podrías añadir campo 'descripcion' aquí si tus modelos la tuvieran --}}

                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.catalogos.index', $catalogoInfo['slug']) }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Item</button>
                </div>
            </form>
         </div>
    </div>
</div>
@endsection