@extends('./Layouts.landing') {{-- Usa tu layout principal --}}

@section('title', 'Panel de Administración - Cementerio')

@section('content')
<div class="container-fluid mt-4 mb-5" style="max-width: 1320px;"> {{-- Un poco más ancho para admin --}}

    {{-- Encabezado y Bienvenida --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded">
        <div>
            <h2 class="h4 mb-0">Panel de Administración</h2>
            @php
                // Obtener nombre del admin
                $nombreAdmin = Auth::user()->nombre ?? Auth::user()->username ?? 'Administrador';
            @endphp
            <p class="mb-0 text-muted">Bienvenido, {{ $nombreAdmin }}!</p>
        </div>
        <div>
            {{-- Botón/Enlace de Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

     {{-- (Opcional) Sección de Estadísticas Rápidas (Implementar más adelante) --}}
    {{-- <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card text-center"><div class="card-body"><h5 class="card-title">Nichos Ocupados</h5><p class="card-text fs-4">--</p></div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body"><h5 class="card-title">Nichos Disponibles</h5><p class="card-text fs-4">--</p></div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body"><h5 class="card-title">Contratos por Vencer (<30d)</h5><p class="card-text fs-4">--</p></div></div></div>
        <div class="col-md-3"><div class="card text-center text-danger"><div class="card-body"><h5 class="card-title">Solicitudes Pendientes</h5><p class="card-text fs-4">--</p></div></div></div>
    </div> --}}


    {{-- Sección Principal con Opciones de Gestión (Usando Cards) --}}
    <div class="row g-4">

{{-- Card: Gestión de Usuarios --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-users-cog me-2 text-primary"></i>Gestión de Usuarios</h5>
            <p class="card-text text-muted">Crear, ver, editar y eliminar cuentas de usuario y sus roles (Admin, Ayudante, Auditor, Consulta).</p>
            {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-primary mt-auto">Administrar Usuarios</a>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>
            {{-- Card: Asignacion de tareas a ayudante --}}
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><i class="fas fa-tasks me-2 text-primary"></i>Asignar tarea a ayudante</h5>
                        <p class="card-text text-muted">Esta opcion servira para asignar y desasignar tareas a usuarios ayudantes para que agilices los procesos </p>
                         {{-- Placeholder --}}
                        <a href="#" class="btn btn-info mt-auto disabled">Asignar Tarea</a>
                    </div>
                </div>
            </div>
        {{-- Card: Gestión de Nichos --}}
{{-- Card: Gestión de Nichos --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-archive me-2 text-info"></i>Gestión de Nichos</h5>
            <p class="card-text text-muted">Registrar, consultar, editar y gestionar el estado de los nichos físicos (Adulto/Niño, ubicación, histórico).</p>
             {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('admin.nichos.index') }}" class="btn btn-info mt-auto">Administrar Nichos</a>
             {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>

       {{-- Card: Gestión de Contratos --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-file-contract me-2 text-success"></i>Gestión de Contratos</h5>
            <p class="card-text text-muted">Crear nuevos contratos de ocupación, ver existentes, gestionar renovaciones y estado general.</p>
            {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('admin.contratos.index') }}" class="btn btn-success mt-auto">Administrar Contratos</a>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>

   {{-- Card: Gestión de Solicitudes (Unificado) --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            {{-- Puedes usar un icono más genérico como 'inbox' o 'tasks' --}}
            <h5 class="card-title"><i class="fas fa-inbox me-2 text-primary"></i>Gestión de Solicitudes</h5>
            <p class="card-text text-muted">Revisar y procesar solicitudes pendientes de boletas y exhumaciones enviadas por los usuarios.</p>
            {{-- Enlazar a la nueva ruta de admin --}}
            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-primary mt-auto">Administrar Solicitudes</a>
        </div>
    </div>
</div>


{{-- Card: Gestión de Ocupantes --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-user-clock me-2 text-secondary"></i>Gestión de Ocupantes</h5>
            <p class="card-text text-muted">Registrar, consultar y editar la información de las personas fallecidas (incluyendo personajes históricos).</p>
             {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('admin.ocupantes.index') }}" class="btn btn-secondary mt-auto">Administrar Ocupantes</a>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>

{{-- Card: Gestión de Responsables --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-address-book me-2" style="color: #6f42c1;"></i>Gestión de Responsables</h5>
            <p class="card-text text-muted">Consultar y editar la información de contacto y dirección de los responsables de los contratos.</p> {{-- Texto ajustado --}}
            {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('admin.responsables.index') }}" class="btn mt-auto" style="background-color: #6f42c1; color: white;">Administrar Responsables</a>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>

{{-- Card: Gestión de Catálogos --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-tags me-2" style="color: #fd7e14;"></i>Gestión de Catálogos</h5>
            <p class="card-text text-muted">Administrar las opciones disponibles en el sistema (tipos de nicho, estados, géneros, destinos, etc.).</p>
            {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('admin.catalogos.dashboard') }}" class="btn mt-auto" style="background-color: #fd7e14; color: white;">Administrar Catálogos</a>
             {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>
{{-- Card: Reportes y Estadísticas --}}
<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-chart-line me-2" style="color: #17a2b8;"></i>Reportes y Estadísticas</h5>
            <p class="card-text text-muted">Generar informes detallados sobre ingresos, ocupación, contratos, pagos, exhumaciones y exportarlos.</p>
             {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('admin.reportes.dashboard') }}" class="btn mt-auto" style="background-color: #17a2b8; color: white;">Generar Reportes</a>
             {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>

<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-user-edit me-2"></i>Mi Perfil</h5>
            <p class="card-text text-muted">Visualiza y actualiza tu información de contacto registrada en el sistema (teléfono, correo, dirección).</p>
            {{-- ***** CAMBIO AQUÍ ***** --}}
            <a href="{{ route('perfil.edit') }}" class="btn btn-secondary mt-auto">Editar mi Información</a>
            {{-- ***** FIN CAMBIO ***** --}}
        </div>
    </div>
</div>


    </div> {{-- Fin de .row --}}

</div> {{-- Fin de .container-fluid --}}
@endsection

@push('styles')
<style>
    /* Estilos generales para cards (igual que en consulta) */
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card-title i {
        /* color por defecto definido en línea arriba */
        margin-right: 0.75rem; /* Un poco más de espacio */
        width: 20px; /* Ancho fijo para alinear iconos */
        text-align: center;
    }
</style>
@endpush

@push('scripts')
{{-- Puedes añadir JS específico si es necesario más adelante --}}
<script>
    // Ejemplo: Tooltips
    // var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    // var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    //   return new bootstrap.Tooltip(tooltipTriggerEl)
    // })
</script>
@endpush