<?php

namespace App\Console\Commands;

use App\Services\PrivateFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MigrateLegacyPrivateFiles extends Command
{
    protected $signature = 'storage:migrate-legacy-private
                            {--prefix=* : Only migrate one or more private prefixes}
                            {--dry-run : Report planned copies without writing files}
                            {--force : Replace a destination only when its checksum differs}
                            {--report= : Write a JSON audit report to a path outside public storage}
                            {--json : Emit machine-readable results}';

    protected $description = 'Copy and verify legacy private files from public storage without deleting the source';

    /** @var list<string> */
    private const PREFIXES = [
        'assignments',
        'attendance',
        'bootcamp-sesi-materi',
        'bacaan-lampiran',
    ];

    public function handle(): int
    {
        $requestedPrefixes = collect($this->option('prefix'))
            ->map(fn ($prefix) => trim((string) $prefix, ' /\\'))
            ->filter()
            ->values();

        $prefixes = $requestedPrefixes->isEmpty()
            ? collect(self::PREFIXES)
            : $requestedPrefixes->unique()->values();

        $unknownPrefixes = $prefixes->diff(self::PREFIXES)->values();
        if ($unknownPrefixes->isNotEmpty()) {
            $this->error('Unknown private prefix: '.$unknownPrefixes->implode(', '));

            return self::FAILURE;
        }

        $public = Storage::disk('public');
        $local = Storage::disk('local');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $results = [];
        $databaseAudit = $this->auditDatabaseReferences($local, $public, $prefixes->all());

        foreach ($prefixes as $prefix) {
            foreach ($public->allFiles($prefix) as $sourcePath) {
                $results[] = $this->copyAndVerify(
                    $public,
                    $local,
                    $sourcePath,
                    $dryRun,
                    $force,
                );
            }
        }

        $summary = [
            'dry_run' => $dryRun,
            'force' => $force,
            'prefixes' => $prefixes->all(),
            'files_seen' => count($results),
            'copied' => collect($results)->where('status', 'copied')->count(),
            'already_verified' => collect($results)->where('status', 'already_verified')->count(),
            'planned' => collect($results)->where('status', 'planned')->count(),
            'skipped' => collect($results)->where('status', 'skipped')->count(),
            'failed' => collect($results)->where('status', 'failed')->count(),
            'results' => $results,
            'database_references' => $databaseAudit['references'],
            'database_errors' => $databaseAudit['errors'],
            'missing_database_files' => collect($databaseAudit['references'])
                ->where('status', 'missing')
                ->count(),
            'invalid_database_paths' => collect($databaseAudit['references'])
                ->where('status', 'invalid_path')
                ->count(),
        ];

        $reportPath = $this->option('report');
        if ($reportPath) {
            $this->writeReport((string) $reportPath, $summary);
        }

        if ($this->option('json')) {
            $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(
                ['Status', 'Source', 'Destination', 'Reason'],
                collect($results)->map(fn (array $result) => [
                    $result['status'],
                    $result['source'],
                    $result['destination'],
                    $result['reason'] ?? '',
                ])->all(),
            );
            $this->info(sprintf(
                'Seen: %d | Copied: %d | Verified: %d | Planned: %d | Skipped: %d | Failed: %d',
                $summary['files_seen'],
                $summary['copied'],
                $summary['already_verified'],
                $summary['planned'],
                $summary['skipped'],
                $summary['failed'],
            ));
        }

        return ($summary['failed'] > 0
            || $summary['missing_database_files'] > 0
            || $summary['invalid_database_paths'] > 0
            || $databaseAudit['errors'] !== [])
            ? self::FAILURE
            : self::SUCCESS;
    }

    /**
     * @param  list<string>  $prefixes
     * @return array{references: list<array{table: string, column: string, database_path: string, local_exists: bool, public_exists: bool, status: string}>, errors: list<array{table: string, column: string, error: string}>}
     */
    private function auditDatabaseReferences($local, $public, array $prefixes): array
    {
        $references = [];
        $errors = [];
        $sources = [
            ['table' => 'assignment_submissions', 'column' => 'file_path', 'prefix' => 'assignments'],
            ['table' => 'bootcamp_live_class_attendances', 'column' => 'proof_file', 'prefix' => 'attendance'],
            ['table' => 'bootcamp_sessions', 'column' => 'materi_file', 'prefix' => 'bootcamp-sesi-materi'],
            ['table' => 'course_materials', 'column' => 'lampiran_path', 'prefix' => 'bacaan-lampiran'],
        ];

        foreach ($sources as $source) {
            if (! in_array($source['prefix'], $prefixes, true)) {
                continue;
            }

            try {
                $paths = DB::table($source['table'])
                    ->whereNotNull($source['column'])
                    ->where($source['column'], '<>', '')
                    ->pluck($source['column']);

                foreach ($paths as $path) {
                    $path = (string) $path;
                    if (! $this->isSafePrivatePath($path, $source['prefix'])) {
                        $references[] = [
                            'table' => $source['table'],
                            'column' => $source['column'],
                            'database_path' => $path,
                            'local_exists' => false,
                            'public_exists' => false,
                            'status' => 'invalid_path',
                        ];

                        continue;
                    }

                    $localExists = $local->exists($path);
                    $publicExists = $public->exists($path);
                    $references[] = [
                        'table' => $source['table'],
                        'column' => $source['column'],
                        'database_path' => $path,
                        'local_exists' => $localExists,
                        'public_exists' => $publicExists,
                        'status' => $localExists || $publicExists ? 'available' : 'missing',
                    ];
                }
            } catch (\Throwable $exception) {
                $errors[] = [
                    'table' => $source['table'],
                    'column' => $source['column'],
                    'error' => $exception->getMessage(),
                ];
            }
        }

        return ['references' => $references, 'errors' => $errors];
    }

    private function isSafePrivatePath(string $path, string $prefix): bool
    {
        return str_starts_with($path, $prefix.'/')
            && ! str_contains($path, "\0")
            && ! str_starts_with($path, '/')
            && ! str_starts_with($path, '\\')
            && ! preg_match('/^[A-Za-z]:[\\\\\/]/', $path)
            && ! preg_match('#(^|[\\\\/])\.\.([\\\\/]|$)#', $path);
    }

    private function writeReport(string $path, array $summary): void
    {
        if (is_link($path)) {
            throw new RuntimeException('Migration reports cannot overwrite symlink targets.');
        }

        $directory = dirname($path);
        $normalizedDirectory = str_replace('\\', '/', $directory);
        if ($normalizedDirectory === 'public' || str_starts_with($normalizedDirectory, 'public/')) {
            throw new RuntimeException('Migration reports must not be written inside public/.');
        }

        $resolvedPublicDirectory = realpath(public_path());
        $resolvedExistingDirectory = is_dir($directory) ? realpath($directory) : false;
        if ($resolvedPublicDirectory === false) {
            throw new RuntimeException('Unable to resolve public directory.');
        }

        if ($resolvedExistingDirectory !== false) {
            $resolvedExistingDirectory = rtrim(str_replace('\\', '/', $resolvedExistingDirectory), '/');
            $resolvedPublicDirectory = rtrim(str_replace('\\', '/', $resolvedPublicDirectory), '/');
            if ($resolvedExistingDirectory === $resolvedPublicDirectory
                || str_starts_with($resolvedExistingDirectory, $resolvedPublicDirectory.'/')) {
                throw new RuntimeException('Migration reports must not be written inside public/.');
            }
        }

        if (! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create report directory.');
        }

        $resolvedDirectory = realpath($directory);
        if ($resolvedDirectory === false) {
            throw new RuntimeException('Unable to resolve report directory.');
        }

        $resolvedDirectory = rtrim(str_replace('\\', '/', $resolvedDirectory), '/');
        $resolvedPublicDirectory = rtrim(str_replace('\\', '/', $resolvedPublicDirectory), '/');
        if ($resolvedDirectory === $resolvedPublicDirectory
            || str_starts_with($resolvedDirectory, $resolvedPublicDirectory.'/')) {
            throw new RuntimeException('Migration reports must not be written inside public/.');
        }

        $encoded = json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            throw new RuntimeException('Unable to encode migration report.');
        }

        $temporaryPath = tempnam($resolvedDirectory, '.private-migration-');
        if ($temporaryPath === false) {
            throw new RuntimeException('Unable to create temporary migration report.');
        }

        try {
            chmod($temporaryPath, 0600);
            if (file_put_contents($temporaryPath, $encoded) === false || ! rename($temporaryPath, $path)) {
                throw new RuntimeException('Unable to atomically write migration report.');
            }
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    /**
     * @return array{status: string, source: string, destination: string, source_sha256: string|null, destination_sha256: string|null, reason?: string}
     */
    private function copyAndVerify(
        $public,
        $local,
        string $sourcePath,
        bool $dryRun,
        bool $force,
    ): array {
        $result = [
            'status' => 'failed',
            'source' => $sourcePath,
            'destination' => $sourcePath,
            'source_sha256' => null,
            'destination_sha256' => null,
        ];

        try {
            $sourceHash = $this->hashStream($public, $sourcePath);
            $result['source_sha256'] = $sourceHash;

            if ($local->exists($sourcePath)) {
                $destinationHash = $this->hashStream($local, $sourcePath);
                $result['destination_sha256'] = $destinationHash;

                if ($destinationHash === $sourceHash) {
                    $result['status'] = 'already_verified';
                    $result['reason'] = 'Destination exists with matching SHA-256.';

                    return $result;
                }

                if (! $force) {
                    $result['status'] = 'skipped';
                    $result['reason'] = 'Destination exists with a different SHA-256; use --force only after review.';

                    return $result;
                }
            }

            if ($dryRun) {
                $result['status'] = 'planned';
                $result['reason'] = 'Copy planned; no file was written.';

                return $result;
            }

            if (! is_object($local->getAdapter())
                || ! $local->getAdapter() instanceof \League\Flysystem\Local\LocalFilesystemAdapter) {
                throw new RuntimeException('Atomic legacy migration requires the local filesystem adapter.');
            }

            $temporaryPath = $sourcePath.'.migration-'.bin2hex(random_bytes(12));
            $sourceStream = $public->readStream($sourcePath);
            if (! is_resource($sourceStream)) {
                throw new RuntimeException('Unable to open source stream.');
            }

            try {
                if (! $local->writeStream($temporaryPath, $sourceStream)) {
                    throw new RuntimeException('Temporary destination write returned false.');
                }
            } finally {
                fclose($sourceStream);
            }

            try {
                $temporaryHash = $this->hashStream($local, $temporaryPath);
                $currentSourceHash = $this->hashStream($public, $sourcePath);
                if ($currentSourceHash !== $sourceHash || $temporaryHash !== $sourceHash) {
                    throw new RuntimeException('Source changed or temporary copy SHA-256 verification failed.');
                }

                $temporaryAbsolutePath = $local->path($temporaryPath);
                $destinationAbsolutePath = $local->path($sourcePath);
                $backupPath = $sourcePath.'.migration-backup-'.bin2hex(random_bytes(12));
                $backupAbsolutePath = $local->path($backupPath);
                $hasExistingDestination = $local->exists($sourcePath);
                $switched = false;

                if ($hasExistingDestination && ! $force) {
                    throw new RuntimeException('Destination exists and replacement was not authorized.');
                }

                try {
                    if ($hasExistingDestination && ! rename($destinationAbsolutePath, $backupAbsolutePath)) {
                        throw new RuntimeException('Unable to protect the existing destination before switch.');
                    }

                    if (! rename($temporaryAbsolutePath, $destinationAbsolutePath)) {
                        throw new RuntimeException('Unable to switch verified copy into place.');
                    }
                    $switched = true;

                    $result['destination_sha256'] = $this->hashStream($local, $sourcePath);
                    if ($result['destination_sha256'] !== $sourceHash) {
                        throw new RuntimeException('Post-switch SHA-256 verification failed.');
                    }

                    if ($hasExistingDestination && is_file($backupAbsolutePath)) {
                        unlink($backupAbsolutePath);
                    }
                } catch (\Throwable $exception) {
                    if ($switched && is_file($destinationAbsolutePath)) {
                        unlink($destinationAbsolutePath);
                    }
                    if ($hasExistingDestination && is_file($backupAbsolutePath)) {
                        rename($backupAbsolutePath, $destinationAbsolutePath);
                    }
                    throw $exception;
                }
            } finally {
                if ($local->exists($temporaryPath)) {
                    $local->delete($temporaryPath);
                }
            }

            app(PrivateFileService::class)->path($sourcePath);
            $result['status'] = 'copied';
            $result['reason'] = 'Copied and verified with matching SHA-256.';
        } catch (\Throwable $exception) {
            $result['reason'] = $exception->getMessage();
        }

        return $result;
    }

    private function hashStream($disk, string $path): string
    {
        $stream = $disk->readStream($path);
        if (! is_resource($stream)) {
            throw new RuntimeException('Unable to open file stream for hashing.');
        }

        try {
            $hash = hash_init('sha256');
            hash_update_stream($hash, $stream);

            return hash_final($hash);
        } finally {
            fclose($stream);
        }
    }
}
