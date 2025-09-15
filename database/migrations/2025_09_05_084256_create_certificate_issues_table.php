<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_issues', function (Blueprint $t) {
            $t->uuid('id')->primary();   // ✅ PK pakai UUID

            $t->string('serial')->unique();

            // FK ke template (UUID)
            $t->foreignUuid('template_id')
              ->constrained('certificate_templates')
              ->cascadeOnDelete();

            // FK ke users (UUID)
            $t->foreignUuid('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

            // FK ke courses (UUID, nullable)
            $t->foreignUuid('course_id')
              ->nullable()
              ->constrained('courses')
              ->nullOnDelete();

            // polymorphic assessment
            $t->enum('assessment_type', ['course', 'psych'])->default('course');
            $t->uuid('assessment_id')->nullable(); // 👈 pakai UUID biar konsisten

            $t->decimal('score', 5, 2)->unsigned()->nullable();

            $t->timestamp('issued_at')->useCurrent();
            $t->string('pdf_path')->nullable();

            $t->timestamps();

            // index bantu untuk lookup polymorphic
            $t->index(['assessment_type', 'assessment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_issues');
    }
};
