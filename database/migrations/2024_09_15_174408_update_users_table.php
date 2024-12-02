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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->after('email')->index();
            $table->unsignedBigInteger('role_id')->after('phone')->index();
            $table->softDeletes();
            $table->unique(['email', 'role_id', 'deleted_at']);
            $table->unique(['phone', 'role_id', 'deleted_at']);
            $table->index(['role_id', 'phone']);
            $table->dropUnique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'deleted_at']);
        });
    }
};
