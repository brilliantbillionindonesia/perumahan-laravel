<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notification_recipients', function (Blueprint $table) {
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'notification_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['housing_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
