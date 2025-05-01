@extends('./Layouts.landing')
@section('title', 'Admin Dashboard - Cementerio General de Quetzaltenango')
@section('content')
<div class="container mt-4">
    <h1>Hola Administrador, {{ $user->nombre ?? 'Admin' }}!</h1>
    <p>Este es tu panel de control principal.</p>

    {{-- Botón/Enlace de Logout --}}
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
    </form>
</div>
@endsection