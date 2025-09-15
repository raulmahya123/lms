<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole  = Role::where('name', 'admin')->first();
        $userRole   = Role::where('name', 'user')->first();
        $mentorRole = Role::where('name', 'mentor')->first();

        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole?->id, // Role juga harus disesuaikan: kalau roles.id sudah UUID, pastikan migrationnya pakai uuid
            ]
        );

        // User
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'Regular User',
                'password' => Hash::make('password'),
                'role_id'  => $userRole?->id,
            ]
        );

        // Mentor
        User::firstOrCreate(
            ['email' => 'mentor@example.com'],
            [
                'name'     => 'Mentor Utama',
                'password' => Hash::make('password'),
                'role_id'  => $mentorRole?->id,
            ]
        );
    }
}
