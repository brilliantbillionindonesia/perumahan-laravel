<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\HousingUser;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class FamilyController extends Controller
{
    public function card(Request $request){
        $validator = Validator::make($request->all(), [
            'family_card_id' => ['required', 'exists:family_cards,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = FamilyCard::where('id', $request->input('family_card_id'))
        ->with([
            'village:code,name',
            'subdistrict:code,name',
            'district:code,name',
            'province:code,name',
        ])
        ->first();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function list(Request $request){
        $validator = Validator::make($request->all(), [
            'family_card_id' => ['required', 'exists:family_cards,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

       $data = DB::table('family_cards as fc')
            ->join('citizens as c', function ($join) {
                $join->on('c.family_card_id', '=', 'fc.id')
                    ->whereNull('c.death_certificate_id'); // filter yg di ON clause
            })
            ->join('family_members as fm', 'fm.citizen_id', '=', 'c.id')
            ->where('fc.id', $request->input('family_card_id'))
            ->select(
                'fc.id as family_card_id',
                'c.id as citizen_id',
                'fm.id as family_member_id',
                'c.citizen_card_number',
                'c.fullname',
                'c.gender',
                'c.religion',
                'c.birth_place',
                'c.birth_date',
                'c.education_type',
                'c.work_type',
                'c.blood_type',
                'fm.relationship_status',
                'c.marital_status'
            )
            ->get();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function myList(Request $request){
        $housingUser = HousingUser::where('housing_id', $request->current_housing->housing_id)
        ->where('user_id', $request->user()->id)
        ->where('is_active', 1)
        ->first();

        if (!$housingUser) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ], HttpStatusCodes::HTTP_UNAUTHORIZED);
        }

        if(!$housingUser->citizen_id){
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Keluarga tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $citizen = Citizen::where('id', $housingUser->citizen_id)->first();

        $data = DB::table('family_cards as fc')
            ->join('citizens as c', function ($join) {
                $join->on('c.family_card_id', '=', 'fc.id')
                    ->whereNull('c.death_certificate_id'); // filter yg di ON clause
            })
            ->join('family_members as fm', 'fm.citizen_id', '=', 'c.id')
            ->where('fc.id', $citizen->family_card_id)
            ->select(
                'fc.id as family_card_id',
                'c.id as citizen_id',
                'fm.id as family_member_id',
                'c.citizen_card_number',
                'c.fullname',
                'c.gender',
                'c.religion',
                'c.birth_place',
                'c.birth_date',
                'c.education_type',
                'c.work_type',
                'c.blood_type',
                'fm.relationship_status',
                'c.marital_status'
            )
            ->get();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function myCard(Request $request){
        $housingUser = HousingUser::where('housing_id', $request->current_housing->housing_id)
        ->where('user_id', $request->user()->id)
        ->where('is_active', 1)
        ->first();

        if (!$housingUser) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ], HttpStatusCodes::HTTP_UNAUTHORIZED);
        }

        if(!$housingUser->citizen_id){
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Keluarga tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $citizen = Citizen::where('id', $housingUser->citizen_id)->first();

        $data = FamilyCard::where('id', $citizen->family_card_id)
        ->with([
            'village:code,name',
            'subdistrict:code,name',
            'district:code,name',
            'province:code,name',
        ])
        ->first();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }
}
