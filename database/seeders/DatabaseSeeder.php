<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Buat user admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nip' => '123456789'
        ]);

        // Buat user guru
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'nip' => '987654321'
        ]);

        // Buat kelas contoh
        Kelas::create([
            'nama_kelas' => 'X IPA 1',
            'tingkat' => '10',
            'jurusan' => 'IPA',
            'tahun_ajaran' => '2024'
        ]);
    }
}
