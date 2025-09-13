<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    \App\Models\Role::firstOrCreate(['name' => 'admin']);
    \App\Models\Role::firstOrCreate(['name' => 'user']);
    \App\Models\Role::firstOrCreate(['name'=> 'mentor']);
}

}
