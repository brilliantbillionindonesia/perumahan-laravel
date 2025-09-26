<?php

namespace App\Http\Middleware;

use App\Constants\HttpStatusCodes;
use App\Models\PermissionRole;
use Closure;
use Illuminate\Support\Facades\Http;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        $permission_role = PermissionRole::where('permission_code', $permission)
        ->where('role_code', $request->current_housing->role_code)->first();

        if (!$permission_role) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                'message' => 'Forbidden Access',
            ], HttpStatusCodes::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
