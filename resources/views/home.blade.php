@extends('Layouts.landing')

@section('title', 'Inicio - Gestión de Nichos Cementerio Quetzaltenango')

@section('content')

<div class="container mt-4"> {{-- Opcional: usa un contenedor si tu layout lo requiere/permite --}}

    <div class="row">
        <div class="col-12 text-center mb-4">
            {{-- Puedes añadir un logo o imagen representativa aquí --}}
            {{-- <img src="{{ asset('images/logo_cementerio.png') }}" alt="Cementerio General Quetzaltenango" style="max-height: 150px;"> --}}
            <h1 class="display-4">Sistema de Gestión Digital de Nichos</h1>
            <p class="lead">Cementerio General de Quetzaltenango</p>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <p class="text-center">
                Bienvenido al portal digital diseñado para modernizar y facilitar la administración
                de los nichos municipales. Este sistema permite una gestión eficiente, transparente y segura
                de la información relacionada con ocupantes, contratos y procesos administrativos.
            </p>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Sección para Usuarios Externos (Consulta) --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users"></i> Para Familias y Responsables</h5>
                    <p class="card-text">
                        Si usted es responsable de un nicho, puede acceder al sistema para:
                    </p>
                    <ul>
                        <li>Consultar el estado actual del nicho asignado.</li>
                        <li>Verificar los detalles de su contrato (fechas de inicio y fin).</li>
                        <li>Consultar el estado de sus pagos (simulados).</li>
                        <li>Iniciar solicitudes de exhumación (sujeto a aprobación).</li>
                        <li>Acceder a la información de forma clara y oportuna.</li>
                    </ul>
                    <a href="{{ route('login') }}" class="btn btn-secondary">Acceder como Usuario Registrado</a>
                    {{-- O podrías tener un enlace directo a un formulario de consulta pública si existiera --}}
                    {{-- <a href="/consulta-publica" class="btn btn-info">Consulta Pública de Nichos</a> --}}
                </div>
            </div>
        </div>

        {{-- Sección para Personal Municipal --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-building"></i> Para Personal Municipal</h5>
                    <p class="card-text">
                        El personal autorizado de la Municipalidad puede utilizar este sistema para:
                    </p>
                    <ul>
                        <li>Administrar el registro completo de nichos, ocupantes y responsables.</li>
                        <li>Gestionar contratos, renovaciones y la generación de boletas de pago.</li>
                        <li>Registrar pagos y comprobantes.</li>
                        <li>Administrar los procesos de exhumación y restricciones históricas.</li>
                        <li>Generar reportes y visualizar estadísticas clave para la toma de decisiones.</li>
                        <li>Gestionar usuarios y roles del sistema.</li>
                    </ul>
                     <a href="{{ route('login') }}" class="btn btn-primary">Acceso Personal Municipal</a>
                </div>
            </div>
        </div>
    </div>

    <hr>

  

</div> {{-- Cierre del contenedor opcional --}}

@endsection


@push('styles')
<style>
    .display-4 { font-weight: 300; }
    .card-title i { margin-right: 8px; }
</style>
@endpush 

