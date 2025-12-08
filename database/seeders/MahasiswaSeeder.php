<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create([
            'role' => 'mahasiswa',
            'status' => 'aktif',
            'password' => Hash::make('password123'),
        ])->each(function ($user) {
            Profile::create([
                'user_id' => $user->id,  // Fixed: user_id bukan id_user
                'nim' => fake()->numerify('202401##'),  // Format: 20240101, 20240102, dst
                'tempat_lahir' => fake()->city(),
                'tanggal_lahir' => fake()->date('Y-m-d', '-18 years'),
                'jenis_kelamin' => fake()->randomElement(['L', 'P']),
                'alamat' => fake()->address(),
                'no_hp' => fake()->phoneNumber(),
                'id_jurusan' => null,  // Bisa diisi nanti jika ada data jurusan
            ]);
        });
    }
}
