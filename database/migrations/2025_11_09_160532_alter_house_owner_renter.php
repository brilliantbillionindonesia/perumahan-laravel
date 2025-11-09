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
        Schema::table('houses', function (Blueprint $table) {
            $table->string('owner_citizen_id')->nullable()->after('head_citizen_id');
            $table->string('renter_citizen_id')->nullable()->after('owner_citizen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->dropColumn('owner_citizen_id');
            $table->dropColumn('renter_citizen_id');
        });
    }
};
