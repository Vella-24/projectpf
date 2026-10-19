<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Staff Data Entry',
                'email' => 'entry@ubaya.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'data_entry',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Validator Akademik',
                'email' => 'validator@ubaya.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'validator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Super Administrator',
                'email' => 'admin@ubaya.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
