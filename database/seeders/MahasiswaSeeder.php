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
            'password' => Hash::make('password123'),
        ])->each(function ($user) {
            Profile::create([
                'id_user' => $user->id,
                'nim' => fake()->numerify('23123456'),
                'alamat' => fake()->address(),
                'no_hp' => fake()->phoneNumber(),
            ]);
        });
    }
}
