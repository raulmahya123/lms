<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('psy_tests', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('name');
            $t->string('slug')->unique();

            $t->enum('track', ['backend','frontend','fullstack','qa','devops','pm','custom'])->index();
            $t->enum('type', ['likert','mcq','iq','disc','big5','custom'])->default('likert');

            $t->unsignedInteger('time_limit_min')->nullable();
            $t->boolean('is_active')->default(true)->index();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('psy_tests');
    }
};
