<?php

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

Route::get("/", function () {
    return view("welcome-simple");
});

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

    Route::resource("doctores", DoctorController::class)->except([
        "show",
        // "update"

    ]);
    Route::get("doctores", [DoctorController::class, "index"])->name(
        "doctores.index"
    );
    Route::get("doctores/{doctor}", [DoctorController::class, "show"])->name("doctores.show");
    Route::get("doctores/agregar", [DoctorController::class, "create"])->name(
        "doctores.create",
    );
    Route::get("doctor/data", [DoctorController::class, "dataTable"])->name(
        "doctor.data",
    );
    Route::get("doctores/{doctor}/download-image", [
        DoctorController::class,
        "downloadImage",
    ])->name("doctor.download-image");

});

// Route::middleware('auth:api')->group(function({
    
// }));

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');