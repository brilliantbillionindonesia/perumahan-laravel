<?php

namespace App\Http\Controllers\Api\Financial;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Services\ActivityLogService;
use App\Models\FinancialCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
            'with_trash' => ['nullable', 'boolean'],
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

        $data = FinancialCategory::selectRaw('
            id,
            code,
            name,
            type,
            deleted_at
        ')->where(function($query) use ($request) {
            $query->where('housing_id', $request->input('housing_id'))
            ->orWhereNull('housing_id');
        });


        if ($request->input('with_trash', false)) {
            $data = $data->withTrashed();
        }

        if ($request->input('search')) {
            $data = $data->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $data->orderBy('created_at', 'desc');

        $data = $data->limit($perPage)->offset(($page - 1) * $perPage);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data->get()->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

    public function show(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'exists:financial_categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = FinancialCategory::selectRaw('
            id,
            code,
            name,
            type,
            deleted_at
        ')
        ->where(function($query) use ($request) {
            $query->where('housing_id', $request->input('housing_id'))
            ->orWhereNull('housing_id');
        })
        ->withTrashed()
        ->where('id', $request->input('id'))
        ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => ['required', 'string', Rule::unique('financial_categories', 'name')->whereNull('deleted_at'), 'max:50'],
            "type" => ['required', 'string', Rule::in(['expense', 'income'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = FinancialCategory::create([
            'housing_id' => $request->input('housing_id'),
            'code' => strtolower(str_replace(' ', '_', $request->input('name'))),
            'name' => ucwords($request->input('name')),
            'type' => $request->input('type'),
        ]);

        ActivityLogService::logModel(
            model: $data->getTable(),
            rowId: $data->id,
            json: $data->toArray(), // cast ke array
            type: 'create',
        );

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil disimpan',
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'exists:financial_categories,id,deleted_at,NULL'],
            "name" => ['nullable', 'string', 'max:50', Rule::unique('financial_categories', 'name')->ignore($request->input('id'))],
            "type" => ['nullable', 'string', Rule::in(['expense', 'income'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = FinancialCategory::where('id', $request->input('id'))
        ->where('housing_id', $request->input('housing_id'))
        ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $data->name = $request->input('name', $data->name);
        $data->code = strtolower(str_replace(' ', '_', $request->input('name', $data->name)));
        $data->type = $request->input('type', $data->type);
        $data->save();

        ActivityLogService::logModel(
            model: $data->getTable(),
            rowId: $request->input('id'),
            json: $data->toArray(), // cast ke array
            type: 'update',
        );

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil diperbarui',
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function delete(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'exists:financial_categories,id,deleted_at,NULL'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = FinancialCategory::where('id', $request->input('id'))
        ->where('housing_id', $request->input('housing_id'))
        ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $data->delete();

        ActivityLogService::logModel(
            model: $data->getTable(),
            rowId: $request->input('id'),
            json: $data->toArray(), // cast ke array
            type: 'delete',
        );

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil dihapus',
        ], HttpStatusCodes::HTTP_OK);
    }

    public function restore(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                Rule::exists('financial_categories', 'id')->whereNotNull('deleted_at'),
            ],
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = FinancialCategory::where('id', $request->input('id'))
        ->where('housing_id', $request->input('housing_id'))
        ->withTrashed()
        ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $data->restore();

        ActivityLogService::logModel(
            model: $data->getTable(),
            rowId: $request->input('id'),
            json: $data->toArray(), // cast ke array
            type: 'restore',
        );

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil dipulihkan',
        ], HttpStatusCodes::HTTP_OK);
    }

}
