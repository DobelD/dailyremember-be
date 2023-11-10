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
        Schema::create('progress_vocabularies', function (Blueprint $table) {
            $table->id();
            $table->integer('total_word')->nullable();
            $table->integer('remember')->nullable();
            $table->integer('no_remember')->nullable();
            $table->integer('target_day')->nullable();
            $table->integer('target_remember_perday')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_vocabularies');
    }
};
