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
            $table->uuid('id')->primary();   // ✅ PK pakai UUID

            // FK ke coupons, users, courses, plans (semua UUID)
            $table->foreignUuid('coupon_id')
                  ->constrained('coupons')
                  ->cascadeOnDelete();

            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignUuid('course_id')
                  ->nullable()
                  ->constrained('courses')
                  ->nullOnDelete();

            $table->foreignUuid('plan_id')
                  ->nullable()
                  ->constrained('plans')
                  ->nullOnDelete();

            $table->timestamp('used_at')->nullable();
            $table->unsignedInteger('amount_discounted')->default(0);
            $table->timestamps();

            // biar 1 user nggak bisa pakai kupon yg sama berkali-kali untuk scope yang sama
            $table->unique(['coupon_id', 'user_id', 'course_id', 'plan_id'], 'coupon_unique_scope');
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
