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
        Schema::create('psy_questions', function (Blueprint $t) {
            $t->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke psy_tests (UUID)
            $t->foreignUuid('test_id')
              ->constrained('psy_tests')
              ->cascadeOnDelete();

            $t->text('prompt');
            $t->string('trait_key')->nullable(); // misal: logic, conscientiousness, etc.
            $t->enum('qtype', ['likert','mcq'])->default('likert');
            $t->unsignedSmallInteger('ordering')->default(0);

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psy_questions');
    }
};
