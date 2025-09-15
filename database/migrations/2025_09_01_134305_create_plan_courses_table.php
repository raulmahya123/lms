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
        Schema::create('plan_courses', function (Blueprint $table) {
            $table->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke plans & courses (dua-duanya UUID)
            $table->foreignUuid('plan_id')
                  ->constrained('plans')
                  ->cascadeOnDelete();

            $table->foreignUuid('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();

            $table->unique(['plan_id', 'course_id']); // 1 course cuma boleh 1x masuk plan yg sama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_courses');
    }
};
