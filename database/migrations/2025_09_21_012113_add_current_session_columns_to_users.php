<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // kolom untuk kunci 1-device
            $table->string('current_session_id', 100)->nullable()->index()->after('remember_token');
            $table->timestamp('current_login_at')->nullable()->after('current_session_id');
            $table->string('current_ip', 64)->nullable()->after('current_login_at');
            $table->string('current_ua_hash', 64)->nullable()->after('current_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_session_id','current_login_at','current_ip','current_ua_hash']);
        });
    }
};
