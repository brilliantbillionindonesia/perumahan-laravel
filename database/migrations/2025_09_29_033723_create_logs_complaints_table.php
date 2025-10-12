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
        Schema::create('complaint_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('complaint_id');
            $table->uuid('logged_by');
            $table->datetime('logged_at');
            $table->string('status_code');

            $table->text('note')->nullable()->change();

            $table->text('note')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('complaint_id', 'idx_complaint_logs');
            $table->index('logged_by', 'idx_logged_by_logs');
            $table->index('status_code', 'idx_status_code_logs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_logs');
    }
};
