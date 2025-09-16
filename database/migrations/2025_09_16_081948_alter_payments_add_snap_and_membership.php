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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->string('snap_redirect_url')->nullable()->after('snap_token');
            }
            if (!Schema::hasColumn('payments', 'membership_id')) {
                $table->foreignUuid('membership_id')->nullable()->after('user_id')
                      ->constrained('memberships')->nullOnDelete();
            }
            // order_id/reference harus unik untuk Midtrans
            $table->unique('reference', 'payments_reference_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_reference_unique');
            if (Schema::hasColumn('payments', 'membership_id')) {
                $table->dropConstrainedForeignId('membership_id');
            }
            if (Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->dropColumn('snap_redirect_url');
            }
            if (Schema::hasColumn('payments', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
        });
    }
};