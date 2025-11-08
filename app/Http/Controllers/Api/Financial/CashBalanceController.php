<?php

namespace App\Http\Controllers\Api\Financial;

use App\Http\Controllers\Controller;
use App\Http\Services\ActivityLogService;
use App\Models\FinancialTransaction;
use DB;
use Illuminate\Http\Request;
use App\Models\CashBalance;
use App\Constants\HttpStatusCodes;
use Illuminate\Validation\Rule;
use Validator;


class CashBalanceController extends Controller
{
    public function latest(Request $request){
        $data = CashBalance::where('housing_id', $request->housing_id)
        ->where('payment_method', 'all')
        ->orderBy('created_at', 'desc')
        ->first();

        if (!$data) {
            return response()->json([
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], HttpStatusCodes::HTTP_OK);
        }

        $cashBalance = CashBalance::where('housing_id', $request->housing_id)
        ->where('year', $data->year)
        ->where('month', $data->month)
        ->where('payment_method', 'cash')
        ->first();

        $nonCashBalance = CashBalance::where('housing_id', $request->housing_id)
        ->where('year', $data->year)
        ->where('month', $data->month)
        ->where('payment_method', 'non_cash')
        ->first();

        $data->cash = $cashBalance ? $cashBalance->closing_balance : 0;
        $data->non_cash = $nonCashBalance ? $nonCashBalance->closing_balance : 0;

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


    // initial amount digunakan saat awal pendaftaran perumahan untuk menambahkan saldo awal
    // jika perumahan sudah ada, tidak perlu menggunakan initial amount
    public function initialAmount(Request $request){
        $validator = Validator::make($request->all(), [
            "amount_cash" => ['required', 'numeric', 'min:0'],
            "amount_non_cash" => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $existCashBalance = CashBalance::where('housing_id', $request->input('housing_id'))
        ->where('payment_method', 'cash')
        ->first();

        if ($existCashBalance) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_CONFLICT,
                'message' => 'Cash balance dengan method cash sudah ada',
            ], HttpStatusCodes::HTTP_CONFLICT);
        }

        $existNonCashBalance = CashBalance::where('housing_id', $request->input('housing_id'))
        ->where('payment_method', 'non_cash')
        ->first();

        if ($existNonCashBalance) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_CONFLICT,
                'message' => 'Cash balance dengan method non cash sudah ada',
            ], HttpStatusCodes::HTTP_CONFLICT);
        }

        $existAllBalance = CashBalance::where('housing_id', $request->input('housing_id'))
        ->where('year', date('Y'))
        ->where('month', date('m'))
        ->where('payment_method', 'all')
        ->first();

        $cash = CashBalance::create([
            'housing_id' => $request->input('housing_id'),
            'year' => date('Y'),
            'month' => date('m'),
            'opening_balance' => $request->input('amount_cash'),
            'closing_balance' => $request->input('amount_cash'),
            'payment_method' => "cash",
        ]);

        $noncash = CashBalance::create([
            'housing_id' => $request->input('housing_id'),
            'year' => date('Y'),
            'month' => date('m'),
            'opening_balance' => $request->input('amount_non_cash'),
            'closing_balance' => $request->input('amount_non_cash'),
            'payment_method' => "non_cash",
        ]);

        if ($existAllBalance) {
            $existAllBalance->opening_balance = $existAllBalance->opening_balance + $request->input('amount_cash') + $request->input('amount_non_cash');
            $existAllBalance->closing_balance = $existAllBalance->closing_balance + $request->input('amount_cash') + $request->input('amount_non_cash');
            $existAllBalance->save();

            $all = $existAllBalance;
        } else {
            $all = CashBalance::create([
                'housing_id' => $request->input('housing_id'),
                'year' => date('Y'),
                'month' => date('m'),
                'opening_balance' => $request->input('amount_cash') + $request->input('amount_non_cash'),
                'closing_balance' => $request->input('amount_cash') + $request->input('amount_non_cash'),
                'payment_method' => 'all',
            ]);
        }

        ActivityLogService::logModel(
            model: 'cash_balances',
            rowId: $cash->id,
            json: $cash->toArray(), // cast ke array
            type: 'create',
        );

        ActivityLogService::logModel(
            model: 'cash_balances',
            rowId: $noncash->id,
            json: $noncash->toArray(), // cast ke array
            type: 'create',
        );

        ActivityLogService::logModel(
            model: 'cash_balances',
            rowId: $all->id,
            json: $all->toArray(), // cast ke array
            type: 'create',
        );


        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Berhasil menambahkan saldo awal',
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
