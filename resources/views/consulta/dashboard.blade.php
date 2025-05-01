@extends('./Layouts.landing') {{-- Asegúrate que este layout incluya Bootstrap 5 CSS/JS --}}

@section('title', 'Portal de Consulta')

@section('content')
<div class="container-fluid mt-4 mb-5" style="max-width: 1200px;"> {{-- Usar container-fluid o ajustar max-width según prefieras --}}

    {{-- Encabezado y Bienvenida --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded">
        <div>
            <h2 class="h4 mb-0">Portal de Consulta</h2>
            {{-- Intenta obtener el nombre, si no, usa el email --}}
            @php
                // Suponiendo que tu tabla 'usuarios' tiene un campo 'nombre'
                // Si no, puedes quitar esta lógica y solo usar el email o ajustar el campo.
                $nombreUsuario = Auth::user()->nombre ?? Auth::user()->username ?? Auth::user()->email ?? 'Usuario';
            @endphp
            <p class="mb-0 text-muted">Bienvenido, {{ $nombreUsuario }}!</p>
        </div>
        <div>
            {{-- Botón/Enlace de Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> {{-- Asume Font Awesome, si no lo usas, quita el <i> --}}
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    {{-- Sección Principal con Opciones (Usando Cards) --}}
    <div class="row g-4">

        {{-- Card: Consultar Nicho/Contrato --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-search me-2"></i>Consultar Nicho/Contrato</h5>
                    <p class="card-text text-muted">Busca y visualiza la información detallada del nicho y el contrato asociado a tu responsabilidad.</p>
                    {{-- Enlace placeholder: Llevará a la página/modal de consulta --}}
                    <a href="#" class="btn btn-primary mt-auto disabled">Ver mis Contratos</a> {{-- Usa mt-auto para alinear al fondo --}}
                </div>
            </div>
        </div>

        {{-- Card: Solicitar Boleta de Pago --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-file-invoice-dollar me-2"></i>Solicitar Boleta de Pago</h5>
                    <p class="card-text text-muted">Genera una solicitud para recibir la boleta necesaria para la renovación o pago de tu contrato.</p>
                    {{-- Enlace placeholder: Llevará a la página/modal de solicitud --}}
                    <a href="#" class="btn btn-success mt-auto disabled">Solicitar Boleta</a>
                </div>
            </div>
        </div>

        {{-- Card: Solicitar Exhumación --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-skull-crossbones me-2"></i>Solicitar Exhumación</h5>
                    <p class="card-text text-muted">Inicia el proceso de solicitud para la exhumación de los restos del ocupante asociado a tu contrato.</p>
                    {{-- Enlace placeholder: Llevará a la página/modal de solicitud --}}
                    <a href="#" class="btn btn-warning mt-auto disabled">Solicitar Exhumación</a>
                </div>
            </div>
        </div>

        {{-- Card: Seguimiento de Solicitudes (Opcional pero recomendado) --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-tasks me-2"></i>Seguimiento de Solicitudes</h5>
                    <p class="card-text text-muted">Consulta el estado actual de tus solicitudes de boletas de pago o exhumaciones.</p>
                    {{-- Enlace placeholder: Llevará a la página de seguimiento --}}
                    <a href="#" class="btn btn-info mt-auto disabled">Ver mis Solicitudes</a>
                </div>
            </div>
        </div>

        {{-- Card: Mi Perfil (Opcional pero recomendado) --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-user-edit me-2"></i>Mi Perfil</h5>
                    <p class="card-text text-muted">Visualiza y actualiza tu información de contacto registrada en el sistema (teléfono, correo, dirección).</p>
                    {{-- Enlace placeholder: Llevará a la página de edición de perfil --}}
                    <a href="#" class="btn btn-secondary mt-auto disabled">Editar mi Información</a>
                </div>
            </div>
        </div>

    </div> {{-- Fin de .row --}}

</div> {{-- Fin de .container-fluid --}}
@endsection

@push('styles')
{{-- Puedes añadir CSS específico para esta página si es necesario --}}
<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    /* Opcional: Iconos más grandes o con color */
    .card-title i {
        color: #6c757d; /* Color gris por defecto para iconos */
        margin-right: 0.5rem;
    }
    /* Colores específicos para iconos si prefieres */
     .fa-search { color: #0d6efd; } /* Azul Primary */
     .fa-file-invoice-dollar { color: #198754; } /* Verde Success */
     .fa-skull-crossbones { color: #ffc107; } /* Amarillo Warning */
     .fa-tasks { color: #0dcaf0; } /* Cyan Info */
     .fa-user-edit { color: #6c757d; } /* Gris Secondary */
</style>
@endpush

@push('scripts')
{{-- Puedes añadir JS específico para esta página si es necesario --}}
<script>
    // Ejemplo: Añadir tooltips a los botones si usas Bootstrap JS
    // var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    // var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    //   return new bootstrap.Tooltip(tooltipTriggerEl)
    // })
</script>
@endpush