@extends('./Layouts.landing') {{-- O layout Ayudante si tienes uno diferente --}}
@section('title', 'Portal de Ayudante')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Portal de Ayudante</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard Ayudante</li>
    </ol>

    {{-- Encabezado Bienvenida y Logout --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded">
        <div>
            <h2 class="h5 mb-0">Panel de Operaciones</h2>
            {{-- Es más seguro obtener el usuario directamente de Auth si $user no siempre se pasa --}}
            @php $nombreAyudante = Auth::user()->nombre ?? Auth::user()->username ?? 'Ayudante'; @endphp
            <p class="mb-0 text-muted">Bienvenido, {{ $nombreAyudante }}!</p>
        </div>
        <div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline"> @csrf <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión</button> </form>
        </div>
    </div>

    <p class="mb-4">Acciones rápidas para tareas operativas.</p>

    {{-- Cards de Acciones Principales Ayudante --}}
    <div class="row g-4">

        {{-- Card: Registrar Ocupante --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-user-plus me-2 text-success"></i>Registrar Ocupante</h5>
                    <p class="card-text text-muted">Ingresar datos de una nueva persona fallecida.</p>
                    {{-- Ahora esta ruta es accesible por el Ayudante --}}
                    <a href="{{ route('admin.ocupantes.create') }}" class="btn btn-success mt-auto">Nuevo Registro</a>
                </div>
            </div>
        </div>



         {{-- Card: Consultar Solicitudes --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                     <h5 class="card-title"><i class="fas fa-inbox me-2 text-secondary"></i>Ver Solicitudes Pend.</h5>
                    <p class="card-text text-muted">Consultar las solicitudes pendientes.</p>
                     {{-- Ahora esta ruta es accesible --}}
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary mt-auto">Ver Solicitudes</a>
                </div>
            </div>
        </div>

        {{-- Card: Mi Perfil --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-user-cog me-2 text-dark"></i>Mi Perfil</h5>
                    <p class="card-text text-muted">Actualizar tu información y contraseña.</p>
                    {{-- Usa la ruta general de perfil (accesible por todos los autenticados) --}}
                    <a href="{{ route('perfil.edit') }}" class="btn btn-dark mt-auto">Editar mi Perfil</a>
                </div>
            </div>
        </div>

    </div> {{-- Fin .row --}}

</div> {{-- Fin .container-fluid --}}
@endsection

{{-- Puedes añadir push styles/scripts si es necesario --}}
@push('styles')
{{-- <link href="{{ asset('css/ayudante-specific.css') }}" rel="stylesheet"> --}}
@endpush

@push('scripts')
{{-- <script src="{{ asset('js/ayudante-dashboard.js') }}"></script> --}}
@endpush    