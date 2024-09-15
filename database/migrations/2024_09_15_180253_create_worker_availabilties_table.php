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
        Schema::create('worker_availabilties', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('worker_id')->index();
            $table->foreign('worker_id')->references('id')->on('workers')->onUpdate('cascade')->onDelete('cascade');

            $table->string('day');
            $table->string('from');
            $table->string('to');

            $table->index(['worker_id', 'day']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_availabilties');
    }
};
