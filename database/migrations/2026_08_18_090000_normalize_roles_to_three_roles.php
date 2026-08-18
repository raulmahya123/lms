<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $mentor = Role::firstOrCreate(['name' => 'mentor']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $legacyMentorIds = Role::query()
            ->whereIn('name', ['guru', 'dosen'])
            ->pluck('id');

        if ($legacyMentorIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('role_id', $legacyMentorIds)
                ->update(['role_id' => $mentor->id]);
        }

        $salesIds = Role::query()
            ->where('name', 'sales')
            ->pluck('id');

        if ($salesIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('role_id', $salesIds)
                ->update(['role_id' => $user->id]);
        }

        Role::query()
            ->whereIn('name', ['guru', 'dosen', 'sales'])
            ->delete();

        $admin->touch();
        $mentor->touch();
        $user->touch();
    }

    public function down(): void
    {
        Role::firstOrCreate(['name' => 'guru']);
        Role::firstOrCreate(['name' => 'sales']);
    }
};

