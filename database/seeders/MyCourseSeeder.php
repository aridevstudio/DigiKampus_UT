<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;

class MyCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a mahasiswa user
        $mahasiswa = User::where('role', 'mahasiswa')->first();

        if (!$mahasiswa) {
            $this->command->info('No mahasiswa found. Please create a mahasiswa user first.');
            return;
        }

        // Get existing courses or create new ones
        $courses = Course::aktif()->take(4)->get();

        if ($courses->isEmpty()) {
            $this->command->info('No active courses found. Please run CourseSeeder first.');
            return;
        }

        // Create enrollments for mahasiswa
        $progressValues = [76, 100, 0, 45];
        foreach ($courses as $index => $course) {
            $progress = $progressValues[$index] ?? rand(0, 100);

            Enrollment::updateOrCreate(
                [
                    'id_mahasiswa' => $mahasiswa->id,
                    'id_course' => $course->id_course,
                ],
                [
                    'tanggal_daftar' => Carbon::now()->subDays(rand(10, 60)),
                    'progress' => $progress,
                    'status' => $progress >= 100 ? 'selesai' : 'aktif',
                ]
            );

            // Create materials for each course
            $materials = [
                [
                    'judul_material' => 'Pengenalan ' . $course->nama_course,
                    'tipe' => 'video',
                    'konten' => 'Pengenalan materi kursus',
                    'video_url' => 'https://example.com/video1.mp4',
                    'urutan' => 1,
                    'durasi' => 15,
                ],
                [
                    'judul_material' => 'Dasar-dasar ' . $course->nama_course,
                    'tipe' => 'video',
                    'konten' => 'Materi dasar kursus',
                    'video_url' => 'https://example.com/video2.mp4',
                    'urutan' => 2,
                    'durasi' => 25,
                ],
                [
                    'judul_material' => 'Praktik ' . $course->nama_course,
                    'tipe' => 'video',
                    'konten' => 'Materi praktik',
                    'video_url' => 'https://example.com/video3.mp4',
                    'urutan' => 3,
                    'durasi' => 30,
                ],
                [
                    'judul_material' => 'Modul Materi ' . $course->nama_course,
                    'tipe' => 'file',
                    'konten' => 'PDF modul pembelajaran',
                    'video_url' => null,
                    'urutan' => 4,
                    'durasi' => 0,
                ],
            ];

            foreach ($materials as $materialData) {
                CourseMaterial::updateOrCreate(
                    [
                        'id_course' => $course->id_course,
                        'judul_material' => $materialData['judul_material'],
                    ],
                    $materialData
                );
            }
        }

        $this->command->info('MyCourse seeder completed successfully!');
        $this->command->info('Created enrollments and materials for ' . $courses->count() . ' courses');
    }
}
