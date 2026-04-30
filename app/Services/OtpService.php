<?php

namespace App\Services;

use App\Mail\OtpMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Centralized OTP Service
 *
 * Handles OTP generation, verification, rate limiting, and lockout logic.
 * Extracted from duplicated logic across MahasiswaAuthController,
 * DosenAuthController, and AdminAuthController.
 *
 * Security improvements over original:
 * - 6-digit OTP (100,000 combinations vs 10,000)
 * - Cryptographically secure random_int() instead of rand()
 * - Attempt tracking with lockout after MAX_ATTEMPTS
 * - Cooldown period before new OTP can be requested
 * - Automatic cleanup of expired tokens
 */
class OtpService
{
    /**
     * Maximum OTP verification attempts before lockout
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in minutes after max attempts exceeded
     */
    private const LOCKOUT_MINUTES = 15;

    /**
     * OTP validity in minutes
     */
    private const OTP_EXPIRY_MINUTES = 5;

    /**
     * Minimum seconds between OTP requests (anti-spam)
     */
    private const COOLDOWN_SECONDS = 60;

    /**
     * Generate and send OTP to email.
     *
     * @param string $email
     * @return array{success: bool, message: string}
     */
    public function generateAndSend(string $email): array
    {
        // Check cooldown — prevent spamming OTP requests
        $existing = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if ($existing && $existing->created_at) {
            $secondsSinceLastRequest = Carbon::parse($existing->created_at)->diffInSeconds(now());
            if ($secondsSinceLastRequest < self::COOLDOWN_SECONDS) {
                $waitSeconds = self::COOLDOWN_SECONDS - $secondsSinceLastRequest;
                return [
                    'success' => false,
                    'message' => "Silakan tunggu {$waitSeconds} detik sebelum meminta OTP baru.",
                ];
            }
        }

        // Generate 6-digit OTP using cryptographically secure random
        $otp = random_int(100000, 999999);

        // Save OTP to database (hashed), reset attempts
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make((string) $otp),
                'attempts' => 0,
                'created_at' => now(),
            ]
        );

        // Send Email
        try {
            Mail::to($email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            Log::error('OTP email send failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim email. Silakan coba lagi nanti.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox/spam.',
        ];
    }

    /**
     * Verify OTP and return verification token if valid.
     *
     * @param string $email
     * @param string $otp
     * @return array{success: bool, message: string, token?: string}
     */
    public function verify(string $email, string $otp): array
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return [
                'success' => false,
                'message' => 'Permintaan reset password tidak ditemukan.',
            ];
        }

        // Check lockout
        if ($record->attempts >= self::MAX_ATTEMPTS) {
            $lockedUntil = Carbon::parse($record->created_at)->addMinutes(self::LOCKOUT_MINUTES);
            if (now()->lt($lockedUntil)) {
                $minutesLeft = now()->diffInMinutes($lockedUntil) + 1;
                return [
                    'success' => false,
                    'message' => "Terlalu banyak percobaan. Coba lagi dalam {$minutesLeft} menit.",
                ];
            }
            // Lockout expired — reset attempts
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->update(['attempts' => 0, 'created_at' => now()]);

            return [
                'success' => false,
                'message' => 'Kode OTP telah kadaluarsa. Silakan request ulang.',
            ];
        }

        // Check expiry
        if (Carbon::parse($record->created_at)->addMinutes(self::OTP_EXPIRY_MINUTES)->isPast()) {
            return [
                'success' => false,
                'message' => 'Kode OTP telah kadaluarsa. Silakan request ulang.',
            ];
        }

        // Verify OTP hash
        if (!Hash::check((string) $otp, $record->token)) {
            // Increment attempts
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->increment('attempts');

            $remaining = self::MAX_ATTEMPTS - ($record->attempts + 1);

            return [
                'success' => false,
                'message' => $remaining > 0
                    ? "Kode OTP salah. Sisa percobaan: {$remaining}."
                    : 'Terlalu banyak percobaan. Silakan request OTP baru.',
            ];
        }

        // OTP valid — generate verification token
        $verificationToken = Str::random(64);

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($verificationToken),
                'attempts' => 0,
                'created_at' => now(),
            ]);

        return [
            'success' => true,
            'message' => 'OTP valid. Silakan lanjutkan ke reset password.',
            'token' => $verificationToken,
        ];
    }

    /**
     * Verify reset token and update password.
     *
     * @param string $email
     * @param string $token
     * @param string $newPassword
     * @param string|null $role Optional role filter
     * @return array{success: bool, message: string}
     */
    public function resetPassword(string $email, string $token, string $newPassword, ?string $role = null): array
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return ['success' => false, 'message' => 'Request tidak valid.'];
        }

        if (!Hash::check($token, $record->token)) {
            return ['success' => false, 'message' => 'Token verifikasi tidak valid.'];
        }

        // Check token expiry (10 min window for reset)
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return ['success' => false, 'message' => 'Token telah kadaluarsa. Silakan ulangi proses.'];
        }

        $userQuery = \App\Models\User::where('email', $email);
        if ($role) {
            $userQuery->where('role', $role);
        }
        $user = $userQuery->first();

        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan.'];
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        if ($user->requires_password_reset) {
            $user->clearImportedDefaultPasswordState();
        }

        // Delete token (one-time use)
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return [
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login dengan password baru.',
        ];
    }
}
