<?php

namespace App\Http\Controllers\Api\Financial;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Services\ActivityLogService;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class FeeController extends Controller
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

        $data = Fee::selectRaw('
            id,
            financial_category_code,
            name,
            amount,
            frequency,
            due_day,
            billing_date,
            deleted_at
        ')->where('housing_id', $request->input('housing_id'));

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
            "id" => ['required', 'exists:fees,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Fee::selectRaw('
            id,
            financial_category_code,
            name,
            amount,
            frequency,
            due_day,
            billing_date,
            deleted_at
        ')->where('housing_id', $request->input('housing_id'))
        ->where('id', $request->input('id'))
        ->withTrashed()
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
            "financial_category_code" => ['required', 'exists:financial_categories,code'],
            "name" => ['required', 'string', 'max:255', Rule::unique('fees', 'name')->whereNull('deleted_at')],
            "amount" => ['required', 'numeric'],
            "frequency" => ['required', 'string', Rule::in(['once', 'recurring'])],
            "due_day" => ['required_if:frequency,recurring', 'integer', 'min:1', 'max:31'],
            "billing_date" => ['required_if:frequency,once', 'date'],
        ], [
            "billing_date.required_if" => 'Tanggal pembayaran harus diisi',
            "due_day.required_if" => 'Hari pembayaran harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Fee::create([
            'housing_id' => $request->input('housing_id'),
            'financial_category_code' => $request->input('financial_category_code'),
            'name' => ucwords($request->input('name')),
            'amount' => $request->input('amount'),
            'frequency' => $request->input('frequency'),
            'due_day' => $request->input('due_day'),
            'billing_date' => $request->input('billing_date'),
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
            "id" => ['required', 'exists:fees,id'],
            "financial_category_code" => ['nullable', 'exists:financial_categories,code,deleted_at,NULL'],
            "name" => ['nullable', 'string', 'max:255', Rule::unique('fees', 'name')->whereNull('deleted_at')->ignore($request->input('id'))],
            "amount" => ['nullable', 'numeric'],
            "frequency" => ['nullable', 'string', Rule::in(['once', 'recurring'])],
            "due_day" => ['required_if:frequency,recurring', 'integer', 'min:1', 'max:31'],
            "billing_date" => ['required_if:frequency,once', 'date'],
        ], [
            "billing_date.required_if" => 'Tanggal pembayaran harus diisi',
            "due_day.required_if" => 'Hari pembayaran harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Fee::where('id', $request->input('id'))
        ->where('housing_id', $request->input('housing_id'))
        ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $data->financial_category_code = $request->input('financial_category_code', $data->financial_category_code);
        $data->name = $request->input('name', $data->name);
        $data->amount = $request->input('amount', $data->amount);
        $data->frequency = $request->input('frequency', $data->frequency);
        $data->due_day = $request->input('due_day', $data->due_day);
        $data->billing_date = $request->input('billing_date', $data->billing_date);
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
            'id' => [
                'required',
                Rule::exists('fees', 'id')->whereNull('deleted_at'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Fee::where('id', $request->input('id'))
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
                Rule::exists('fees', 'id')->whereNotNull('deleted_at'),
            ],
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Fee::where('id', $request->input('id'))
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
