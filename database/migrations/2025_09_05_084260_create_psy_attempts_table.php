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
        Schema::create('psy_attempts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('test_id')->constrained('psy_tests')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->json('score_json')->nullable();   // per trait
            $t->integer('total_score')->nullable(); // JANGAN pakai ->after() saat create
            $t->string('result_key')->nullable();
            $t->text('recommendation_text')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psy_attempts');
    }
};
