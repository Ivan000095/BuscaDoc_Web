<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MensajeriaController;
use App\Http\Controllers\Api\EspecialidadController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FarmaciaController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ReplyController;
use App\Http\Controllers\Api\CitaController;

Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now(),
        'version' => '1.0.0',
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});



Route::get('/especialidades', [EspecialidadController::class, 'index'])->name('api.especialidades.index');
Route::get('/dashboard/especialidades', [EspecialidadController::class, 'apiDashboard']);

Route::apiResource('doctors', DoctorController::class);
Route::get('/statistics', [DoctorController::class, 'stats'])->name('api.doctors.stats');


Route::get('/farmacias', [FarmaciaController::class, 'index'])->name('api.farmacias.index');
Route::get('/farmacias/{id}', [FarmaciaController::class, 'show'])->name('api.farmacias.show');

Route::get('/buscar', [SearchController::class, 'apiSearch']);

Route::get('/users/{userId}/comments', [CommentController::class, 'index']);
Route::get('/comments/{commentId}/replies', [ReplyController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::get('/tokens', [AuthController::class, 'tokens']);
        Route::delete('/tokens', [AuthController::class, 'revokeToken']);
    });


    Route::get('/home-dashboard', [HomeController::class, 'getHomeData']);


    Route::get('/user/{id}', [UserController::class, 'show'])->name('api.user.show');
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::post('/usuarios/fcm-token', [UserController::class, 'guardarFcmToken']);


    Route::apiResource('pacientes', PacienteController::class)->names('api.pacientes');


    Route::get('/mensajes/contactos', [MensajeriaController::class, 'getContactosApi']);
    Route::get('/mensajes/{id}', [MensajeriaController::class, 'getMensajesApi']);
    Route::post('/mensajes', [MensajeriaController::class, 'storeApi']);

    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/users/{userId}/can-review', [CommentController::class, 'canReview']);
    Route::post('/comments/{commentId}/reply', [ReplyController::class, 'store']);

    Route::get('/citas', [CitaController::class, 'index']);
    Route::patch('/citas/{id}/status', [CitaController::class, 'updateStatus']);
    Route::post('/citas/{id}/solicitar-cambio', [CitaController::class, 'solicitarCambio']);
    Route::post('/citas/{id}/responder-cambio', [CitaController::class, 'responderCambio']);
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);

    Route::get('/mis-expedientes', function (Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->expedientes()->select('id', 'nombre_completo')->get()
        ]);
    });

    Route::post('/doctores/{id}/agendar', [App\Http\Controllers\Api\CitaController::class, 'store']);

    Route::get('/expedientes', [App\Http\Controllers\Api\ExpedienteController::class, 'index']);
    Route::post('/expedientes', [App\Http\Controllers\Api\ExpedienteController::class, 'store']);
    Route::get('/expedientes/{id}', [App\Http\Controllers\Api\ExpedienteController::class, 'show']);
    Route::put('/expedientes/{id}', [App\Http\Controllers\Api\ExpedienteController::class, 'update']);
    
    Route::post('/citas/{id}/finalizar', [App\Http\Controllers\Api\CitaController::class, 'finalizarConDiagnostico']);

});


Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint no encontrado',
        'error' => 'La ruta solicitada no existe',
    ], 404);
});