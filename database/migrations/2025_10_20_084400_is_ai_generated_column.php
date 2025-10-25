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
        // Citizens
        Schema::table('citizens', function (Blueprint $table) {
            if (!Schema::hasColumn('citizens', 'is_ai_generated')) {
                $table->boolean('is_ai_generated')->default(false)->after('death_certificate_id')
                    ->comment('Menandakan data hasil dari AI scanner (true) atau manual (false)');
            }
        });

        // Family Cards
        Schema::table('family_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('family_cards', 'is_ai_generated')) {
                $table->boolean('is_ai_generated')->default(false)->after('postal_code')
                    ->comment('Menandakan data hasil dari AI scanner (true) atau manual (false)');
            }
        });

        // Family Members
        Schema::table('family_members', function (Blueprint $table) {
            if (!Schema::hasColumn('family_members', 'is_ai_generated')) {
                $table->boolean('is_ai_generated')->default(false)->after('mother_name')
                    ->comment('Menandakan data hasil dari AI scanner (true) atau manual (false)');
            }
        });

        // Family Documents
        Schema::table('family_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('family_documents', 'is_ai_generated')) {
                $table->boolean('is_ai_generated')->default(false)->after('doc_file')
                    ->comment('Menandakan data hasil dari AI scanner (true) atau manual (false)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->dropColumn('is_ai_generated');
        });

        Schema::table('family_cards', function (Blueprint $table) {
            $table->dropColumn('is_ai_generated');
        });

        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn('is_ai_generated');
        });

        Schema::table('family_documents', function (Blueprint $table) {
            $table->dropColumn('is_ai_generated');
        });
    }
};
