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
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('product_id')->index();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('vendor_id')->index();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onUpdate('cascade')->onDelete('cascade');

            $table->string('unit_price');
            $table->string('qty');
            $table->string('total');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
