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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke users (UUID)
            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // FK ke plans (UUID, nullable)
            $table->foreignUuid('plan_id')
                  ->nullable()
                  ->constrained('plans')
                  ->nullOnDelete();

            // FK ke courses (UUID, nullable)
            $table->foreignUuid('course_id')
                  ->nullable()
                  ->constrained('courses')
                  ->nullOnDelete();

            $table->unsignedInteger('amount'); // rupiah
            $table->enum('status', ['pending','paid','failed'])->default('pending');
            $table->string('provider')->nullable();   // midtrans, xendit, manual
            $table->string('reference')->nullable();  // invoice no / trx id
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
