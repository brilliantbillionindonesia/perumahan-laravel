<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ForceJsonMiddleware
{
    public function handle($request, Closure $next)
    {
        // pastikan server menganggap request ini expect JSON
        $request->headers->set('Accept', 'application/json');

        /** @var SymfonyResponse $response */
        $response = $next($request);

        // kalau bukan JsonResponse, set Content-Type JSON
        if (!$response instanceof JsonResponse) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
