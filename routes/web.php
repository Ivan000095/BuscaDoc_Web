<?php

use App\Http\Controllers\EspecialidadesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\FarmaciaController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\RespuestaController; // Mantener para las respuestas
use App\Http\Controllers\BackupController;    // Nuevo controlador integrado

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\GoogleController;

use App\Models\Especialidad;




use App\Http\Controllers\RankingController;
use App\Http\Controllers\ExpedienteController;
use Illuminate\Support\Facades\Artisan;

// --- RUTAS PÚBLICAS ---
Route::get('/', [HomeController::class, 'index'])->name('inicio');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/buscar', [SearchController::class, 'search'])->name('global.search');
Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
Route::post('/doctores/reporte-pdf', [DoctorController::class, 'generarReporte'])->name('doctores.reporte');
Route::get('/directorio-medico', [DoctorController::class, 'vistageneral'])->name('doctores.vista');

// RUTAS ESTÁTICAS / CATÁLOGOS
Route::get('/farmacias', [FarmaciaController::class, 'index'])->name('farmacias.catalogo');
Route::get('/especialidades', [EspecialidadesController::class, 'index'])->name('especialidades.index');

// RUTAS DINÁMICAS (DETALLES)
Route::get('/farmacias/{id}', [FarmaciaController::class, 'show'])->name('farmacias.detalle');
Route::get('/especialidades/{id}', [EspecialidadesController::class, 'show'])->name('specs.show');
Route::get('/doctores/{id}', [DoctorController::class, 'show'])->name('doctores.show');

// --- RUTAS DE AUTENTICACIÓN ---
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('register', function () {
    return view('auth.register', ['especialidades' => Especialidad::all()]);
})->middleware('guest')->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

// Google Auth
Route::get('google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback']);

// --- RUTAS PROTEGIDAS (AUTH) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/mi-farmacia', [FarmaciaController::class, 'miFarmacia'])->name('farmacias.mi');
    Route::get('/mi-farmacia/editar', [FarmaciaController::class, 'editarMiFarmacia'])->name('farmacias.mi.editar');
    Route::put('/mi-farmacia', [FarmaciaController::class, 'actualizarMiFarmacia'])->name('farmacias.mi.actualizar');

// Ruta para que el solicitante cree la propuesta
    Route::post('/citas/{id}/solicitar-cambio', [CitaController::class, 'solicitarCambio'])
        ->name('citas.solicitar-cambio');

    // Ruta para que el solicitado acepte o rechace
    Route::post('/solicitudes-cambio/{id}/responder', [CitaController::class, 'responderCambio'])
        ->name('citas.responder-cambio');

    Route::put('/citas/{id}/reprogramar-libre', [CitaController::class, 'reprogramarLibre'])
         ->name('citas.reprogramarLibre');

    // Ruta para mostrar el formulario de edición
    Route::get('/expedientes/{id}/edit', [ExpedienteController::class, 'edit'])->name('expedientes.edit');

    // Ruta para procesar la actualización (usa PUT o PATCH)
    Route::put('/expedientes/{id}', [ExpedienteController::class, 'update'])->name('expedientes.update');


    
    Route::match(['get', 'post'], 'logout', [LoginController::class, 'logout'])
        ->name('logout')
        ->middleware('security:logout');

    Route::middleware(['security:auth'])->group(function () {
        Route::get('/directorio-mapa', [HomeController::class, 'mostrarMapa'])->name('mapa.directorio');
    });

    // Gestión de Respaldos (BackupController)
    Route::get('/respaldos', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/respaldos/crear', [BackupController::class, 'create'])->name('backups.create');
    Route::get('/respaldos/descargar/{file_name}', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('/respaldos/eliminar/{file_name}', [BackupController::class, 'destroy'])->name('backups.destroy');

    // Recursos básicos
    Route::resource('users', UserController::class);
    Route::resource('pacientes', PacienteController::class);
    Route::post('/pacientes/reporte-pdf', [PacienteController::class, 'generarReporte'])->name('pacientes.reporte');

    // Mensajería
    Route::get('/mensajes', [MensajeController::class, 'index'])->name('mensajes.index');
    Route::get('/mensajes/{id}', [MensajeController::class, 'show'])->name('mensajes.show');
    Route::post('/mensajes', [MensajeController::class, 'store'])->name('mensajes.store');

    // Citas
    Route::post('/doctores/{id}/agendar', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/mis-citas', [CitaController::class, 'index'])->name('pacientes.citas');
    Route::patch('/citas/{id}/estado', [CitaController::class, 'updateStatus'])->name('citas.status');

    // Comentarios y Respuestas
    Route::post('/comentarios', [ComentarioController::class, 'store'])->name('comentarios.save');
    Route::post('/respuestas', [RespuestaController::class, 'store'])->name('respuestas.store');

    // Reportes de usuario
    Route::prefix('reportes')->group(function () {
        Route::get('/create', [ReporteController::class, 'create'])->name('reportes.user.create');
        Route::post('/', [ReporteController::class, 'store'])->name('reportes.store');
        Route::get('/mis-reportes', [ReporteController::class, 'misReportes'])->name('reportes.mis');
    });

    // Gestión de Farmacia Propia
    Route::prefix('mi-farmacia')->group(function () {
        Route::get('/', [FarmaciaController::class, 'miFarmacia'])->name('farmacias.mi');
        Route::get('/editar', [FarmaciaController::class, 'editarMiFarmacia'])->name('farmacias.mi.editar');
        Route::put('/', [FarmaciaController::class, 'actualizarMiFarmacia'])->name('farmacias.mi.actualizar');
    });

    // Gestión de Doctores
    Route::get('doctor/data', [DoctorController::class, 'dataTable'])->name('doctor.data');
    Route::get('doctorstore', [DoctorController::class, 'create'])->name('doctores.agregar');
    Route::get('doctores/{doctor}/download-image', [DoctorController::class, 'downloadImage'])->name('doctor.download-image');
    Route::resource('doctores', DoctorController::class)->except(['show']);

    Route::middleware(['can.citas'])->group(function () {
        Route::get('/mis-citas-doc', [CitaController::class, 'index'])->name('doctores.citas');
    });
});

// --- RUTAS PARA ADMINISTRADOR ---
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/reportes', [ReporteController::class, 'adminIndex'])->name('admin.reportes.index');
    Route::get('/reportes/{id}', [ReporteController::class, 'adminShow'])->name('admin.reportes.show');
    Route::put('/reportes/{id}', [ReporteController::class, 'adminUpdate'])->name('admin.reportes.update');
    
    Route::get('/farmacias', [FarmaciaController::class, 'adminIndex'])->name('admin.farmacias.index');
    Route::get('/farmacias/crear', [FarmaciaController::class, 'adminCreate'])->name('admin.farmacias.create');
    Route::post('/farmacias', [FarmaciaController::class, 'adminStore'])->name('admin.farmacias.store');
    Route::get('/farmacias/{id}/editar', [FarmaciaController::class, 'adminEdit'])->name('admin.farmacias.edit');
    Route::put('/farmacias/{id}', [FarmaciaController::class, 'adminUpdate'])->name('admin.farmacias.update');
    Route::delete('/farmacias/{id}', [FarmaciaController::class, 'adminDestroy'])->name('admin.farmacias.destroy');
    Route::post('/farmacias/reporte-pdf', [FarmaciaController::class, 'generarReporte'])->name('admin.farmacias.reporte');
});

    Route::get("doctor/data", [DoctorController::class, "dataTable"])->name("doctor.data");
    Route::get("doctores/agregar", [DoctorController::class, "create"])->name("doctores.agregar");
    Route::get("doctores/{doctor}/download-image", [DoctorController::class, "downloadImage"])->name("doctor.download-image");
    Route::resource("doctores", DoctorController::class)->except(['show']);
    Route::resource('users', UserController::class);
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    
    Route::get('/mis-citas', [CitaController::class, 'index'])->name('pacientes.citas');
    // Solo doctores con la opción "citas" activa podrán entrar aquí
    Route::middleware(['auth', 'can.citas'])->group(function () {
        Route::get('/mis-citas-doc', [CitaController::class, 'index'])->name('doctores.citas');
        // ... otras rutas de citas
    });
    
    Route::post('/doctor/{id}/agendar', [App\Http\Controllers\CitaController::class, 'store'])->name('citas.store');


    Route::delete('/citas/{id}', [CitaController::class, 'destroy'])->name('citas.destroy');

    Route::patch('/citas/{id}/estado', [App\Http\Controllers\CitaController::class, 'updateStatus'])->name('citas.status');
    Route::get('/mensajes', [MensajeController::class, 'index'])->name('mensajes.index');
    Route::get('/mensajes/{id}', [MensajeController::class, 'show'])->name('mensajes.show');
    Route::post('/mensajes', [MensajeController::class, 'store'])->name('mensajes.store');
    Route::get('/directorio-mapa', [App\Http\Controllers\HomeController::class, 'mostrarMapa'])->name('mapa.directorio');


Auth::routes();
Route::get('/expedientes/crear', [ExpedienteController::class, 'create'])->name('expedientes.create');
Route::post('/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');
Route::get('/mis-expedientes', [ExpedienteController::class, 'index'])->name('expedientes.index');
    Route::get('/farmacias/{id}', [FarmaciaController::class, 'show'])->name('farmacias.detalle');
Route::resource('mensajes', App\Http\Controllers\MensajeController::class)->only('index', 'store');

Route::get('google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/directorio-medico', [DoctorController::class, 'vistageneral'])->name('doctores.vista');

Route::post('/comentarios', [ComentarioController::class, 'store'])
->middleware('auth')
->name('comentarios.store');

Route::get('/farmacias', [FarmaciaController::class, 'index'])->name('farmacias.catalogo');

Route::get('register', function () {
    $especialidades = Especialidad::all();
    return view('auth.register', compact('especialidades'));
})->middleware('guest')->name('register');

Route::post('register', [RegisterController::class, 'register']);

Route::post('/notas-medicas/{cita}', [App\Http\Controllers\NotaMedicaController::class, 'store'])->name('notas.store');

Route::resource('pacientes', App\Http\Controllers\PacienteController::class);

Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');

Route::get('/expedientes/{id}', [ExpedienteController::class, 'show'])->name('expedientes.show')->middleware('auth');

Route::get('/api/disponibilidad/{doctorId}', [App\Http\Controllers\CitaController::class, 'getDisponibilidad']);
// Route::resource('doctors', App\Http\Controllers\DoctorController::class)->except('show');

// Route::resource('comentarios', App\Http\Controllers\ComentarioController::class)->only('store', 'destroy');

// Route::resource('respuestas', App\Http\Controllers\RespuestaController::class)->only('store');

// Route::resource('mensajes', App\Http\Controllers\MensajeController::class)->only('index', 'store');

// Route::resource('doctors', App\Http\Controllers\DoctorController::class)->except('show');

// Route::resource('comentarios', App\Http\Controllers\ComentarioController::class)->only('store', 'destroy');
    
// Route::resource('respuestas', App\Http\Controllers\RespuestaController::class)->only('store');

// Route::resource('mensajes', App\Http\Controllers\MensajeController::class)->only('index', 'store');
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::resource('doctors', App\Http\Controllers\DoctorController::class)->except('show');

// Route::resource('comentarios', App\Http\Controllers\ComentarioController::class)->only('store', 'destroy');

// Route::resource('respuestas', App\Http\Controllers\RespuestaController::class)->only('store');

// cristian cristian

// Route::resource("products", ProductController::class)->except([
//     "show",
//     "update",
// ]);
// Route::get("productos", [ProductController::class, "index"])->name(
//     "products.index",
// );
// Route::get("productos/agregar", [ProductController::class, "create"])->name(
//     "products.create",
// );
// Route::get("products/data", [ProductController::class, "dataTable"])->name(
//     "products.data",
// );
// Route::get("products/{product}/download-image", [
//     ProductController::class,
//     "downloadImage",
// ])->name("products.download-image");

// Route::resource("doctores", DoctorController::class)->except([
//     "show",
//     // "update"

// ]);
// Route::get("doctores", [DoctorController::class, "index"])->name(
//     "doctores.index"
// );
// Route::get("doctores/{doctor}", [DoctorController::class, "show"])->name("doctores.show");
// Route::get("doctores/agregar", [DoctorController::class, "create"])->name(
//     "doctores.create",
// );
// Route::get("doctor/data", [DoctorController::class, "dataTable"])->name(
//     "doctor.data",
// );
// Route::get("doctores/{doctor}/download-image", [
//     DoctorController::class,
//     "downloadImage",
// ])->name("doctor.download-image");
// --- MANTENIMIENTO Y UTILIDADES ---
Route::get('/correr-seeders', function () {
    try {
        Artisan::call('db:seed', ['--force' => true]);
        return '¡Seeders ejecutados con éxito!';
    } catch (\Exception $e) {
        return 'Hubo un error: ' . $e->getMessage();
    }
});

// Ruta de backup con token de seguridad integrado
Route::get('/backup/uP8&vQ8#zL8*nX8!', function () {
    try {
        Artisan::call('backup:run');
        Artisan::call('backup:clean');
        return 'Backup completado exitosamente';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
