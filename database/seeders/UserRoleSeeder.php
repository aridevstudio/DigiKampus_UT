<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');
        $adminNomorInduk = '19880001';
        $dosenNomorInduk = '19880002';

        $admin = User::updateOrCreate(
            ['email' => 'admin@digikampus.test'],
            [
                'name' => 'Admin DigiKampus',
                'password' => $defaultPassword,
                'role' => 'admin',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'nomor_induk' => $adminNomorInduk,
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-01-10',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Admin Pusat No. 1',
                'no_hp' => '081234567800',
                'id_jurusan' => null,
            ]
        );

        $dosen = User::updateOrCreate(
            ['email' => 'dosen@digikampus.test'],
            [
                'name' => 'Dosen DigiKampus',
                'password' => $defaultPassword,
                'role' => 'dosen',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        $mahasiswa = User::updateOrCreate(
            ['email' => 'mahasiswa@digikampus.test'],
            [
                'name' => 'Mahasiswa DigiKampus',
                'password' => $defaultPassword,
                'role' => 'mahasiswa',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $dosen->id],
            [
                'nomor_induk' => $dosenNomorInduk,
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1988-03-15',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Pendidikan No. 10',
                'no_hp' => '081234567801',
                'id_jurusan' => null,
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $mahasiswa->id],
            [
                'nomor_induk' => 20260001,
                'tempat_lahir' => 'Sukabumi',
                'tanggal_lahir' => '2004-07-22',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Kampus Utama No. 1',
                'no_hp' => '081234567802',
                'id_jurusan' => null,
            ]
        );

        $this->command->info('UserRoleSeeder berhasil: admin, dosen, mahasiswa siap dipakai.');
        $this->command->line('Login test:');
        $this->command->line('- admin admin@digikampus.test / password123');
        $this->command->line("- dosen Nomor Induk {$dosenNomorInduk} / password123");
        $this->command->line('- mahasiswa Nomor Induk 20260001 / password123');
    }
}
