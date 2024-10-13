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
        Schema::create('order_winches', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('vendor_id')->index()->nullable();;
            $table->foreign('vendor_id')->references('id')->on('vendors')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('worker_id')->index()->nullable();;
            $table->foreign('worker_id')->references('id')->on('workers')->onUpdate('cascade')->onDelete('cascade');

            $table->string('from_lat');
            $table->string('from_lon');
            $table->string('from_text')->nullable();

            $table->string('to_lat');
            $table->string('to_lon');
            $table->string('to_text')->nullable();

            $table->string('distance_in_meters');
            $table->string('duration_in_minutes');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_winches');
    }
};
