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
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
        $table->unsignedInteger('amount'); // rupiah
        $table->enum('status',['pending','paid','failed'])->default('pending');
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
