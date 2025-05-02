<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\consulta\DashboardController;
use App\Http\Controllers\admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\admin\AdminUserController;
use App\Http\Controllers\consulta\LocationWebController;
use App\Http\Controllers\admin\AdminNichoController;
use App\Http\Controllers\admin\AdminContratoController;
use App\Http\Controllers\consulta\BoletaPdfController;
use App\Http\Controllers\admin\AdminSolicitudController;
use App\Http\Controllers\admin\AdminExhumacionController;
use App\Http\Controllers\admin\AdminOcupanteController;
use App\Http\Controllers\admin\AdminResponsableController;
use App\Http\Controllers\admin\AdminCatalogoController;
use App\Http\Controllers\admin\AdminReportController;
use App\Http\Controllers\Auditor\AuditorDashboardController;
Route::view('/', 'home')->name('home');
Route::view('/blog', 'blog')->name('blog');
//Logout
Route::post('/logout', [LoginController::class,'logout'])->middleware('auth') ->name('logout');
Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth/register')->name('register');

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])
         ->name('login');
    Route::post('/login', [LoginController::class, 'login'])
         ->name('login.post');
});

Route::get('/get/departamentos/{departamento}/municipios',
    [LocationWebController::class, 'getMunicipios'])
    ->name('web.location.municipios');

Route::get('/get/municipios/{municipio}/localidades', [LocationWebController::class, 'getLocalidades'])
     ->name('web.location.localidades'); 

//rutas protegidas
Route::middleware(['auth'])->group(function () {


     Route::get('/mi-perfil', [DashboardController::class, 'editProfile'])
     ->name('perfil.edit'); // Nombre: consulta.perfil.edit

Route::put('/mi-perfil', [DashboardController::class, 'updateProfile'])
     ->name('perfil.update'); // Nombre: consulta.perfil.update

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
             ->name('dashboard'); // Nombre final: admin.dashboard


     // ADMINISTRAR CATALOGOS

     Route::get('/catalogos', [AdminCatalogoController::class, 'catalogIndex'])
     ->name('catalogos.dashboard'); // Página que lista los catálogos disponibles

Route::get('/catalogos/{catalogoSlug}', [AdminCatalogoController::class, 'index'])
     ->name('catalogos.index'); // Muestra lista de items de UN catálogo

Route::get('/catalogos/{catalogoSlug}/crear', [AdminCatalogoController::class, 'create'])
     ->name('catalogos.create'); // Formulario para crear item

Route::post('/catalogos/{catalogoSlug}', [AdminCatalogoController::class, 'store'])
     ->name('catalogos.store'); // Guarda nuevo item

Route::get('/catalogos/{catalogoSlug}/{id}/editar', [AdminCatalogoController::class, 'edit'])
     ->name('catalogos.edit'); // Formulario para editar item

Route::put('/catalogos/{catalogoSlug}/{id}', [AdminCatalogoController::class, 'update'])
     ->name('catalogos.update'); // Actualiza item

Route::delete('/catalogos/{catalogoSlug}/{id}', [AdminCatalogoController::class, 'destroy'])
     ->name('catalogos.destroy'); // Elimina item

        // --- Gestión de Usuarios ---
        Route::get('/usuarios', [AdminUserController::class, 'index'])
             ->name('usuarios.index'); // admin.usuarios.index

        Route::get('/usuarios/crear', [AdminUserController::class, 'create'])
             ->name('usuarios.create'); // admin.usuarios.create

        Route::post('/usuarios', [AdminUserController::class, 'store'])
             ->name('usuarios.store');

        Route::get('/usuarios/{user}/editar', [AdminUserController::class, 'edit'])
             ->name('usuarios.edit'); // admin.usuarios.edit

        Route::put('/usuarios/{user}', [AdminUserController::class, 'update'])
             ->name('usuarios.update'); // admin.usuarios.update

        // Usaremos PUT para desactivar/activar
        Route::put('/usuarios/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
            ->name('usuarios.toggleStatus'); // admin.usuarios.toggleStatus

//AQUI VAN OS NICHOS
Route::get('/nichos', [AdminNichoController::class, 'index'])
->name('nichos.index'); // admin.nichos.index

Route::get('/nichos/crear', [AdminNichoController::class, 'create'])
->name('nichos.create'); // admin.nichos.create

Route::post('/nichos', [AdminNichoController::class, 'store'])
->name('nichos.store'); // admin.nichos.store

Route::get('/nichos/{nicho}/editar', [AdminNichoController::class, 'edit'])
->name('nichos.edit'); // admin.nichos.edit

Route::put('/nichos/{nicho}', [AdminNichoController::class, 'update'])
->name('nichos.update'); // admin.nichos.update
Route::get('/contratos', [AdminContratoController::class, 'index'])
             ->name('contratos.index'); // admin.contratos.index

             //CONTRATOS
        Route::get('/contratos/crear', [AdminContratoController::class, 'create'])
             ->name('contratos.create'); // admin.contratos.create

        Route::post('/contratos', [AdminContratoController::class, 'store'])
             ->name('contratos.store'); // admin.contratos.store

        Route::get('/contratos/{contrato}/editar', [AdminContratoController::class, 'edit'])
             ->name('contratos.edit'); // admin.contratos.edit

        Route::put('/contratos/{contrato}', [AdminContratoController::class, 'update'])
             ->name('contratos.update'); // admin.contratos.update


             //ESTADISTICAS


             Route::get('/reportes', [AdminReportController::class, 'dashboard'])
             ->name('reportes.dashboard'); // admin.reportes.dashboard
    
        // Rutas específicas para generar/exportar reportes (Ejemplos)
        Route::get('/reportes/contratos-vencer', [AdminReportController::class, 'reporteContratosPorVencer'])
             ->name('reportes.contratosVencer'); // GET para mostrar o POST si hay filtros complejos
    
        Route::get('/reportes/pagos-pendientes/exportar/{format?}', [AdminReportController::class, 'exportarPagosPendientes'])
             ->name('reportes.pagosPendientes.export'); // format = pdf o excel
    
        Route::get('/reportes/ocupacion/exportar/{format?}', [AdminReportController::class, 'exportarOcupacion'])
             ->name('reportes.ocupacion.export');

       //SOLICITUDES

       Route::get('/solicitudes', [AdminSolicitudController::class, 'index'])
       ->name('solicitudes.index'); // admin.solicitudes.index

  Route::put('/solicitudes/{solicitud}/procesar-boleta', [AdminSolicitudController::class, 'processBoleta'])
       ->name('solicitudes.processBoleta'); // admin.solicitudes.processBoleta

  Route::put('/solicitudes/{solicitud}/aprobar-exhumacion', [AdminSolicitudController::class, 'approveExhumacion'])
       ->name('solicitudes.approveExhumacion'); // admin.solicitudes.approveExhumacion

  Route::put('/solicitudes/{solicitud}/rechazar', [AdminSolicitudController::class, 'reject'])
       ->name('solicitudes.reject'); // admin.solicitudes.reject



       //OCUPANTES
       Route::get('/ocupantes', [AdminOcupanteController::class, 'index'])
       ->name('ocupantes.index'); // admin.ocupantes.index

  Route::get('/ocupantes/crear', [AdminOcupanteController::class, 'create'])
       ->name('ocupantes.create'); // admin.ocupantes.create

  Route::post('/ocupantes', [AdminOcupanteController::class, 'store'])
       ->name('ocupantes.store'); // admin.ocupantes.store

  Route::get('/ocupantes/{ocupante}/editar', [AdminOcupanteController::class, 'edit'])
       ->name('ocupantes.edit'); // admin.ocupantes.edit

  Route::put('/ocupantes/{ocupante}', [AdminOcupanteController::class, 'update'])
       ->name('ocupantes.update'); // admin.ocupantes.update

       //RESPONSABLES


       Route::get('/responsables', [AdminResponsableController::class, 'index'])
       ->name('responsables.index'); // admin.responsables.index

  Route::get('/responsables/{responsable}/editar', [AdminResponsableController::class, 'edit'])
       ->name('responsables.edit'); // admin.responsables.edit

  Route::put('/responsables/{responsable}', [AdminResponsableController::class, 'update'])
       ->name('responsables.update'); // admin.responsables.update



    }); 

    // --- Rutas Usuario Consulta ---
    Route::prefix('consulta')->name('consulta.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
             ->name('dashboard'); // Nombre final: consulta.dashboard

        // NUEVA RUTA para ver los contratos
        Route::get('/mis-contratos', [DashboardController::class, 'showMyContracts'])
             ->name('contratos.index'); // Nombre final: consulta.contratos.index
        // Podrías añadir una ruta para ver detalles de UN contrato específico más adelante
        // Route::get('/contratos/{contrato}', [DashboardController::class, 'showContractDetail'])
        //      ->name('contratos.show');
        Route::post('/contratos/{contrato}/solicitar-boleta', [DashboardController::class, 'requestBoleta'])
             ->name('boleta.request'); // Nombre final: consulta.boleta.request

        Route::post('/solicitar-boleta-modal', [DashboardController::class, 'requestBoletaFromModal'])
             ->name('boleta.request.modal');

        Route::post('/solicitar-exhumacion-modal', [DashboardController::class, 'requestExhumacionFromModal'])
             ->name('exhumacion.request.modal'); // Nombre final: consulta.exhumacion.request.modal

        Route::get('/mis-solicitudes', [DashboardController::class, 'showMyRequests'])
             ->name('solicitudes.index'); // Nombre final: consulta.solicitudes.index


             Route::get('/boleta/pdf/{pago}', [BoletaPdfController::class, 'download'])
             ->middleware('auth') // <-- Asegura que solo usuarios logueados accedan
             ->name('boleta.pdf.download'); 
    });

     //AUDITOR
     // Aquí puedes definir las rutas específicas para el auditor
    Route::prefix('auditor')->middleware('auditor')->name('auditor.')->group(function () {

     Route::get('/dashboard', [AuditorDashboardController::class, 'index'])
          ->name('dashboard'); // auditor.dashboard



 });

});

