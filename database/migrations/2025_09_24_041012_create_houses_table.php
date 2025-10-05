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
        Schema::create('houses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->string('house_name');
            $table->string('block');
            $table->integer('number');
            $table->uuid('family_card_id');
            $table->uuid('head_citizen_id');
            $table->timestamps();

            // untuk join ke dues
            $table->index('housing_id', 'idx_houses_housing');

            // untuk join ke citizens (head_citizen_id)
            $table->index('head_citizen_id', 'idx_houses_headcitizen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
