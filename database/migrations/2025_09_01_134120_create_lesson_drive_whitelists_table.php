<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lesson_drive_whitelists', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // lesson_id UUID → relasi ke lessons.id
            $table->foreignUuid('lesson_id')
                  ->constrained('lessons')
                  ->cascadeOnDelete();

            // kalau users.id masih bigint, ganti foreignUuid → foreignId
            $table->foreignUuid('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('email'); 
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id','email']); // 1 email per lesson
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_drive_whitelists');
    }
};
