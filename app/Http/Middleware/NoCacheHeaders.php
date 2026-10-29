<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Evita que el navegador guarde en caché las páginas que requieren
 * sesión iniciada (panel admin, dashboard, perfil). Sin esto, al usar
 * el botón "atrás" del navegador después de cerrar sesión (o entre
 * usuarios distintos en el mismo equipo), podría mostrarse una versión
 * guardada de una pantalla que ya no debería ser accesible.
 */
class NoCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
