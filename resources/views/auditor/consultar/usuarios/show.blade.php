@extends('./Layouts.landing') {{-- O el layout del auditor --}}
{{-- Asegúrate de que el layout tenga la sección 'title' y 'content' --}}
{{-- Si no existe, puedes crearla o usar un layout diferente que sí la tenga. --}}

{{-- Título de la página --}}
@section('title', 'Detalle Usuario: ' . $user->nombre)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalle Usuario: {{ $user->nombre }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.dashboard') }}">Consultar Datos</a></li>
        <li class="breadcrumb-item"><a href="{{ route('auditor.consultar.usuarios.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Detalle #{{ $user->id }}</li>
    </ol>

     <div class="card mb-4">
        <div class="card-header"><i class="fas fa-user-circle me-1"></i> Información del Usuario</div>
        <div class="card-body">
             <dl class="row">
                <dt class="col-sm-3">ID:</dt><dd class="col-sm-9">{{ $user->id }}</dd>
                <dt class="col-sm-3">Nombre:</dt><dd class="col-sm-9">{{ $user->nombre }}</dd>
                <dt class="col-sm-3">Username:</dt><dd class="col-sm-9">{{ $user->username ?? '-' }}</dd>
                <dt class="col-sm-3">Email:</dt><dd class="col-sm-9">{{ $user->email }}</dd>
                <dt class="col-sm-3">Rol:</dt><dd class="col-sm-9">{{ $user->rol->nombre ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Estado:</dt><dd class="col-sm-9">@if($user->activo) <span class="badge bg-success">Activo</span> @else <span class="badge bg-danger">Inactivo</span> @endif</dd>
                <dt class="col-sm-3">Creado:</dt><dd class="col-sm-9">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</dd>
                <dt class="col-sm-3">Actualizado:</dt><dd class="col-sm-9">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-' }}</dd>

                {{-- Mostrar info del responsable si es rol Consulta --}}
                @if($user->rol_id == 4 && $user->responsable)
                    <dt class="col-sm-3 text-primary pt-2">Responsable Vinculado:</dt><dd class="col-sm-9 pt-2"></dd>
                    <dt class="col-sm-3">ID Responsable:</dt><dd class="col-sm-9">{{ $user->responsable_id }} <a href="{{ route('auditor.consultar.responsables.show', $user->responsable_id) }}" class="btn btn-link btn-sm p-0"><i class="fas fa-eye fa-xs"></i></a></dd>
                    <dt class="col-sm-3">Nombre Resp.:</dt><dd class="col-sm-9">{{ $user->responsable->nombreCompleto }}</dd>
                    <dt class="col-sm-3">DPI Resp.:</dt><dd class="col-sm-9">{{ $user->responsable->dpi }}</dd>
                @endif
            </dl>
        </div>
     </div>

     <div class="text-center mt-3">
         <a href="{{ route('auditor.consultar.usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Listado de Usuarios
        </a>
    </div>

</div>
@endsection