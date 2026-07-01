<?php

namespace App\Services;

use App\Models\AppRegistryOverride;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * Hardcoded Apps Hub catalog — pure LAUNCHER implementation.
 *
 * Per PM refactor (Apps menu = launcher, bukan embedded):
 *   - Setiap app adalah kartu yang ketika di-klik langsung membuka URL
 *     eksternal di tab baru (`target="_blank" rel="noopener noreferrer"`).
 *   - Tidak ada lagi integrated view, AI chat, embedded compiler, dll.
 *
 * Developer-owned hardcoded list (developer boleh edit). Admin hanya boleh:
 *   - Activate/deactivate (`is_active` override)
 *   - Edit `display_name`, `description`, `access_roles`
 *   - Opsional: override `external_url` (untuk E-Library yang URL perpustakaan
 *     berubah-ubah). Override database lebih diutamakan dari catalog.
 *
 * Adding new app:
 *   1. Tambahkan entry di `CATALOG` (slug → name/icon/color/url/category/...).
 *   2. Admin boleh override tampilannya via panel tanpa code change.
 *
 * Removing app:
 *   - Hapus entry dari `CATALOG`. Tidak ada lagi view per-app.
 */
class AppRegistryService
{
    /**
     * Cache key bumped to _v2 after `external_url` column was added.
     * Earlier _v1 entries (without `external_url`) still work, but bumping
     * forces a one-time cache flush so the new shape is canonical.
     */
    public const CACHE_KEY = 'app_registry_overrides_v2';

    /**
     * Definition per entry:
     *   - slug          : unique key (folder-safe, lowercase, hyphenated)
     *   - display_name  : fallback label jika admin tidak override
     *   - short_label   : ≤6 chars, badge di kartu
     *   - description   : fallback copy kartu
     *   - icon          : SVG key — sparkles|chip|github|code|video|palette|library
     *   - color         : theme — violet|sky|slate|emerald|amber|rose|indigo
     *   - category      : lihat CATEGORY_ORDER
     *   - external_url  : URL tujuan (developer hardcode, admin opsional override)
     */
    public const CATALOG = [
        'ai-assistant' => [
            'slug' => 'ai-assistant',
            'display_name' => 'AI Assistant',
            'short_label' => 'AI',
            'description' => 'Asisten AI ChatGPT untuk brainstorming, rangkuman materi, dan eksplorasi ide belajar di luar DigiKampus.',
            'icon' => 'sparkles',
            'color' => 'violet',
            'category' => 'AI & Otomasi',
            'external_url' => 'https://chatgpt.com/',
        ],
        'copilot' => [
            'slug' => 'copilot',
            'display_name' => 'Microsoft Copilot',
            'short_label' => 'Copilot',
            'description' => 'Asisten AI Microsoft untuk produktivitas — drafting, ringkasan dokumen, dan eksplorasi ide.',
            'icon' => 'chip',
            'color' => 'sky',
            'category' => 'AI & Otomasi',
            'external_url' => 'https://copilot.microsoft.com/',
        ],
        'teachable-machine' => [
            'slug' => 'teachable-machine',
            'display_name' => 'Google Teachable Machine',
            'short_label' => 'TM',
            'description' => 'Latih model machine learning tanpa menulis kode — klasifikasi gambar, suara, dan pose.',
            'icon' => 'video',
            'color' => 'amber',
            'category' => 'AI & Otomasi',
            'external_url' => 'https://teachablemachine.withgoogle.com/',
        ],
        'github' => [
            'slug' => 'github',
            'display_name' => 'GitHub',
            'short_label' => 'Git',
            'description' => 'Platform version control dan kolaborasi kode sumber terbuka untuk project pemrograman.',
            'icon' => 'github',
            'color' => 'slate',
            'category' => 'Code & Build',
            'external_url' => 'https://github.com/',
        ],
        'online-compiler' => [
            'slug' => 'online-compiler',
            'display_name' => 'Online Compiler',
            'short_label' => 'Code',
            'description' => 'Editor kode online untuk coba, jalankan, dan lihat output tanpa instalasi software tambahan.',
            'icon' => 'code',
            'color' => 'emerald',
            'category' => 'Code & Build',
            'external_url' => 'https://www.programiz.com/',
        ],
        'canva' => [
            'slug' => 'canva',
            'display_name' => 'Canva Education',
            'short_label' => 'Canva',
            'description' => 'Platform desain grafis untuk membuat presentasi, poster, infografis, dan tugas visual.',
            'icon' => 'palette',
            'color' => 'rose',
            'category' => 'Design & Visual',
            'external_url' => 'https://www.canva.com/education/',
        ],
        'e-library' => [
            'slug' => 'e-library',
            'display_name' => 'E-Library',
            'short_label' => 'Books',
            'description' => 'Akses cepat ke koleksi buku digital, jurnal, dan repository perpustakaan.',
            'icon' => 'library',
            'color' => 'indigo',
            'category' => 'Research & Library',
            // empty default — admin wajib isi via panel (field external_url).
            'external_url' => null,
        ],
    ];

    public const CATEGORY_ORDER = [
        'AI & Otomasi' => 1,
        'Code & Build' => 2,
        'Design & Visual' => 3,
        'Research & Library' => 4,
    ];

    /**
     * Merged catalog dengan override diterapkan. Filter role + is_active
     * tetap dilakukan di caller (AppsHubController) — service hanya merge.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        $overrides = $this->getOverrides();
        $merged = [];
        foreach (self::CATALOG as $slug => $entry) {
            $merged[$slug] = $this->merge($entry, $overrides[$slug] ?? null);
        }
        return $merged;
    }

    public function get(string $slug): ?array
    {
        if (!isset(self::CATALOG[$slug])) {
            return null;
        }
        return $this->merge(self::CATALOG[$slug], $this->getOverrides()[$slug] ?? null);
    }

    /**
     * Admin-facing list: catalog metadata + override fields + flag `_has_override`.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAdminList(): array
    {
        $overrides = $this->getOverrides();
        $out = [];
        foreach (self::CATALOG as $slug => $entry) {
            $merged = $entry;
            $override = $overrides[$slug] ?? null;
            // Admin-facing URL: kalau admin override terisi, pakai itu;
            // kalau tidak, pakai fallback dari catalog (null untuk e-library
            // yang BELUM diisi admin akan kelihatan empty di form).
            $merged['display_name'] = $override && !empty($override['display_name']) ? $override['display_name'] : $entry['display_name'];
            $merged['description']  = $override && !empty($override['description'])  ? $override['description']  : $entry['description'];
            $merged['external_url'] = $override && !empty($override['external_url']) ? $override['external_url'] : ($entry['external_url'] ?? null);
            $merged['is_active']    = $override ? (bool) ($override['is_active'] ?? true) : true;
            $merged['access_roles'] = $override && !empty($override['access_roles'])
                ? (array) $override['access_roles']
                : ['mahasiswa'];
            $merged['_has_override'] = (bool) $override;
            $out[$slug] = $merged;
        }
        return $out;
    }

    /**
     * Persist full override — dipanggil dari admin edit form.
     * Field `external_url` adalah nullable admin override.
     */
    public function saveOverride(string $slug, array $data): void
    {
        $this->assertKnownSlug($slug);

        $row = AppRegistryOverride::firstOrNew(['slug' => $slug]);
        $row->fill([
            'display_name' => $this->nullableString($data['display_name'] ?? null),
            'description'  => $this->nullableString($data['description'] ?? null),
            'external_url' => $this->nullableString($data['external_url'] ?? null),
            'is_active'    => (bool) ($data['is_active'] ?? false),
            'access_roles' => $this->normalizeRoles($data['access_roles'] ?? null),
        ]);
        $row->save();
        Cache::forget(self::CACHE_KEY);
    }

    public function setEnabled(string $slug, bool $enabled): void
    {
        $this->assertKnownSlug($slug);
        $row = AppRegistryOverride::firstOrNew(['slug' => $slug]);
        $row->is_active = $enabled;
        $row->save();
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Group merged catalog by category, ordered by CATEGORY_ORDER.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getGroupedByCategory(): array
    {
        $items = $this->all();
        $grouped = [];
        foreach ($items as $slug => $entry) {
            $cat = $entry['category'] ?? 'Lainnya';
            $grouped[$cat][] = array_merge(['key' => $slug], $entry);
        }
        uksort($grouped, function ($a, $b) {
            $oa = self::CATEGORY_ORDER[$a] ?? 999;
            $ob = self::CATEGORY_ORDER[$b] ?? 999;
            return $oa <=> $ob;
        });
        return $grouped;
    }

    public function isKnownSlug(string $slug): bool
    {
        return isset(self::CATALOG[$slug]);
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function assertKnownSlug(string $slug): void
    {
        if (!isset(self::CATALOG[$slug])) {
            throw new InvalidArgumentException("Unknown app slug: {$slug}");
        }
    }

    private function getOverrides(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return AppRegistryOverride::query()->get()->keyBy('slug')->map(function ($row) {
                return [
                    'slug'         => $row->slug,
                    'display_name' => $row->display_name,
                    'description'  => $row->description,
                    'external_url' => $row->external_url,
                    'is_active'    => (bool) $row->is_active,
                    'access_roles' => $row->access_roles,
                    'updated_at'   => optional($row->updated_at)->toIso8601String(),
                ];
            })->all();
        });
    }

    /**
     * Precedence: override (admin) > catalog (developer hardcode) > null.
     * Caller wajib cek `.external_url` truthiness sebelum dipakai sebagai href.
     */
    private function merge(array $catalog, ?array $override): array
    {
        $entry = $catalog;
        $entry['is_active'] = $override ? (bool) ($override['is_active'] ?? true) : true;
        $entry['access_roles'] = $override && !empty($override['access_roles'])
            ? (array) $override['access_roles']
            : ['mahasiswa'];

        $entry['display_name'] = ($override && !empty($override['display_name']))
            ? $override['display_name']
            : $catalog['display_name'];
        $entry['description'] = ($override && !empty($override['description']))
            ? $override['description']
            : $catalog['description'];
        $entry['external_url'] = ($override && !empty($override['external_url']))
            ? $override['external_url']
            : ($catalog['external_url'] ?? null);

        $entry['_has_override'] = (bool) $override;
        return $entry;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function normalizeRoles(mixed $roles): array
    {
        if (!is_array($roles)) {
            return ['mahasiswa'];
        }
        $allowed = ['mahasiswa', 'dosen', 'admin'];
        $normalized = array_values(array_intersect($roles, $allowed));
        return empty($normalized) ? ['mahasiswa'] : $normalized;
    }
}
