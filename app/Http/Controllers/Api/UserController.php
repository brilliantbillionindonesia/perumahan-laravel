<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Repositories\HousingRepository;
use App\Http\Services\ActivityLogService;
use App\Models\HousingUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWelcomeEmailJob;
use Str;

class UserController extends Controller
{
    public function changeRole(Request $request){
        $validator = Validator::make($request->all(), [
            'housing_user_id'   => ['required', 'exists:housing_users,id'],
            'role_code' => ['required', Rule::exists('roles', 'code')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = HousingUser::where('id', $request->input('housing_user_id'))
        ->where('is_active', 1)
        ->where('housing_id', $request->current_housing->housing_id);

        $user = $user->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'code'    => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'User tidak ditemukan',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = [
            'role_code' => $request->input('role_code'),
        ];

        $user->update($data);


        ActivityLogService::logModel(
            model: HousingUser::getModel()->getTable(),
            rowId: $user->id,
            json: $user->toArray(),
            type: 'update',
        );

        return response()->json([
            'success' => true,
            'code'    => HttpStatusCodes::HTTP_OK,
            'message' => 'Role berhasil diganti',
            'user'    => $user,
        ], HttpStatusCodes::HTTP_OK);
    }

    public function list(Request $request){
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'order_by' => ['nullable', 'string'],
            'order_dir' => ['nullable', Rule::in(['asc', 'desc', 'ASC', 'DESC'])],
            'with_trashed' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page = (int) ($request->input('page', 1));
        $perPage = (int) ($request->input('per_page', 15));

        $builder = HousingRepository::queryHousingUser($request->input('housing_id'));
        $builder->orderBy('hu.created_at', 'desc');

        $paginator = $builder->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
                'last' => $paginator->url($paginator->lastPage()),
            ],
        ], HttpStatusCodes::HTTP_OK);


    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $generatedPassword = Str::random(8);

        $user = User::create([
            'name' => ucwords($request->input('name')),
            'email' => $request->input('email'),
            'password' => bcrypt($generatedPassword),
        ]);

        HousingUser::create([
            'user_id' => $user->id,
            'housing_id' => $request->input('housing_id'),
            'role_code' => 'citizen',
            'is_active' => false,
        ]);

        SendWelcomeEmailJob::dispatch($user->id, $generatedPassword);

        return response()->json([
            'success' => true,
            'code'    => HttpStatusCodes::HTTP_OK,
            'message' => 'User berhasil ditambahkan',
            'user'    => $user,
        ], HttpStatusCodes::HTTP_OK);

    }
}
