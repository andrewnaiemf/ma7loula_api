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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();

            $table->mediumText('details')->nullable();

            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('government_id');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('government_id')->references('id')->on('governments')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
