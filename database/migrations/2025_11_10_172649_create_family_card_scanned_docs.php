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
        Schema::create('family_card_scanned_docs', function (Blueprint $table) {
            $table->id();
            $table->uuid('family_card_id')->nullable();
            $table->uuid('housing_id');
            $table->string('house_block', 50);
            $table->integer('house_number');
            $table->string('ownership_status');
            $table->string('file_name');
            $table->string('path');
            $table->longText('data_json');
            $table->longText('data_json_verified')->nullable();
            $table->uuid('verified_by')->nullable();
            $table->datetime('verified_at')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->integer('accuracy')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('housing_id', 'idx_housing_family_card_scanned_docs');
            $table->index('file_name', 'idx_file_family_card_scanned_docs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_card_scanned_docs');
    }
};
