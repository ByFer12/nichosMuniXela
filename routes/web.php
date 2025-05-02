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
use App\Http\Controllers\Auditor\AuditorConsultaNichoController;
use App\Http\Controllers\Auditor\AuditorConsultaContratoController;
use App\Http\Controllers\Auditor\AuditorConsultaPagoController;
use App\Http\Controllers\Auditor\AuditorConsultaOcupanteController;
use App\Http\Controllers\Auditor\AuditorConsultaResponsableController;
use App\Http\Controllers\Auditor\AuditorConsultaUsuarioController;
use App\Http\Controllers\Auditor\AuditorConsultaCatalogoController;
use App\Http\Controllers\Auditor\AuditorReportController;
use App\Http\Controllers\admin\AdminPagoController;
use App\Http\Controllers\Ayudante\AyudanteDashboardController;
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

     Route::prefix('ayudante')->middleware('ayudante')->name('ayudante.')->group(function () {
          Route::get('/dashboard', [AyudanteDashboardController::class, 'index'])->name('dashboard');
          // Las acciones del ayudante (crear ocupante, etc.) usarán las rutas admin.*
      });

     Route::get('/mi-perfil', [DashboardController::class, 'editProfile'])
     ->name('perfil.edit'); // Nombre: consulta.perfil.edit

Route::put('/mi-perfil', [DashboardController::class, 'updateProfile'])
     ->name('perfil.update'); // Nombre: consulta.perfil.update

     Route::prefix('admin')->name('admin.')->group(function () {

          // --- RUTAS EXCLUSIVAS DEL ADMINISTRADOR ---
          // Agrupamos las rutas que SOLO el admin (rol 1) puede usar
          Route::middleware('admin')->group(function() {
              // Dashboard Admin (Solo Admin)
              Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
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

                        Route::get('/contratos/crear', [AdminContratoController::class, 'create'])
                        ->name('contratos.create'); // admin.contratos.create
           
                   Route::post('/contratos', [AdminContratoController::class, 'store'])
                        ->name('contratos.store'); // admin.contratos.store
           
                   Route::get('/contratos/{contrato}/editar', [AdminContratoController::class, 'edit'])
                        ->name('contratos.edit'); // admin.contratos.edit
           
                   Route::put('/contratos/{contrato}', [AdminContratoController::class, 'update'])
                        ->name('contratos.update'); // admin.contratos.update


               
          Route::get('/responsables', [AdminResponsableController::class, 'index'])
          ->name('responsables.index'); // admin.responsables.index

     Route::get('/responsables/{responsable}/editar', [AdminResponsableController::class, 'edit'])
          ->name('responsables.edit'); // admin.responsables.edit

     Route::put('/responsables/{responsable}', [AdminResponsableController::class, 'update'])
          ->name('responsables.update'); // admin.responsables.update
              // Gestión de Usuarios (Solo Admin)
              Route::get('/usuarios', [AdminUserController::class, 'index'])->name('usuarios.index');
              Route::get('/usuarios/crear', [AdminUserController::class, 'create'])->name('usuarios.create');
              Route::post('/usuarios', [AdminUserController::class, 'store'])->name('usuarios.store');
              Route::get('/usuarios/{user}/editar', [AdminUserController::class, 'edit'])->name('usuarios.edit');
              Route::put('/usuarios/{user}', [AdminUserController::class, 'update'])->name('usuarios.update');
              Route::put('/usuarios/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('usuarios.toggleStatus');
              // Route::delete('/usuarios/{user}', [AdminUserController::class, 'destroy'])->name('usuarios.destroy'); // Si tienes eliminar
  
              // Gestión de Catálogos (Solo Admin)
              Route::get('/catalogos', [AdminCatalogoController::class, 'catalogIndex'])->name('catalogos.dashboard');
              Route::get('/catalogos/{catalogoSlug}', [AdminCatalogoController::class, 'index'])->name('catalogos.index');
              Route::get('/catalogos/{catalogoSlug}/crear', [AdminCatalogoController::class, 'create'])->name('catalogos.create');
              Route::post('/catalogos/{catalogoSlug}', [AdminCatalogoController::class, 'store'])->name('catalogos.store');
              Route::get('/catalogos/{catalogoSlug}/{id}/editar', [AdminCatalogoController::class, 'edit'])->name('catalogos.edit');
              Route::put('/catalogos/{catalogoSlug}/{id}', [AdminCatalogoController::class, 'update'])->name('catalogos.update');
              Route::delete('/catalogos/{catalogoSlug}/{id}', [AdminCatalogoController::class, 'destroy'])->name('catalogos.destroy');
  
              // Reportes (Generalmente Solo Admin)
              Route::get('/reportes', [AdminReportController::class, 'dashboard'])->name('reportes.dashboard');
              Route::get('/reportes/contratos-vencer', [AdminReportController::class, 'reporteContratosPorVencer'])->name('reportes.contratosVencer');
              Route::get('/reportes/pagos-pendientes/exportar/{format?}', [AdminReportController::class, 'exportarPagosPendientes'])->name('reportes.pagosPendientes.export');
              Route::get('/reportes/ocupacion/exportar/{format?}', [AdminReportController::class, 'exportarOcupacion'])->name('reportes.ocupacion.export');
  
               // Acciones Críticas (Solo Admin - Ej: Procesar/Aprobar/Rechazar Solicitudes si Ayudante solo puede verlas)
               // Si Ayudante SÓLO puede ver solicitudes, las acciones van aquí.
               // Si Ayudante puede hacer algo más, se mueven al grupo compartido.
               // Por ahora, las dejamos aquí asumiendo que Ayudante solo consulta:
               Route::put('/solicitudes/{solicitud}/procesar-boleta', [AdminSolicitudController::class, 'processBoleta'])->name('solicitudes.processBoleta');
               Route::put('/solicitudes/{solicitud}/aprobar-exhumacion', [AdminSolicitudController::class, 'approveExhumacion'])->name('solicitudes.approveExhumacion');
               Route::put('/solicitudes/{solicitud}/rechazar', [AdminSolicitudController::class, 'reject'])->name('solicitudes.reject');
  
               // Acciones Críticas en Nichos/Contratos (Ej: Editar campos sensibles, Eliminar)
               // Si Ayudante solo puede ver/crear, las ediciones complejas o eliminaciones van aquí.
               Route::get('/nichos/{nicho}/editar', [AdminNichoController::class, 'edit'])->name('nichos.edit'); // Ejemplo si editar es solo admin
               Route::put('/nichos/{nicho}', [AdminNichoController::class, 'update'])->name('nichos.update'); // Ejemplo si editar es solo admin
               Route::delete('/nichos/{nicho}', [AdminNichoController::class, 'destroy'])->name('nichos.destroy');
  
          }); // --- FIN RUTAS EXCLUSIVAS ADMIN ---
  
  
          // --- RUTAS COMPARTIDAS (Admin Y Ayudante) ---
          // Aplicamos el NUEVO middleware a este grupo
          Route::middleware('admin_or_ayudante')->group(function() {
               
               
               Route::get('/nichos/crear', [AdminNichoController::class, 'create'])
         ->name('nichos.create');
              // Gestión de Ocupantes (Ayudante: Crear, Leer, Actualizar)
              Route::get('/ocupantes', [AdminOcupanteController::class, 'index'])->name('ocupantes.index'); // Ver lista
              Route::get('/ocupantes/crear', [AdminOcupanteController::class, 'create'])->name('ocupantes.create'); // Formulario crear
              Route::post('/ocupantes', [AdminOcupanteController::class, 'store'])->name('ocupantes.store'); // Guardar nuevo
              Route::get('/ocupantes/{ocupante}/editar', [AdminOcupanteController::class, 'edit'])->name('ocupantes.edit'); // Formulario editar
              Route::put('/ocupantes/{ocupante}', [AdminOcupanteController::class, 'update'])->name('ocupantes.update'); // Guardar edición
              // Route::get('/ocupantes/{ocupante}', [AdminOcupanteController::class, 'show'])->name('ocupantes.show'); // Si tienes vista detalle
  
              Route::get('/solicitudes', [AdminSolicitudController::class, 'index'])
              ->name('solicitudes.index');
 
         // Rutas para PROCESAR las solicitudes
         Route::put('/solicitudes/{solicitud}/procesar-boleta', [AdminSolicitudController::class, 'processBoleta'])
              ->name('solicitudes.processBoleta');
 
         Route::put('/solicitudes/{solicitud}/aprobar-exhumacion', [AdminSolicitudController::class, 'approveExhumacion'])
              ->name('solicitudes.approveExhumacion');
 
         Route::put('/solicitudes/{solicitud}/rechazar', [AdminSolicitudController::class, 'reject'])
              ->name('solicitudes.reject');
 
         // Si tienes una ruta para ver detalles de UNA solicitud, también iría aquí
         // Route::get('/solicitudes/{solicitud}', [AdminSolicitudController::class, 'show'])->name('solicitudes.show');
          
            
              // Gestión de Pagos (Ayudante: Generar Boleta, Leer)
              Route::get('/pagos/generar', [AdminPagoController::class, 'showGenerateBoletaForm'])->name('pagos.showGenerate'); // Ruta para MOSTRAR el form de generar boleta (SIN ID)
              Route::post('/pagos/generar', [AdminPagoController::class, 'generateBoleta'])->name('pagos.generate'); // Ruta para PROCESAR el form de generar boleta
              // Route::get('/pagos', [AdminPagoController::class, 'index'])->name('pagos.index'); // Si hay una lista de pagos/boletas
              // Route::get('/pagos/{pago}', [AdminPagoController::class, 'show'])->name('pagos.show'); // Si hay vista detalle de pago
  
              // Gestión de Solicitudes (Ayudante: Leer)
              Route::get('/solicitudes', [AdminSolicitudController::class, 'index'])->name('solicitudes.index'); // Ver lista (Compartido)
              Route::get('/solicitudes/{solicitud}', [AdminSolicitudController::class, 'show'])->name('solicitudes.show'); // Si hay vista detalle
  
               // Gestión de Exhumaciones (Ayudante: Leer Solicitud)
               Route::get('/exhumaciones', [AdminExhumacionController::class, 'index'])->name('exhumaciones.index'); // Si hay lista de exhumaciones
               Route::get('/exhumaciones/solicitudes', [AdminSolicitudController::class, 'index'])->name('exhumaciones.solicitudes.index'); // O usa la lista de solicitudes filtrada
  
          }); // --- FIN RUTAS COMPARTIDAS ---
  
      }); // --- FIN PREFIJO ADMIN ---
  
  
      // --- Rutas Usuario Consulta (Ya están bien) ---
      Route::prefix('consulta')->middleware('auth')->name('consulta.')->group(function () { // Añadido middleware aquí también por si acaso
          Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
          Route::get('/mis-contratos', [DashboardController::class, 'showMyContracts'])->name('contratos.index');
          Route::post('/contratos/{contrato}/solicitar-boleta', [DashboardController::class, 'requestBoleta'])->name('boleta.request');
          Route::post('/solicitar-boleta-modal', [DashboardController::class, 'requestBoletaFromModal'])->name('boleta.request.modal');
          Route::post('/solicitar-exhumacion-modal', [DashboardController::class, 'requestExhumacionFromModal'])->name('exhumacion.request.modal');
          Route::get('/mis-solicitudes', [DashboardController::class, 'showMyRequests'])->name('solicitudes.index');
          Route::get('/boleta/pdf/{pago}', [BoletaPdfController::class, 'download'])->name('boleta.pdf.download');
      });
  
      // --- Rutas Auditor (Ya están bien, protegidas por 'auditor') ---
      Route::prefix('auditor')->middleware('auditor')->name('auditor.')->group(function () {
           Route::get('/dashboard', [AuditorDashboardController::class, 'index'])->name('dashboard');
           Route::prefix('consultar')->name('consultar.')->group(function() {
               Route::get('/', function () { return view('auditor.consultar.dashboard'); })->name('dashboard');
               Route::get('/nichos', [AuditorConsultaNichoController::class, 'index'])->name('nichos.index');
               Route::get('/nichos/{nicho}', [AuditorConsultaNichoController::class, 'show'])->name('nichos.show');
               // ... (resto de rutas auditor consulta) ...
                Route::get('/responsables/{responsable}', [AuditorConsultaResponsableController::class, 'show'])->name('responsables.show');
                Route::get('/usuarios', [AuditorConsultaUsuarioController::class, 'index'])->name('usuarios.index');
                Route::get('/usuarios/{user}', [AuditorConsultaUsuarioController::class, 'show'])->name('usuarios.show');
                Route::get('/catalogos/{catalogoSlug}', [AuditorConsultaCatalogoController::class, 'index'])->name('catalogos.index');
           });
           Route::prefix('reportes')->name('reportes.')->group(function() {
                Route::get('/', [AuditorReportController::class, 'index'])->name('index');
                // ... (resto de rutas auditor reportes) ...
                 Route::get('/pagos-pendientes-antiguos/exportar/{format?}', [AuditorReportController::class, 'exportarPagosPendientesAntiguos'])->name('exportar.pagosAntiguos');
            });
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

          Route::get('/dashboard', [AuditorDashboardController::class, 'index'])->name('dashboard');
  
          // --- Rutas de Consulta (Read-Only) ---
          // Usamos un prefijo 'consultar' para agruparlas lógicamente
          Route::prefix('consultar')->name('consultar.')->group(function() {
  
              // Dashboard de Consulta (Página principal con enlaces)
              Route::get('/', function () {
                  // Puedes pasar datos si es necesario o simplemente mostrar la vista
                  return view('auditor.consultar.dashboard');
              })->name('dashboard'); // auditor.consultar.dashboard
  
              // Nichos
              Route::get('/nichos', [AuditorConsultaNichoController::class, 'index'])->name('nichos.index');
              Route::get('/nichos/{nicho}', [AuditorConsultaNichoController::class, 'show'])->name('nichos.show');
  
              // Contratos
              Route::get('/contratos', [AuditorConsultaContratoController::class, 'index'])->name('contratos.index');
              Route::get('/contratos/{contrato}', [AuditorConsultaContratoController::class, 'show'])->name('contratos.show');
  
              // Pagos (Incluye ruta para ver comprobante)
              Route::get('/pagos', [AuditorConsultaPagoController::class, 'index'])->name('pagos.index');
              Route::get('/pagos/{pago}/comprobante', [AuditorConsultaPagoController::class, 'showComprobante'])->name('pagos.comprobante');
              // Podrías tener un show para detalles del pago si fuera necesario
              // Route::get('/pagos/{pago}', [AuditorConsultaPagoController::class, 'show'])->name('pagos.show');
  
              // Ocupantes
              Route::get('/ocupantes', [AuditorConsultaOcupanteController::class, 'index'])->name('ocupantes.index');
              Route::get('/ocupantes/{ocupante}', [AuditorConsultaOcupanteController::class, 'show'])->name('ocupantes.show');
  
               // Responsables
              Route::get('/responsables', [AuditorConsultaResponsableController::class, 'index'])->name('responsables.index');
              Route::get('/responsables/{responsable}', [AuditorConsultaResponsableController::class, 'show'])->name('responsables.show');
  
               // Usuarios
              Route::get('/usuarios', [AuditorConsultaUsuarioController::class, 'index'])->name('usuarios.index');
              Route::get('/usuarios/{user}', [AuditorConsultaUsuarioController::class, 'show'])->name('usuarios.show');
  
              // Catálogos (Vista unificada para ver items de un catálogo)
              Route::get('/catalogos/{catalogoSlug}', [AuditorConsultaCatalogoController::class, 'index'])
                   ->name('catalogos.index'); // auditor.consultar.catalogos.index
  
          }); // Fin del prefijo 'consultar'

          Route::prefix('reportes')->name('reportes.')->group(function() {
               // Página principal de reportes de auditoría
               Route::get('/', [AuditorReportController::class, 'index'])->name('index');
   
               // Rutas específicas para VER reportes (HTML)
               Route::get('/conciliacion-ingresos', [AuditorReportController::class, 'verConciliacionIngresos'])->name('ver.conciliacion');
               Route::get('/contratos-vencidos-sin-accion', [AuditorReportController::class, 'verContratosVencidosSinAccion'])->name('ver.vencidosSinAccion');
               Route::get('/pagos-pendientes-antiguos', [AuditorReportController::class, 'verPagosPendientesAntiguos'])->name('ver.pagosAntiguos');
               // ... añadir rutas para otros reportes visualizables
   
               // Rutas para EXPORTAR reportes (Excel/PDF)
               Route::get('/conciliacion-ingresos/exportar/{format?}', [AuditorReportController::class, 'exportarConciliacionIngresos'])->name('exportar.conciliacion');
               Route::get('/contratos-vencidos-sin-accion/exportar/{format?}', [AuditorReportController::class, 'exportarContratosVencidosSinAccion'])->name('exportar.vencidosSinAccion');
               Route::get('/pagos-pendientes-antiguos/exportar/{format?}', [AuditorReportController::class, 'exportarPagosPendientesAntiguos'])->name('exportar.pagosAntiguos');
                // ... añadir rutas para otras exportaciones
   
           });
  
          // --- (Rutas futuras de reportes específicos auditor) ---
  
      });

});

