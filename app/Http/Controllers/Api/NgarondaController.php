<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Services\ActivityLogService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NgarondaController extends Controller
{
    public function list(Request $request){
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        // !!! LOGIC IS HERE

        $date = $request->input('date');
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => [
                'date' => $date,
                'names' => [
                    'name1',
                    'name2',
                    'name3'
                ]
            ]
        ], HttpStatusCodes::HTTP_OK);


    }

    public function store(Request $request){
        // validator
        // validator fails
        // logic

        ActivityLogService::logModel(
            model: 'roles',
            rowId: 1,
            json: (array) [], // cast ke array
            type: 'create',
        );

        // response
    }

    public function update(Request $request){
        // validator
        // validator fails
        // logic

        ActivityLogService::logModel(
            model: 'roles',
            rowId: 1,
            json: (array) [], // cast ke array
            type: 'update',
        );

        // response
    }

    public function delete(Request $request){
        // validator
        // validator fails
        // logic

        ActivityLogService::logModel(
            model: 'roles',
            rowId: 1,
            json: (array) [], // cast ke array
            type: 'update',
        );

        // response
    }

}
