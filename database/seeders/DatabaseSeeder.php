<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Admin — akses penuh ke semua fitur
        User::firstOrCreate(
            ['email' => 'admin@webmonitor.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Akun User — hanya bisa melakukan ping
        User::firstOrCreate(
            ['email' => 'user@webmonitor.com'],
            [
                'name'     => 'Regular User',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );
    }
}