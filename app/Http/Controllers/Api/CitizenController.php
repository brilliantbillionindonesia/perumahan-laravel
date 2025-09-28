<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Constants\RelationshipStatusOption;
use App\Http\Controllers\Controller;
use App\Http\Repositories\CitizenRepository;
use Illuminate\Http\Request;
use Validator;

class CitizenController extends Controller
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
        $data = CitizenRepository::queryCitizen($request->input('housing_id'));

        if($request->input('only_head')) {
            $data->where('fm.relationship_status', RelationshipStatusOption::KEPALA_KELUARGA);
        }

        if($request->input('blood_type')) {
            $data->where('c.blood_type', $request->input('blood_type'));
        }

        if($request->input('gender')) {
            $data->where('c.gender', $request->input('gender'));
        }

        if($request->input('marital_status')) {
            $data->where('c.marital_status', $request->input('marital_status'));
        }

        if($request->input('work_type')) {
            $data->where('c.work_type', $request->input('work_type'));
        }

        $data->limit($perPage)
        ->offset(($page - 1) * $perPage);

        if($request->input('search')) {
            $data->where(function($q) use($request) {
                $q->where('c.fullname', 'like', '%' . $request->input('search') . '%');
                // ->orWhere('hs.`number`', 'like', '%' . $request->input('search') . '%');
            });
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data->get()->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

}
