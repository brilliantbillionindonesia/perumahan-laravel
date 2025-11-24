<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Repositories\HousingRepository;
use App\Http\Services\ActivityLogService;
use App\Jobs\SendEmailToMarketing;
use App\Jobs\SendGeneratedPassword;
use App\Models\Citizen;
use App\Models\Housing;
use App\Models\HousingUser;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWelcomeEmailJob;
use Str;

class UserController extends Controller
{
    public function changeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'housing_user_id' => ['required', 'exists:housing_users,id'],
            'role_code' => ['required', Rule::exists('roles', 'code')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
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
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
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
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Role berhasil diganti',
            'user' => $user,
        ], HttpStatusCodes::HTTP_OK);
    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'order_by' => ['nullable', 'string'],
            'order_dir' => ['nullable', Rule::in(['asc', 'desc', 'ASC', 'DESC'])],
            'with_trashed' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string'],
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
        $builder->when($request->input('search'), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('u.name', 'like', '%' . $search . '%')
                    ->orWhere('u.email', 'like', '%' . $search . '%');
            });
        });
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
        ], HttpStatusCodes::HTTP_OK);


    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "housing_user_id" => ['required', 'exists:housing_users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = HousingRepository::queryHousingUser($request->input('housing_id'));
        $data->where('hu.id', $request->input('housing_user_id'));
        $data = $data->first();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'citizen_id' => ['nullable', 'exists:citizens,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required'],
        ], [
            'email.unique' => 'Email sudah terdaftar',
            'email.required' => 'Email wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $generatedPassword = Str::random(8);
        $checkuser = User::where('email', $request->input('email'))->first();

        if (!$checkuser) {
            $isNewUser = true;
            $user = User::create([
                'name' => ucwords($request->input('name')),
                'email' => $request->input('email'),
                'password' => bcrypt($generatedPassword),
                'is_generated_password' => true
            ]);
        } else {
            $isNewUser = false;
            $user = $checkuser;
        }

        if ($request->input('citizen_id')) {
            HousingUser::updateOrCreate(
                [
                    'housing_id' => $request->input('housing_id'),
                    'citizen_id' => $request->input('citizen_id'),
                ],
                [
                    'user_id' => $user->id,
                    'role_code' => 'citizen',
                    'is_active' => true,
                ]
            );
        } else {
            HousingUser::create([
                'housing_id' => $request->input('housing_id'),
                'user_id' => $user->id,
                'role_code' => 'citizen',
                'is_active' => true,
            ]);
        }

        $housingName = $request->current_housing->housing_name;

        SendWelcomeEmailJob::dispatch($user->id, $generatedPassword, $isNewUser, $housingName)->onQueue('notifications');

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'User berhasil ditambahkan',
            'user' => $user,
        ], HttpStatusCodes::HTTP_OK);

    }

    public function storeDemo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'unique:users'],
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'email.required' => 'Email wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $generatedPassword = Str::random(8);
        $checkuser = User::where('email', $request->input('email'))->first();

        if (!$checkuser) {
            $isNewUser = true;
            $user = User::create([
                'name' => ucwords($request->input('name')),
                'email' => $request->input('email'),
                'password' => bcrypt($generatedPassword),
                'is_generated_password' => true
            ]);
        } else {
            $isNewUser = false;
            $user = $checkuser;
        }

        $housingId = Housing::where('is_demo', 1)->first(); 

        if (!$housingId) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Maaf demo aplikasi sedang tidak tersedia. Silahkan hubungi tim kami.',
            ]);
        }

        if ($request->input('citizen_id')) {
            HousingUser::updateOrCreate(
                [
                    'housing_id' => $housingId->id,
                ],
                [
                    'user_id' => $user->id,
                    'role_code' => 'citizen',
                    'is_active' => true,
                ]
            );
        } else {
            HousingUser::create([
                'housing_id' => $housingId->id,
                'user_id' => $user->id,
                'role_code' => 'citizen',
                'is_active' => true,
            ]);
        }

        $housingName = $housingId->housing_name;
        SendWelcomeEmailJob::dispatch($user->id, $generatedPassword, $isNewUser, $housingName)->onQueue('notifications');
        SendEmailToMarketing::dispatch($user->id)->onQueue('notifications');
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Anda berhasil daftar untuk Demo Aplikasi',
            'user' => $user,
        ], HttpStatusCodes::HTTP_OK);

    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'
            ],
            'confirm_password' => ['required', 'string', 'min:8', 'same:password'],
        ], [
            'password.regex' => 'Password harus memiliki minimal 1 huruf besar dan 1 karakter khusus',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = auth()->user();
        $user->password = bcrypt($request->input('password'));
        $user->email_verified_at = now();
        $user->is_generated_password = false;
        $user->save();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Password berhasil diubah',
        ], HttpStatusCodes::HTTP_OK);
    }

    public function generatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $generatedPassword = Str::random(8);

        $user = User::where('email', $request->input('email'))->first();
        $user->password = bcrypt($generatedPassword);
        $user->is_generated_password = true;
        $user->save();

        SendGeneratedPassword::dispatch($user->id, $generatedPassword)->onQueue('notifications');

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'password berhasil diubah',
        ], HttpStatusCodes::HTTP_OK);

    }

    public function syncCitizen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'citizen_id' => ['nullable', 'exists:citizens,id'],
            'housing_user_id' => ['nullable', 'exists:housing_users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $housingUserByCitizen = HousingUser::where('housing_id', $request->input('housing_id'))
        ->where('citizen_id', $request->input('citizen_id'))
        ->first();

        $housingUserById = HousingUser::where('id', $request->input('housing_user_id'))
        ->where('housing_id', $request->input('housing_id'))
        ->first();

        if(!$housingUserByCitizen && !$housingUserById) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Housing user tidak ditemukan',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $housingUserById->citizen_id = $request->input('citizen_id');
        $housingUserById->save();

        $citizen = Citizen::where('id', $request->input('citizen_id'))->first();

        User::where('id', $housingUserById->user_id)
        ->update([
            'name' => $citizen->fullname
        ]);

        HousingUser::where('citizen_id', $request->input('citizen_id'))
        ->where('housing_id', $request->input('housing_id'))
        ->whereNull('user_id')
        ->delete();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Pengguna berhasil disinkronkan',
        ], HttpStatusCodes::HTTP_OK);

    }
}
