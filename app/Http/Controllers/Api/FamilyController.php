<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function list(Request $request)
    {
        $data = Complaint::with(['category:code,name,id', 'status:code,name,id'])->get();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:complaints,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Complaint::with(['category:code,name,id', 'status:code,name,id'])
            ->find($request->id);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => ['required', 'string'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:complaint_categories,id'],
            'status_id'   => ['required', 'exists:complaint_statuses,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $complaint = Complaint::create($request->only([
            'title', 'description', 'category_id', 'status_id'
        ]));

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_CREATED,
            'data' => $complaint
        ], HttpStatusCodes::HTTP_CREATED);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => ['required', 'exists:complaints,id'],
            'title'       => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:complaint_categories,id'],
            'status_id'   => ['nullable', 'exists:complaint_statuses,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $complaint = Complaint::find($request->id);
        $complaint->update($request->only(['title', 'description', 'category_id', 'status_id']));

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $complaint
        ], HttpStatusCodes::HTTP_OK);
    }
}