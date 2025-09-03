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
    Schema::create('lessons', function (Blueprint $table) {
        $table->id();
        $table->foreignId('module_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->json('content')->nullable();        // atau simpan URL konten/video
        $table->json('content_url')->nullable();  // opsional
        $table->unsignedInteger('ordering')->default(1);
        $table->boolean('is_free')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
