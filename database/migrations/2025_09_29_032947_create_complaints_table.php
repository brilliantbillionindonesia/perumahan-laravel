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
        Schema::create('complaints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->uuid('user_id');
            $table->string('title');
            $table->string('category_code');
            $table->text('description');
            $table->string('status_code')->default('new');
            $table->uuid('updated_by')->nullable();
            $table->datetime('submitted_at');
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('housing_id', 'idx_housing_complaints');
            $table->index('user_id', 'idx_user_complaints');
            $table->index('category_code', 'idx_category_code_complaints');
            $table->index('status_code', 'idx_status_code_complaints');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
