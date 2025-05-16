@extends('Layouts.landing')
@section('title', 'Blog - Cementerio General de Quetzaltenango')
@section('content')

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Nuestro Blog</h2>

    <div class="row">
        {{-- Tarjeta de Blog 1 --}}
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100"> {{-- h-100 para que todas las tarjetas tengan la misma altura --}}
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Imagen de Blog 1"> {{-- Imagen de placeholder --}}
                <div class="card-body">
                    <h5 class="card-title">Historia del Cementerio</h5>
                    <p class="card-text">Un vistazo a los orígenes y la evolución histórica de este emblemático lugar en Quetzaltenango.</p>
                    <a href="#" class="btn btn-primary">Leer más</a> {{-- Enlace a la entrada completa --}}
                </div>
            </div>
        </div>

        {{-- Tarjeta de Blog 2 --}}
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Imagen de Blog 2">
                <div class="card-body">
                    <h5 class="card-title">Arquitectura y Monumentos de los nichos municipales jejej</h5>
                    <p class="card-text">Descubre la belleza arquitectónica y los importantes monumentos que alberga el cementerio.</p>
                    <a href="#" class="btn btn-primary">Leer más</a>
                </div>
            </div>
        </div>

        {{-- Tarjeta de Blog 3 --}}
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Imagen de Blog 3">
                <div class="card-body">
                    <h5 class="card-title">Personajes Ilustres</h5>
                    <p class="card-text">Conoce a algunas de las personalidades destacadas de Quetzaltenango que descansan aquí.</p>
                    <a href="#" class="btn btn-primary">Leer más</a>
                </div>
            </div>
        </div>

        {{-- Tarjeta de Blog 4 --}}
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Imagen de Blog 4">
                <div class="card-body">
                    <h5 class="card-title">Visitas y Recorridos</h5>
                    <p class="card-text">Información sobre cómo visitar el cementerio y posibles recorridos guiados disponibles.</p>
                    <a href="#" class="btn btn-primary">Leer más</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
