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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_code')->nullable();
            $table->uuid('housing_id');
            $table->uuid('house_id');
            $table->uuid('due_id');
            $table->uuid('paid_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->decimal('amount', 20, 2);
            $table->string('method')->default('cash');
            $table->text('note')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
