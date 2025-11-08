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
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'non_cash'])->after('transaction_date')->nullable();
            $table->string('payment_media', )->after('payment_method')->nullable();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('method', ['cash', 'non_cash'])->default('cash')->after('amount')->nullable();
            $table->string('media')->after('method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_media');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('method');
            $table->dropColumn('media');
        });

    }
};
