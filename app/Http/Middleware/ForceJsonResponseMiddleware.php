<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Forzar que las rutas API siempre esperen y devuelvan JSON
        if ($request->is("api/*")) {
            // Agregar header Accept: application/json si no existe
            if (
                !$request->hasHeader("Accept") ||
                !str_contains($request->header("Accept"), "application/json")
            ) {
                $request->headers->set("Accept", "application/json");
            }

            // Agregar header Content-Type para requests con contenido
            if (
                $request->isMethod("POST") ||
                $request->isMethod("PUT") ||
                $request->isMethod("PATCH")
            ) {
                if (
                    !$request->hasHeader("Content-Type") &&
                    !$request->hasFile()
                ) {
                    $request->headers->set("Content-Type", "application/json");
                }
            }
        }

        $response = $next($request);

        // Solo procesar rutas API
        if (!$request->is("api/*")) {
            return $response;
        }

        // Si la respuesta es un redirect y estamos en API, convertir a JSON
        if ($response->isRedirection()) {
            $location = $response->headers->get("Location");

            // Detectar redirects al login (no autenticado)
            if (str_contains($location, "/login")) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "No autorizado",
                        "error" => "Token de acceso requerido",
                    ],
                    401,
                );
            }

            // Otros redirects
            return response()->json(
                [
                    "success" => false,
                    "message" => "Acceso denegado",
                    "error" => "No tienes permisos para acceder a este recurso",
                ],
                403,
            );
        }

        // Si es una respuesta HTML en una ruta API, convertir a JSON
        $contentType = $response->headers->get("Content-Type", "");
        if (str_contains($contentType, "text/html") && $request->is("api/*")) {
            $statusCode = $response->getStatusCode();

            // Determinar el tipo de error basado en el código de estado
            if ($statusCode === 404) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Recurso no encontrado",
                        "error" => "El endpoint solicitado no existe",
                    ],
                    404,
                );
            }

            if ($statusCode === 403) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Acceso prohibido",
                        "error" =>
                            "No tienes permisos para acceder a este recurso",
                    ],
                    403,
                );
            }

            if ($statusCode === 401) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "No autorizado",
                        "error" => "Token de acceso inválido o expirado",
                    ],
                    401,
                );
            }

            if ($statusCode >= 500) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Error interno del servidor",
                        "error" => "Se produjo un error inesperado",
                    ],
                    $statusCode,
                );
            }

            // Para otros códigos, respuesta genérica
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error en la solicitud",
                    "error" =>
                        "La respuesta no pudo ser procesada correctamente",
                ],
                $statusCode,
            );
        }

        // Asegurar que la respuesta tenga el header JSON correcto
        if (
            $request->is("api/*") &&
            !str_contains($contentType, "application/json")
        ) {
            $response->headers->set("Content-Type", "application/json");
        }

        return $response;
    }
}
