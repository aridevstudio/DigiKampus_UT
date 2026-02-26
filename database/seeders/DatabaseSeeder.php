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
        // admin: admin@digikampus.test / password123
        // dosen: dosen@digikampus.test / password123
        // mahasiswa: mahasiswa@digikampus.test / password123
        $this->call([
            UserRoleSeeder::class,
            MahasiswaSeeder::class,
        ]);
    }
}
