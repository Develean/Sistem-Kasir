<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin Toko',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // User Kasir
        User::updateOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name'     => 'Staff Kasir',
                'password' => Hash::make('kasir123'),
                'role'     => 'kasir',
            ]
        );
    }
}
