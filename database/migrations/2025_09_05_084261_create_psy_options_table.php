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
       Schema::create('psy_options', function (Blueprint $t) {
    $t->id();
    $t->foreignId('question_id')->constrained('psy_questions')->cascadeOnDelete();
    $t->string('label');
    $t->integer('value'); // -2..+2 atau skor MCQ
    $t->unsignedSmallInteger('ordering')->default(0);
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psy_options');
    }
};
