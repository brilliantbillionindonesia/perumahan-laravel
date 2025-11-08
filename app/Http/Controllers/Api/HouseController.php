<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Repositories\HouseRepository;
use App\Models\Citizen;
use App\Models\House;
use App\Models\Housing;
use App\Models\HousingUser;
use Illuminate\Http\Request;
use Validator;


class HouseController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
            'only_head' => ['nullable', 'boolean'],
            'blood_type' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'marital_status' => ['nullable', 'string'],
            'work_type' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page = (int) ($request->input('page', 1));
        $perPage = (int) ($request->input('per_page', 10));
        $data = HouseRepository::queryHouse();

        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('houses.block', 'like', '%' . $request->input('search') . '%');
                $q->orwhere('houses.number', 'like', '%' . $request->input('search') . '%');
                $q->orwhere('chead.fullname', 'like', '%' . $request->input('search') . '%');
                $q->orwhere('house_name', 'like', '%' . $request->input('search') . '%');
            });
        }

        $data->orderBy('houses.block', 'desc');
        $data->orderBy('houses.number', 'asc');

        $data->limit($perPage)
            ->offset(($page - 1) * $perPage);


        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data->get()->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'exists:houses,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = HouseRepository::queryHouse()
            ->where('houses.id', $request->input('id'))
            ->first();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function showByFamilyCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "family_card_id" => ['required', 'exists:houses,family_card_id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = HouseRepository::queryHouse()
            ->where('houses.family_card_id', $request->input('family_card_id'))
            ->first();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function me(Request $request)
    {
        $citizen = Citizen::where('id', $request->current_housing->citizen_id)->first();

        if (!$citizen) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan'
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $house = House::where('family_card_id', $citizen->family_card_id)
            ->where('housing_id', $request->input('housing_id'))
            ->first();

        if (!$house) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan'
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $data = HouseRepository::queryHouse()
            ->where('houses.id', $house->id)
            ->first();

        if (!$data) {
            return response()->json([
                'success' => true,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan'
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }


}
