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
        Schema::create('qa_threads', function (Blueprint $t) {
            $t->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke users (UUID)
            $t->foreignUuid('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

            // FK ke courses (UUID, nullable)
            $t->foreignUuid('course_id')
              ->nullable()
              ->constrained('courses')
              ->nullOnDelete();

            // FK ke lessons (UUID, nullable)
            $t->foreignUuid('lesson_id')
              ->nullable()
              ->constrained('lessons')
              ->nullOnDelete();

            $t->string('title');
            $t->longText('body');
            $t->enum('status', ['open', 'resolved', 'closed'])->default('open');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qa_threads');
    }
};
