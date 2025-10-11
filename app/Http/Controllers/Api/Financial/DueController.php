<?php

namespace App\Http\Controllers\Api\Financial;

use App\Constants\DueStatus;
use App\Constants\DueStatusOption;
use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Repositories\Financial\DueRepository;
use App\Http\Services\ActivityLogService;
use App\Http\Services\PushService;
use App\Jobs\DispatchPayDue;
use App\Models\CashBalance;
use App\Models\Citizen;
use App\Models\Due;
use App\Models\Fee;
use App\Models\FinancialTransaction;
use App\Models\House;
use App\Models\Payment;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class DueController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['paid', 'unpaid'])],
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

        $data = DueRepository::duesSummary($request);
        $data->when($request->input('search'), function ($query) use ($request) {
            $search = "%" . $request->input('search') . "%";
            $query->havingRaw("(MAX(c.fullname) LIKE ? OR CONCAT(h.block, '/', h.number) LIKE ?)", [$search, $search]);
        });
        $data->limit($perPage)
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
            "house_id" => ['required', 'exists:houses,id'],
            "periode" => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = DueRepository::duesSummary($request)->first();

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

    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "house_id" => ['required', 'exists:houses,id'],
            'status' => ['nullable', Rule::in(['paid', 'unpaid'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = DueRepository::showDetailQuery($request)->get()->toArray();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "due_id" => ['required', 'exists:dues,id', 'array'],
            "note" => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = Due::whereIn('id', $request->input('due_id'))
        ->whereIn('status', DueStatusOption::NOTPAID)
        ->get();
        foreach ($data as $key => $value) {
            $index = $key + 1;
            DB::transaction(function () use ($value, $request, $index) {
                $now = now();

                $transactionCode = generateTransactionCode($value->housing_id, $now);

                $value->status = "paid";
                $value->save();

                ActivityLogService::logModel(
                    model: $value->getTable(),
                    rowId: $value->id,
                    json: $value->toArray(), // ini tetap array untuk JSON
                    type: 'update',
                );

                $house = House::where('id', $value->house_id)->first();

                $payment = new Payment();
                $payment->housing_id = $value->housing_id;
                $payment->transaction_code = $transactionCode;
                $payment->house_id = $value->house_id;
                $payment->due_id = $value->id;
                $payment->amount = $value->amount;
                $payment->paid_at = $now;
                $payment->note = $request->input('note');
                $payment->created_by = auth()->user()->id;
                $payment->save();

                ActivityLogService::logModel(
                    model: $payment->getTable(),
                    rowId: $payment->id,
                    json: $payment->toArray(), // ini tetap array untuk JSON
                    type: 'create',
                );


                $fee = Fee::where('id', $value->fee_id)->first();

                $financialTransaction = new FinancialTransaction();
                $financialTransaction->transaction_code = $transactionCode;
                $financialTransaction->housing_id = $value->housing_id;
                $financialTransaction->house_id = $value->house_id;
                $financialTransaction->financial_category_code = $fee->financial_category_code;
                $financialTransaction->amount = $value->amount;
                $financialTransaction->transaction_date = $now;
                $financialTransaction->type = "income";
                $financialTransaction->note = "Pembayaran " . $fee->name ." ".$house->block." - ".$house->number;
                $financialTransaction->save();

                ActivityLogService::logModel(
                    model: $financialTransaction->getTable(),
                    rowId: $financialTransaction->id,
                    json: $financialTransaction->toArray(), // ini tetap array untuk JSON
                    type: 'create',
                );

                $cashBalance = CashBalance::where('housing_id', $value->housing_id)
                ->where('year', $now->year)
                ->where('month', $now->month)
                ->first();

                if($cashBalance) {
                    $cashBalance->income += $value->amount;
                    $cashBalance->closing_balance += $value->amount;
                    $cashBalance->save();

                    ActivityLogService::logModel(
                        model: $cashBalance->getTable(),
                        rowId: $cashBalance->id,
                        json: $cashBalance->toArray(), // ini tetap array untuk JSON
                        type: 'create',
                    );

                } else {
                    $prevBalance = CashBalance::where('housing_id', $value->housing_id)
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->first();

                    $openingBalance = $prevBalance ? $prevBalance->closing_balance : 0;

                    $cashBalance = new CashBalance();
                    $cashBalance->housing_id = $value->housing_id;
                    $cashBalance->opening_balance = $openingBalance;
                    $cashBalance->year = $now->year;
                    $cashBalance->month = $now->month;
                    $cashBalance->income = $value->amount;
                    $cashBalance->closing_balance = $value->amount;
                    $cashBalance->save();

                    ActivityLogService::logModel(
                        model: $cashBalance->getTable(),
                        rowId: $cashBalance->id,
                        json: $cashBalance->toArray(), // ini tetap array untuk JSON
                        type: 'update',
                    );
                }

                // DispatchPayDue::dispatch(
                //     houseId: $value->house_id,
                //     transactionCode : $transactionCode
                // )->onQueue('notifications');

                (new DispatchPayDue(houseId: $value->house_id, transactionCode : $transactionCode))->handle(new PushService());

            });
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => "Pembayaran berhasil"
        ], HttpStatusCodes::HTTP_OK);
    }
}
