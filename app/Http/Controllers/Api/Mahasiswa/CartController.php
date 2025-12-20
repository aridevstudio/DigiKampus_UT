<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * @tags Mahasiswa Cart
 */
class CartController extends Controller
{
    /**
     * Get Cart Items
     * 
     * Endpoint untuk mendapatkan daftar course di keranjang.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $cartItems = Cart::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->course->harga ?? 0;
        });

        return response()->json([
            'success' => true,
            'message' => 'Cart items retrieved successfully',
            'data' => [
                'items' => $cartItems->map(function ($item) {
                    return [
                        'id_cart' => $item->id_cart,
                        'course' => new CourseResource($item->course),
                    ];
                }),
                'total_items' => $cartItems->count(),
                'total_harga' => $total,
                'total_harga_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
            ]
        ]);
    }

    /**
     * Add to Cart
     * 
     * Endpoint untuk menambahkan course ke keranjang.
     * 
     * @security BearerToken
     * @bodyParam id_course integer required Course ID to add.
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

        // Check if already enrolled
        $enrolled = Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();

        if ($enrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di kursus ini'
            ], 422);
        }

        // Check if already in cart
        $exists = Cart::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Course sudah ada di keranjang'
            ], 422);
        }

        Cart::create([
            'id_mahasiswa' => $user->id,
            'id_course' => $courseId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course berhasil ditambahkan ke keranjang'
        ]);
    }

    /**
     * Remove from Cart
     * 
     * Endpoint untuk menghapus course dari keranjang.
     * 
     * @security BearerToken
     * @param Request $request
     * @param int $id Cart item ID
     * @return JsonResponse
     */
    public function remove(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $cartItem = Cart::where('id_mahasiswa', $user->id)
            ->where('id_cart', $id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang'
        ]);
    }

    /**
     * Clear Cart
     * 
     * Endpoint untuk mengosongkan keranjang.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();

        Cart::where('id_mahasiswa', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }
}
