<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('psy_answers', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('attempt_id')->constrained('psy_attempts')->cascadeOnDelete();
            $t->foreignUuid('question_id')->constrained('psy_questions')->cascadeOnDelete();
            $t->foreignUuid('option_id')->nullable()->constrained('psy_options')->nullOnDelete();

            $t->integer('value')->nullable(); // untuk soal tanpa option (numeric)
            $t->timestamps();

            $t->unique(['attempt_id','question_id']); // 1 jawaban per soal per attempt
        });
    }
    public function down(): void {
        Schema::dropIfExists('psy_answers');
    }
};
