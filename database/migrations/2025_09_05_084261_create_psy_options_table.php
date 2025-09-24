<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('psy_options', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('question_id')->constrained('psy_questions')->cascadeOnDelete();

            $t->string('label');
            $t->integer('value'); // Likert/MCQ skor (mis. 1..5)
            $t->unsignedSmallInteger('ordering')->default(0);

            $t->timestamps();

            $t->index(['question_id','ordering']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('psy_options');
    }
};
