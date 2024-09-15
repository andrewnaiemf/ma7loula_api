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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type');
            $table->unsignedBigInteger('translatable_id');
            $table->string('key');
            $table->string('lang');
            $table->mediumText('value');
            $table->timestamps();

            $table->unique(['translatable_type', 'translatable_id', 'key', 'lang']);
            $table->index(['translatable_type', 'translatable_id', 'key', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
