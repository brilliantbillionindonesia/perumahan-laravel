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
        Schema::create('cash_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('opening_balance', 20, 2)->default(0);
            $table->decimal('income', 20, 2)->default(0);
            $table->decimal('expense', 20, 2)->default(0);
            $table->decimal('closing_balance', 20, 2)->default(0);
            $table->timestamps();

            $table->index('housing_id', 'idx_housing_balances');
            $table->index('year', 'idx_year_balances');
            $table->index('month', 'idx_month_balances');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_balances');
    }
};
