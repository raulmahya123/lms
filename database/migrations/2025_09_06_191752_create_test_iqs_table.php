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
        Schema::create('test_iqs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('questions')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('duration_minutes')->default(0);

            // === cool-down ===
            $table->unsignedSmallInteger('cooldown_value')->default(1); // 1,2,3 dst
            $table->string('cooldown_unit', 10)->default('month');      // day|week|month

            $table->json('submissions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_iqs');
    }
};