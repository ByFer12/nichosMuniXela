{{-- resources/views/auth/login.blade.php --}}
@extends('./Layouts.landing')

@section("title","Login")

@section('content')

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-container professional-login-form" style="padding: 2rem; border: 1px solid #dee2e6; border-radius: .5rem; background-color: #f8f9fa;">
                <h3 class="text-center mb-4 fw-light">Iniciar Sesión</h3>

                {{-- Mostrar errores de validación o de autenticación --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                         <div>{{ $errors->first() }}</div>
                         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Apunta la acción a la ruta nombrada 'login.attempt' --}}
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                
                    <div class="mb-3">
                        <label for="username" class="form-label">Correo Electrónico o Usuario</label>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Ingresa tu correo o usuario" required>
                    </div>
                
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Ingresa tu contraseña" required>
                    </div>
                
                    <div class="d-grid gap-2 mb-4">
                         <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                    </div>
                
                    <div class="text-center mt-4">
                        ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí de inmediato</a>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

@endsection