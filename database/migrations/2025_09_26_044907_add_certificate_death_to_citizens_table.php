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
        Schema::table('citizens', function (Blueprint $table) {
            $table->uuid('certificate_death')->primary()->after('education_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
