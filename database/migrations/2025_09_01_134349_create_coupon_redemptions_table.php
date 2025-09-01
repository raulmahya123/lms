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
    Schema::create('coupon_redemptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
        $table->timestamp('used_at')->nullable();
        $table->unsignedInteger('amount_discounted')->default(0);
        $table->timestamps();
        $table->unique(['coupon_id','user_id','course_id','plan_id'], 'coupon_unique_scope');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_redemptions');
    }
};
