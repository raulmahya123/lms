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
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();   // ✅ UUID PK

            // quizzes.id sudah UUID → pakai foreignUuid
            $table->foreignUuid('quiz_id')
                  ->constrained('quizzes')
                  ->cascadeOnDelete();

            $table->enum('type', ['mcq', 'short', 'long'])->default('mcq');
            $table->text('prompt');
            $table->unsignedInteger('points')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
