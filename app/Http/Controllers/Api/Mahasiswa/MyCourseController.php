<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Resources\MyCourseResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\CourseResource;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\MaterialProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * @tags Mahasiswa My Courses
 */
class MyCourseController extends Controller
{
    /**
     * Get My Enrolled Courses
     * 
     * Endpoint untuk mendapatkan daftar kursus yang diikuti mahasiswa.
     * Mendukung filter berdasarkan tipe course.
     * 
     * @security BearerToken
     * @queryParam tipe string Optional. Filter by tipe: webinar, tiket, kursus
     * @queryParam status string Optional. Filter by status: aktif, selesai
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $tipe = $request->query('tipe');
        $status = $request->query('status');

        $query = Enrollment::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->orderBy('updated_at', 'desc');

        if ($tipe) {
            $query->byCourseTipe($tipe);
        }

        if ($status === 'aktif') {
            $query->aktif();
        } elseif ($status === 'selesai') {
            $query->selesai();
        }

        $enrollments = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'My courses retrieved successfully',
            'data' => MyCourseResource::collection($enrollments),
            'meta' => [
                'total' => $enrollments->count(),
            ]
        ]);
    }

    /**
     * Get Course Learning Detail
     * 
     * Endpoint untuk mendapatkan detail kursus dengan materials untuk pembelajaran.
     * 
     * @security BearerToken
     * @param Request $request
     * @param int $id Course ID
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        // Check if enrolled
        $enrollment = Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar di kursus ini'
            ], 403);
        }

        $course = Course::with([
            'dosen',
            'materials' => function ($q) {
                $q->orderBy('urutan', 'asc');
            }
        ])->find($id);

        // Get materials with progress
        $materials = $course->materials->map(function ($material) use ($user) {
            return (new MaterialResource($material))->forMahasiswa($user->id);
        });

        return response()->json([
            'success' => true,
            'message' => 'Course detail retrieved successfully',
            'data' => [
                'enrollment' => new MyCourseResource($enrollment),
                'course' => [
                    'id_course' => $course->id_course,
                    'kode_course' => $course->kode_course,
                    'nama_course' => $course->nama_course,
                    'deskripsi' => $course->deskripsi,
                    'dosen' => $course->dosen ? ['name' => $course->dosen->name] : null,
                ],
                'materials' => $materials,
                'total_materials' => $course->materials->count(),
                'completed_materials' => MaterialProgress::whereIn(
                    'id_material',
                    $course->materials->pluck('id_material')
                )
                    ->where('id_mahasiswa', $user->id)
                    ->where('is_completed', true)
                    ->count(),
            ]
        ]);
    }

    /**
     * Mark Material as Complete
     * 
     * Endpoint untuk menandai material sebagai selesai dipelajari.
     * Progress enrollment akan otomatis dihitung ulang.
     * 
     * @security BearerToken
     * @bodyParam watched_duration integer Optional. Duration watched in seconds.
     * @param Request $request
     * @param int $courseId Course ID
     * @param int $materialId Material ID
     * @return JsonResponse
     */
    public function completeMaterial(Request $request, int $courseId, int $materialId): JsonResponse
    {
        $user = $request->user();

        // Check if enrolled
        $enrollment = Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar di kursus ini'
            ], 403);
        }

        // Check if material belongs to course
        $material = CourseMaterial::where('id_course', $courseId)
            ->where('id_material', $materialId)
            ->first();

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material tidak ditemukan'
            ], 404);
        }

        // Update or create progress
        $progress = MaterialProgress::updateOrCreate(
            [
                'id_mahasiswa' => $user->id,
                'id_material' => $materialId,
            ],
            [
                'is_completed' => true,
                'watched_duration' => $request->input('watched_duration', 0),
                'completed_at' => Carbon::now(),
            ]
        );

        // Recalculate enrollment progress
        $enrollment->recalculateProgress($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil ditandai selesai',
            'data' => [
                'material_id' => $materialId,
                'is_completed' => true,
                'enrollment_progress' => $enrollment->progress,
                'enrollment_status' => $enrollment->status,
            ]
        ]);
    }

    /**
     * Get Weekly Target
     * 
     * Endpoint untuk mendapatkan target pembelajaran mingguan.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function weeklyTarget(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get this week's completed materials
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $completedThisWeek = MaterialProgress::where('id_mahasiswa', $user->id)
            ->where('is_completed', true)
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->count();

        // Target: 3 materials per week (configurable)
        $weeklyTarget = 3;

        return response()->json([
            'success' => true,
            'message' => 'Weekly target retrieved successfully',
            'data' => [
                'target' => $weeklyTarget,
                'completed' => $completedThisWeek,
                'remaining' => max(0, $weeklyTarget - $completedThisWeek),
                'percentage' => min(100, round(($completedThisWeek / $weeklyTarget) * 100)),
                'message' => $completedThisWeek >= $weeklyTarget
                    ? 'Target mingguan tercapai!'
                    : ($weeklyTarget - $completedThisWeek) . ' modul lagi untuk mencapai target minggu ini',
            ]
        ]);
    }

    /**
     * Get Course Recommendations
     * 
     * Endpoint untuk mendapatkan rekomendasi kursus berdasarkan yang belum diambil.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('id_mahasiswa', $user->id)
            ->pluck('id_course');

        // Get courses not enrolled, active, with good rating
        $recommendations = Course::with(['dosen'])
            ->whereNotIn('id_course', $enrolledCourseIds)
            ->aktif()
            ->orderBy('rating', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Recommendations retrieved successfully',
            'data' => CourseResource::collection($recommendations)
        ]);
    }
}
