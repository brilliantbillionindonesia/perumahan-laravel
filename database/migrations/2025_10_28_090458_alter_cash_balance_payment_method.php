<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_balances', function (Blueprint $table) {
            $table->enum('payment_method', ['all', 'cash', 'non_cash'])->nullable()->after('closing_balance');
            $table->string('payment_media', )->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_balances', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_media');
        });
    }
};
