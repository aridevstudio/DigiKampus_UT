<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class AdminDosenPhotoValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('profiles');
        Schema::dropIfExists('jurusans');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->enum('role', ['admin', 'mahasiswa', 'dosen'])->default('mahasiswa');
            $table->enum('status', ['aktif', 'nonaktif', 'pending'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('jurusans', function (Blueprint $table) {
            $table->id('id_jurusan');
            $table->string('kode_jurusan')->unique();
            $table->string('nama_jurusan');
            $table->string('fakultas');
            $table->string('jenjang');
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nomor_induk', 50)->nullable()->unique();
            $table->string('no_hp')->nullable();
            $table->unsignedBigInteger('id_jurusan')->nullable();
            $table->text('foto_profile')->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_cannot_store_dosen_with_photo_larger_than_two_mb(): void
    {
        $admin = User::factory()->admin()->create();
        $jurusan = $this->createJurusan();

        $response = $this
            ->from(route('admin.dosen'))
            ->actingAs($admin, 'admin')
            ->post(route('admin.dosen.store'), [
                '_modal' => 'add',
                'name' => 'Dosen Baru',
                'email' => 'dosen.baru@example.com',
                'nomor_induk' => 'DSN-0001',
                'id_jurusan' => $jurusan->id_jurusan,
                'no_hp' => '081234567890',
                'status' => 'aktif',
                'foto' => UploadedFile::fake()->image('foto.jpg')->size(2501),
            ]);

        $response
            ->assertRedirect(route('admin.dosen'))
            ->assertSessionHasErrors(['foto']);

        $this->assertDatabaseMissing('users', [
            'email' => 'dosen.baru@example.com',
        ]);
    }

    public function test_admin_cannot_store_dosen_when_photo_upload_exceeds_server_limit(): void
    {
        $admin = User::factory()->admin()->create();
        $jurusan = $this->createJurusan();
        $oversizedUpload = $this->makeServerRejectedUpload();

        $response = $this
            ->from(route('admin.dosen'))
            ->actingAs($admin, 'admin')
            ->post(route('admin.dosen.store'), [
                '_modal' => 'add',
                'name' => 'Dosen Server Limit',
                'email' => 'dosen.limit@example.com',
                'nomor_induk' => 'DSN-0002',
                'id_jurusan' => $jurusan->id_jurusan,
                'no_hp' => '081234567891',
                'status' => 'aktif',
                'foto' => $oversizedUpload,
            ]);

        $response
            ->assertRedirect(route('admin.dosen'))
            ->assertSessionHasErrors(['foto']);

        $this->assertDatabaseMissing('users', [
            'email' => 'dosen.limit@example.com',
        ]);
    }

    private function createJurusan(): Jurusan
    {
        return Jurusan::create([
            'kode_jurusan' => 'IF',
            'nama_jurusan' => 'Informatika',
            'fakultas' => 'Fakultas Sains',
            'jenjang' => 'S1',
        ]);
    }

    private function makeServerRejectedUpload(): UploadedFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'oversized-photo');
        file_put_contents($tempFile, 'placeholder');

        return new UploadedFile(
            $tempFile,
            'oversized-photo.jpg',
            'image/jpeg',
            UPLOAD_ERR_INI_SIZE,
            true
        );
    }
}
