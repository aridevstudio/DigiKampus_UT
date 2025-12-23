<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswa = User::where('role', 'mahasiswa')->first();

        if (!$mahasiswa) {
            $this->command->info('No mahasiswa found. Please create a mahasiswa user first.');
            return;
        }

        $notifications = [
            [
                'judul' => 'Kursus Baru Tersedia',
                'konten' => 'Kursus "Psikologi Organisasi" kini dapat diambil di menu Get Course.',
                'tipe' => 'kursus_pembelajaran',
                'icon' => 'book',
                'icon_color' => '#3B82F6',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'judul' => 'Balasan dari Tutor',
                'konten' => 'Tutor Anda membalas diskusi di kursus "Data Science".',
                'tipe' => 'kursus_pembelajaran',
                'icon' => 'chat',
                'icon_color' => '#10B981',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'judul' => 'Pengingat Ujian',
                'konten' => 'Workshop "Belajar Front-end Pemula" akan dimulai besok pukul 09.00 WIB.',
                'tipe' => 'jadwal_ujian',
                'icon' => 'calendar',
                'icon_color' => '#F59E0B',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'judul' => 'Pencapaian Baru',
                'konten' => 'Selamat! Anda telah menyelesaikan 5 modul pembelajaran.',
                'tipe' => 'pencapaian',
                'icon' => 'trophy',
                'icon_color' => '#8B5CF6',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'judul' => 'Webinar Selesai',
                'konten' => 'Rekaman webinar "Manajemen Keuangan" sudah tersedia.',
                'tipe' => 'kursus_pembelajaran',
                'icon' => 'video',
                'icon_color' => '#EC4899',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($notifications as $notif) {
            Notification::create([
                'id_mahasiswa' => $mahasiswa->id,
                ...$notif
            ]);
        }

        $this->command->info('Notification seeder completed! Created ' . count($notifications) . ' notifications.');
    }
}
