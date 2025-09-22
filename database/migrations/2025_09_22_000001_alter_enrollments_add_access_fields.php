<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'access_via')) {
                // purchase | membership | free
                $table->string('access_via', 20)->nullable()->after('status');
            }
            if (!Schema::hasColumn('enrollments', 'access_expires_at')) {
                $table->timestamp('access_expires_at')->nullable()->after('access_via');
                $table->index('access_expires_at', 'enrollments_access_expires_at_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'access_expires_at')) {
                $table->dropIndex('enrollments_access_expires_at_index');
                $table->dropColumn('access_expires_at');
            }
            if (Schema::hasColumn('enrollments', 'access_via')) {
                $table->dropColumn('access_via');
            }
        });
    }
};
