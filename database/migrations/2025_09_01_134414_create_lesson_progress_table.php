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
        Schema::create('lesson_progresses', function (Blueprint $table) {
            $table->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke lessons & users (UUID)
            $table->foreignUuid('lesson_id')
                  ->constrained('lessons')
                  ->cascadeOnDelete();

            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->json('progress')->nullable(); // simpan JSON array progress
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']); // 1 user hanya 1 progress per lesson
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progresses');
    }
};
