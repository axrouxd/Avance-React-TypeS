<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Verificat que el usuario esté autenticado
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Verificarr que sea administrador 
        if ($user->role_id !== 1){
            return response()->json(['message' => 'Acceso Denegao requiere permisos de Administrador'], 403);
        }

        return $next($request);
    }
}
