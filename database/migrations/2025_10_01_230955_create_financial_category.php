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
        Schema::create('financial_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('housing_id')->nullable();
            $table->string('code');
            $table->string('name');
            $table->enum('type', ['expense', 'income'])->default('expense');
            $table->softDeletes();
            $table->timestamps();

            $table->index('housing_id', 'idx_housing_fcat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_categories');
    }
};
