<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // true = gratis, false = berbayar
            $table->boolean('is_free')->default(true)->after('cover_url');

            // harga dalam mata uang lokal (Rp) — gunakan decimal agar aman untuk koma
            $table->decimal('price', 12, 2)->nullable()->after('is_free');
            // Catatan:
            // - Untuk kursus gratis, price boleh null.
            // - Untuk kursus berbayar, price wajib diisi (validasi di controller).
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['is_free', 'price']);
        });
    }
};
