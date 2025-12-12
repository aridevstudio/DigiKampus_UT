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
