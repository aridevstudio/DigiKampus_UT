<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\News;
use App\Models\Agenda;
use App\Models\User;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a mahasiswa user (role = 'mahasiswa')
        $mahasiswa = User::where('role', 'mahasiswa')->first();

        if (!$mahasiswa) {
            $this->command->info('No mahasiswa found. Please create a mahasiswa user first.');
            return;
        }

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

        // Create sample courses
        $courses = [
            [
                'kode_course' => 'EKMA4116',
                'nama_course' => 'Manajemen',
                'deskripsi' => 'Mata kuliah Manajemen Bisnis dan Organisasi',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
            ],
            [
                'kode_course' => 'EKMA4115',
                'nama_course' => 'Akuntansi',
                'deskripsi' => 'Mata kuliah Pengantar Akuntansi',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
            ],
            [
                'kode_course' => 'MATA4101',
                'nama_course' => 'Statistika',
                'deskripsi' => 'Mata kuliah Statistika Dasar',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
            ],
            [
                'kode_course' => 'ISIP4110',
                'nama_course' => 'Pengantar Sosiologi',
                'deskripsi' => 'Mata kuliah Dasar Sosiologi',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
            ],
            [
                'kode_course' => 'MKDU4111',
                'nama_course' => 'Pendidikan Kewarganegaraan',
                'deskripsi' => 'Mata kuliah Pendidikan Kewarganegaraan',
                'id_dosen' => $dosen->id,
                'thumbnail' => null,
                'status' => 'aktif',
            ],
        ];

        $createdCourses = [];
        foreach ($courses as $courseData) {
            $course = Course::updateOrCreate(
                ['kode_course' => $courseData['kode_course']],
                $courseData
            );
            $createdCourses[] = $course;
        }

        // Create enrollments for mahasiswa
        $enrollments = [
            ['course_index' => 0, 'progress' => 45, 'status' => 'aktif'],
            ['course_index' => 1, 'progress' => 78, 'status' => 'aktif'],
            ['course_index' => 2, 'progress' => 32, 'status' => 'aktif'],
            ['course_index' => 3, 'progress' => 100, 'status' => 'selesai'],
            ['course_index' => 4, 'progress' => 100, 'status' => 'selesai'],
        ];

        foreach ($enrollments as $enrollData) {
            Enrollment::updateOrCreate(
                [
                    'id_mahasiswa' => $mahasiswa->id,
                    'id_course' => $createdCourses[$enrollData['course_index']]->id_course,
                ],
                [
                    'tanggal_daftar' => Carbon::now()->subDays(rand(10, 60)),
                    'progress' => $enrollData['progress'],
                    'status' => $enrollData['status'],
                ]
            );
        }

        // Create news/announcements
        $newsData = [
            [
                'judul' => 'Reminder Deadline Submission Modul Manajemen',
                'konten' => 'Jangan lupa untuk mengumpulkan tugas modul manajemen sebelum tanggal 20 Desember 2025.',
                'kategori' => 'pengumuman',
                'tanggal_publish' => Carbon::now()->subHours(2),
            ],
            [
                'judul' => 'Webinar Manajemen dibuka minggu ini',
                'konten' => 'Webinar interaktif tentang Manajemen Modern akan diadakan pada hari Sabtu, 21 Desember 2025.',
                'kategori' => 'event',
                'tanggal_publish' => Carbon::now()->subHours(5),
            ],
            [
                'judul' => 'Ikuti Perlombaan Tingkat Nasional ini!',
                'konten' => 'Kompetisi karya tulis ilmiah tingkat nasional terbuka untuk semua mahasiswa UT.',
                'kategori' => 'berita',
                'tanggal_publish' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($newsData as $news) {
            News::updateOrCreate(
                ['judul' => $news['judul']],
                array_merge($news, ['is_active' => true])
            );
        }

        // Create agenda/events for current month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $agendaData = [
            [
                'judul' => 'Webinar Teknik Informatika',
                'deskripsi' => 'Webinar online tentang perkembangan teknologi terkini',
                'tanggal' => Carbon::create($currentYear, $currentMonth, 10),
                'waktu_mulai' => '09:00',
                'waktu_selesai' => '12:00',
                'tipe' => 'webinar',
                'warna' => '#3B82F6',
            ],
            [
                'judul' => 'Deadline Kursus Data Science',
                'deskripsi' => 'Batas akhir pengumpulan tugas data science',
                'tanggal' => Carbon::create($currentYear, $currentMonth, 12),
                'waktu_mulai' => '23:59',
                'waktu_selesai' => null,
                'tipe' => 'deadline',
                'warna' => '#EF4444',
            ],
            [
                'judul' => 'Workshop',
                'deskripsi' => 'Workshop praktik pengembangan web',
                'tanggal' => Carbon::create($currentYear, $currentMonth, 15),
                'waktu_mulai' => '13:00',
                'waktu_selesai' => '16:00',
                'tipe' => 'workshop',
                'warna' => '#F59E0B',
            ],
        ];

        foreach ($agendaData as $agenda) {
            Agenda::updateOrCreate(
                [
                    'id_mahasiswa' => $mahasiswa->id,
                    'judul' => $agenda['judul'],
                ],
                $agenda
            );
        }

        $this->command->info('Dashboard seeder completed successfully!');
        $this->command->info("Created/Updated:");
        $this->command->info("- 5 Courses");
        $this->command->info("- 5 Enrollments for mahasiswa ID: {$mahasiswa->id}");
        $this->command->info("- 3 News items");
        $this->command->info("- 3 Agenda items");
    }
}
