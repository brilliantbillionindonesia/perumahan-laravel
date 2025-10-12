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
        Schema::create('housing_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->uuid('citizen_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('role_code')->default('citizen');
            $table->integer('is_active')->default(1);
            $table->timestamps();

            $table->index('housing_id', 'idx_housing_husers');
            $table->index('citizen_id', 'idx_citizen_husers');
            $table->index('user_id', 'idx_user_husers');
            $table->index('role_code', 'idx_role_code_husers');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housing_users');
    }
};
