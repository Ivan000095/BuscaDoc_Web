<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// IMPORTACIONES DE CONTROLADORES
// ─────────────────────────────────────────────────────────────
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MensajeriaController;
use App\Http\Controllers\Api\EspecialidadController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\FarmaciaController;
use App\Http\Controllers\API\PacienteController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\RespuestaController;

// ─────────────────────────────────────────────────────────────
// HEALTH CHECK / STATUS
// ─────────────────────────────────────────────────────────────
Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now(),
        'version' => '1.0.0',
    ]);
});

// ─────────────────────────────────────────────────────────────
// MÓDULO: AUTENTICACIÓN (PÚBLICO)
// ─────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ─────────────────────────────────────────────────────────────
// MÓDULO: ESPECIALIDADES (PÚBLICO)
// ─────────────────────────────────────────────────────────────
Route::get('/especialidades', [EspecialidadController::class, 'index'])->name('api.especialidades.index');
Route::get('/dashboard/especialidades', [EspecialidadController::class, 'apiDashboard']);

// ─────────────────────────────────────────────────────────────
// MÓDULO: DOCTORES (PÚBLICO - LECTURA)
// ─────────────────────────────────────────────────────────────
Route::apiResource('doctors', DoctorController::class);
Route::get('/statistics', [DoctorController::class, 'stats'])->name('api.doctors.stats');

// ─────────────────────────────────────────────────────────────
// MÓDULO: FARMACIAS (PÚBLICO - LECTURA)
// ─────────────────────────────────────────────────────────────
Route::get('/farmacias', [FarmaciaController::class, 'index'])->name('api.farmacias.index');
Route::get('/farmacias/{id}', [FarmaciaController::class, 'show'])->name('api.farmacias.show');

// ─────────────────────────────────────────────────────────────
// MÓDULO: BÚSQUEDA (PÚBLICO)
// ─────────────────────────────────────────────────────────────
Route::get('/buscar', [SearchController::class, 'apiSearch']);

// ─────────────────────────────────────────────────────────────
// MÓDULO: COMENTARIOS Y RESEÑAS (PÚBLICO - LECTURA)
// ─────────────────────────────────────────────────────────────
Route::get('/users/{userId}/comments', [ComentarioController::class, 'index']);
Route::get('/comments/{commentId}/replies', [RespuestaController::class, 'index']);

// ─────────────────────────────────────────────────────────────
// RUTAS PROTEGIDAS (auth:sanctum)
// ─────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ─── Autenticación (Protegido) ───────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::get('/tokens', [AuthController::class, 'tokens']);
        Route::delete('/tokens', [AuthController::class, 'revokeToken']);
    });

    // ─── Dashboard / Home ────────────────────────────────────
    Route::get('/home-dashboard', [HomeController::class, 'getHomeData']);

    // ─── Gestión de Usuarios ─────────────────────────────────
    Route::get('/user/{id}', [UserController::class, 'show'])->name('api.user.show');
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::post('/usuarios/fcm-token', [UserController::class, 'guardarFcmToken']);

    // ─── Gestión de Pacientes ────────────────────────────────
    Route::apiResource('pacientes', PacienteController::class)->names('api.pacientes');

    // ─── Mensajería / Chat ───────────────────────────────────
    Route::get('/mensajes/contactos', [MensajeriaController::class, 'getContactosApi']);
    Route::get('/mensajes/{id}', [MensajeriaController::class, 'getMensajesApi']);
    Route::post('/mensajes', [MensajeriaController::class, 'storeApi']);

    // ─── Mi Farmacia (dueño) ─────────────────────────────────
    Route::get('/mi-farmacia', [FarmaciaController::class, 'miFarmacia'])->name('api.farmacias.yo');
    Route::get('/mi-farmacia/editar', [FarmaciaController::class, 'editarMiFarmacia'])->name('api.farmacias.yo.editar');
    Route::put('/mi-farmacia', [FarmaciaController::class, 'actualizarMiFarmacia'])->name('api.farmacias.yo.actualizar');

    // ─── Comentarios y Reseñas (Escritura protegida) ─────────
    Route::post('/comments', [ComentarioController::class, 'store']);
    Route::get('/users/{userId}/can-review', [ComentarioController::class, 'canReview']);
    Route::post('/comments/{commentId}/reply', [RespuestaController::class, 'store']);

});

// ─────────────────────────────────────────────────────────────
// FALLBACK - RUTA NO ENCONTRADA
// ─────────────────────────────────────────────────────────────
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint no encontrado',
        'error' => 'La ruta solicitada no existe',
    ], 404);
});