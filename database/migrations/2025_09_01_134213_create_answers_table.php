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
        $table->id();
        $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
        $table->foreignId('question_id')->constrained()->cascadeOnDelete();
        $table->foreignId('option_id')->nullable()->constrained()->nullOnDelete(); // untuk MCQ
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
