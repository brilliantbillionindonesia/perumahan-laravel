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
        Schema::create('fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->string('financial_category_code');
            $table->string('name');
            $table->decimal('amount',20, 2);
            $table->enum('frequency', ['once', 'recurring'])->default('once');
            $table->integer('due_day')->nullable();
            $table->dateTime('billing_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('housing_id', 'idx_housing_fees');
            $table->index('financial_category_code', 'idx_fin_cat_code_fees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
