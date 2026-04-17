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

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\GoogleController;
use App\Models\Especialidad;

// RUTAS PÚBLICAS
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('inicio');
Route::get('/buscar', [SearchController::class, 'search'])->name('global.search');
Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
Route::post('/doctores/reporte-pdf', [App\Http\Controllers\DoctorController::class, 'generarReporte'])->name('doctores.reporte');
Route::get('/directorio-medico', [DoctorController::class, 'vistageneral'])->name('doctores.vista');

//  RUTAS ESTÁTICAS
Route::get('/farmacias', [FarmaciaController::class, 'index'])->name('farmacias.catalogo');
Route::get('/especialidades', [EspecialidadesController::class, 'index'])->name('especialidades.index');

//  RUTAS DINÁMICAS
Route::get('/farmacias/{id}', [FarmaciaController::class, 'show'])->name('farmacias.detalle');
Route::get('/especialidades/{id}', [EspecialidadesController::class, 'show'])->name('specs.show');
Route::get('/doctores/{id}', [DoctorController::class, 'show'])->name('doctores.show');

// RUTAS DE AUTENTICACIÓN
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('register', function () {
    return view('auth.register', ['especialidades' => Especialidad::all()]);
})->middleware('guest')->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

Route::get('google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback']);

//RUTAS PROTEGIDAS
Route::middleware(['auth'])->group(function () {
    Route::match(['get', 'post'], 'logout', [LoginController::class, 'logout'])
        ->name('logout')
        ->middleware('security:logout');

    Route::middleware(['security:auth'])->group(function () {
        Route::get('/directorio-mapa', [HomeController::class, 'mostrarMapa'])->name('mapa.directorio');
    });

    Route::resource('users', UserController::class);
    Route::resource('pacientes', PacienteController::class);
    Route::post('/pacientes/reporte-pdf', [App\Http\Controllers\PacienteController::class, 'generarReporte'])->name('pacientes.reporte');

    Route::get('/mensajes', [MensajeController::class, 'index'])->name('mensajes.index');
    Route::get('/mensajes/{id}', [MensajeController::class, 'show'])->name('mensajes.show');
    Route::post('/mensajes', [MensajeController::class, 'store'])->name('mensajes.store');

    Route::post('/doctores/{id}/agendar', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/mis-citas', [CitaController::class, 'index'])->name('pacientes.citas');
    Route::patch('/citas/{id}/estado', [CitaController::class, 'updateStatus'])->name('citas.status');

    Route::post('/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');

    Route::prefix('reportes')->group(function () {
        Route::get('/create', [ReporteController::class, 'create'])->name('reportes.user.create');
        Route::post('/', [ReporteController::class, 'store'])->name('reportes.store');
        Route::get('/mis-reportes', [ReporteController::class, 'misReportes'])->name('reportes.mis');
    });

    Route::prefix('mi-farmacia')->group(function () {
        Route::get('/', [FarmaciaController::class, 'miFarmacia'])->name('farmacias.mi');
        Route::get('/editar', [FarmaciaController::class, 'editarMiFarmacia'])->name('farmacias.mi.editar');
        Route::put('/', [FarmaciaController::class, 'actualizarMiFarmacia'])->name('farmacias.mi.actualizar');
    });

    Route::get('doctor/data', [DoctorController::class, 'dataTable'])->name('doctor.data');
    Route::get('doctorstore', [DoctorController::class, 'create'])->name('doctores.agregar');
    Route::get('doctores/{doctor}/download-image', [DoctorController::class, 'downloadImage'])->name('doctor.download-image');
    Route::resource('doctores', DoctorController::class)->except(['show']);

    Route::middleware(['can.citas'])->group(function () {
        Route::get('/mis-citas-doc', [CitaController::class, 'index'])->name('doctores.citas');
    });
});

// RUTAS PARA ADMON

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
    Route::post('/farmacias/reporte-pdf', [App\Http\Controllers\FarmaciaController::class, 'generarReporte'])->name('admin.farmacias.reporte');
});