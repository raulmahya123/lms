<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // FK membership (aman kalau sudah ada, kita cek dulu)
            if (!Schema::hasColumn('payments', 'membership_id')) {
                $table->foreignId('membership_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete()
                    ->after('course_id');
            }

            // reference (order_id) + index hanya jika kolom BELUM ada
            if (!Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable()->after('provider');
                // bikin index sekarang juga (default name: payments_reference_index)
                $table->index('reference', 'payments_reference_index');
            }

            // Snap fields
            if (!Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('reference');
            }
            if (!Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->string('snap_redirect_url')->nullable()->after('snap_token');
            }

            // Midtrans meta
            if (!Schema::hasColumn('payments', 'midtrans_transaction_id')) {
                $table->string('midtrans_transaction_id')->nullable()->after('provider');
            }
            if (!Schema::hasColumn('payments', 'midtrans_payment_type')) {
                $table->string('midtrans_payment_type')->nullable()->after('midtrans_transaction_id');
            }
            if (!Schema::hasColumn('payments', 'midtrans_settlement_time')) {
                $table->timestamp('midtrans_settlement_time')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // urutan aman untuk rollback parsial

            if (Schema::hasColumn('payments','membership_id')) {
                $table->dropConstrainedForeignId('membership_id');
            }

            if (Schema::hasColumn('payments','snap_token')) {
                $table->dropColumn('snap_token');
            }
            if (Schema::hasColumn('payments','snap_redirect_url')) {
                $table->dropColumn('snap_redirect_url');
            }

            if (Schema::hasColumn('payments','midtrans_transaction_id')) {
                $table->dropColumn('midtrans_transaction_id');
            }
            if (Schema::hasColumn('payments','midtrans_payment_type')) {
                $table->dropColumn('midtrans_payment_type');
            }
            if (Schema::hasColumn('payments','midtrans_settlement_time')) {
                $table->dropColumn('midtrans_settlement_time');
            }

            if (Schema::hasColumn('payments','reference')) {
                // drop index by name supaya tidak tergantung Doctrine
                try { $table->dropIndex('payments_reference_index'); } catch (\Throwable $e) {}
                $table->dropColumn('reference');
            }
        });
    }
};
