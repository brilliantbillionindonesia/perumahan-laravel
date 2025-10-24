<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Constants\RelationshipStatusOption;
use App\Http\Controllers\Controller;
use App\Http\Services\ActivityLogService;
use App\Http\Services\UploadConvertImageService;
use App\Jobs\DispatchGuestStore;
use App\Models\Citizen;
use App\Models\Guest;
use App\Models\House;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class GuestController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
            'is_me' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $housingId = $request->input('housing_id');

        // Pagination setup
        $page = max((int) $request->get('page', 1), 1);
        $perPage = min((int) $request->get('per_page', 10), 30);

        // Query dasar
        $query = DB::table('guests as g')
            ->join('houses as h', 'h.id', '=', 'g.house_id')
            ->join('citizens as c', 'h.head_citizen_id', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'g.registered_by')
            ->where('g.housing_id', $housingId)
            ->select(

                'g.id',
                'g.name',
                'g.relationship',
                'h.block',
                'h.number',
                'c.fullname',
                'g.registered_at',
                'u.name as registered_by'
            );

        if ($request->get('is_me')) {
            $query->where('g.registered_by', $request->user()->id);
        }

        // Filter search (title & description)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('g.name', 'like', "%{$search}%")
                    ->orWhere('c.fullname', 'like', "%{$search}%");
            });
        }

        // Order by terbaru
        $query->orderBy('registered_at', 'desc');

        // Pagination
        $data = $query->limit($perPage)
            ->offset(($page - 1) * $perPage);

        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Berhasil menampilkan data',
                'data' => $data->get()->toArray(),
            ],
            HttpStatusCodes::HTTP_OK
        );
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:guests,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $housingId = $request->input('housing_id');

        $query = DB::table('guests as g')
            ->join('houses as h', 'h.id', '=', 'g.house_id')
            ->join('citizens as c', 'h.head_citizen_id', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'g.registered_by')
            ->where('g.housing_id', $housingId)
            ->where('g.id', $request->id)
            ->select(
                'g.id',
                'g.name',
                'g.relationship',
                'h.block',
                'h.number',
                'c.fullname',
                'g.registered_at',
                'u.name as registered_by'
            );

        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Berhasil menampilkan data pengaduan',
                'data' => $query->first()
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }

    public function getIdentification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:guests,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $data = Guest::where('id', $request->id)->first();
        $filePath = $data->identification;
        $fileUrl = $filePath
            ? asset('storage/' . ltrim($filePath, '/')) // ✅ ubah ke URL publik
            : null;
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $fileUrl
        ], HttpStatusCodes::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'relationship' => [
                'required',
                'string',
                Rule::in(RelationshipStatusOption::all()),
            ],
        ], [
            'relationship.in' => 'Relationship must be one of: ' . implode(', ', RelationshipStatusOption::all()),
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        if (!$request->current_housing->citizen_id) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNAUTHORIZED,
                'message' => 'Anda tidak dapat menambahkan tamu karena Anda belum memiliki keluarga.',
            ], HttpStatusCodes::HTTP_UNAUTHORIZED);
        }

        $citizen = Citizen::where('id', $request->current_housing->citizen_id)->first();
        $house = House::where('family_card_id', $citizen->family_card_id)->first();

        $convert = UploadConvertImageService::setFolder('guests')->uploadConvert($request)->original;

        $createGuest = Guest::create([
            'housing_id' => $request->input('housing_id'),
            'name' => ucwords($request->name),
            'relationship' => $request->relationship,
            'family_card_id' => $citizen->family_card_id,
            'identification' => $convert['path'],
            'registered_by' => auth()->user()->id,
            'registered_at' => now(),
            'house_id' => $house->id,
        ]);

        ActivityLogService::logModel(
            model: $createGuest->getTable(),
            rowId: $createGuest->id,
            json: $createGuest->toArray(), // ini tetap array untuk JSON
            type: 'create',
        );

        DispatchGuestStore::dispatch(
            guestId: $createGuest->id
        )->onQueue('notifications');

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_CREATED,
            'data' => $createGuest,
        ], HttpStatusCodes::HTTP_CREATED);
    }
}
