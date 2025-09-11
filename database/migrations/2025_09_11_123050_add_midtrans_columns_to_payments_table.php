<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // kalau mau relasi langsung ke memberships
            if (!Schema::hasColumn('payments', 'membership_id')) {
                $table->foreignId('membership_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('reference');
            }

            if (!Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->string('snap_redirect_url')->nullable()->after('snap_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignIdIfExists('membership_id');
            $table->dropColumn(['snap_token', 'snap_redirect_url']);
        });
    }
};
