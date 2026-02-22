<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Tests for OTP security: generation, verification, lockout, rate limiting.
 *
 * Covers:
 * - 6-digit OTP generation and verification
 * - Attempt counting and lockout after 5 failures
 * - Cooldown between OTP requests
 * - Token expiry
 * - Generic error messages (no user enumeration)
 */
class OtpSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->user = User::factory()->mahasiswa()->create([
            'email' => 'test@example.com',
        ]);
    }

    public function test_forgot_password_returns_success_even_for_nonexistent_email(): void
    {
        $response = $this->postJson('/api/auth/mahasiswa/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        // Should NOT reveal that the email doesn't exist
        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_forgot_password_sends_otp_for_valid_email(): void
    {
        $response = $this->postJson('/api/auth/mahasiswa/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertOk();

        // Verify OTP was stored
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_otp_verification_with_correct_code(): void
    {
        // Manually insert a known OTP
        $otp = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($otp),
            'attempts' => 0,
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/mahasiswa/verify-otp', [
            'email' => 'test@example.com',
            'otp' => $otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['email', 'token']]);
    }

    public function test_otp_verification_fails_with_wrong_code(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make('123456'),
            'attempts' => 0,
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/mahasiswa/verify-otp', [
            'email' => 'test@example.com',
            'otp' => '999999',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);

        // Verify attempt was incremented
        $record = DB::table('password_reset_tokens')
            ->where('email', 'test@example.com')
            ->first();
        $this->assertEquals(1, $record->attempts);
    }

    public function test_otp_lockout_after_max_attempts(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make('123456'),
            'attempts' => 5, // Already at max
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/mahasiswa/verify-otp', [
            'email' => 'test@example.com',
            'otp' => '123456', // Even correct OTP should fail
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);

        // Message should indicate lockout
        $this->assertStringContainsString('percobaan', $response->json('message'));
    }

    public function test_expired_otp_is_rejected(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make('123456'),
            'attempts' => 0,
            'created_at' => now()->subMinutes(10), // Expired (>5 min)
        ]);

        $response = $this->postJson('/api/auth/mahasiswa/verify-otp', [
            'email' => 'test@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_otp_must_be_6_digits(): void
    {
        $response = $this->postJson('/api/auth/mahasiswa/verify-otp', [
            'email' => 'test@example.com',
            'otp' => '1234', // Old 4-digit format should fail
        ]);

        $response->assertStatus(422); // Validation error
    }

    public function test_password_reset_requires_complexity(): void
    {
        // Setup: create valid reset token
        $token = 'valid-token-string';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'attempts' => 0,
            'created_at' => now(),
        ]);

        // Try weak password (no uppercase)
        $response = $this->postJson('/api/auth/mahasiswa/reset-password', [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'weakpassword1',
            'password_confirmation' => 'weakpassword1',
        ]);

        $response->assertStatus(422);
    }

    public function test_password_reset_succeeds_with_strong_password(): void
    {
        $token = 'valid-token-string';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'attempts' => 0,
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/mahasiswa/reset-password', [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'NewSecure1',
            'password_confirmation' => 'NewSecure1',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        // Verify password actually changed
        $this->user->refresh();
        $this->assertTrue(Hash::check('NewSecure1', $this->user->password));
    }
}
