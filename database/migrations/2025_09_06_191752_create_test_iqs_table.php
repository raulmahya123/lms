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
        Schema::create('test_iq', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('questions')->nullable();     // admin bisa isi belakangan
            $table->boolean('is_active')->default(false);
            $table->integer('duration_minutes')->default(0);
            $table->json('submissions')->nullable();   // hasil user (opsional)
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('test_iq');
    }
};
