<?php

namespace App\Services;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PrivateFileService
{
    /**
     * Return the disk containing a relative application path.
     *
     * The local disk is authoritative. The public fallback is intentionally
     * temporary for files written before the private-storage migration; callers
     * must authorize the resource before invoking this service.
     */
    public function diskFor(string $path): FilesystemAdapter
    {
        $this->assertSafeRelativePath($path);

        $local = Storage::disk('local');
        if ($local->exists($path)) {
            return $local;
        }

        // Only these prefixes were historically written to the public disk by
        // private-file features. Do not allow arbitrary path collisions to
        // silently fall back from private storage to public storage.
        if ($this->isKnownLegacyPrivatePrefix($path)) {
            $public = Storage::disk('public');
            if ($public->exists($path)) {
                return $public;
            }
        }

        throw new NotFoundHttpException('File not found.');
    }

    public function download(string $path, ?string $downloadName = null)
    {
        $disk = $this->diskFor($path);
        $safeName = $this->safeDownloadName($downloadName ?: basename($path));

        return $disk->download($path, $safeName);
    }

    public function path(string $path): string
    {
        return $this->diskFor($path)->path($path);
    }

    private function safeDownloadName(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name) ?: 'download';

        return trim($name, '. ') ?: 'download';
    }

    private function isKnownLegacyPrivatePrefix(string $path): bool
    {
        foreach (['assignments/', 'attendance/', 'bootcamp-sesi-materi/', 'bacaan-lampiran/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function assertSafeRelativePath(string $path): void
    {
        if (
            $path === ''
            || str_contains($path, "\0")
            || str_starts_with($path, '/')
            || str_starts_with($path, '\\')
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $path)
            || preg_match('#(^|[\\\\/])\.\.([\\\\/]|$)#', $path)
        ) {
            throw new NotFoundHttpException('File not found.');
        }
    }
}
