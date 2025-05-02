@extends('./Layouts.landing') {{-- O usa './Layouts.landing' si prefieres ese --}}
@section('title', 'Portal de Auditoría')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Portal de Auditoría</h1>
    <ol class="breadcrumb mb-4">
        {{-- El link al 'dashboard' del admin no aplica aquí, quizás solo a 'Reportes' o a nada --}}
        <li class="breadcrumb-item active">Dashboard Auditor</li>
    </ol>

   

    {{-- Encabezado Bienvenida y Logout --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded">
        <div>
            <h2 class="h5 mb-0">Panel de Auditor</h2>
            @php
                $nombreAuditor = $user->nombre ?? $user->username ?? 'Auditor';
            @endphp
            <p class="mb-0 text-muted">Bienvenido, {{ $nombreAuditor }}!</p>
        </div>
        <div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline"> @csrf <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión</button> </form>
        </div>
    </div>

    <p class="mb-4">Desde aquí puede acceder a las funciones de consulta de datos y generación de reportes de auditoría.</p>

    {{-- Cards de Acciones Principales --}}
    <div class="row g-4">

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-database me-2 text-info"></i>Consultar Datos</h5>
                    <p class="card-text text-muted">Acceder en modo lectura a los registros operativos y catálogos del sistema.</p>
                    {{-- ***** CAMBIO AQUÍ: Enlazar al dashboard de consulta ***** --}}
                    <a href="{{ route('auditor.consultar.dashboard') }}" class="btn btn-info mt-auto">Iniciar Consulta</a>
                </div>
            </div>
        </div>

        {{-- Card: Generar Reportes Auditoría --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-clipboard-list me-2 text-success"></i>Reportes de Auditoría</h5>
                    <p class="card-text text-muted">Generar y exportar reportes específicos para validación...</p>
                    {{-- ***** CAMBIO AQUÍ ***** --}}
                    <a href="{{ route('auditor.reportes.index') }}" class="btn btn-success mt-auto">Ver Reportes</a>
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

    </div> {{-- Fin .row --}}

</div> {{-- Fin .container-fluid --}}
@endsection

@push('styles')
<style>
    /* Puedes reutilizar los estilos de hover de las cards si están en el layout */
    .card { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    .card-title i { margin-right: 0.5rem; }
</style>
@endpush