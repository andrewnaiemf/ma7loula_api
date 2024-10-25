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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->enum('type', ['bt-vendor', 'bt-car', 'car-parts', 'workshop', 'winch']);

            $table->index(['user_id']);
            $table->index(['type']);

            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->string('tax_no')->nullable();
            $table->string('company_licence_no')->nullable();
            $table->string('company_licence_expire_date')->nullable();
            $table->string('address')->nullable();
            $table->string('id_image')->nullable();
            $table->string('company_licence_image')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vendors');
    }
};
