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
            $table->json('content')->nullable();
            $table->json('content_url')->nullable();
            $table->unsignedInteger('ordering')->default(1);
            $table->boolean('is_free')->default(false);

            // === Tambahan whitelist Google Drive per email ===
            $table->json('drive_emails')->nullable(); // simpan max 4 email yg diijinkan
            $table->string('drive_link')->nullable(); // link Google Drive utama
            $table->enum('drive_status', ['pending','approved','rejected'])
                  ->default('pending');

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

