<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class DeviceController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'platform' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $device = DeviceToken::updateOrCreate([
            'user_id' => auth()->user()->id,
        ], [
            'token' => $request->input('token'),
            'platform' => $request->input('platform'),
            'is_active' => 1
        ]);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
            'data' => $device
        ], HttpStatusCodes::HTTP_OK);

    }

}
