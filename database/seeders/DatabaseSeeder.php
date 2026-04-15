<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Perpustakaan',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Buat Siswa (Pakai 'student' sesuai ENUM)
        User::updateOrCreate(
            ['username' => 'siswa'],
            [
                'name' => 'Siswa Contoh',
                'email' => 'siswa@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'student', // HARUS SAMA DENGAN ENUM
            ]
        );

        $this->call([
            BookSeeder::class,
        ]);
    }
}