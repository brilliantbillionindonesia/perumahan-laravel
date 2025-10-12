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
        Schema::create('patrollings', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('housing_id');
            $table->uuid('citizen_id');
            $table->string('house_id');
            $table->date('patrol_date');
            $table->string('presence')->nullable();
            $table->text('note')->nullable();
            $table->string('replaced_by')->nullable(); 
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrollings');
    }
};
