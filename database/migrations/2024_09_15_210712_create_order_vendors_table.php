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
        Schema::create('order_vendors', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('vendor_id')->index();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('worker_id')->index();
            $table->foreign('worker_id')->references('id')->on('workers')->onUpdate('cascade')->onDelete('cascade');

            $table->string('status')->index();

            $table->boolean('has_service');
            $table->string('delivery_time');
            $table->string('delivery_date');

            $table->string('products_price');
            $table->string('services_price');
            $table->string('tax_price');
            $table->string('delivery_price');
            $table->string('total');


            $table->index(['vendor_id', 'status']);
            $table->index(['worker_id', 'status']);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_vendors');
    }
};
