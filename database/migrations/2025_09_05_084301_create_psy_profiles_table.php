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
    $t->id();
    $t->foreignId('test_id')->constrained('psy_tests')->cascadeOnDelete();
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
