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
        Schema::create('psy_answers', function (Blueprint $t) {
    $t->id();
    $t->foreignId('attempt_id')->constrained('psy_attempts')->cascadeOnDelete();
    $t->foreignId('question_id')->constrained('psy_questions')->cascadeOnDelete();
    $t->foreignId('option_id')->nullable()->constrained('psy_options')->nullOnDelete();
    $t->integer('value')->nullable();     // untuk likert direct
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psy_answers');
    }
};
