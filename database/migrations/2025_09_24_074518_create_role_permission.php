<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('permission_roles', function (Blueprint $table) {
            $table->id();
            $table->string('permission_code');
            $table->string('role_code');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['permission_code', 'role_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
