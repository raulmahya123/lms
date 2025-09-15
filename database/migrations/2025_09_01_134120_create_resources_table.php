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
        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary();   // ✅ pakai UUID untuk PK

            // lessons.id sudah UUID → pakai foreignUuid
            $table->foreignUuid('lesson_id')
                  ->constrained('lessons')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->string('url');              // link file/drive
            $table->string('type')->nullable(); // pdf, link, zip, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
