<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Services\ActivityLogService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function changeRole(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id'   => ['required', 'exists:users,id'],
            'role_code' => ['required', Rule::exists('roles', 'code')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('id', $request->input(key: 'user_id'))->first();
        $data = [
            'role_code' => $request->input('role_code'),
        ];
        $user->update($data);

        ActivityLogService::logModel(
            model: User::getModel()->getTable(),
            rowId: $user->id,
            json: $user->toArray(),
            type: 'update',
        );

        return response()->json([
            'success' => true,
            'code'    => HttpStatusCodes::HTTP_OK,
            'message' => 'Role berhasil diganti',
            'user'    => $user->load('role'),
        ], HttpStatusCodes::HTTP_OK);
    }
}
