<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo aplicar a rutas API o requests que esperan JSON
        if (!$request->is("api/*") && !$request->expectsJson()) {
            return $response;
        }

        // Detectar redirects y convertirlos a JSON para APIs
        if ($response->isRedirection()) {
            $statusCode = $response->getStatusCode();

            // Si es un redirect de autenticación (401/403)
            if ($statusCode === 302 || $statusCode === 301) {
                $location = $response->headers->get("Location");

                // Si está redirigiendo al login, es un error de autenticación
                if (str_contains($location, "/login")) {
                    return response()->json(
                        [
                            "success" => false,
                            "message" => "No autorizado",
                            "error" => "Token de acceso inválido o ausente",
                        ],
                        401,
                    );
                }

                // Otros redirects
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Redirección no permitida en API",
                        "error" =>
                            "El endpoint requiere autenticación o permisos específicos",
                        "redirect_url" => $location,
                    ],
                    401,
                );
            }
        }

        // Si la respuesta ya es JSON, no modificarla
        if (
            $response->headers->get("Content-Type") === "application/json" ||
            str_contains(
                $response->headers->get("Content-Type", ""),
                "application/json",
            )
        ) {
            return $response;
        }

        // Manejar diferentes códigos de estado HTTP
        $statusCode = $response->getStatusCode();

        // Si es una excepción no manejada
        if ($statusCode >= 400 && $statusCode < 600) {
            $data = [
                "success" => false,
                "message" => $this->getStatusMessage($statusCode),
                "error" => $response->getContent() ?: "Error del servidor",
                "status_code" => $statusCode,
            ];

            return response()->json($data, $statusCode);
        }

        return $response;
    }

    /**
     * Obtener mensaje basado en el código de estado
     */
    private function getStatusMessage(int $statusCode): string
    {
        $messages = [
            400 => "Solicitud incorrecta",
            401 => "No autorizado",
            403 => "Prohibido",
            404 => "Recurso no encontrado",
            405 => "Método no permitido",
            422 => "Error de validación",
            429 => "Demasiadas solicitudes",
            500 => "Error interno del servidor",
            502 => "Bad Gateway",
            503 => "Servicio no disponible",
        ];

        return $messages[$statusCode] ?? "Error del servidor";
    }
}
