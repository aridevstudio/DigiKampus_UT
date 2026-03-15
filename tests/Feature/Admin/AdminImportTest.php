<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\ExcelImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminImportTest extends TestCase
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
    // Template Download
    // =====================

    public function test_download_mahasiswa_template(): void
    {
        $response = $this->actingAsAdmin()
            ->get(route('admin.import.template', 'mahasiswa'));

        $response->assertOk();
        $response->assertDownload('template_import_mahasiswa.xlsx');
    }

    public function test_download_dosen_template(): void
    {
        $response = $this->actingAsAdmin()
            ->get(route('admin.import.template', 'dosen'));

        $response->assertOk();
        $response->assertDownload('template_import_dosen.xlsx');
    }

    public function test_download_invalid_type_returns_404(): void
    {
        $response = $this->actingAsAdmin()
            ->get(route('admin.import.template', 'invalid'));

        $response->assertNotFound();
    }

    // =====================
    // Preview Import
    // =====================

    public function test_preview_rejects_invalid_type(): void
    {
        $file = UploadedFile::fake()->create('test.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.import.preview', 'invalid'), ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_preview_rejects_non_excel_file(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.import.preview', 'mahasiswa'), ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_preview_rejects_oversized_file(): void
    {
        $file = UploadedFile::fake()->create('test.xlsx', 6000, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.import.preview', 'mahasiswa'), ['file' => $file]);

        $response->assertStatus(422);
    }

    // =====================
    // Confirm Import
    // =====================

    public function test_confirm_fails_without_preview(): void
    {
        $response = $this->actingAsAdmin()
            ->postJson(route('admin.import.confirm', 'mahasiswa'), [
                'strategy' => 'skip',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', fn($v) => str_contains($v, 'Tidak ada data preview'));
    }

    public function test_confirm_rejects_invalid_strategy(): void
    {
        $response = $this->actingAsAdmin()
            ->withSession([
                'import_preview_mahasiswa' => ['valid_rows' => [['nama' => 'Test']]],
            ])
            ->postJson(route('admin.import.confirm', 'mahasiswa'), [
                'strategy' => 'delete_all', // invalid
            ]);

        $response->assertStatus(422);
    }

    // =====================
    // ExcelImportService Unit-ish Tests
    // =====================

    public function test_generate_template_creates_xlsx(): void
    {
        $path = ExcelImportService::generateTemplate('mahasiswa');

        $this->assertFileExists($path);
        $this->assertStringEndsWith('.xlsx', $path);

        // Clean up
        @unlink($path);
    }

    public function test_generate_template_dosen_creates_xlsx(): void
    {
        $path = ExcelImportService::generateTemplate('dosen');

        $this->assertFileExists($path);
        $this->assertStringEndsWith('.xlsx', $path);

        @unlink($path);
    }

    public function test_preview_real_template_mahasiswa(): void
    {
        $path = ExcelImportService::generateTemplate('mahasiswa');

        $preview = ExcelImportService::preview($path, 'mahasiswa');

        $this->assertArrayHasKey('header', $preview);
        $this->assertArrayHasKey('valid_rows', $preview);
        $this->assertArrayHasKey('valid_count', $preview);
        $this->assertContains('nama', $preview['header']);
        $this->assertContains('nomor_induk', $preview['header']);
        $this->assertContains('email', $preview['header']);
        // Template has 3 sample rows
        $this->assertEquals(3, $preview['valid_count']);

        @unlink($path);
    }

    public function test_execute_import_creates_users(): void
    {
        $validRows = [
            ['nama' => 'Import User 1', 'nomor_induk' => '2099001', 'email' => 'import1@example.com', 'no_hp' => '081234567890', '_row' => 2],
            ['nama' => 'Import User 2', 'nomor_induk' => '2099002', 'email' => 'import2@example.com', 'no_hp' => '081234567891', '_row' => 3],
        ];

        $result = ExcelImportService::executeImport($validRows, 'mahasiswa', 'skip', $this->admin->id);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(2, $result['imported']);
        $this->assertDatabaseHas('users', ['email' => 'import1@example.com', 'role' => 'mahasiswa']);
        $this->assertDatabaseHas('users', ['email' => 'import2@example.com', 'role' => 'mahasiswa']);
    }

    public function test_execute_import_skip_strategy(): void
    {
        // Pre-create a user
        User::factory()->mahasiswa()->create(['email' => 'exists@example.com']);

        $validRows = [
            ['nama' => 'Existing', 'nomor_induk' => '9999', 'email' => 'exists@example.com', '_row' => 2],
            ['nama' => 'New User', 'nomor_induk' => '1111', 'email' => 'new@example.com', '_row' => 3],
        ];

        $result = ExcelImportService::executeImport($validRows, 'mahasiswa', 'skip', $this->admin->id);

        $this->assertEquals(1, $result['imported']);
        $this->assertEquals(1, $result['skipped']);
    }

    public function test_execute_import_stop_strategy(): void
    {
        User::factory()->mahasiswa()->create(['email' => 'blocker@example.com']);

        $validRows = [
            ['nama' => 'Blocker', 'nomor_induk' => '8888', 'email' => 'blocker@example.com', '_row' => 2],
        ];

        $result = ExcelImportService::executeImport($validRows, 'mahasiswa', 'stop', $this->admin->id);

        $this->assertEquals('stopped', $result['status']);
        $this->assertEquals(0, $result['imported']);
    }
}
