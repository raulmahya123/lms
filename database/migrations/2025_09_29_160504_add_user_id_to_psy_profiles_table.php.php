// database/migrations/2025_09_29_000002_fix_user_id_type_on_psy_profiles.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('psy_profiles', function (Blueprint $table) {
            // Hapus FK & kolom lama (ulid 26 char)
            if (Schema::hasColumn('psy_profiles', 'user_id')) {
                // Aman untuk constrained FK:
                $table->dropConstrainedForeignId('user_id');
            }
        });

        Schema::table('psy_profiles', function (Blueprint $table) {
            // Buat ulang sebagai UUID 36 char
            $table->foreignUuid('user_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('psy_profiles', function (Blueprint $table) {
            // Balik lagi ke ULID (jika perlu rollback)
            $table->dropConstrainedForeignId('user_id');
            $table->foreignUlid('user_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->nullOnDelete();
        });
    }
};
