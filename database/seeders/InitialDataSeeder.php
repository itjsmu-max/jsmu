<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Roles
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Super Admin',
            'description' => 'Full access',
        ]);

        $userRoleId = DB::table('roles')->insertGetId([
            'name' => 'User',
            'description' => 'Regular user',
        ]);

        // Accounts
        DB::table('accounts')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRoleId,
        ]);

        DB::table('accounts')->insert([
            'username' => 'user1',
            'password' => Hash::make('user123'),
            'role_id' => $userRoleId,
        ]);
    }
}
