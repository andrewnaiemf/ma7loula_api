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
        Schema::create('worker_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('worker_id');
            $table->foreign('worker_id')->references('id')->on('workers')->onUpdate('cascade')->onDelete('cascade');

            $table->string('tire_delivery_price');
            $table->string('tire_service_price');
            $table->string('battery_delivery_price');
            $table->string('battery_srevice_price');
            
            $table->timestamps();

            $table->index('worker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_zones');
    }
};
