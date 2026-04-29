<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && (int)$request->user()->id_role === 2) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Accès refusé : Réservé aux administrateurs.'
        ], 403);
    }
}