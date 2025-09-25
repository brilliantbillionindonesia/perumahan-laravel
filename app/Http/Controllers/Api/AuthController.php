<?php

namespace App\Http\Controllers\API;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // jika berhasil, buat token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], HttpStatusCodes::HTTP_OK);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], HttpStatusCodes::HTTP_OK);
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
            DB::raw('role.name as role_name')
        )
        ->first();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_CREATED,
            'message' => "Success",
            'data' => $rows
        ], HttpStatusCodes::HTTP_CREATED);

    }
}
