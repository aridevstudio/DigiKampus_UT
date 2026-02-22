<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdminLoginRequest;
use App\Http\Resources\AdminResource;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @tags Admin Authentication
 */
class AdminAuthController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}
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

        // Revoke old tokens to prevent accumulation
        $user->tokens()->delete();
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

        // P0 FIX: Always return success to prevent user enumeration
        $user = User::where('email', $email)->where('role', 'admin')->first();
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
        $result = $this->otpService->resetPassword(
            $validated['email'],
            $validated['token'],
            $validated['password'],
            'admin'
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 400);
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
