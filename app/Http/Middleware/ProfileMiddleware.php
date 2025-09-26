<?php

namespace App\Http\Middleware;

use App\Constants\HttpStatusCodes;
use App\Http\Repositories\HousingRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProfileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'housing_id' => ['required', 'exists:housings,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = HousingRepository::queryHousing($request->user()->id)
        ->where('hu.housing_id', $request->input('housing_id'))
        ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Housing ID not matched',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $request->current_housing = $data;

        return $next($request);
    }
}
