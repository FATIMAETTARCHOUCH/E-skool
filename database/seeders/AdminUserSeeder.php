<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['username' => 'admin'],
            [
                'role' => 'admin',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'), // simple default
                'is_first_login' => false,
            ]
        );
    }
}
