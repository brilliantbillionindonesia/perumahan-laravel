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
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->string('province_code');
            $table->string('district_code');
            $table->string('subdistrict_code');
            $table->string('name');
            $table->string('code');
            $table->softDeletes();
            $table->timestamps();

            $table->index('province_code', 'idx_prv_code_villages');
            $table->index('district_code', 'idx_dis_code_villages');
            $table->index('subdistrict_code', 'idx_subdis_code_villages');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
