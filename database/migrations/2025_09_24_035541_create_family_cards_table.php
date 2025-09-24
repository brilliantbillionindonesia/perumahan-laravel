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
        Schema::create('family_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('family_card_number');
            $table->string('adress');
            $table->string('rt');
            $table->string('rw');
            $table->string('subdistrict');
            $table->string('district');
            $table->string('province');
            $table->string('postal_code');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_card');
    }
};
