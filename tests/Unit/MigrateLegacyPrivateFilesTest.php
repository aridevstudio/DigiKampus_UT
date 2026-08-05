<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class MigrateLegacyPrivateFilesTest extends TestCase
{
    private string $reportPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportPath = storage_path('framework/testing/private-migration-report.json');
        @unlink($this->reportPath);
    }

    protected function tearDown(): void
    {
        @unlink($this->reportPath);
        parent::tearDown();
    }

    public function test_command_copies_verifies_and_preserves_legacy_source(): void
    {
        $this->mockDatabaseReferences();
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('assignments/submission.pdf', 'source-content');

        $exitCode = Artisan::call('storage:migrate-legacy-private', [
            '--prefix' => ['assignments'],
            '--report' => $this->reportPath,
            '--json' => true,
        ]);

        $output = Artisan::output();
        $this->assertSame(0, $exitCode, $output);
        Storage::disk('public')->assertExists('assignments/submission.pdf');
        Storage::disk('local')->assertExists('assignments/submission.pdf');
        $this->assertSame(
            Storage::disk('public')->get('assignments/submission.pdf'),
            Storage::disk('local')->get('assignments/submission.pdf'),
        );
        $this->assertFileExists($this->reportPath);
        $this->assertSame(
            'copied',
            json_decode((string) file_get_contents($this->reportPath), true)['results'][0]['status'] ?? null,
        );
    }

    public function test_command_is_idempotent_when_destination_matches(): void
    {
        $this->mockDatabaseReferences();
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('attendance/proof.png', 'same-content');
        Storage::disk('local')->put('attendance/proof.png', 'same-content');

        $exitCode = Artisan::call('storage:migrate-legacy-private', [
            '--prefix' => ['attendance'],
            '--json' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('already_verified', $output);
        $this->assertStringContainsString('"copied": 0', $output);
    }

    public function test_command_does_not_overwrite_divergent_destination_without_force(): void
    {
        $this->mockDatabaseReferences();
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('assignments/submission.pdf', 'source-content');
        Storage::disk('local')->put('assignments/submission.pdf', 'different-content');

        $exitCode = Artisan::call('storage:migrate-legacy-private', [
            '--prefix' => ['assignments'],
            '--json' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame('different-content', Storage::disk('local')->get('assignments/submission.pdf'));
        $this->assertStringContainsString('different SHA-256', Artisan::output());
    }

    public function test_force_replaces_divergent_destination_but_keeps_public_source(): void
    {
        $this->mockDatabaseReferences();
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('assignments/submission.pdf', 'source-content');
        Storage::disk('local')->put('assignments/submission.pdf', 'different-content');

        $exitCode = Artisan::call('storage:migrate-legacy-private', [
            '--prefix' => ['assignments'],
            '--force' => true,
            '--json' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame('source-content', Storage::disk('local')->get('assignments/submission.pdf'));
        $this->assertSame('source-content', Storage::disk('public')->get('assignments/submission.pdf'));
    }

    public function test_command_fails_and_reports_missing_database_references(): void
    {
        $this->mockDatabaseReferences(['assignments/missing.pdf']);
        Storage::fake('public');
        Storage::fake('local');

        $exitCode = Artisan::call('storage:migrate-legacy-private', [
            '--prefix' => ['assignments'],
            '--dry-run' => true,
            '--json' => true,
        ]);
        $output = Artisan::output();
        $summary = json_decode($output, true);

        $this->assertSame(1, $exitCode, $output);
        $this->assertSame(1, $summary['missing_database_files'] ?? 0, $output);
        $this->assertSame('missing', $summary['database_references'][0]['status'] ?? null, $output);
    }

    /** @param list<string> $assignmentPaths */
    private function mockDatabaseReferences(array $assignmentPaths = []): void
    {
        $query = Mockery::mock();
        $query->shouldReceive('whereNotNull')->andReturnSelf();
        $query->shouldReceive('where')->andReturnSelf();
        $query->shouldReceive('pluck')->andReturnUsing(
            fn (string $column): Collection => $column === 'file_path'
                ? collect($assignmentPaths)
                : collect(),
        );

        DB::shouldReceive('table')->andReturn($query);
    }
}
