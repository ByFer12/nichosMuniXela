@extends('./Layouts.landing') {{-- O el layout del Auditor --}}
@section('title', 'Consultar Datos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Consultar Datos del Sistema</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('auditor.dashboard') }}">Dashboard Auditor</a></li>
        <li class="breadcrumb-item active">Consultar Datos</li>
    </ol>

   

    <p>Seleccione el área de datos que desea consultar en modo de solo lectura:</p>

    <div class="row g-3">
        {{-- Enlaces a las vistas index de consulta --}}
        <div class="col-md-4"><a href="{{ route('auditor.consultar.nichos.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-archive fa-fw me-2"></i>Nichos</a></div>
        <div class="col-md-4"><a href="{{ route('auditor.consultar.contratos.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-file-contract fa-fw me-2"></i>Contratos</a></div>
        <div class="col-md-4"><a href="{{ route('auditor.consultar.pagos.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-money-check-alt fa-fw me-2"></i>Pagos</a></div>
        <div class="col-md-4"><a href="{{ route('auditor.consultar.ocupantes.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-user-clock fa-fw me-2"></i>Ocupantes</a></div>
        <div class="col-md-4"><a href="{{ route('auditor.consultar.responsables.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-address-book fa-fw me-2"></i>Responsables</a></div>
        <div class="col-md-4"><a href="{{ route('auditor.consultar.usuarios.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-users fa-fw me-2"></i>Usuarios</a></div>

         {{-- Enlaces a Catálogos (Ejemplo) --}}
         <div class="col-12 mt-3"><hr><h6>Catálogos del Sistema:</h6></div>
         <div class="col-md-4"><a href="{{ route('auditor.consultar.catalogos.index', ['catalogoSlug' => 'tipos-nicho']) }}" class="list-group-item list-group-item-action list-group-item-light"><i class="fas fa-tag fa-fw me-2"></i>Tipos de Nicho</a></div>
         <div class="col-md-4"><a href="{{ route('auditor.consultar.catalogos.index', ['catalogoSlug' => 'estados-nicho']) }}" class="list-group-item list-group-item-action list-group-item-light"><i class="fas fa-tag fa-fw me-2"></i>Estados de Nicho</a></div>
         <div class="col-md-4"><a href="{{ route('auditor.consultar.catalogos.index', ['catalogoSlug' => 'tipos-genero']) }}" class="list-group-item list-group-item-action list-group-item-light"><i class="fas fa-tag fa-fw me-2"></i>Tipos de Género</a></div>
         <div class="col-md-4"><a href="{{ route('auditor.consultar.catalogos.index', ['catalogoSlug' => 'estados-pago']) }}" class="list-group-item list-group-item-action list-group-item-light"><i class="fas fa-tag fa-fw me-2"></i>Estados de Pago</a></div>
         <div class="col-md-4"><a href="{{ route('auditor.consultar.catalogos.index', ['catalogoSlug' => 'estados-exhumacion']) }}" class="list-group-item list-group-item-action list-group-item-light"><i class="fas fa-tag fa-fw me-2"></i>Estados Exhumación</a></div>
         <div class="col-md-4"><a href="{{ route('auditor.consultar.catalogos.index', ['catalogoSlug' => 'destinos-restos']) }}" class="list-group-item list-group-item-action list-group-item-light"><i class="fas fa-tag fa-fw me-2"></i>Destinos de Restos</a></div>

    </div>

</div>
@endsection