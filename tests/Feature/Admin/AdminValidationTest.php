<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    private function actingAsAdmin(): static
    {
        Auth::guard('admin')->login($this->admin);
        return $this->withSession(['admin_login' => true]);
    }

    // =====================
    // Photo Upload Tests
    // =====================

    public function test_store_dosen_rejects_photo_over_2mb(): void
    {
        $photo = UploadedFile::fake()->create('huge.jpg', 3000, 'image/jpeg');

        $response = $this->actingAsAdmin()
            ->post(route('admin.dosen.store'), [
                'nama' => 'Dr. Test',
                'nomor_induk' => '12345678',
                'email' => 'test@example.com',
                'no_hp' => '081234567890',
                'id_jurusan' => 1,
                'status' => 'aktif',
                'foto' => $photo,
            ]);

        // Should be redirected back with validation errors
        $response->assertSessionHasErrors('foto');
    }

    public function test_store_dosen_accepts_webp_photo(): void
    {
        $photo = UploadedFile::fake()->create('avatar.webp', 500, 'image/webp');

        $response = $this->actingAsAdmin()
            ->post(route('admin.dosen.store'), [
                'nama' => 'Dr. WebP',
                'nomor_induk' => '99887766',
                'email' => 'webp@example.com',
                'no_hp' => '081234567890',
                'id_jurusan' => 1,
                'status' => 'aktif',
                'foto' => $photo,
            ]);

        // Should not have foto validation error (may redirect for other reasons)
        $response->assertSessionDoesntHaveErrors('foto');
    }

    public function test_store_mahasiswa_rejects_photo_over_2mb(): void
    {
        $photo = UploadedFile::fake()->create('huge.png', 3000, 'image/png');

        $response = $this->actingAsAdmin()
            ->post(route('admin.mahasiswa.store'), [
                'nama' => 'Mahasiswa Test',
                'nomor_induk' => '2024001001',
                'email' => 'mhs@example.com',
                'no_hp' => '081234567890',
                'id_jurusan' => 1,
                'status' => 'aktif',
                'foto' => $photo,
            ]);

        $response->assertSessionHasErrors('foto');
    }

    // =====================
    // Uniqueness Tests
    // =====================

    public function test_store_mahasiswa_rejects_duplicate_nomor_induk(): void
    {
        // Create existing user with Nomor Induk
        $existingUser = User::factory()->mahasiswa()->create();
        $existingUser->profile()->create(['nomor_induk' => '2024001001']);

        $response = $this->actingAsAdmin()
            ->post(route('admin.mahasiswa.store'), [
                'nama' => 'New Student',
                'nomor_induk' => '2024001001', // duplicate
                'email' => 'new@example.com',
                'no_hp' => '081234567890',
                'id_jurusan' => 1,
                'status' => 'aktif',
            ]);

        $response->assertSessionHasErrors('nomor_induk');
    }

    public function test_store_dosen_rejects_duplicate_email(): void
    {
        User::factory()->dosen()->create(['email' => 'existing@example.com']);

        $response = $this->actingAsAdmin()
            ->post(route('admin.dosen.store'), [
                'nama' => 'Dr. New',
                'nomor_induk' => '99999999',
                'email' => 'existing@example.com', // duplicate
                'no_hp' => '081234567890',
                'id_jurusan' => 1,
                'status' => 'aktif',
            ]);

        $response->assertSessionHasErrors('email');
    }
}
