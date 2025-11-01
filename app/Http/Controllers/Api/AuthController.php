<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\Housing;
use App\Models\HousingUser;
use App\Models\PermissionRole;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Jika ingin langsung memberikan token saat register
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
            'token' => 'nullable|string',
            'platform' => 'nullable|string',
        ], [
            'email.exists' => 'Email tidak ditemukan',
            'password.required' => 'Password harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();
        $userToken = null;
        if($request->token) {
            $userToken = DeviceToken::where('user_id', $user->id)
            ->first();

            if($userToken) {
                if($userToken->token != $request->token) {
                    return response()->json([
                        'success' => false,
                        'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Akun masih login di perangkat lain',
                    ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
        }

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Email atau password salah',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($userToken){
            $userToken->token = $request->token;
            $userToken->platform = $request->platform;
            $userToken->save();
        } else {
            DeviceToken::create([
                'user_id' => $user->id,
                'platform' => $request->platform,
                'token' => $request->token
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Berhasil login',
            'token' => $token,
            'user' => $user,
        ], HttpStatusCodes::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($request->token) {
            DeviceToken::where('user_id', $request->user()->id)->delete();
        }

        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], HttpStatusCodes::HTTP_OK);
    }

    public function checkToken(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNAUTHORIZED,
                'message' => 'Token tidak ditemukan',
            ], HttpStatusCodes::HTTP_UNAUTHORIZED);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || $accessToken->expires_at?->isPast()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNAUTHORIZED,
                'message' => 'Token sudah tidak aktif atau kedaluwarsa',
            ], HttpStatusCodes::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_CREATED,
            'message' => "Token masih aktif",
            'data' => $accessToken->tokenable
        ], HttpStatusCodes::HTTP_CREATED);
    }

    public function me(Request $request)
    {
        $userId    = $request->user()->id;
        $housingId = $request->current_housing->housing_id;

        $rows = DB::table('users as u')
        ->join('housing_users as hu', 'hu.user_id', '=', 'u.id')
        ->join('roles as role', 'role.code', '=', 'hu.role_code')
        ->where('hu.user_id', $userId)
        ->where('hu.housing_id', $housingId)
        ->select(
            'u.id',
            'u.name',
            'u.email',
            DB::raw('role.code as role_code'),
            DB::raw('role.name as role_name')
        )
        ->first();

        $otherHousing = HousingUser::where('user_id', $userId)
        ->where('housing_id', '!=', $housingId)
        ->where('is_active', 1)
        ->get();
        $permissionRole = PermissionRole::where('role_code', $rows->role_code)->pluck('permission_code')->toArray();
        $rows->other_housing = count($otherHousing);
        $rows->permissions = $permissionRole;

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_CREATED,
            'message' => "Success",
            'data' => $rows
        ], HttpStatusCodes::HTTP_CREATED);

    }
}
