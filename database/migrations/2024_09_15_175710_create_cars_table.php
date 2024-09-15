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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('engine');

            $table->unsignedBigInteger('car_brand_id');
            $table->foreign('car_brand_id')->references('id')->on('car_brands')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('car_model_id');
            $table->foreign('car_model_id')->references('id')->on('car_models')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
