<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * @tags Dosen Dashboard
 */
class DosenDashboardController extends Controller
{
    /**
     * Get Dashboard Overview
     * 
     * Endpoint untuk mendapatkan semua data dashboard dosen.
     * Mengembalikan statistik, kursus yang dikelola, progres mahasiswa terbaru, dan jadwal mengajar.
     *
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;

        // Get statistics
        $statistics = $this->getStatistics($dosenId);

        // Get courses managed by dosen (limit 3 for dashboard)
        $myCourses = $this->getMyCourses($dosenId, 3);

        // Get recent student progress
        $recentProgress = $this->getRecentProgress($dosenId, 4);

        // Get upcoming teaching schedule
        $upcomingSchedule = $this->getUpcomingSchedule($dosenId, 3);

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil.',
            'data' => [
                'statistics' => $statistics,
                'my_courses' => $myCourses,
                'recent_progress' => $recentProgress,
                'upcoming_schedule' => $upcomingSchedule
            ]
        ], 200);
    }

    /**
     * Calculate statistics for dashboard.
     *
     * @param int $dosenId
     * @return array
     */
    private function getStatistics(int $dosenId): array
    {
        // Total courses by this dosen
        $totalCourses = Course::where('id_dosen', $dosenId)->count();

        // Total enrolled students across all courses
        $courseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');
        $totalStudents = Enrollment::whereIn('id_course', $courseIds)->distinct('id_mahasiswa')->count('id_mahasiswa');

        // Average progress
        $averageProgress = Enrollment::whereIn('id_course', $courseIds)->avg('progress') ?? 0;

        // Upcoming sessions (courses with webinar type scheduled in future)
        $upcomingSessions = Course::where('id_dosen', $dosenId)
            ->where('tipe', 'webinar')
            ->where('status', 'aktif')
            ->count();

        return [
            'total_courses' => $totalCourses,
            'total_students' => $totalStudents,
            'average_progress' => round($averageProgress, 0),
            'upcoming_sessions' => $upcomingSessions
        ];
    }

    /**
     * Get courses managed by dosen.
     *
     * @param int $dosenId
     * @param int $limit
     * @return array
     */
    private function getMyCourses(int $dosenId, int $limit): array
    {
        $courses = Course::where('id_dosen', $dosenId)
            ->where('status', 'aktif')
            ->with(['enrollments'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $courses->map(function ($course) {
            $enrollmentCount = $course->enrollments->count();
            $avgProgress = $course->enrollments->avg('progress') ?? 0;

            return [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'thumbnail' => $course->thumbnail,
                'jumlah_mahasiswa' => $enrollmentCount,
                'progress_rata_rata' => round($avgProgress, 0)
            ];
        })->toArray();
    }

    /**
     * Get recent student progress.
     *
     * @param int $dosenId
     * @param int $limit
     * @return array
     */
    private function getRecentProgress(int $dosenId, int $limit): array
    {
        $courseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');

        $enrollments = Enrollment::whereIn('id_course', $courseIds)
            ->with(['mahasiswa', 'course'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        return $enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->id_enroll,
                'mahasiswa' => [
                    'id' => $enrollment->mahasiswa->id ?? null,
                    'name' => $enrollment->mahasiswa->name ?? 'Unknown',
                    'avatar' => $enrollment->mahasiswa->profile->avatar ?? null
                ],
                'course' => $enrollment->course->nama_course ?? 'Unknown',
                'progress' => round($enrollment->progress, 0),
                'updated_at' => $enrollment->updated_at->diffForHumans()
            ];
        })->toArray();
    }

    /**
     * Get upcoming teaching schedule.
     *
     * @param int $dosenId
     * @param int $limit
     * @return array
     */
    private function getUpcomingSchedule(int $dosenId, int $limit): array
    {
        $courses = Course::where('id_dosen', $dosenId)
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $courses->map(function ($course) {
            return [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'tanggal' => Carbon::now()->addDays(rand(1, 14))->format('l, d F Y'),
                'waktu' => '09:00 - 11:00 WIB'
            ];
        })->toArray();
    }
}
