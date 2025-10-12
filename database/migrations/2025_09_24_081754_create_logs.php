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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table');
            $table->string('row_id');
            $table->enum('type', ['create', 'update', 'delete', 'restore']);
            $table->longText('json')->nullable();
            $table->uuid('logged_by');
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->index('table', 'idx_table_logs');
            $table->index('row_id', 'idx_row_id_logs');
            $table->index('type', 'idx_type_logs');
            $table->index('logged_by', 'idx_logged_by_logs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
