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
        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id();
            $table->string('province_code');
            $table->string('district_code');
            $table->string('name');
            $table->string('code');
            $table->softDeletes();
            $table->timestamps();
            $table->index('province_code', 'idx_prv_code_subdistricts');
            $table->index('district_code', 'idx_dis_code_subdistricts');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subdistricts');
    }
};
