<?php

namespace Tests\Feature\Admin;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    private function createJurusan(): Jurusan
    {
        return Jurusan::create([
            'kode_jurusan' => 'PGSD',
            'nama_jurusan' => 'PGSD',
            'fakultas' => 'FKIP',
            'jenjang' => 'S1',
        ]);
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

    public function test_store_mahasiswa_uses_nomor_induk_as_default_password(): void
    {
        $jurusan = $this->createJurusan();

        $this->actingAsAdmin()
            ->post(route('admin.mahasiswa.store'), [
                'name' => 'Mahasiswa Default Password',
                'nomor_induk' => '2026001001',
                'email' => 'mhs-default@example.com',
                'id_jurusan' => $jurusan->id_jurusan,
                'status' => 'aktif',
            ])
            ->assertRedirect(route('admin.mahasiswa'));

        $user = User::where('email', 'mhs-default@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('2026001001', $user->password));
        $this->assertTrue((bool) $user->requires_password_reset);
    }

    public function test_store_dosen_uses_nomor_induk_as_default_password(): void
    {
        $jurusan = $this->createJurusan();

        $this->actingAsAdmin()
            ->post(route('admin.dosen.store'), [
                'name' => 'Dosen Default Password',
                'nomor_induk' => '1988001001',
                'email' => 'dosen-default@example.com',
                'id_jurusan' => [$jurusan->id_jurusan],
            ])
            ->assertRedirect(route('admin.dosen'));

        $user = User::where('email', 'dosen-default@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('1988001001', $user->password));
        $this->assertTrue((bool) $user->requires_password_reset);
    }

    public function test_update_mahasiswa_can_change_password(): void
    {
        $jurusan = $this->createJurusan();
        $mahasiswa = User::factory()->mahasiswa()->create([
            'email' => 'mhs-edit-password@example.com',
            'password' => Hash::make('old-password'),
            'requires_password_reset' => true,
        ]);
        $mahasiswa->profile()->create([
            'nomor_induk' => '2026002001',
            'id_jurusan' => $jurusan->id_jurusan,
        ]);

        $this->actingAsAdmin()
            ->put(route('admin.mahasiswa.update', $mahasiswa->id), [
                '_modal' => 'edit',
                '_id' => $mahasiswa->id,
                'name' => 'Mahasiswa Edit Password',
                'nomor_induk' => '2026002001',
                'email' => 'mhs-edit-password@example.com',
                'id_jurusan' => $jurusan->id_jurusan,
                'status' => 'aktif',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('admin.mahasiswa'));

        $mahasiswa->refresh();

        $this->assertTrue(Hash::check('new-password-123', $mahasiswa->password));
        $this->assertFalse((bool) $mahasiswa->requires_password_reset);
    }

    public function test_update_dosen_can_change_password(): void
    {
        $jurusan = $this->createJurusan();
        $dosen = User::factory()->dosen()->create([
            'email' => 'dosen-edit-password@example.com',
            'password' => Hash::make('old-password'),
            'requires_password_reset' => true,
        ]);
        $profile = $dosen->profile()->create([
            'nomor_induk' => '1988002001',
            'id_jurusan' => $jurusan->id_jurusan,
        ]);
        $profile->jurusans()->sync([$jurusan->id_jurusan]);

        $this->actingAsAdmin()
            ->put(route('admin.dosen.update', $dosen->id), [
                '_modal' => 'edit',
                '_id' => $dosen->id,
                'name' => 'Dosen Edit Password',
                'nomor_induk' => '1988002001',
                'email' => 'dosen-edit-password@example.com',
                'id_jurusan' => [$jurusan->id_jurusan],
                'password' => 'new-password-456',
                'password_confirmation' => 'new-password-456',
            ])
            ->assertRedirect(route('admin.dosen'));

        $dosen->refresh();

        $this->assertTrue(Hash::check('new-password-456', $dosen->password));
        $this->assertFalse((bool) $dosen->requires_password_reset);
    }
}
