<?php

namespace App\Http\Controllers\Api\Financial;

use App\Http\Controllers\Controller;
use App\Http\Services\ActivityLogService;
use App\Jobs\DispatchTransactionStore;
use App\Models\Citizen;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\House;
use App\Models\HousingUser;
use App\Models\Payment;
use DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use App\Models\CashBalance;
use App\Constants\HttpStatusCodes;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Validator;


class TransactionController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cash_balance_id" => ['required', 'exists:cash_balances,id'],
            "search" => ['nullable', 'string'],
            "type" => ['nullable', Rule::in(['all', 'expense', 'income'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page = (int) ($request->input('page', 1));
        $perPage = (int) ($request->input('per_page', 5));

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
            ->whereMonth('transaction_date', $month)
            ->when($request->input('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('note', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->when($request->input('type'), function ($query) use ($request) {
                $type = $request->input('type') == 'all' ? null : $request->input('type');
                if ($type != null) {
                    $query->where('type', $request->input('type'));
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit($perPage)
            ->offset(($page - 1) * $perPage)
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

    public function proofPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['nullable', 'string'],
            'is_me' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page = (int) ($request->input('page', 1));
        $perPage = (int) ($request->input('per_page', 5));

        $housingUser = HousingUser::where('housing_id', $request->housing_id)
            ->where('user_id', auth()->user()->id)->first();
        if (!$housingUser) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        if ($request->input('is_me')) {
            $citizen = Citizen::where('id', $housingUser->citizen_id)->first();

            if (!$citizen) {
                return response()->json([
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Data tidak ditemukan',
                ], HttpStatusCodes::HTTP_NOT_FOUND);
            }

            $house = House::where('family_card_id', $citizen->family_card_id)->first();

            if (!$house) {
                return response()->json([
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Data tidak ditemukan',
                ], HttpStatusCodes::HTTP_NOT_FOUND);
            }
        }

        $search = $request->input('search');

        $payments = DB::table('payments as p')
            ->join('dues as d', 'p.due_id', '=', 'd.id')
            ->join('fees as f', 'd.fee_id', '=', 'f.id')
            ->join('houses as h', 'p.house_id', '=', 'h.id')
            ->join('citizens as c', 'h.head_citizen_id', '=', 'c.id');

        if ($request->input('is_me')) {
            $payments = $payments->when($request->input('is_me'), function ($q) use ($house) {
                $q->where('p.house_id', '=', $house->id);
            });
        }
        $payments = $payments->where('p.housing_id', $request->input('housing_id'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('f.name', 'like', "%{$search}%")
                        ->orWhere('p.transaction_code', 'like', "%{$search}%");
                });
            })
            ->groupBy('p.transaction_code')
            ->selectRaw('
                MAX(p.id)                           as payment_id,
                MAX(c.fullname)                          as fullname,
                MAX(h.block)                          as house_block,
                MAX(h.number)                          as house_number,
                p.transaction_code,
                COUNT(*)                            as items_count,
                SUM(p.amount)                       as amount,
                MAX(p.paid_at)                      as paid_at,
                GROUP_CONCAT(DISTINCT d.periode
                    ORDER BY d.periode SEPARATOR ", ") as periode,
                GROUP_CONCAT(DISTINCT f.name
                    ORDER BY f.name SEPARATOR ", ")   as fee_name
            ')
            ->orderByDesc(DB::raw('MAX(p.paid_at)'))
            ->limit($perPage)
            ->offset(($page - 1) * $perPage)
            ->get();


        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
            'data' => $payments->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

    public function proofPaymentDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // "payment_id" => ['required', 'exists:payments,id'],
            "transaction_code" => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }


        $payment = DB::table('payments as p')
            ->join('dues as d', 'p.due_id', '=', 'd.id')
            ->join('fees as f', 'd.fee_id', '=', 'f.id')
            ->select(
                'p.id as payment_id',
                'd.id as due_id',
                'd.periode',
                'f.id as fee_id',
                'f.name as fee_name',
                'p.transaction_code',
                'p.amount',
                'p.paid_at'
            )
            ->where('p.transaction_code', $request->input('transaction_code'))
            ->get();

        if (!$payment) {
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
            'data' => $payment->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financial_category_code' => ['required', 'exists:financial_categories,code'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'note' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(['expense', 'income'])],
            'evidence' => ['nullable', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $financialCategory = FinancialCategory::where('code', $request->input('financial_category_code'))
            ->where('type', $request->input('type'))
            ->first();

        if (!$financialCategory) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Kategori tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        try {
            $transaction = null;

            DB::transaction(function () use ($request, &$transaction) {
                $now = now();
                $hid = $request->input('housing_id');
                $type = $request->input('type');
                $amount = (float) $request->input('amount');

                // Ambil cash balance bulan berjalan (LOCK)
                /** @var \App\Models\CashBalance|null $cashBalance */
                $cashBalance = CashBalance::where('housing_id', $hid)
                    ->where('year', $now->year)
                    ->where('month', $now->month)
                    ->lockForUpdate()
                    ->first();

                // Jika belum ada, ambil saldo penutup terakhir (LOCK juga agar konsisten)
                $openingBalance = 0.0;
                if (!$cashBalance) {
                    $prev = CashBalance::where('housing_id', $hid)
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->lockForUpdate()
                        ->first();
                    $openingBalance = (float) optional($prev)->closing_balance ?? 0.0;

                    $cashBalance = new CashBalance();
                    $cashBalance->housing_id = $hid;
                    $cashBalance->year = $now->year;
                    $cashBalance->month = $now->month;
                    $cashBalance->opening_balance = $openingBalance;
                    $cashBalance->income = 0;
                    $cashBalance->expense = 0;
                    $cashBalance->closing_balance = $openingBalance; // start = opening
                    $cashBalance->save();
                    ActivityLogService::logModel(
                        model: $cashBalance->getTable(),
                        rowId: $cashBalance->id,
                        json: $cashBalance->toArray(), // ini tetap array untuk JSON
                        type: 'create',
                    );
                    // setelah save, closing_balance = openingBalance (nol perubahan)
                }

                // Saldo yang benar2 tersedia untuk dibelanjakan adalah closing_balance saat ini
                $available = (float) $cashBalance->closing_balance;

                // Tolak jika pengeluaran melebihi saldo
                if ($type === 'expense' && $amount > $available) {
                    // Lempar exception supaya transaksi di-rollback
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Saldo kas tidak mencukupi untuk melakukan pengeluaran.',
                        'data' => [
                            'balance' => $available,
                            'amount' => $amount,
                        ],
                    ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY));
                }

                // Update agregat kas
                if ($type === 'expense') {
                    $cashBalance->expense = (float) $cashBalance->expense + $amount;
                    $cashBalance->closing_balance = (float) $cashBalance->closing_balance - $amount;
                } else { // income
                    $cashBalance->income = (float) $cashBalance->income + $amount;
                    $cashBalance->closing_balance = (float) $cashBalance->closing_balance + $amount;
                }
                $cashBalance->save();

                ActivityLogService::logModel(
                    model: $cashBalance->getTable(),
                    rowId: $cashBalance->id,
                    json: $cashBalance->toArray(), // ini tetap array untuk JSON
                    type: 'update',
                );

                // Buat transaksi
                $transactionCode = generateTransactionCode($hid, $now); // fungsi milikmu

                $transaction = new FinancialTransaction();
                $transaction->housing_id = $hid;
                $transaction->transaction_code = $transactionCode;
                $transaction->financial_category_code = $request->input('financial_category_code');
                $transaction->amount = $amount;
                $transaction->transaction_date = $request->input('transaction_date');
                $transaction->note = $request->input('note');
                $transaction->type = $type;

                // ====== SIMPAN FILE BUKTI (JIKA ADA) ======
                if ($request->hasFile('evidence')) {
                    $file = $request->file('evidence');
                    $ext = $file->getClientOriginalExtension();
                    $dir = "evidences/transactions/{$now->year}/{$now->format('m')}";
                    $filename = Str::uuid()->toString() . '.' . $ext;

                    // Simpan ke disk 'public'
                    $path = $file->storeAs($dir, $filename, 'public');
                    $transaction->evidence = $path; // contoh: evidences/transactions/2025/10/uuid.jpg
                }
                $transaction->save();

                ActivityLogService::logModel(
                    model: $transaction->getTable(),
                    rowId: $transaction->id,
                    json: $transaction->toArray(),
                    type: 'create',
                );

                DispatchTransactionStore::dispatch(
                    transactionId: $transaction->id,
                    cashBalanceId: $cashBalance->id
                )->onQueue('notifications');

            });

            return response()->json([
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Data berhasil disimpan',
                'data' => $transaction->toArray(),
            ], HttpStatusCodes::HTTP_OK);

        } catch (HttpResponseException $e) {
            // Ini dari insufficient funds di atas — langsung teruskan responsenya
            throw $e;

        } catch (\Throwable $e) {
            // fallback error lain
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi',
            ], HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function byCategory(Request $request)
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

        $data = DB::table('financial_transactions as ft')
            ->join('financial_categories as fc', 'ft.financial_category_code', '=', 'fc.code')
            ->select(
                'ft.financial_category_code as code',
                'fc.name',
                'ft.type',
                DB::raw('SUM(ft.amount) as total')
            )
            ->where('ft.housing_id', $request->input('housing_id'))
            ->whereYear('ft.transaction_date', $year)
            ->whereMonth('ft.transaction_date', $month)
            ->groupBy('ft.financial_category_code', 'fc.name', 'ft.type')
            ->orderByRaw('sum(ft.amount) desc')
            ->get();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);

    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:financial_transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $data = $validator->validated();

        $transaction = FinancialTransaction::
            with('category:code,name')
            ->where('id', $data['id'])
            ->first();

        $arr = [
            'evidence_url' => $transaction->evidence_url,
            'financial_category_name' => $transaction->category->name
        ];

        $transaction = $transaction->toArray();

        $transaction = array_merge($transaction, $arr);

        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Berhasil menampilkan data transaksi',
                'data' => $transaction,
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }


}
