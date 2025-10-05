<?php

namespace App\Http\Controllers\Api\Financial;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use App\Models\CashBalance;
use App\Constants\HttpStatusCodes;
use Illuminate\Validation\Rule;
use Validator;


class CashBalanceController extends Controller
{
    public function latest(Request $request){
        $data = CashBalance::where('housing_id', $request->housing_id)
        ->orderBy('created_at', 'desc')
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

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'year' => ['nullable', 'string'],
            'month' => ['nullable', 'string'],
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

        $data = CashBalance::where('housing_id', $request->input('housing_id'))
        ->when($request->input('year'), function ($query) use ($request) {
            $query->where('year', $request->input('year'));
        })
        ->when($request->input('month'), function ($query) use ($request) {
            $query->where('month', $request->input('month'));
        })
        ->orderBy('created_at', 'desc')
        ->limit($perPage)
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
            "cash_balance_id" => ['required', 'exists:cash_balances,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }


        $data = CashBalance::where('id', $request->input('cash_balance_id'))
        ->where('housing_id', $request->input('housing_id'))
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
            'message' => 'Success',
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function transaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cash_balance_id" => ['required', 'exists:cash_balances,id'],
            "search" => ['nullable', 'string'],
            "type" => ['nullable', Rule::in(['expense', 'income'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }


        $cashBalance = CashBalance::where('id', $request->input('cash_balance_id'))
        ->where('housing_id', $request->input('housing_id'))
        ->first();

        if (!$cashBalance) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $year = $cashBalance->year;
        $month = $cashBalance->month;

        $data = FinancialTransaction::where('housing_id', $request->input('housing_id'))
        ->whereYear('transaction_date', $year)
        ->when($request->input('search'), function ($query) use ($request) {
            $query->where('note', 'like', '%' . $request->input('search') . '%');
        })
        ->when($request->input('type'), function ($query) use ($request) {
            if($request->input('type') != null || $request->input('type') != 'all') {
                $query->where('type', $request->input('type'));
            }
        })
        ->whereMonth('transaction_date', $month)
        ->orderBy('created_at', 'desc')
        ->get();

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
            'message' => 'Success',
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }
}
