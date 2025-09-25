<?php

namespace App\Http\Middleware;

use App\Constants\HttpStatusCodes;
use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {

        if ($request->current_housing->role_code != $role) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                'message' => 'Forbidden Access',
            ], HttpStatusCodes::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
