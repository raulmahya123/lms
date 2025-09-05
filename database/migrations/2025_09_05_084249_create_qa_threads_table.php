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
        Schema::create('qa_threads', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->longText('body');
            $t->enum('status', ['open', 'resolved', 'closed'])->default('open');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qa_threads');
    }
};
