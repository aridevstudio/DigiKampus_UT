<?php

namespace Tests\Unit;

use App\Services\PrivateFileService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class PrivateFileServiceTest extends TestCase
{
    public function test_private_disk_is_preferred_for_a_file(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('local')->put('assignments/report.pdf', 'private');
        Storage::disk('public')->put('assignments/report.pdf', 'legacy');

        $service = app(PrivateFileService::class);

        $resolvedPath = str_replace('\\', '/', $service->path('assignments/report.pdf'));

        $this->assertStringContainsString('testing/disks/local/assignments/report.pdf', $resolvedPath);
    }

    public function test_public_disk_is_used_only_as_a_legacy_fallback(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('public')->put('assignments/legacy.pdf', 'legacy');

        $path = app(PrivateFileService::class)->path('assignments/legacy.pdf');

        $normalizedPath = str_replace('\\', '/', $path);
        $this->assertStringContainsString('assignments/legacy.pdf', $normalizedPath);
        $this->assertStringContainsString('assignments/legacy.pdf', $normalizedPath);
        $this->assertStringNotContainsString('app/private', $normalizedPath);
    }

    public function test_unknown_path_does_not_fall_back_to_public_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('public')->put('profiles/private-looking.pdf', 'legacy');

        $this->expectException(NotFoundHttpException::class);

        app(PrivateFileService::class)->path('profiles/private-looking.pdf');
    }

    public function test_path_traversal_is_rejected_before_storage_lookup(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $this->expectException(NotFoundHttpException::class);

        app(PrivateFileService::class)->path('../.env');
    }

    public function test_local_disk_is_not_registered_as_a_direct_file_server(): void
    {
        $this->assertFalse((bool) config('filesystems.disks.local.serve'));
    }

    public function test_absolute_paths_are_rejected(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $this->expectException(NotFoundHttpException::class);

        app(PrivateFileService::class)->path('/etc/passwd');
    }
}
