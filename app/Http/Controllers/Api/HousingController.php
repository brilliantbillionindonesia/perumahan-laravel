<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Models\HousingUser;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Repositories\HousingRepository;

class HousingController extends Controller
{

    public function list(Request $request)
    {
        $data = HousingRepository::queryHousing($request->user()->id)->get();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

    public function show(Request $request){
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
                'message' => 'Data not found',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);

    }
}
