<?php

use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;
use App\Models\Especialidad;
use App\Http\Controllers\FarmaciaController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ChatbotController;

Route::get("/", function () {
    return view("welcome-simple");
})->name('welcome');

// Custom auth routes with better control
Route::get("login", [LoginController::class, "showLoginForm"])->name("login");
Route::post("login", [LoginController::class, "login"]);
Route::match(["get", "post"], "logout", [LoginController::class, "logout"])
    ->name("logout")
    ->middleware(["auth", "security:logout"]);

Route::get("register", [
    RegisterController::class,
    "showRegistrationForm",
])->name("register");
Route::post("register", [RegisterController::class, "register"]);

Route::get("password/reset", [
    ForgotPasswordController::class,
    "showLinkRequestForm",
])->name("password.request");
Route::post("password/email", [
    ForgotPasswordController::class,
    "sendResetLinkEmail",
])->name("password.email");
Route::get("password/reset/{token}", [
    ResetPasswordController::class,
    "showResetForm",
])->name("password.reset");
Route::post("password/reset", [ResetPasswordController::class, "reset"])->name(
    "password.update",
);
Route::get("password/confirm", [
    ConfirmPasswordController::class,
    "showConfirmForm",
])->name("password.confirm");
Route::post("password/confirm", [ConfirmPasswordController::class, "confirm"]);

Route::middleware(["auth", "security:auth"])->group(function () {
    Route::get("/home", [HomeController::class, "index"])->name("home");

Route::middleware('auth')->group(function () {
    Route::get('/reportes/create', [ReporteController::class, 'create'])->name('reportes.user.create');
    Route::post('/reportes', [ReporteController::class, 'store'])
        ->name('reportes.store');
    Route::get('/reportes/mis-reportes', [ReporteController::class, 'misReportes'])
        ->name('reportes.mis');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/mi-farmacia', [FarmaciaController::class, 'miFarmacia'])->name('farmacias.mi');
    Route::get('/mi-farmacia/editar', [FarmaciaController::class, 'editarMiFarmacia'])->name('farmacias.mi.editar');
    Route::put('/mi-farmacia', [FarmaciaController::class, 'actualizarMiFarmacia'])->name('farmacias.mi.actualizar');
});

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
    Route::get('/farmacias/{id}', [FarmaciaController::class, 'show'])->name('farmacias.detalle');
});

    Route::get("doctor/data", [DoctorController::class, "dataTable"])->name("doctor.data");
    Route::get("doctores/agregar", [DoctorController::class, "create"])->name("doctores.agregar");
    Route::get("doctores/{doctor}/download-image", [DoctorController::class, "downloadImage"])->name("doctor.download-image");
    Route::resource("doctores", DoctorController::class);
    Route::resource('users', UserController::class);
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/doctores/{id}/agendar', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/mis-citas', [CitaController::class, 'index'])->name('pacientes.citas');
    Route::get('/mis-citas-doc', [CitaController::class, 'index'])->name('doctores.citas');
    Route::patch('/citas/{id}/estado', [App\Http\Controllers\CitaController::class, 'updateStatus'])->name('citas.status');
    Route::get('/buscar', [App\Http\Controllers\SearchController::class, 'search'])->name('global.search');
    Route::get('/mensajes', [MensajeController::class, 'index'])->name('mensajes.index');
    Route::get('/mensajes/{id}', [MensajeController::class, 'show'])->name('mensajes.show');
    Route::post('/mensajes', [MensajeController::class, 'store'])->name('mensajes.store');
    Route::get('/directorio-mapa', [App\Http\Controllers\HomeController::class, 'mostrarMapa'])->name('mapa.directorio');
});

Auth::routes();

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

Route::resource('pacientes', App\Http\Controllers\PacienteController::class);

Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');

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