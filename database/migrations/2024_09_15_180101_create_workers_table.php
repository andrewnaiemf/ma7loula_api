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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique()->index();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('vendor_id')->index();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onUpdate('cascade')->onDelete('cascade');

            $table->string('lat')->nullable();
            $table->string('lon')->nullable();

            $table->string('car_plate_number')->nullable();

            $table->enum('type', ['winch', 'delivery', 'emergency'])->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'lat', 'lon']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('workers');
    }
};
