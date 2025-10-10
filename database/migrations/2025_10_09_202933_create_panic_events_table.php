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
        Schema::create('panic_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('housing_id');
            $table->uuid('citizen_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('house_id')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->decimal('latitude', 15, 6)->nullable();
            $table->decimal('longitude', 15, 6)->nullable();
            $table->text('note')->nullable();
            $table->dateTime('handled_at')->nullable();
            $table->uuid('handled_by')->nullable();
            $table->timestamps();

            $table->index('housing_id', 'idx_housing_panic_events');
            $table->index('citizen_id', 'idx_citizen_panic_events');
            $table->index('house_id', 'idx_house_panic_events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panic_events');
    }
};
