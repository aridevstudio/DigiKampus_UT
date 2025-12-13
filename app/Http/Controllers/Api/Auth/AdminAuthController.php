<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdminLoginRequest;
use App\Http\Resources\AdminResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @tags Admin Authentication
 */
class AdminAuthController extends Controller
{
    /**
     * Login Admin (Email & Password)
     * 
     * Endpoint untuk login admin menggunakan email dan password.
     * Akan mengembalikan token authentication jika berhasil.
     *
     * @operationId adminAuth.login
     * @param AdminLoginRequest $request
     * @return JsonResponse
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Cari user berdasarkan email dengan role admin
        $user = User::where('email', $validated['email'])
            ->where('role', 'admin')
            ->first();

        // Validasi user exists
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar sebagai admin.'
            ], 404);
        }

        // Validasi password
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah. Silakan coba lagi.'
            ], 401);
        }

        // Validasi status aktif
        if ($user->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang tidak aktif. Hubungi super admin untuk informasi lebih lanjut.'
            ], 403);
        }

        // Generate API token
        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new AdminResource($user),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }

    /**
     * Forgot Password - Send OTP via Email (Admin)
     * 
     * Endpoint untuk request reset password admin. 
     * Mengirimkan kode OTP 4 digit ke email admin.
     *
     * @operationId adminAuth.forgotPassword
     * @param \App\Http\Requests\Api\ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(\App\Http\Requests\Api\ForgotPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $email = $validated['email'];

        // Validate email belongs to an Admin
        $user = User::where('email', $email)->where('role', 'admin')->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar sebagai admin.'
            ], 404);
        }

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
     * Verify OTP (Admin)
     * 
     * Endpoint untuk memverifikasi kode OTP yang diterima admin.
     * Jika valid, akan mengembalikan token verifikasi untuk reset password.
     *
     * @operationId adminAuth.verifyOtp
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
     * Reset Password (Admin)
     * 
     * Endpoint untuk mengubah password baru admin menggunakan token verifikasi.
     *
     * @operationId adminAuth.resetPassword
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
        $user = User::where('email', $email)->where('role', 'admin')->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

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
     * Get Admin Profile
     * 
     * Endpoint untuk mendapatkan data profile admin yang sedang login.
     * Memerlukan Bearer token di header Authorization.
     *
     * @security BearerToken
     * @operationId adminAuth.profile
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Data profile berhasil diambil.',
            'data' => new AdminResource($user)
        ], 200);
    }

    /**
     * Logout Admin
     * 
     * Endpoint untuk logout dan menghapus token authentication.
     * Memerlukan Bearer token di header Authorization.
     *
     * @security BearerToken
     * @operationId adminAuth.logout
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
