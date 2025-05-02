@extends('./Layouts.landing')
@section('title', 'Editar Item - ' . $catalogoInfo['title'])

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Item en: {{ $catalogoInfo['title'] }}</h1>
    <ol class="breadcrumb mb-4">
         <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.catalogos.dashboard') }}">Catálogos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.catalogos.index', $catalogoInfo['slug']) }}">{{ $catalogoInfo['title'] }}</a></li>
        <li class="breadcrumb-item active">Editar #{{ $item->id }}</li>
    </ol>

     <div class="card mb-4">
         <div class="card-header"><i class="fas fa-edit me-1"></i> Editar Item</div>
         <div class="card-body">
            {{-- Formulario Dinámico --}}
             <form action="{{ route('admin.catalogos.update', [$catalogoInfo['slug'], $item->id]) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                 <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $item->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                 </div>

                 <div class="mt-4 text-end">
                     <a href="{{ route('admin.catalogos.index', $catalogoInfo['slug']) }}" class="btn btn-secondary me-2">Cancelar</a>
                     <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
         </div>
    </div>
</div>
@endsection