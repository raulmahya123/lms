<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('psy_attempts', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('test_id')->constrained('psy_tests')->cascadeOnDelete();
            $t->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $t->timestamp('started_at')->nullable();
            $t->timestamp('submitted_at')->nullable()->index();

            $t->json('score_json')->nullable();     // rata-rata per trait + _total
            $t->integer('total_score')->nullable(); // denormalized _total (cepat untuk filter)
            $t->string('result_key')->nullable()->index();
            $t->text('recommendation_text')->nullable();

            $t->timestamps();

            $t->index(['user_id','test_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('psy_attempts');
    }
};
