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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_code');
            $table->uuid('housing_id');
            $table->string('financial_category_code');
            $table->uuid('house_id')->nullable();
            $table->decimal('amount', 20, 2);
            $table->dateTime('transaction_date');
            $table->string('note')->nullable();
            $table->enum('type', ['expense', 'income']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
