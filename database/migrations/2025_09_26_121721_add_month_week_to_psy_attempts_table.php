<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('psy_attempts', function (Blueprint $t) {
            $t->unsignedTinyInteger('week')->nullable()->index()->after('submitted_at');  // 1..53
            $t->unsignedTinyInteger('month')->nullable()->index()->after('week');        // 1..12
        });
    }
    public function down(): void {
        Schema::table('psy_attempts', function (Blueprint $t) {
            $t->dropColumn(['week','month']);
        });
    }
};
