<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('psy_profiles', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('test_id')->constrained('psy_tests')->cascadeOnDelete();

            $t->string('key');   // e.g. "BACKEND_LOW", "FRONTEND_HIGH"
            $t->string('name');  // e.g. "Backend Fit — Low"
            $t->integer('min_total')->default(0);
            $t->integer('max_total')->default(9999);
            $t->text('description')->nullable();

            $t->timestamps();

            $t->index(['test_id','min_total','max_total']);
            $t->unique(['test_id','key']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('psy_profiles');
    }
};
