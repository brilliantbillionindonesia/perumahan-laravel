<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Financial\DueController;
use App\Jobs\DispatchReleasedDue;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

class GenerateFee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-fee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan bulanan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new DueController;
        $request = new Request([
            'due_date_payment' => date('d'),
        ]);
        $controller->generate($request);
    }
}
