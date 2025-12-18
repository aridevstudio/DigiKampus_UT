<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrolledCourseResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\AgendaResource;
use App\Models\Enrollment;
use App\Models\News;
use App\Models\Agenda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Mahasiswa Dashboard
 */
class MahasiswaDashboardController extends Controller
{
    /**
     * Get Dashboard Overview
     * 
     * Endpoint untuk mendapatkan semua data dashboard mahasiswa.
     * Mengembalikan data progress belajar, kursus yang diikuti, berita, dan agenda.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get progress statistics
        $progressData = $this->getProgressData($user->id);

        // Get enrolled courses (limit 3 for dashboard)
        $enrolledCourses = Enrollment::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->where('status', 'aktif')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        // Get latest news (limit 3)
        $news = News::active()
            ->published()
            ->orderBy('tanggal_publish', 'desc')
            ->take(3)
            ->get();

        // Get current month agenda
        $agenda = Agenda::where('id_mahasiswa', $user->id)
            ->byMonth(now()->month, now()->year)
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'progress' => $progressData,
                'enrolled_courses' => EnrolledCourseResource::collection($enrolledCourses),
                'news' => NewsResource::collection($news),
                'agenda' => AgendaResource::collection($agenda),
            ]
        ]);
    }

    /**
     * Get Learning Progress
     * 
     * Endpoint untuk mendapatkan statistik progress belajar mahasiswa.
     * Mengembalikan total kemajuan, kursus aktif, selesai, dan tertunda.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function progress(Request $request): JsonResponse
    {
        $user = $request->user();
        $progressData = $this->getProgressData($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Progress data retrieved successfully',
            'data' => $progressData
        ]);
    }

    /**
     * Get Enrolled Courses
     * 
     * Endpoint untuk mendapatkan daftar kursus yang sedang diikuti mahasiswa.
     * Menampilkan nama kursus, thumbnail, progress, dan informasi dosen.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function enrolledCourses(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrolledCourses = Enrollment::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Enrolled courses retrieved successfully',
            'data' => EnrolledCourseResource::collection($enrolledCourses)
        ]);
    }

    /**
     * Get News and Announcements
     * 
     * Endpoint untuk mendapatkan daftar berita dan pengumuman.
     * Mengembalikan berita yang aktif dan sudah dipublish.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function news(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);

        $news = News::active()
            ->published()
            ->orderBy('tanggal_publish', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'News retrieved successfully',
            'data' => NewsResource::collection($news)
        ]);
    }

    /**
     * Get Agenda / Activity Calendar
     * 
     * Endpoint untuk mendapatkan agenda kegiatan mahasiswa berdasarkan bulan.
     * Default menampilkan bulan ini jika tidak ada parameter.
     * 
     * @security BearerToken
     * @queryParam month int Bulan (1-12). Defaults to current month.
     * @queryParam year int Tahun. Defaults to current year.
     * @param Request $request
     * @return JsonResponse
     */
    public function agenda(Request $request): JsonResponse
    {
        $user = $request->user();
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $agenda = Agenda::where('id_mahasiswa', $user->id)
            ->byMonth($month, $year)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Group agenda by date for calendar view
        $groupedAgenda = $agenda->groupBy(function ($item) {
            return $item->tanggal->format('Y-m-d');
        });

        return response()->json([
            'success' => true,
            'message' => 'Agenda retrieved successfully',
            'data' => [
                'month' => (int) $month,
                'year' => (int) $year,
                'items' => AgendaResource::collection($agenda),
                'grouped_by_date' => $groupedAgenda->map(function ($items) {
                    return AgendaResource::collection($items);
                }),
            ]
        ]);
    }

    /**
     * Calculate progress data for a mahasiswa.
     *
     * @param int $mahasiswaId
     * @return array
     */
    private function getProgressData(int $mahasiswaId): array
    {
        $enrollments = Enrollment::where('id_mahasiswa', $mahasiswaId)->get();

        $kursusAktif = $enrollments->where('status', 'aktif')->count();
        $kursusSelesai = $enrollments->where('status', 'selesai')->count();

        // Calculate courses with progress < 50% as "tertunda"
        $kursusTertunda = $enrollments
            ->where('status', 'aktif')
            ->where('progress', '<', 50)
            ->count();

        // Calculate total progress as average of all active enrollments
        $totalKemajuan = 0;
        $activeEnrollments = $enrollments->where('status', 'aktif');

        if ($activeEnrollments->count() > 0) {
            $totalKemajuan = round($activeEnrollments->avg('progress'), 0);
        }

        return [
            'total_kemajuan' => $totalKemajuan,
            'kursus_aktif' => $kursusAktif,
            'kursus_selesai' => $kursusSelesai,
            'kursus_tertunda' => $kursusTertunda,
        ];
    }
}
