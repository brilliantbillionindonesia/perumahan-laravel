<?php

namespace App\Jobs;

use App\Http\Services\PushService;
use App\Models\FinancialTransaction;
use App\Models\HousingUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchTransactionStore implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $transactionId, public string $cashBalanceId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(PushService $push): void
    {
        $transaction = FinancialTransaction::where('id', $this->transactionId)->first();
        $management = HousingUser::selectRaw('housing_users.user_id,token')->where('housing_id', $transaction->housing_id)
        ->join('device_tokens', 'device_tokens.user_id', '=', 'housing_users.user_id')
        ->where('role_code', '!=', 'citizen')->get();

        if($management->isEmpty()){
            return;
        }

        $tokens = $management->pluck('token')->filter()->all();

        $push->sendNotification(
            tokens: $tokens,
            title: 'Transaksi Baru',
            body:  'Transaksi baru telah tercatat  • Tap untuk buka',
            channel : 'notification_channel',
            data: [
                'type'      => 'transaction',
                'housing_id' => $transaction->housing_id,
                'id'        => $transaction->id,
                'cash_balance_id' => $this->cashBalanceId,
                'created_at'=> $transaction->created_at->toIso8601String(),
            ],
        );


    }
}
