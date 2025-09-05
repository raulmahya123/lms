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
       Schema::create('certificate_templates', function (Blueprint $t) {
    $t->id();
    $t->string('name');
    $t->string('background_url')->nullable();
    $t->json('fields_json')->nullable();   // misal mapping {name, course, date, score}
    $t->json('svg_json')->nullable();      // opsional: layout vektor
    $t->boolean('is_active')->default(true);
    $t->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
