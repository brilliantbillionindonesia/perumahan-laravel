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
        Schema::create('housings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('housing_name');
            $table->string('address');
            $table->string('rt');
            $table->string('rw');
            $table->string('village_code');
            $table->string('subdistrict_code');
            $table->string('district_code');
            $table->string('province_code');
            $table->string('postal_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housings');
    }
};
