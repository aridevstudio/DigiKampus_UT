<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\MaterialProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Dosen Student Progress
 */
class DosenStudentProgressController extends Controller
{
    /**
     * Get Student Progress List
     * 
     * Endpoint untuk mendapatkan daftar progres mahasiswa pada kursus yang diajarkan.
     * Mendukung filter berdasarkan kursus, status, dan pencarian nama.
     *
     * @security BearerToken
     * @queryParam course_id int Filter by specific course ID.
     * @queryParam status string Filter status: semua, belum_mulai, sedang_berjalan, selesai. Defaults to semua.
     * @queryParam search string Cari berdasarkan nama mahasiswa.
     * @queryParam per_page int Jumlah item per halaman. Defaults to 10.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;
        $courseId = $request->query('course_id');
        $status = $request->query('status', 'semua');
        $search = $request->query('search');
        $perPage = $request->query('per_page', 10);

        // Get courses owned by dosen
        $dosenCourseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');

        // Build query
        $query = Enrollment::whereIn('id_course', $dosenCourseIds)
            ->with(['mahasiswa.profile', 'course.materials']);

        // Filter by specific course
        if ($courseId) {
            $query->where('id_course', $courseId);
        }

        // Filter by status
        if ($status !== 'semua') {
            switch ($status) {
                case 'belum_mulai':
                    $query->where('progress', 0);
                    break;
                case 'sedang_berjalan':
                    $query->where('progress', '>', 0)->where('progress', '<', 100);
                    break;
                case 'selesai':
                    $query->where('progress', 100);
                    break;
            }
        }

        // Search by student name
        if ($search) {
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('updated_at', 'desc')->paginate($perPage);

        // Get available courses for filter dropdown
        $availableCourses = Course::where('id_dosen', $dosenId)
            ->select('id_course', 'nama_course')
            ->get();

        // Map student progress data
        $studentsData = $enrollments->getCollection()->map(function ($enrollment) {
            $mahasiswa = $enrollment->mahasiswa;
            $course = $enrollment->course;

            // Get last accessed module
            $lastProgress = MaterialProgress::whereIn(
                'id_material',
                $course->materials->pluck('id_material')
            )
                ->where('id_mahasiswa', $mahasiswa->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            $lastModule = $lastProgress
                ? $course->materials->firstWhere('id_material', $lastProgress->id_material)
                : null;

            // Determine status
            if ($enrollment->progress >= 100) {
                $statusText = 'Selesai';
            } elseif ($enrollment->progress > 0) {
                $statusText = 'Sedang Berjalan';
            } else {
                $statusText = 'Belum Mulai';
            }

            return [
                'id' => $enrollment->id_enroll,
                'mahasiswa' => [
                    'id' => $mahasiswa->id,
                    'name' => $mahasiswa->name,
                    'nomor_induk' => $mahasiswa->profile->nomor_induk ?? null,
                    'avatar' => $mahasiswa->profile->avatar ?? null
                ],
                'course' => [
                    'id' => $course->id_course,
                    'nama' => $course->nama_course
                ],
                'modul_terakhir' => $lastModule ? [
                    'id' => $lastModule->id_material,
                    'judul' => $lastModule->judul_material
                ] : null,
                'progress' => round($enrollment->progress, 0),
                'status' => $statusText,
                'aktivitas_terakhir' => $enrollment->updated_at->diffForHumans()
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data progres mahasiswa berhasil diambil.',
            'data' => [
                'students' => $studentsData,
                'filters' => [
                    'courses' => $availableCourses
                ],
                'pagination' => [
                    'current_page' => $enrollments->currentPage(),
                    'last_page' => $enrollments->lastPage(),
                    'per_page' => $enrollments->perPage(),
                    'total' => $enrollments->total()
                ]
            ]
        ], 200);
    }

    /**
     * Get Student Detail Progress
     * 
     * Endpoint untuk mendapatkan detail progres satu mahasiswa pada kursus tertentu.
     * Menampilkan semua modul dan status penyelesaian masing-masing.
     *
     * @security BearerToken
     * @param Request $request
     * @param int $enrollmentId Enrollment ID
     * @return JsonResponse
     */
    public function show(Request $request, int $enrollmentId): JsonResponse
    {
        $dosenId = $request->user()->id;
        $dosenCourseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');

        $enrollment = Enrollment::where('id_enroll', $enrollmentId)
            ->whereIn('id_course', $dosenCourseIds)
            ->with(['mahasiswa.profile', 'course.materials'])
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Data enrollment tidak ditemukan.'
            ], 404);
        }

        $mahasiswa = $enrollment->mahasiswa;
        $course = $enrollment->course;

        // Get all material progress for this student
        $materialsProgress = $course->materials->map(function ($material) use ($mahasiswa) {
            $progress = MaterialProgress::where('id_material', $material->id_material)
                ->where('id_mahasiswa', $mahasiswa->id)
                ->first();

            return [
                'id' => $material->id_material,
                'judul' => $material->judul_material,
                'tipe' => $material->tipe,
                'urutan' => $material->urutan,
                'is_completed' => $progress ? $progress->is_completed : false,
                'completed_at' => $progress && $progress->is_completed ? $progress->updated_at->format('Y-m-d H:i') : null
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Detail progres mahasiswa berhasil diambil.',
            'data' => [
                'mahasiswa' => [
                    'id' => $mahasiswa->id,
                    'name' => $mahasiswa->name,
                    'email' => $mahasiswa->email,
                    'nomor_induk' => $mahasiswa->profile->nomor_induk ?? null,
                    'avatar' => $mahasiswa->profile->avatar ?? null,
                    'bergabung_sejak' => $mahasiswa->created_at->format('F Y')
                ],
                'course' => [
                    'id' => $course->id_course,
                    'nama' => $course->nama_course
                ],
                'progress' => round($enrollment->progress, 0),
                'tanggal_daftar' => $enrollment->tanggal_daftar->format('Y-m-d'),
                'materials' => $materialsProgress
            ]
        ], 200);
    }
}
