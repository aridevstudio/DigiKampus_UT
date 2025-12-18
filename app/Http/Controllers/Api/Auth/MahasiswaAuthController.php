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

        // Generate OTP 4 digit
        $otp = rand(1000, 9999);

        // Save OTP to database (hashed)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        // Send Email (menggunakan konfigurasi dari .env)
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email. Silakan coba lagi nanti.',
                'error' => $e->getMessage() // Debug only
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox/spam.',
            'data' => [
                'email' => $email,
                'expires_in' => '5 minutes'
            ]
        ], 200);
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
        $email = $validated['email'];
        $otp = $validated['otp'];

        // Get OTP record
        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        // Check if record exists
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan reset password tidak ditemukan.'
            ], 404);
        }

        // Check expiry (5 minutes)
        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP telah kadaluarsa. Silakan request ulang.'
            ], 400);
        }

        // Verify OTP hash
        if (!Hash::check($otp, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah.'
            ], 400);
        }

        // Generate verification token for next step
        $verificationToken = \Illuminate\Support\Str::random(64);

        // Update record with verification token (hashed)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($verificationToken),
                'created_at' => now() // Reset expiry for this token
            ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP valid. Silakan lanjutkan ke reset password.',
            'data' => [
                'email' => $email,
                'token' => $verificationToken // Token ini dipakai untuk reset password
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
        $email = $validated['email'];
        $token = $validated['token'];
        $password = $validated['password'];

        // Get record
        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Request tidak valid.'], 400);
        }

        // Verify token
        if (!Hash::check($token, $record->token)) {
            return response()->json(['success' => false, 'message' => 'Token verifikasi tidak valid.'], 400);
        }

        // Update User Password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($password);
        $user->save();

        // Delete token (One-time use)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login dengan password baru.'
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
            $filename = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('profile_photos', $filename, 'public');

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
     * **⚠️ WARNING:** This endpoint does NOT verify current password for security.
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
