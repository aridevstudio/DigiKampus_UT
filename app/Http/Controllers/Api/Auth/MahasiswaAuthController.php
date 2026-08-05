<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MahasiswaLoginRequest;
use App\Http\Resources\MahasiswaResource;
use App\Models\User;
use App\Services\DeviceSessionLimitService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @tags Mahasiswa Authentication
 */
class MahasiswaAuthController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}
    /**
     * Login mahasiswa dengan Nomor Induk dan password
     *
     * Endpoint untuk login mahasiswa menggunakan Nomor Induk dan password.
     * Akan mengembalikan token authentication jika berhasil.
     *
     * @param MahasiswaLoginRequest $request
     * @return JsonResponse
     */
    public function login(MahasiswaLoginRequest $request, DeviceSessionLimitService $deviceSessionLimitService): JsonResponse
    {
        $validated = $request->validated();

        // Cari user berdasarkan Nomor Induk melalui relasi profile
        $user = User::whereHas('profile', function ($q) use ($validated) {
            $q->where('nomor_induk', $validated['nomor_induk']);
        })->with('profile')->first();

        // Validasi user exists
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor Induk tidak ditemukan. Pastikan Nomor Induk Anda sudah terdaftar.'
            ], 404);
        }

        // Validasi password
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah. Silakan coba lagi.'
            ], 401);
        }

        $passwordResetNotice = null;
        if (
            $user->usesImportedDefaultPassword($validated['password'])
            || ($user->status === 'pending' && $user->requires_password_reset)
        ) {
            $passwordResetNotice = 'Akun impor ini masih memakai password default. Anda tetap bisa masuk, tetapi sangat disarankan segera mengganti password melalui forgot password.';
        }

        // Validasi role mahasiswa
        if ($user->role !== 'mahasiswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya mahasiswa yang dapat login melalui endpoint ini.'
            ], 403);
        }

        // Validasi status aktif
        if ($user->status !== 'aktif' && !$user->requires_password_reset) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang tidak aktif. Hubungi admin untuk informasi lebih lanjut.'
            ], 403);
        }

        if (!$deviceSessionLimitService->canIssueFreshApiToken($user)) {
            return response()->json([
                'success' => false,
                'message' => $deviceSessionLimitService->limitMessage(),
            ], 403);
        }

        // Generate API token (revoke old tokens first to prevent accumulation)
        $user->tokens()->delete();
        $token = $user->createToken('mahasiswa-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new MahasiswaResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'requires_password_reset' => (bool) $user->requires_password_reset,
                'password_notice' => $passwordResetNotice,
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
        $user = $request->user()->load(['profile.jurusan']);

        return response()->json([
            'success' => true,
            'message' => 'Data profile berhasil diambil.',
            'data' => new MahasiswaResource($user)
        ], 200);
    }

    /**
     * Forgot Password - Send OTP via Email
     *
     * Endpoint untuk request reset password.
     * Mengirimkan kode OTP 4 digit ke email mahasiswa.
     *
     * @param \App\Http\Requests\Api\ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(\App\Http\Requests\Api\ForgotPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $email = $validated['email'];

        // P0 FIX: Always return success to prevent user enumeration
        $user = User::where('email', $email)->where('role', 'mahasiswa')->first();
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'Jika email terdaftar, kode OTP telah dikirim. Silakan cek inbox/spam.',
                'data' => ['email' => $email, 'expires_in' => '5 minutes']
            ], 200);
        }

        $result = $this->otpService->generateAndSend($email);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success']
                ? 'Jika email terdaftar, kode OTP telah dikirim. Silakan cek inbox/spam.'
                : $result['message'],
            'data' => ['email' => $email, 'expires_in' => '5 minutes']
        ], $result['success'] ? 200 : 429);
    }

    /**
     * Verify OTP
     *
     * Endpoint untuk memverifikasi kode OTP yang diterima user.
     * Jika valid, akan mengembalikan token verifikasi untuk reset password.
     *
     * @param \App\Http\Requests\Api\VerifyOtpRequest $request
     * @return JsonResponse
     */
    public function verifyOtp(\App\Http\Requests\Api\VerifyOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->otpService->verify($validated['email'], $validated['otp']);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'email' => $validated['email'],
                'token' => $result['token'],
            ]
        ], 200);
    }

    /**
     * Reset Password
     *
     * Endpoint untuk mengubah password baru menggunakan token verifikasi.
     *
     * @param \App\Http\Requests\Api\ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(\App\Http\Requests\Api\ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->otpService->resetPassword(
            $validated['email'],
            $validated['token'],
            $validated['password'],
            'mahasiswa'
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 400);
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

    /**
     * Update profile mahasiswa
     *
     * Endpoint untuk mengubah data profil mahasiswa yang sedang login.
     * Field yang bisa diupdate: name, email, no_hp, alamat, tanggal_lahir, tempat_lahir, jenis_kelamin, bio, photo.
     * Photo upload opsional (multipart/form-data).
     *
     * @security BearerToken
     * @param \App\Http\Requests\Api\UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(\App\Http\Requests\Api\UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update user data (name, email)
        $userData = [];
        if (isset($validated['name'])) {
            $userData['name'] = $validated['name'];
        }
        if (isset($validated['email'])) {
            $userData['email'] = $validated['email'];
        }

        if (!empty($userData)) {
            $user->update($userData);
        }

        // Update profile data
        $profileData = [];
        $profileFields = ['no_hp', 'alamat', 'tanggal_lahir', 'tempat_lahir', 'jenis_kelamin', 'bio'];

        foreach ($profileFields as $field) {
            if (isset($validated[$field])) {
                $profileData[$field] = $validated[$field];
            }
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->profile->foto_profile) {
                $oldPhotoPath = storage_path('app/public/' . $user->profile->foto_profile);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            // Store new photo
            $photo = $request->file('photo');
            $path = $photo->store('profile_photos', 'public');

            $profileData['foto_profile'] = $path;
        }

        if (!empty($profileData)) {
            $user->profile()->update($profileData);
        }

        // Reload relationships
        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => new MahasiswaResource($user)
        ], 200);
    }

    /**
     * Ubah Password Mahasiswa
     *
     * Endpoint untuk mengubah password mahasiswa yang sedang login.
     * Hanya memerlukan password baru tanpa verifikasi password lama.
     *
     * **âš ï¸ WARNING:** This endpoint does NOT verify current password for security.
     * Implemented per client requirement. NOT RECOMMENDED for production use.
     * Consider adding current_password verification for better security.
     *
     * @operationId mahasiswaAuth.changePassword
     * @security BearerToken
     *
     * @bodyContent application/json {
     *   "new_password": "newpassword123"
     * }
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Password berhasil diubah."
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validasi gagal. Periksa kembali data yang Anda masukkan.",
     *   "errors": {
     *     "new_password": ["Password baru wajib diisi."]
     *   }
     * }
     *
     * @param \App\Http\Requests\Api\ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(\App\Http\Requests\Api\ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update password directly (NO current password verification)
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ], 200);
    }
}
