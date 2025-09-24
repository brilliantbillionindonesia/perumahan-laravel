<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Log;

class LogController extends Controller
{
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'table'   => ['required'],
            'row_id' => ['required'],
            'json' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $log = new Log();
        $log->table = $request->input('table');
        $log->row_id = $request->input('row_id');
        $log->logged_by = auth()->user()->id;
        $log->logged_at = now();
        $log->json = $request->input('json');
        $log->save();

        return response()->json([
            'success' => true,
            'code'    => HttpStatusCodes::HTTP_OK,
            'message' => 'Log berhasil ditambahkan',
        ], HttpStatusCodes::HTTP_OK);
    }
}
