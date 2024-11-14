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
        Schema::table('order_emergencies', function (Blueprint $table) {
            $table->string('lat')->index()->nullable();
            $table->string('lon')->index()->nullable();
            $table->mediumText('location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_emergencies', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lon', 'location']);
        });
    }
};
