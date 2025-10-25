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
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->string('name');
            $table->string('relationship');
            $table->uuid('family_card_id');
            $table->uuid('house_id');
            $table->uuid('registered_by')->nullable();
            $table->dateTime('registered_at')->nullable();
            $table->text('identification')->nullable();
            $table->longText('data_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
