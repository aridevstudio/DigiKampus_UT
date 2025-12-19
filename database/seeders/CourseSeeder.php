<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a dosen user
        $dosen = User::where('role', 'dosen')->first();

        if (!$dosen) {
            $dosen = User::create([
                'name' => 'Dr. Ahmad Fauzi',
                'email' => 'ahmad.fauzi@ut.ac.id',
                'password' => bcrypt('password123'),
                'role' => 'dosen',
                'status' => 'aktif',
            ]);
        }

        // Sample courses data
        $courses = [
            // Webinar courses
            [
                'kode_course' => 'WEB001',
                'nama_course' => 'Dasar-dasar Desain UI/U',
                'deskripsi' => 'Pelajari prinsip fundamental desain antarmuka pengguna',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
                'tipe' => 'webinar',
                'harga' => 150000,
                'rating' => 4.5,
                'jumlah_ulasan' => 1250,
            ],
            [
                'kode_course' => 'WEB002',
                'nama_course' => 'Digital Marketing Strategy',
                'deskripsi' => 'Strategi pemasaran digital untuk bisnis modern',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
                'tipe' => 'webinar',
                'harga' => 200000,
                'rating' => 4.3,
                'jumlah_ulasan' => 890,
            ],

            // Kursus courses
            [
                'kode_course' => 'KRS001',
                'nama_course' => 'Pengembangan Web dengan JavaScript',
                'deskripsi' => 'Kuasai framework JavaScript populer untuk membangun aplikasi web',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
                'tipe' => 'kursus',
                'harga' => 250000,
                'rating' => 4.7,
                'jumlah_ulasan' => 2800,
            ],
            [
                'kode_course' => 'KRS002',
                'nama_course' => 'Analisis Data dengan Python',
                'deskripsi' => 'Dari pemula hingga mahir, pelajari cara mengolah dan analisis data',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
                'tipe' => 'kursus',
                'harga' => 199000,
                'rating' => 4.4,
                'jumlah_ulasan' => 1980,
            ],
            [
                'kode_course' => 'KRS003',
                'nama_course' => 'Machine Learning Fundamentals',
                'deskripsi' => 'Memahami dasar-dasar machine learning dan implementasinya',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
                'tipe' => 'kursus',
                'harga' => 350000,
                'rating' => 4.8,
                'jumlah_ulasan' => 1500,
            ],

            // Tiket courses (events)
            [
                'kode_course' => 'TKT001',
                'nama_course' => 'Tech Conference 2025',
                'deskripsi' => 'Tiket masuk untuk acara teknologi terbesar tahun ini',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
                'tipe' => 'tiket',
                'harga' => 500000,
                'rating' => 0,
                'jumlah_ulasan' => 0,
            ],
            [
                'kode_course' => 'TKT002',
                'nama_course' => 'Startup Summit Indonesia',
                'deskripsi' => 'Konferensi startup terbesar dengan speaker internasional',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
                'tipe' => 'tiket',
                'harga' => 750000,
                'rating' => 0,
                'jumlah_ulasan' => 0,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::updateOrCreate(
                ['kode_course' => $courseData['kode_course']],
                $courseData
            );
        }

        $this->command->info('Course seeder completed successfully!');
        $this->command->info('Created/Updated: ' . count($courses) . ' courses');
    }
}
