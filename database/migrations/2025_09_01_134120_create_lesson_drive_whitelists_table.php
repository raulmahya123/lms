<?php

// database/migrations/2025_09_10_000001_create_lesson_drive_whitelists_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lesson_drive_whitelists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // user pemilik email (jika ada)
            $table->string('email'); // email yang di-whitelist
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable(); // opsional: waktu verifikasi
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
