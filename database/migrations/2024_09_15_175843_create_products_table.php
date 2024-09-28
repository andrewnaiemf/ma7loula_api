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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();
            $table->mediumText('description')->nullable()->default(null);

            $table->unsignedBigInteger('category_id')->index();
            $table->foreign('category_id')->references('id')->on('product_categories')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('product_brands')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('vendor_id')->index();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onUpdate('cascade')->onDelete('cascade');

            $table->string('price');
            $table->string('price_before_discount')->nullable()->default(null);

            $table->integer('stock')->nullable()->default(null);

            $table->enum('status', ['published', 'pending', 'rejected'])->default('pending');

            $table->unsignedBigInteger('default_media_id')->nullable()->index();
            $table->foreign('default_media_id')->references('id')->on('media')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
