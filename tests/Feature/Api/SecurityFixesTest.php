<?php

namespace Tests\Feature\Api;

use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'tipe' => 'gratis',
            'kategori' => 'kursus',
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

    public function test_mahasiswa_token_cannot_access_dosen_api(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $token = $mahasiswa->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/dosen/profile')
            ->assertForbidden();
    }

    public function test_mahasiswa_token_cannot_create_dosen_course(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $token = $mahasiswa->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/dosen/courses', [
                'nama_course' => 'Unauthorized course',
            ])
            ->assertForbidden();
    }

    public function test_dosen_token_cannot_access_admin_api(): void
    {
        $dosen = User::factory()->dosen()->create();
        $token = $dosen->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/profile')
            ->assertForbidden();
    }

    public function test_admin_token_cannot_access_dosen_api(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/dosen/profile')
            ->assertForbidden();
    }

    public function test_dosen_resource_does_not_expose_oauth_metadata(): void
    {
        $dosen = User::factory()->dosen()->create();
        $dosen->forceFill([
            'google_id' => 'google-internal-id',
            'provider' => 'google',
        ])->save();
        $token = $dosen->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/dosen/profile')
            ->assertOk()
            ->assertJsonMissingPath('data.google_id')
            ->assertJsonMissingPath('data.provider');
    }

    public function test_admin_resource_does_not_expose_provider_metadata(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->forceFill(['provider' => 'internal'])->save();
        $token = $admin->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/profile')
            ->assertOk()
            ->assertJsonMissingPath('data.provider');
    }

    public function test_dosen_cannot_read_unrelated_student_messages(): void
    {
        $dosen = User::factory()->dosen()->create();
        $student = User::factory()->mahasiswa()->create();
        $token = $dosen->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/dosen/messages/{$student->id}")
            ->assertForbidden();
    }

    public function test_student_can_download_only_own_private_submission(): void
    {
        Storage::fake('local');

        $dosen = User::factory()->dosen()->create();
        $owner = User::factory()->mahasiswa()->create();
        $otherStudent = User::factory()->mahasiswa()->create();
        $course = Course::create([
            'kode_course' => 'SEC-DOWNLOAD',
            'nama_course' => 'Private files',
            'id_dosen' => $dosen->id,
            'status' => 'aktif',
        ]);
        Enrollment::create([
            'id_mahasiswa' => $owner->id,
            'id_course' => $course->id_course,
            'status' => 'aktif',
        ]);
        $material = CourseMaterial::create([
            'id_course' => $course->id_course,
            'judul_material' => 'Assignment',
            'tipe' => 'tugas',
            'konten' => '{}',
            'urutan' => 1,
        ]);
        $path = 'assignments/private-test.docx';
        Storage::disk('local')->put($path, 'private');
        AssignmentSubmission::create([
            'id_course' => $course->id_course,
            'id_material' => $material->id_material,
            'id_mahasiswa' => $owner->id,
            'file_path' => $path,
            'original_file_name' => 'private-test.docx',
            'file_size' => 7,
            'status' => 'submitted',
        ]);

        $this->actingAs($otherStudent, 'mahasiswa')
            ->get(route('mahasiswa.assignment-download', [$course->id_course, $material->id_material]))
            ->assertNotFound();

        $this->actingAs($owner, 'mahasiswa')
            ->get(route('mahasiswa.assignment-download', [$course->id_course, $material->id_material]))
            ->assertDownload('private-test.docx');

        $this->assertFalse((bool) config('filesystems.disks.local.serve'));
    }

    public function test_private_file_service_rejects_direct_public_path_collisions(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('public')->put('profiles/private-looking.pdf', 'not private');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        app(\App\Services\PrivateFileService::class)->path('profiles/private-looking.pdf');
    }

    public function test_security_policies_are_registered(): void
    {
        $this->assertSame(
            \App\Policies\CoursePolicy::class,
            get_class(Gate::getPolicyFor(Course::class))
        );
        $this->assertSame(
            \App\Policies\AssignmentSubmissionPolicy::class,
            get_class(Gate::getPolicyFor(AssignmentSubmission::class))
        );
    }

    public function test_persisted_html_sinks_use_escaped_text_rendering(): void
    {
        foreach ([
            resource_path('views/pages/mahasiswa/news.blade.php'),
            resource_path('views/Auth/admin/kelola-bacaan.blade.php'),
            resource_path('views/Auth/dosen/kelola-bacaan.blade.php'),
        ] as $view) {
            $contents = file_get_contents($view);
            $this->assertIsString($contents);
            $this->assertStringNotContainsString('x-html', $contents);
        }
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
