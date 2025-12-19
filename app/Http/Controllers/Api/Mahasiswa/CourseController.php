<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\CourseRating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Mahasiswa Courses
 */
class CourseController extends Controller
{
    /**
     * Get All Courses
     * 
     * Endpoint untuk mendapatkan semua kursus yang aktif.
     * Mendukung filter pencarian dengan query parameter 'search'.
     * 
     * @security BearerToken
     * @queryParam search string Optional. Search keyword for course name or description.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $courses = Course::with(['dosen', 'jurusan'])
            ->aktif()
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'All courses retrieved successfully',
            'data' => CourseResource::collection($courses)
        ]);
    }

    /**
     * Get Webinar Courses
     * 
     * Endpoint untuk mendapatkan kursus dengan tipe webinar.
     * 
     * @security BearerToken
     * @queryParam search string Optional. Search keyword for course name or description.
     * @param Request $request
     * @return JsonResponse
     */
    public function webinar(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $courses = Course::with(['dosen', 'jurusan'])
            ->aktif()
            ->byTipe('webinar')
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Webinar courses retrieved successfully',
            'data' => CourseResource::collection($courses)
        ]);
    }

    /**
     * Get Tiket Courses
     * 
     * Endpoint untuk mendapatkan kursus dengan tipe tiket (event).
     * 
     * @security BearerToken
     * @queryParam search string Optional. Search keyword for course name or description.
     * @param Request $request
     * @return JsonResponse
     */
    public function tiket(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $courses = Course::with(['dosen', 'jurusan'])
            ->aktif()
            ->byTipe('tiket')
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tiket courses retrieved successfully',
            'data' => CourseResource::collection($courses)
        ]);
    }

    /**
     * Get Kursus Courses
     * 
     * Endpoint untuk mendapatkan kursus dengan tipe kursus (regular course).
     * 
     * @security BearerToken
     * @queryParam search string Optional. Search keyword for course name or description.
     * @param Request $request
     * @return JsonResponse
     */
    public function kursus(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $courses = Course::with(['dosen', 'jurusan'])
            ->aktif()
            ->byTipe('kursus')
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Kursus courses retrieved successfully',
            'data' => CourseResource::collection($courses)
        ]);
    }

    /**
     * Get Course Detail
     * 
     * Endpoint untuk mendapatkan detail kursus berdasarkan ID.
     * 
     * @security BearerToken
     * @param int $id Course ID
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $course = Course::with(['dosen', 'jurusan', 'materials', 'assignments'])
            ->find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Course detail retrieved successfully',
            'data' => new CourseResource($course)
        ]);
    }

    /**
     * Rate a Course
     * 
     * Endpoint untuk memberikan rating ke course.
     * Satu mahasiswa hanya bisa memberikan rating 1x per course.
     * Jika sudah pernah rating, akan mengembalikan error.
     * 
     * @security BearerToken
     * @bodyParam rating integer required Rating value (1-5).
     * @bodyParam ulasan string Optional. Review text.
     * @param Request $request
     * @param int $id Course ID
     * @return JsonResponse
     */
    public function rate(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        // Validate request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000',
        ]);

        // Find course
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        // Check if user already rated this course
        if ($course->hasRatedBy($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah memberikan rating untuk course ini'
            ], 422);
        }

        // Create rating
        CourseRating::create([
            'id_course' => $course->id_course,
            'id_mahasiswa' => $user->id,
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
        ]);

        // Recalculate course rating
        $course->recalculateRating();

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil diberikan',
            'data' => [
                'rating_anda' => $request->rating,
                'rating_course' => $course->rating,
                'jumlah_ulasan' => $course->jumlah_ulasan,
            ]
        ]);
    }
}

