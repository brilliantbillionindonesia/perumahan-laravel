<?php

namespace App\Http\Middleware;

use App\Constants\HttpStatusCodes;
use Closure;
use Illuminate\Support\Facades\Http;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (! $request->user() || ! $request->user()->hasPermission($permission)) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                'message' => 'Forbidden Access',
            ], HttpStatusCodes::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
