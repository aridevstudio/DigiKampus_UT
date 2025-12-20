<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Favorite;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Mahasiswa Favorites
 */
class FavoriteController extends Controller
{
    /**
     * Get Favorites
     * 
     * Endpoint untuk mendapatkan daftar course yang disimpan.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $favorites = Favorite::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Favorites retrieved successfully',
            'data' => $favorites->map(function ($item) {
                return [
                    'id_favorite' => $item->id_favorite,
                    'course' => new CourseResource($item->course),
                    'saved_at' => $item->created_at->toDateTimeString(),
                ];
            })
        ]);
    }

    /**
     * Add to Favorites
     * 
     * Endpoint untuk menyimpan course ke favorites.
     * 
     * @security BearerToken
     * @bodyParam id_course integer required Course ID to save.
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'id_course' => 'required|integer|exists:courses,id_course',
        ]);

        $courseId = $request->id_course;

        // Check if already favorited
        $exists = Favorite::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Course sudah ada di favorites'
            ], 422);
        }

        Favorite::create([
            'id_mahasiswa' => $user->id,
            'id_course' => $courseId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course berhasil disimpan ke favorites'
        ]);
    }

    /**
     * Remove from Favorites
     * 
     * Endpoint untuk menghapus course dari favorites.
     * 
     * @security BearerToken
     * @param Request $request
     * @param int $id Favorite item ID
     * @return JsonResponse
     */
    public function remove(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $favorite = Favorite::where('id_mahasiswa', $user->id)
            ->where('id_favorite', $id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari favorites'
        ]);
    }

    /**
     * Toggle Favorite
     * 
     * Endpoint untuk toggle favorite/unfavorite course.
     * 
     * @security BearerToken
     * @param Request $request
     * @param int $courseId Course ID to toggle
     * @return JsonResponse
     */
    public function toggle(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user();

        // Check if course exists
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course tidak ditemukan'
            ], 404);
        }

        $favorite = Favorite::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => 'Course dihapus dari favorites',
                'is_favorited' => false
            ]);
        }

        Favorite::create([
            'id_mahasiswa' => $user->id,
            'id_course' => $courseId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course disimpan ke favorites',
            'is_favorited' => true
        ]);
    }
}
