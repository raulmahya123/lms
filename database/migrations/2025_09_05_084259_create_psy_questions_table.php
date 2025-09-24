<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('psy_questions', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('test_id')->constrained('psy_tests')->cascadeOnDelete();

            $t->text('prompt');
            $t->string('trait_key')->nullable();  // contoh: logic, system, ui, qa_mindset
            $t->enum('qtype', ['likert','mcq'])->default('likert');
            $t->unsignedSmallInteger('ordering')->default(0);

            $t->timestamps();

            $t->index(['test_id','ordering']);
            // (opsional) kalau mau pastikan unik urutan per test:
            // $t->unique(['test_id','ordering']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('psy_questions');
    }
};
