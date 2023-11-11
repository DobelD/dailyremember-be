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
        Schema::create('speakings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('id_transcript')->nullable();
            $table->text('transcript')->nullable();
            $table->string('duration')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speakings');
    }
};
