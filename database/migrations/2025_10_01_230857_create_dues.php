<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->uuid('house_id');
            $table->uuid('fee_id');
            $table->decimal('amount', 20, 2);
            $table->string('status');
            $table->string('periode');
            $table->timestamps();

            // untuk filter housing_id + grouping
            $table->index(['housing_id', 'house_id', 'fee_id', 'periode', 'created_at'], 'idx_dues_housing_house_fee_periode_created');

            // kalau sering query by periode saja
            $table->index(['housing_id', 'periode'], 'idx_dues_housing_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues');
    }
};
