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
            $t->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke psy_tests (UUID)
            $t->foreignUuid('test_id')
              ->constrained('psy_tests')
              ->cascadeOnDelete();

            // FK ke users (UUID)
            $t->foreignUuid('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

            $t->timestamp('started_at')->nullable();
            $t->timestamp('submitted_at')->nullable();

            $t->json('score_json')->nullable();   // per trait
            $t->integer('total_score')->nullable();
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
