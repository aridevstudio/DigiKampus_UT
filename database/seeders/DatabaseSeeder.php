<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed 3 akun default role
        // admin: Nomor Induk 19880001 / password123
        // dosen: Nomor Induk 19880002 / password123
        // mahasiswa: Nomor Induk 20260001 / password123
        $this->call([
            UserRoleSeeder::class,
            MahasiswaSeeder::class,
        ]);
    }
}
