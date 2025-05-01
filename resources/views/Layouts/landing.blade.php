<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/auth/navigation.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <head>
    <title>@yield('title')</title>
</head>
<body>
    @include('Layouts._partials.menu')
    <div class="container mt-4">
    @yield('content')
    </div>
   
<footer>
    <div class="container text-center" style="margin-top: auto">
        <p class="text-muted">&copy; 2023 Cementerio General de Quetzaltenango. Todos los derechos reservados.</p>
        <p class="text-muted">Sistema de Gestión Digital de Nichos</p>
    </div>
</footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>