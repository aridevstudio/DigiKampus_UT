<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for security fixes: mass assignment, SQL injection, token expiry.
 */
class SecurityFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_rating_not_mass_assignable(): void
    {
        $dosen = User::factory()->dosen()->create();

        $course = Course::create([
            'kode_course' => 'TEST001',
            'nama_course' => 'Test Course',
            'deskripsi' => 'Test',
            'id_dosen' => $dosen->id,
            'status' => 'aktif',
            'tipe' => 'kursus',
            'harga' => 0,
            'rating' => 5.0, // This should be ignored
            'jumlah_ulasan' => 999, // This should be ignored
        ]);

        // rating and jumlah_ulasan should NOT be set via mass assignment
        $this->assertEquals(0, $course->fresh()->rating);
        $this->assertEquals(0, $course->fresh()->jumlah_ulasan);
    }

    public function test_sanctum_token_has_expiration(): void
    {
        $expiration = config('sanctum.expiration');

        $this->assertNotNull($expiration, 'Sanctum token expiration must not be null');
        $this->assertEquals(1440, $expiration, 'Sanctum token should expire in 24 hours');
    }

    public function test_dosen_login_revokes_old_tokens(): void
    {
        $dosen = User::factory()->dosen()->create([
            'password' => Hash::make('Password1'),
        ]);

        // First login
        $this->postJson('/api/auth/dosen/login', [
            'email' => $dosen->email,
            'password' => 'Password1',
        ]);

        $this->assertEquals(1, $dosen->tokens()->count());

        // Second login should revoke old token
        $this->postJson('/api/auth/dosen/login', [
            'email' => $dosen->email,
            'password' => 'Password1',
        ]);

        $this->assertEquals(1, $dosen->tokens()->count());
    }

    public function test_admin_login_revokes_old_tokens(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('Password1'),
        ]);

        $this->postJson('/api/auth/admin/login', [
            'email' => $admin->email,
            'password' => 'Password1',
        ]);

        $this->assertEquals(1, $admin->tokens()->count());

        $this->postJson('/api/auth/admin/login', [
            'email' => $admin->email,
            'password' => 'Password1',
        ]);

        $this->assertEquals(1, $admin->tokens()->count());
    }
}
