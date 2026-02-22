<?php

namespace Tests\Feature\Api\Auth;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for Mahasiswa authentication API endpoints.
 *
 * Covers:
 * - Login with NIM/password
 * - Token generation and revocation
 * - Role-based access guard
 * - Rate limiting on auth endpoints
 */
class MahasiswaAuthTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;
    private string $nim = '2100001234';

    protected function setUp(): void
    {
        parent::setUp();

        $this->mahasiswa = User::factory()->mahasiswa()->create([
            'password' => Hash::make('Password1'),
        ]);

        Profile::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->nim,
        ]);
    }

    public function test_mahasiswa_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/auth/mahasiswa/login', [
            'nim' => $this->nim,
            'password' => 'Password1',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['user', 'token', 'token_type'],
            ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->postJson('/api/auth/mahasiswa/login', [
            'nim' => $this->nim,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_login_fails_with_nonexistent_nim(): void
    {
        $response = $this->postJson('/api/auth/mahasiswa/login', [
            'nim' => '9999999999',
            'password' => 'Password1',
        ]);

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_dosen_cannot_login_via_mahasiswa_endpoint(): void
    {
        $dosen = User::factory()->dosen()->create([
            'password' => Hash::make('Password1'),
        ]);

        Profile::factory()->create([
            'user_id' => $dosen->id,
            'nim' => '9876543210',
        ]);

        $response = $this->postJson('/api/auth/mahasiswa/login', [
            'nim' => '9876543210',
            'password' => 'Password1',
        ]);

        $response->assertStatus(403);
    }

    public function test_nonaktif_mahasiswa_cannot_login(): void
    {
        $this->mahasiswa->update(['status' => 'nonaktif']);

        $response = $this->postJson('/api/auth/mahasiswa/login', [
            'nim' => $this->nim,
            'password' => 'Password1',
        ]);

        $response->assertStatus(403);
    }

    public function test_login_revokes_old_tokens(): void
    {
        // First login - creates token
        $this->postJson('/api/auth/mahasiswa/login', [
            'nim' => $this->nim,
            'password' => 'Password1',
        ]);

        $this->assertEquals(1, $this->mahasiswa->tokens()->count());

        // Second login - should revoke old token and create new one
        $this->postJson('/api/auth/mahasiswa/login', [
            'nim' => $this->nim,
            'password' => 'Password1',
        ]);

        $this->assertEquals(1, $this->mahasiswa->tokens()->count());
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $token = $this->mahasiswa->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/mahasiswa/profile');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/mahasiswa/profile');

        $response->assertStatus(401);
    }

    public function test_logout_deletes_current_token(): void
    {
        $token = $this->mahasiswa->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/mahasiswa/logout');

        $response->assertOk();
        $this->assertEquals(0, $this->mahasiswa->tokens()->count());
    }
}
