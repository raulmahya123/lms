<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_season_locks', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();

            // Season identity
            $table->string('season_key', 32);      // mis. "2025W37" / epoch start / dsb
            $table->dateTime('season_start');      // <- ganti dari timestamp()
            $table->dateTime('season_end');        // <- ganti dari timestamp()

            // Counter attempt
            $table->unsignedInteger('attempt_count')->default(0);
            $table->dateTime('last_attempt_at')->nullable(); // <- ganti dari timestamp()->nullable()

            $table->timestamps();

            // Index & constraint
            $table->unique(['user_id', 'quiz_id', 'season_key'], 'uq_user_quiz_season');
            $table->index(['user_id', 'quiz_id']);
            $table->index(['season_start', 'season_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_season_locks');
    }
};
