<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrar TODOS los middleware aliases en una sola llamada
        $middleware->alias([
            // Aliases de tu aplicación
            "security" => \App\Http\Middleware\SecurityMiddleware::class,
            "api.response" => \App\Http\Middleware\ApiResponseMiddleware::class,
            "force.json" =>
                \App\Http\Middleware\ForceJsonResponseMiddleware::class,

            // Aliases de Sanctum (movidos aquí)
            "auth:sanctum" =>
                \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            "ability" =>
                \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);

        // Configurar middleware para APIs
        $middleware->api([
            \App\Http\Middleware\ForceJsonResponseMiddleware::class,
            \App\Http\Middleware\ApiResponseMiddleware::class,
        ]);

        // Registrar middleware web globalmente si es necesario
        $middleware->web(
            append: [
                // Aquí podríamos agregar middleware web si fuera necesario
            ],
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejar excepciones de autenticación para APIs
        $exceptions->render(function (
            \Illuminate\Auth\AuthenticationException $e,
            $request,
        ) {
            if ($request->is("api/*") || $request->expectsJson()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "No autorizado",
                        "error" => "Token de acceso inválido o expirado",
                    ],
                    401,
                );
            }

            // Para rutas web, redirigir al login
            return redirect()->guest(route("login"));
        });

        // Manejar otras excepciones para APIs
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            $request,
        ) {
            if ($request->is("api/*") || $request->expectsJson()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Recurso no encontrado",
                        "error" => "El endpoint solicitado no existe",
                    ],
                    404,
                );
            }
        });

        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e,
            $request,
        ) {
            if ($request->is("api/*") || $request->expectsJson()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Método no permitido",
                        "error" =>
                            "El método HTTP no está permitido para este endpoint",
                    ],
                    405,
                );
            }
        });
    })
    ->create();
    