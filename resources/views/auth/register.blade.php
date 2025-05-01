@extends('Layouts.landing')


@section("title","Register")
@section('content')

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6"> {{-- Un poco más ancho para más campos --}}
            <div class="register-container professional-form"> {{-- Reutilizamos clase base de estilo --}}
                <h3 class="text-center mb-4">Crear Cuenta</h3>

                <form action="#" method="POST"> {{-- Reemplaza # con tu URL de procesamiento de registro --}}
                    @csrf {{-- Si usas Laravel, esto es importante para seguridad --}}

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa tu nombre completo" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Elige un nombre de usuario" required>
                         {{-- Opcional: Añadir texto de ayuda --}}
                         {{-- <div id="usernameHelp" class="form-text">Tu nombre de usuario será público.</div> --}}
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                    </div>

                    <div class="mb-4"> {{-- mb-4 para más espacio antes del botón --}}
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Crea una contraseña segura" required>
                    </div>

                     <div class="mb-4"> {{-- Campo para confirmar contraseña --}}
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repite tu contraseña" required>
                    </div>

                    {{-- Campo oculto para el rol. Generalmente se asigna en el backend. --}}
                    {{-- Aquí asignamos un valor por defecto (ej: 1 para 'usuario'). --}}
                    {{-- Asegúrate de que este valor sea válido en tu tabla `roles`. --}}
                    <input type="hidden" name="rol_id" value="1">

                    {{-- El campo 'activo' (TINYINT) casi siempre se maneja en el backend al crear el usuario. --}}


                    <div class="d-grid gap-2 mb-4"> {{-- d-grid gap-2 hace el botón full-width --}}
                         <button type="submit" class="btn btn-primary btn-lg">Registrarse</button> {{-- btn-lg para un botón más grande --}}
                    </div>

                    <div class="text-center mt-4">
                        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-decoration-none">Inicia Sesión aquí</a> {{-- Reemplaza route('login') con tu URL de login --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection