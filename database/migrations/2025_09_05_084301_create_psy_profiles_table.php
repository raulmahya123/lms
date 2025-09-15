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
        Schema::create('psy_profiles', function (Blueprint $t) {
            $t->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke psy_tests (UUID)
            $t->foreignUuid('test_id')
              ->constrained('psy_tests')
              ->cascadeOnDelete();

            // FK ke users (UUID)
            $t->foreignUuid('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

            $t->string('key');   // e.g. "backend_fit_high"
            $t->string('name');  // e.g. "Strong Backend Fit"
            $t->integer('min_total')->default(0);
            $t->integer('max_total')->default(9999);
            $t->text('description')->nullable();

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psy_profiles');
    }
};
