<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Un client connecté ne doit même pas savoir que l'espace de gestion existe
        abort_unless($request->user()?->is_admin, 404);

        return $next($request);
    }
}
