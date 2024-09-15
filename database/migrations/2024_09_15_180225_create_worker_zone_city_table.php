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
        Schema::create('worker_zone_city', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('worker_id');
            $table->foreign('worker_id')->references('id')->on('workers')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('worker_zone_id');
            $table->foreign('worker_zone_id')->references('id')->on('worker_zones')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');

            $table->index(['worker_id', 'worker_zone_id']);
            $table->index(['city_id']);

            $table->unique(['city_id', 'worker_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_zone_city');
    }
};
