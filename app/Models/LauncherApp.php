<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * LauncherApp = model untuk tabel `apps` (single source of truth).
 *
 * Nama class `LauncherApp` (bukan `App`) untuk menghindari benturan dengan
 * namespace root `App\\` di kode import. Tabel tetap `apps`.
 *
 * Dipakai bersama `App\Services\AppRegistryService` yang mengurus:
 *   - Query launcher untuk role tertentu
 *   - Filter is_active + role
 *   - Color theme derivation dari icon (via service constant)
 *
 * Class ini HANYA berisi kolom, casts, scopes — tanpa business logic
 * untuk filtering/launching. Service adalah authority untuk akses role.
 */
class LauncherApp extends Model
{
    use HasFactory;

    protected $table = 'apps';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'url',
        'icon',
        'image_icon',
        'is_active',
        'allowed_roles',
        'open_mode',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allowed_roles' => 'array',
    ];

    /**
     * Daftar icon yang tersedia untuk dipilih admin. Selaras dengan
     * `App\Services\AppRegistryService::ICON_THEME` — TOLAK pilih di luar
     * daftar ini untuk mencegah injeksi dan menjaga konsistensi visual.
     */
    public const ALLOWED_ICONS = [
        'sparkles', 'chip', 'github', 'code',
        'video', 'palette', 'library', 'globe',
    ];

    /** Role names yang valid. "all" adalah wildcard (handled di service). */
    public const ALLOWED_ROLES = ['mahasiswa', 'dosen', 'admin', 'all'];

    /* -----------------------------------------------------------------
     | Scopes
     * -----------------------------------------------------------------*/

    /** Hanya app dengan is_active = true. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter app yang BISA diakses user berdasarkan role + wildcard "all".
     *
     * Menggunakan Eloquent Builder whereJsonContains() yang menghasilkan SQL
     * spesifik-driver: `JSON_CONTAINS()` untuk MySQL, `json_each()` untuk
     * SQLite (via ekstensi JSON1 yang aktif di Laravel default), dst.
     * Tidak ada fallback LIKE — driver yang tidak mendukung JSON WHERE
     * akan throw dan harus di-migrasi ke driver yang mendukung.
     */
    public function scopeAccessibleBy(Builder $query, ?string $userRole): Builder
    {
        if ($userRole === null || $userRole === '') {
            // Tanpa role → hanya app yang diizinkan untuk semua.
            return $query->whereJsonContains('allowed_roles', 'all');
        }

        return $query->where(function (Builder $q) use ($userRole) {
            $q->whereJsonContains('allowed_roles', $userRole)
              ->orWhereJsonContains('allowed_roles', 'all');
        });
    }

    /* -----------------------------------------------------------------
     | Helpers
     * -----------------------------------------------------------------*/

    /** Apakah URL valid untuk di-launch (truthy + http/https scheme)? */
    public function hasLaunchableUrl(): bool
    {
        $u = trim((string) ($this->url ?? ''));
        if ($u === '') {
            return false;
        }
        return (bool) preg_match('#^https?://#i', $u);
    }

    /** Apakah role tertentu diizinkan melihat app ini? */
    public function isAccessibleBy(?string $userRole): bool
    {
        $allowed = $this->allowed_roles ?? [];
        if (!is_array($allowed) || empty($allowed)) {
            return false;
        }
        if (in_array('all', $allowed, true)) {
            return true;
        }
        if ($userRole === null) {
            return false;
        }
        return in_array($userRole, $allowed, true);
    }
}
