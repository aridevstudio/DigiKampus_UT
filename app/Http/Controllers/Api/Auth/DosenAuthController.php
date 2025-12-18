<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DosenLoginRequest;
use App\Http\Requests\Api\DosenRegisterRequest;
use App\Http\Resources\DosenResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class DosenAuthController extends Controller
{
    /**
     * Register Dosen (Regular)
     * 
     * @operationId dosenAuth.register
     * @bodyContent application/json {
     *   "name": "Dr. John Doe",
     *   "email": "john.doe@ut.ac.id",
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
     *   "email": "john.doe@ut.ac.id",
     *   "password": "password123"
     * }
     * @response 200
     */
    public function login(DosenLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])
            ->where('role', 'dosen')
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        if ($user->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif.'
            ], 403);
        }

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
            return response()->json([
                'success' => false,
                'message' => 'Gagal login dengan Google.',
                'error' => $e->getMessage()
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

        // Validate email belongs to a Dosen
        $user = User::where('email', $email)->where('role', 'dosen')->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar sebagai dosen.'
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
        $user = User::where('email', $email)->where('role', 'dosen')->first();

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
