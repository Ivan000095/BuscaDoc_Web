<?php

use App\Http\Controllers\API\DoctorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes. Todas las rutas aquí serán prefijadas con /api
|--------------------------------------------------------------------------
*/

// RUTAS PUBLICAS
// -----------------------------------------------------------------
Route::prefix("auth")->group(function () {
    // /api/auth/register
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);
});

// Ruta para verificar el estado de la API
Route::get("/status", function () {
    return response()->json([
        "success" => true,
        "message" => "API funcionando correctamente",
        "timestamp" => now(),
        "version" => "1.0.0",
    ]);
});

// RUTAS PROTEGIDAS (requieren autenticación)
// -----------------------------------------------------------------
Route::middleware("auth:sanctum")->group(function () {
    Route::prefix("auth")->group(function () {
        // Rutas de autenticación para usuarios autenticados
        // por ejemplo: POST /api/auth/logout
        Route::post("/logout", [AuthController::class, "logout"]);
        Route::post("/logout-all", [AuthController::class, "logoutAll"]);
        Route::get("/me", [AuthController::class, "me"]);
        Route::put("/profile", [AuthController::class, "updateProfile"]);
        Route::get("/tokens", [AuthController::class, "tokens"]);
        Route::delete("/tokens", [AuthController::class, "revokeToken"]);
    });

    // Rutas de productos (CRUD completo)
    // Route::name('api.products.')->prefix('products')->group(function () {
        
    //     Route::apiResource("/", ProductController::class)->parameters([
    //         "" => "product" // Asegura que el parámetro se llame {product}
    //     ]);

    //     // Rutas adicionales
    //     // GET /api/products/statistics
    //     Route::get("/statistics", [ProductController::class, "stats"])
    //          ->name('stats'); // Nombre: api.products.stats

    //     // POST /api/products/{product}/upload-image
    //     Route::post("/{product}/upload-image", [
    //         ProductController::class,
    //         "uploadImage",
    //     ])->name('upload-image'); // Nombre: api.products.upload-image

    //     // DELETE /api/products/{product}/image
    //     Route::delete("/{product}/image", [
    //         ProductController::class,
    //         "deleteImage",
    //     ])->name('delete-image'); // Nombre: api.products.delete-image
    // });

    Route::get("/statistics", [DoctorController::class, "stats"])->name('stats'); 
    Route::apiResource('doctors', App\Http\Controllers\API\DoctorController::class);
});

// Manejo de rutas no encontradas
Route::fallback(function () {
    return response()->json(
        [
            "success" => false,
            "message" => "Endpoint no encontrado",
            "error" => "La ruta solicitada no existe",
        ],
        404,
    );
});




Route::apiResource('doctors', App\Http\Controllers\API\DoctorController::class);

Route::apiResource('comentarios', App\Http\Controllers\API\ComentarioController::class);

Route::apiResource('respuestas', App\Http\Controllers\API\RespuestaController::class);

Route::apiResource('mensajes', App\Http\Controllers\API\MensajeController::class);
