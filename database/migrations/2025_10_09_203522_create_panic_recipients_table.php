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
        Schema::create('panic_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('panic_event_id');
            $table->uuid('user_id');
            $table->enum('status', ['pending', 'delivered', 'read'])->default('pending');
            $table->dateTime('notified_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->dateTime('last_reminded_at')->nullable();
            $table->smallInteger('reminder_count')->default(0);
            $table->timestamps();

            $table->index('panic_event_id', 'idx_panic_event_panic_recipients');
            $table->index('user_id', 'idx_user_panic_recipients');
            $table->index(['panic_event_id', 'status'], 'idx_panic_event_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panic_recipients');
    }
};
