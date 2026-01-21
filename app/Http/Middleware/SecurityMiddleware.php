<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$options,
    ): Response {
        $response = $next($request);

        // Apply security headers based on options
        if (in_array("logout", $options)) {
            $this->addLogoutSecurityHeaders($response, $request);
        } elseif (in_array("auth", $options)) {
            $this->addAuthSecurityHeaders($response);
        } else {
            $this->addBasicSecurityHeaders($response);
        }

        return $response;
    }

    /**
     * Add comprehensive security headers for logout
     */
    private function addLogoutSecurityHeaders(
        Response $response,
        Request $request,
    ): void {
        // Clear all site data on logout
        $response->headers->set(
            "Clear-Site-Data",
            '"cache", "cookies", "storage", "executionContexts"',
        );

        // Prevent caching
        $response->headers->set(
            "Cache-Control",
            "no-cache, no-store, max-age=0, must-revalidate, private",
        );
        $response->headers->set("Pragma", "no-cache");
        $response->headers->set("Expires", "Fri, 01 Jan 1990 00:00:00 GMT");

        // Add logout method header for debugging
        $method = $request->isMethod("get") ? "GET" : "POST";
        $response->headers->set("X-Logout-Method", $method);
        $response->headers->set("X-Logout-Time", now()->timestamp);

        // Apply all other security headers
        $this->addBasicSecurityHeaders($response);
    }

    /**
     * Add security headers for authenticated pages
     */
    private function addAuthSecurityHeaders(Response $response): void
    {
        // Prevent back navigation caching
        $response->headers->set(
            "Cache-Control",
            "no-cache, no-store, max-age=0, must-revalidate",
        );
        $response->headers->set("Pragma", "no-cache");
        $response->headers->set("Expires", "Fri, 01 Jan 1990 00:00:00 GMT");

        // Apply basic security headers
        $this->addBasicSecurityHeaders($response);
    }

    /**
     * Add basic security headers for all requests
     */
    private function addBasicSecurityHeaders(Response $response): void
    {
        // Prevent clickjacking
        $response->headers->set("X-Frame-Options", "DENY");

        // Prevent MIME type sniffing
        $response->headers->set("X-Content-Type-Options", "nosniff");

        // XSS Protection
        $response->headers->set("X-XSS-Protection", "1; mode=block");

        // Referrer Policy
        $response->headers->set("Referrer-Policy", "no-referrer");

        // HTTPS Strict Transport Security (only for HTTPS)
        if (request()->secure()) {
            $response->headers->set(
                "Strict-Transport-Security",
                "max-age=31536000; includeSubDomains; preload",
            );
        }
    }
}
