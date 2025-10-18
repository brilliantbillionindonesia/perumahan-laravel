<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Financial\DueController;
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
    protected $description = 'Generate tagihan bul';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new DueController;
        $request = new Request([
            // 'due_date' => date('d'),
            'due_date_payment' => 15
        ]);
        $controller->generate($request);
    }
}
