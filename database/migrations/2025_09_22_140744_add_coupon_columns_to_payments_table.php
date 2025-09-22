<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'coupon_id')) {
                $table->foreignUuid('coupon_id')->nullable()->after('course_id')
                    ->constrained('coupons')->nullOnDelete();
            }
            if (!Schema::hasColumn('payments', 'discount_amount')) {
                $table->unsignedInteger('discount_amount')->default(0)->after('amount');
            }
        });
    }

    public function down(): void {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignIdIfExists('coupon_id');
            $table->dropColumn('discount_amount');
        });
    }
};
