<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireShopAdminMiddleware
{
    /**
     * Valida que el usuario tenga rol de Director o Administrador_Tienda exclusivo para BLANKISOL SHOP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado: Usuario no autenticado o inactivo.',
                ], 401);
            }

            return redirect()->route('shop.login')->with('error', 'Debes iniciar sesión con una cuenta autorizada para acceder al panel de la tienda.');
        }

        // Acceso exclusivo a SHOP: Director General o Administrador de Tienda
        if ($user->role === UserRole::DIRECTOR || $user->role === UserRole::ADMINISTRADOR_TIENDA) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso Denegado: No posees privilegios de administración comercial para BLANKISOL SHOP.',
            ], 403);
        }

        abort(403, 'Acceso Denegado: No posees privilegios de administración comercial para BLANKISOL SHOP.');
    }
}
