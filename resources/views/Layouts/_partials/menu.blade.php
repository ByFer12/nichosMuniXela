{{-- Asume que este código está en un archivo como resources/views/Layouts/partials/navbar.blade.php o similar --}}
<nav class="navbar navbar-expand-lg navbar-light bg-light custom-navbar">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand" href="{{ route('home') }}"> {{-- La marca siempre puede apuntar a home --}}
            Muni Cementerios
        </a>

        {{-- Toggler --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Nav Items --}}
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center"> {{-- align-items-center para alinear verticalmente el botón de logout --}}

                {{-- Enlaces visibles para TODOS (Autenticados y Visitantes) --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('blog') ? 'active' : '' }}" href="{{route('blog')}}">Blog</a>
                </li>

                {{-- Enlaces/Botones SOLO para usuarios AUTENTICADOS --}}
                @auth
                    <li class="nav-item">
                        {{-- Enlace "Home" dinámico según el rol --}}
                        @php
                            $dashboardRoute = route('home'); // Ruta por defecto si el rol no coincide
                            if (Auth::user()->rol_id == 1) {
                                $dashboardRoute = route('admin.dashboard');
                            } elseif (Auth::user()->rol_id == 4) {
                                $dashboardRoute = route('consulta.dashboard');
                            }
                        @endphp
                        {{-- Comprueba si la ruta actual es el dashboard correspondiente para marcar como activo --}}
                        <a class="nav-link {{ request()->url() == $dashboardRoute ? 'active' : '' }}" href="{{ $dashboardRoute }}">Mi Portal</a>
                    </li>
                    <li class="nav-item">
                        {{-- Botón de Logout estilizado como nav-link --}}
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" style="display: block; padding: var(--bs-nav-link-padding-y) var(--bs-nav-link-padding-x); border: none; background: none; color: var(--bs-nav-link-color);">
                                Logout
                            </button>
                        </form>
                    </li>
                @endauth

                {{-- Enlaces SOLO para VISITANTES (No autenticados) --}}
                @guest
                    <li class="nav-item">
                        {{-- Enlace Home estándar para visitantes --}}
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" aria-current="page" href="{{route('home')}}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{route('register')}}">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{route('login')}}">Login</a> {{-- Añadido enlace Login --}}
                    </li>
                @endguest

            </ul>
        </div>
    </div>
</nav>

{{-- El bloque <style> que proporcionaste está bien y puede permanecer igual --}}
<style>
    .custom-navbar {
        padding-top: 1rem;
        padding-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08); /* Sutil sombra para profundidad */
        background-color: #ffffff !important; /* Fondo blanco o un color claro - !important si es necesario sobreescribir Bootstrap */
    }

    .custom-navbar .navbar-brand {
        font-weight: 600; /* Un poco más de grosor a la fuente de la marca */
        color: #333; /* Color oscuro para la marca */
    }

    .custom-navbar .nav-link {
        color: #555; /* Color de los enlaces */
        margin-right: 15px; /* Espacio entre enlaces */
        transition: color 0.3s ease-in-out; /* Transición suave al pasar el mouse */
    }

    .custom-navbar .nav-link:hover,
    .custom-navbar .btn-link:hover { /* Incluir el botón de logout en el hover */
        color: #007bff; /* Color al pasar el mouse (puedes usar tu color principal) */
    }

    .custom-navbar .nav-link.active {
        color: #007bff; /* Color del enlace activo */
        font-weight: 500; /* Ligeramente más grueso para el activo */
    }

    /* Opcional: Línea inferior sutil para el enlace activo (puede que no se vea bien en botón logout) */
    /* .custom-navbar .nav-link.active::after { ... } */

    /* Estilo para el botón de logout para que se parezca más a un nav-link */
    .custom-navbar .btn-link {
        text-decoration: none; /* Quitar subrayado */
        font-size: var(--bs-nav-link-font-size); /* Usar tamaño de fuente de nav-link */
        font-weight: var(--bs-nav-link-font-weight); /* Usar peso de fuente de nav-link */
        color: var(--bs-nav-link-color); /* Usar color de nav-link */
        padding: var(--bs-nav-link-padding-y) var(--bs-nav-link-padding-x); /* Usar padding de nav-link */
        margin-right: 15px; /* Mismo margen que otros nav-link */
    }
    .custom-navbar .btn-link:focus { /* Evitar el outline azul al hacer clic */
        box-shadow: none;
    }

</style>