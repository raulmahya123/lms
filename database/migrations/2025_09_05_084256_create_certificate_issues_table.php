<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_issues', function (Blueprint $t) {
            $t->id();

            $t->string('serial')->unique();

            $t->foreignId('template_id')
              ->constrained('certificate_templates')
              ->cascadeOnDelete();

            $t->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

            $t->foreignId('course_id')
              ->nullable()
              ->constrained()
              ->nullOnDelete();

            // relasi ke entitas penilaian; dibiarkan tanpa FK karena polymorphic/by-type
            $t->enum('assessment_type', ['course', 'psych'])->default('course');
            $t->unsignedBigInteger('assessment_id')->nullable();

            // decimal unsigned: pakai decimal(...)->unsigned()
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
