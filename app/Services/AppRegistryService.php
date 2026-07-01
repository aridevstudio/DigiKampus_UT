<?php

namespace App\Services;

use App\Models\LauncherApp;
use Illuminate\Support\Collection;

/**
 * Apps Hub registry — fully DB-driven.
 *
 * SEBELUM refactor ini, daftar aplikasi di-hardcode di `CATALOG` dan admin
 * hanya bisa override sebagian field. Sekarang:
 *   - Sumber kebenaran tunggal: tabel `apps` (model `App\Models\LauncherApp`).
 *   - Admin punya CRUD penuh via `App\Http\Controllers\Auth\AdminAppsController`.
 *   - Service ini HANYA query + filter; tidak ada konstanta hardcoded untuk
 *     daftar aplikasi.
 *
 * Ikon dan theme color dikelola via konstanta `ICON_THEME` di sini (bukan
 * catalog app). Konstanta ICON hanya untuk mapping visual theme — TIDAK
 * menyimpan data app apapun.
 *
 * Untuk developer menambahkan icon baru:
 *   1. Tambahkan key di `ICON_THEME` (warna + key) di bawah.
 *   2. Tambahkan SVG case di `resources/views/partials/apps/_icon.blade.php`.
 *   3. Tambahkan key di `App\Models\LauncherApp::ALLOWED_ICONS`.
 * Daftar APP sepenuhnya tanggung jawab admin via panel.
 */
class AppRegistryService
{
    /**
     * Daftar icon yang dikenali + theme warna untuk masing-masing.
     *
     * Hanya digunakan untuk rendering visual (Tailwind color class). Tidak
     * ada business logic di sini. Tambah key baru = tambah juga case di
     * view `_icon.blade.php` dan `LauncherApp::ALLOWED_ICONS`.
     */
    public const ICON_THEME = [
        'sparkles' => ['color' => 'violet', 'label' => 'AI / Sparkles'],
        'chip'     => ['color' => 'sky',    'label' => 'Prosesor / Chip'],
        'github'   => ['color' => 'slate',  'label' => 'GitHub / Code Hosting'],
        'code'     => ['color' => 'emerald','label' => 'Source Code'],
        'video'    => ['color' => 'amber',  'label' => 'Video / Multimedia'],
        'palette'  => ['color' => 'rose',   'label' => 'Desain / Palette'],
        'library'  => ['color' => 'indigo', 'label' => 'Library / Books'],
        'globe'    => ['color' => 'teal',   'label' => 'Web / Umum'],
    ];

    /** Daftar open_mode yang valid. List tunggal-sumber. */
    public const OPEN_MODES = ['new_tab', 'same_tab'];

    /**
     * Daftar role yang valid untuk allowed_roles (termasuk special "all").
     */
    public const ROLES = ['mahasiswa', 'dosen', 'admin', 'all'];

    /* -----------------------------------------------------------------
     | READ: Untuk Mahasiswa Launcher
     * -----------------------------------------------------------------*/

    /**
     * Daftar app AKTIF yang dapat diakses oleh user dengan role tertentu.
     * Diurutkan alfabetis agar konsisten antar reload (mencegah UI "shuffle").
     *
     * Filter dilakukan di SQL: is_active=1 + allowed_roles match (atau "all").
     *
     * @return Collection<int, LauncherApp>
     */
    public function activeForUser(?string $userRole): Collection
    {
        return LauncherApp::query()
            ->active()
            ->accessibleBy($userRole)
            ->orderBy('name')
            ->get();
    }

    /**
     * Cari satu app berdasarkan slug. Return null jika tidak ditemukan.
     * Admin route pakai ini untuk edit / update / form binding.
     */
    public function findBySlug(string $slug): ?LauncherApp
    {
        return LauncherApp::query()->where('slug', $slug)->first();
    }

    /* -----------------------------------------------------------------
     | WRITE: Untuk Admin Panel
     * -----------------------------------------------------------------*/

    /**
     * Buat app baru. Slug di-trim + di-lowercase untuk konsistensi.
     * Unique constraint di DB menjamin tidak ada duplikat — race-safe.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): LauncherApp
    {
        $payload = $this->normalizePayload($data);
        return LauncherApp::query()->create($payload);
    }

    /**
     * Update app existing. Slug TIDAK dapat diubah setelah create (immutable)
     * untuk mencegah link rusak / cache invalidation issue. Caller (controller)
     * bertanggung jawab untuk mengabaikan field `slug` di request sebelum
     * memanggil method ini — untuk double-safety kita juga strip slug di sini.
     *
     * @param array<string, mixed> $data
     */
    public function update(LauncherApp $app, array $data): LauncherApp
    {
        unset($data['slug']); // Hard guard — slug immutable.
        $payload = $this->normalizePayload($data);

        $app->fill($payload);
        $app->save();
        return $app;
    }

    /**
     * Hapus app dari DB (hard delete).
     */
    public function delete(LauncherApp $app): bool
    {
        return (bool) $app->delete();
    }

    /**
     * Toggle status aktif/nonaktif. Return app fresh.
     */
    public function toggleActive(LauncherApp $app): LauncherApp
    {
        $app->is_active = ! (bool) $app->is_active;
        $app->save();
        return $app;
    }

    /* -----------------------------------------------------------------
     | Helpers
     * -----------------------------------------------------------------*/

    /**
     * Tentukan warna (theme color) untuk sebuah icon key.
     * Return "slate" sebagai fallback default jika icon tidak dikenali.
     */
    public function iconTheme(string $icon): string
    {
        return self::ICON_THEME[$icon]['color'] ?? 'slate';
    }

    /**
     * Daftar key icon yang tersedia untuk UI select.
     *
     * @return array<string, string> map icon → label.
     */
    public function iconOptions(): array
    {
        $out = [];
        foreach (self::ICON_THEME as $key => $meta) {
            $out[$key] = $meta['label'];
        }
        return $out;
    }

    /**
     * Normalisasi payload sebelum create/update. Tanggung jawab:
     *   - Trim string fields
     *   - Slug → lowercase, ganti karakter invalid dengan '-'. Dipakai hanya
     *     untuk CREATE — caller wajib strip slug dari $data sebelum invoke
     *     method ini untuk UPDATE (lihat AppRegistryService::update()).
     *   - allowed_roles → array (uniques, tidak kosong)
     *   - open_mode → whitelist
     *   - icon → whitelist
     *   - is_active → boolean
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalizePayload(array $data): array
    {
        $payload = [];

        // Name
        $name = isset($data['name']) ? trim((string) $data['name']) : '';
        $payload['name'] = $name;

        // Slug (lowercase + dash). Hanya dipakai untuk CREATE.
        if (isset($data['slug'])) {
            $slug = strtolower(trim((string) $data['slug']));
            $slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug) ?? '';
            $slug = trim($slug, '-');
            $payload['slug'] = $slug !== '' ? $slug : \Illuminate\Support\Str::slug($name);
        }

        // Description (opsional)
        $payload['description'] = isset($data['description']) && $data['description'] !== ''
            ? trim((string) $data['description'])
            : null;

        // URL (opsional) — boleh kosong, akan di-trim dan di-set null
        $payload['url'] = isset($data['url']) && trim((string) $data['url']) !== ''
            ? trim((string) $data['url'])
            : null;

        // Icon — whitelist (fallback ke 'globe' jika invalid)
        $icon = isset($data['icon']) ? (string) $data['icon'] : 'globe';
        $payload['icon'] = in_array($icon, LauncherApp::ALLOWED_ICONS, true)
            ? $icon
            : 'globe';

        // Open mode — whitelist (fallback ke 'new_tab' jika invalid)
        $openMode = isset($data['open_mode']) ? (string) $data['open_mode'] : 'new_tab';
        $payload['open_mode'] = in_array($openMode, self::OPEN_MODES, true)
            ? $openMode
            : 'new_tab';

        // Allowed roles — array unik, default ['mahasiswa'] jika kosong
        $roles = $data['allowed_roles'] ?? ['mahasiswa'];
        if (! is_array($roles)) {
            $roles = ['mahasiswa'];
        }
        $roles = array_values(array_unique(array_filter($roles, fn ($r) =>
            is_string($r) && in_array($r, self::ROLES, true))));
        if (in_array('all', $roles, true)) {
            // Jika "all" dipilih, set ke wildcard saja (abaikan role spesifik lain).
            $roles = ['all'];
        }
        $payload['allowed_roles'] = empty($roles) ? ['mahasiswa'] : $roles;

        // is_active — cast ke boolean
        $payload['is_active'] = array_key_exists('is_active', $data)
            ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN)
            : true;

        return $payload;
    }
}
