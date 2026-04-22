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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('user_car_id');
            $table->foreign('user_car_id')->references('id')->on('user_car')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('car_id');
            $table->foreign('car_id')->references('id')->on('cars')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('address_id');
            $table->foreign('address_id')->references('id')->on('addresses')->onUpdate('cascade')->onDelete('cascade');

            $table->string('status')->index();
            $table->longText('reason')->nullable();

            $table->string('payment_method');
            $table->enum('type', ['tire', 'battery', 'car-parts', 'winch', 'emergency'])->index();
            $table->timestamp('delivery_time')->nullable()->default(null);
            $table->string('products_price');
            $table->string('services_price');
            $table->string('tax_price');
            $table->string('delivery_price');
            $table->string('total');

            $table->string('payment_code')->nullable();

            $table->index(['type', 'status']);
            $table->index(['status', 'user_id']);

            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
