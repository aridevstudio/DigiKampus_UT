<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DosenLoginRequest;
use App\Http\Requests\Api\DosenRegisterRequest;
use App\Http\Resources\DosenResource;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class DosenAuthController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}
    /**
     * Register Dosen (Regular)
     * 
     * @operationId dosenAuth.register
     * @bodyContent application/json {
     *   "name": "Dr. John Doe",
     *   "email": "john.doe@ut.ac.id",
     *   "nomor_induk": "1988123401",
     *   "password": "password123"
     * }
     * @response 201
     */
    public function register(DosenRegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'dosen',
            'provider' => 'email',
            'status' => 'aktif',
        ]);

        $user->profile()->create([
            'nomor_induk' => $validated['nomor_induk'],
        ]);

        $token = $user->createToken('dosen-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data' => [
                'user' => new DosenResource($user),
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Login Dosen (Regular)
     * 
     * @operationId dosenAuth.login
     * @bodyContent application/json {
     *   "nomor_induk": "1988123401",
     *   "password": "password123"
     * }
     * @response 200
     */
    public function login(DosenLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('role', 'dosen')
            ->whereHas('profile', function ($query) use ($validated) {
                $query->where('nomor_induk', $validated['nomor_induk']);
            })
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor Induk atau password salah.'
            ], 401);
        }

        if ($user->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif.'
            ], 403);
        }

        // Revoke old tokens to prevent accumulation
        $user->tokens()->delete();
        $token = $user->createToken('dosen-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new DosenResource($user),
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Redirect to Google OAuth
     * 
     * @operationId dosenAuth.googleRedirect
     * @response 302
     */
    public function redirectToGoogle(): JsonResponse
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'success' => true,
            'redirect_url' => $url
        ]);
    }

    /**
     * Handle Google OAuth Callback
     * 
     * @operationId dosenAuth.googleCallback
     * @response 200
     */
    public function handleGoogleCallback(): JsonResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find or create user
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->id,
                    'provider' => 'google',
                    'name' => $googleUser->name ?? $user->name,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'provider' => 'google',
                    'role' => 'dosen',
                    'status' => 'aktif',
                    'password' => null, // OAuth users don't have password
                ]);
            }

            $token = $user->createToken('dosen-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login dengan Google berhasil.',
                'data' => [
                    'user' => new DosenResource($user),
                    'token' => $token
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Google OAuth failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal login dengan Google.'
            ], 500);
        }
    }

    /**
     * Forgot Password - Send OTP via Email (Dosen)
     * 
     * Endpoint untuk request reset password dosen. 
     * Mengirimkan kode OTP 4 digit ke email dosen.
     *
     * @operationId dosenAuth.forgotPassword
     * @param \App\Http\Requests\Api\ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(\App\Http\Requests\Api\ForgotPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $email = $validated['email'];

        // P0 FIX: Always return success to prevent user enumeration
        $user = User::where('email', $email)->where('role', 'dosen')->first();
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
     * Verify OTP (Dosen)
     * 
     * Endpoint untuk memverifikasi kode OTP yang diterima dosen.
     * Jika valid, akan mengembalikan token verifikasi untuk reset password.
     *
     * @operationId dosenAuth.verifyOtp
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
     * Reset Password (Dosen)
     * 
     * Endpoint untuk mengubah password baru dosen menggunakan token verifikasi.
     *
     * @operationId dosenAuth.resetPassword
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
            'dosen'
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Get Dosen Profile
     * 
     * @security BearerToken
     * @operationId dosenAuth.profile
     * @response 200
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Data profile berhasil diambil.',
            'data' => new DosenResource($user)
        ], 200);
    }

    /**
     * Logout Dosen
     * 
     * @security BearerToken
     * @operationId dosenAuth.logout
     * @response 200
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.'
        ], 200);
    }
}
