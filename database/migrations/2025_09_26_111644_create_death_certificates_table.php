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
        Schema::create('death_certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('citizen_id');
            $table->date('date_of_death');
            $table->string('certificate_number');
            $table->string('document');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_certificates');
    }
};
