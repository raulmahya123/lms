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

            // tambahan deskripsi lengkap
            $table->text('about')->nullable();
            $table->longText('syllabus')->nullable();
            $table->json('reviews')->nullable();
            $table->json('tools')->nullable();

            // siapa pembuat lesson
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->longText('benefits')->nullable();

            $table->json('content')->nullable();
            $table->json('content_url')->nullable();
            $table->unsignedInteger('ordering')->default(1);
            $table->boolean('is_free')->default(false);

            // whitelist Google Drive
            $table->json('drive_emails')->nullable(); 
            $table->string('drive_link')->nullable(); 

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
