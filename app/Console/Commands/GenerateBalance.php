<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Financial\TransactionController;
use Illuminate\Console\Command;

class GenerateBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fitur untuk generate cash balance di awal bulan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new TransactionController;
        $controller->generateBalance();
        return 0;
    }
}
