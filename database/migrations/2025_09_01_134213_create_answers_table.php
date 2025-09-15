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
        Schema::create('answers', function (Blueprint $table) {
            $table->uuid('id')->primary();   // ✅ UUID PK

            // quiz_attempts.id sudah UUID
            $table->foreignUuid('attempt_id')
                  ->constrained('quiz_attempts')
                  ->cascadeOnDelete();

            // questions.id sudah UUID
            $table->foreignUuid('question_id')
                  ->constrained('questions')
                  ->cascadeOnDelete();

            // options.id sudah UUID (nullable karena short/long tidak pakai option)
            $table->foreignUuid('option_id')
                  ->nullable()
                  ->constrained('options')
                  ->nullOnDelete();

            $table->text('text_answer')->nullable(); // untuk short/long
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
