<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MahasiswaLoginRequest;
use App\Http\Resources\MahasiswaResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @tags Mahasiswa Authentication
 */
class MahasiswaAuthController extends Controller
{
    /**
     * Login mahasiswa dengan NIM dan password
     * 
     * Endpoint untuk login mahasiswa menggunakan NIM dan password.
     * Akan mengembalikan token authentication jika berhasil.
     *
     * @param MahasiswaLoginRequest $request
     * @return JsonResponse
     */
    public function login(MahasiswaLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Cari user berdasarkan NIM melalui relasi profile
        $user = User::whereHas('profile', function ($q) use ($validated) {
            $q->where('nim', $validated['nim']);
        })->with('profile')->first();

        // Validasi user exists
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'NIM tidak ditemukan. Pastikan NIM Anda sudah terdaftar.'
            ], 404);
        }

        // Validasi password
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah. Silakan coba lagi.'
            ], 401);
        }

        // Validasi role mahasiswa
        if ($user->role !== 'mahasiswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya mahasiswa yang dapat login melalui endpoint ini.'
            ], 403);
        }

        // Validasi status aktif
        if ($user->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang tidak aktif. Hubungi admin untuk informasi lebih lanjut.'
            ], 403);
        }

        // Generate API token
        $token = $user->createToken('mahasiswa-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new MahasiswaResource($user),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }

    /**
     * Get profile mahasiswa yang sedang login
     * 
     * Endpoint untuk mendapatkan data profile mahasiswa yang sedang login.
     * Memerlukan Bearer token di header Authorization.
     *
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Data profile berhasil diambil.',
            'data' => new MahasiswaResource($user)
        ], 200);
    }

    /**
     * Logout mahasiswa
     * 
     * Endpoint untuk logout dan menghapus token authentication.
     * Memerlukan Bearer token di header Authorization.
     *
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.'
        ], 200);
    }
}
