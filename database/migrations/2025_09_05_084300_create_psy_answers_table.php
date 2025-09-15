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
        Schema::create('psy_answers', function (Blueprint $t) {
            $t->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke psy_attempts (UUID)
            $t->foreignUuid('attempt_id')
              ->constrained('psy_attempts')
              ->cascadeOnDelete();

            // FK ke psy_questions (UUID)
            $t->foreignUuid('question_id')
              ->constrained('psy_questions')
              ->cascadeOnDelete();

            // FK ke psy_options (UUID, nullable)
            $t->foreignUuid('option_id')
              ->nullable()
              ->constrained('psy_options')
              ->nullOnDelete();

            $t->integer('value')->nullable(); // untuk likert direct

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psy_answers');
    }
};
