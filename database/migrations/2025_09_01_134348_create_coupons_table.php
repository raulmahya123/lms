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
    Schema::create('coupons', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->unsignedTinyInteger('discount_percent'); // 0..100
        $table->timestamp('valid_from')->nullable();
        $table->timestamp('valid_until')->nullable();
        $table->unsignedInteger('usage_limit')->nullable(); // null = unlimited
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
