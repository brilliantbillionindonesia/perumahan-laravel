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
        Schema::create('citizens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('family_card_id');
            $table->string('citizen_card_number');
            $table->string('fullname');
            $table->string('gender');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('blood_type');
            $table->string('religion');
            $table->string('marital_status');
            $table->string('work_type');
            $table->string('education_type');
            $table->string('citizenship');
            $table->uuid('death_certificate_id')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizen');
    }
};
