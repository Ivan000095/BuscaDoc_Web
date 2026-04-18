<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importaciones de Controladores (Ordenadas)
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FarmaciaController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\EspecialidadController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MensajeriaController;
use App\Http\Controllers\Api\RespuestaController;
use App\Http\Controllers\Api\ComentarioController;
// use App\Http\Controllers\Api\ProductController; // Descomentar cuando lo uses


Route::get("/status", function () {
    return response()->json([
        "success" => true,
        "message" => "Api funcionando correctamente",
        "timestamp" => now(),
        "version" => "1.0.0",
    ]);
});

Route::prefix("auth")->group(function () {
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);
});

Route::get('/especialidades', [EspecialidadController::class, 'index'])->name('Api.specs.index');
Route::get('/dashboard/especialidades', [EspecialidadController::class, 'ApiDashboard']);
Route::ApiResource('doctors', DoctorController::class);
Route::get('/farmacias', [FarmaciaController::class, 'index'])->name('Api.farmacias.index');
Route::get('/farmacias/{id}', [FarmaciaController::class, 'show'])->name('Api.farmacias.show');
Route::get('/buscar', [App\Http\Controllers\Api\SearchController::class, 'ApiSearch']);


Route::middleware("auth:sanctum")->group(function () {

    Route::prefix("auth")->group(function () {
        Route::post("/logout", [AuthController::class, "logout"]);
        Route::post("/logout-all", [AuthController::class, "logoutAll"]);
        Route::get("/me", [AuthController::class, "me"]);
        Route::put("/profile", [AuthController::class, "updateProfile"]);
        Route::get("/tokens", [AuthController::class, "tokens"]);
        Route::delete("/tokens", [AuthController::class, "revokeToken"]);
    });

    Route::get('/home-dashboard', [HomeController::class, 'getHomeData']);

    Route::get('/user/{id}', [UserController::class, 'show'])->name('Api.user.show');
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::post('/usuarios/fcm-token', [UserController::class, 'guardarFcmToken']);

    Route::get('/mensajes/contactos', [MensajeriaController::class, 'getContactosApi']);
    Route::get('/mensajes/{id}', [MensajeriaController::class, 'getMensajesApi']);
    Route::post('/mensajes', [MensajeriaController::class, 'storeApi']);

    Route::get("/statistics", [DoctorController::class, "stats"])->name('Api.doctors.stats'); 

    Route::get('/mi-farmacia', [FarmaciaController::class, 'miFarmacia'])->name('Api.farmacias.yo');
    Route::get('/mi-farmacia/editar', [FarmaciaController::class, 'editarMiFarmacia'])->name('Api.farmacias.yo.editar');
    Route::put('/mi-farmacia', [FarmaciaController::class, 'actualizarMiFarmacia'])->name('Api.farmacias.yo.actualizar');

    Route::ApiResource('pacientes', PacienteController::class)->names('Api.pacientes'); 

    Route::ApiResource('comentarios', ComentarioController::class);
    Route::ApiResource('respuestas', RespuestaController::class);

});


Route::fallback(function () {
    return response()->json([
        "success" => false,
        "message" => "Endpoint no encontrado",
        "error" => "La ruta solicitada no existe",
    ], 404);
});