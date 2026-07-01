<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LauncherApp;
use App\Services\AppRegistryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin controller untuk Apps Hub — full CRUD.
 *
 * Sebelumnya hanya edit (override). Sekarang:
 *   - index   : list semua apps (termasuk nonaktif) dengan statistik
 *   - create  : tampilkan form tambah
 *   - store   : validasi + create
 *   - edit    : tampilkan form edit
 *   - update  : validasi + update (slug immutable)
 *   - toggle  : flip is_active
 *   - destroy : hapus app
 */
class AdminAppsController extends Controller
{
    public function __construct(private AppRegistryService $registry)
    {
    }

    /* -----------------------------------------------------------------
     | READ
     * -----------------------------------------------------------------*/

    public function index()
    {
        $apps = LauncherApp::query()->orderBy('name')->get();
        return view('Auth.admin.apps', [
            'active' => 'apps',
            'title' => 'Apps Management',
            'apps' => $apps,
        ]);
    }

    public function create()
    {
        return view('Auth.admin.apps-create', [
            'active' => 'apps',
            'title' => 'Tambah Aplikasi',
            'iconOptions' => $this->registry->iconOptions(),
        ]);
    }

    public function edit(string $slug)
    {
        $app = $this->registry->findBySlug($slug);
        if (! $app) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }
        return view('Auth.admin.apps-edit', [
            'active' => 'apps',
            'title' => 'Edit ' . $app->name,
            'app' => $app,
            'slug' => $slug,
            'iconOptions' => $this->registry->iconOptions(),
        ]);
    }

    /* -----------------------------------------------------------------
     | WRITE
     * -----------------------------------------------------------------*/

    public function store(Request $request)
    {
        $data = $this->validatePayload($request, creating: true);
        $app = $this->registry->create($data);

        return redirect()
            ->route('admin.apps.edit', ['slug' => $app->slug])
            ->with('success', 'Aplikasi "' . $app->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, string $slug)
    {
        $app = $this->registry->findBySlug($slug);
        if (! $app) {
            abort(404);
        }
        $data = $this->validatePayload($request, creating: false, currentApp: $app);
        // Slug tidak di-update (immutable).
        unset($data['slug']);
        $this->registry->update($app, $data);

        return redirect()
            ->route('admin.apps.edit', ['slug' => $slug])
            ->with('success', 'Konfigurasi aplikasi berhasil disimpan.');
    }

    public function toggle(Request $request, string $slug)
    {
        $app = $this->registry->findBySlug($slug);
        if (! $app) {
            abort(404);
        }
        $app = $this->registry->toggleActive($app);

        return redirect()
            ->back()
            ->with('success', 'Aplikasi "' . $app->name . '" sekarang ' . ($app->is_active ? 'aktif' : 'nonaktif') . '.');
    }

    public function destroy(Request $request, string $slug)
    {
        $app = $this->registry->findBySlug($slug);
        if (! $app) {
            abort(404);
        }
        $name = $app->name;
        $this->registry->delete($app);

        return redirect()
            ->route('admin.apps.index')
            ->with('success', 'Aplikasi "' . $name . '" berhasil dihapus.');
    }

    /* -----------------------------------------------------------------
     | Validation helper
     * -----------------------------------------------------------------*/

    /**
     * Validasi payload dari request. Dipakai bersama oleh store + update.
     *
     * @return array<string, mixed> normalized payload siap untuk service.
     */
    private function validatePayload(Request $request, bool $creating, ?LauncherApp $currentApp = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:120', 'min:2'],
            'description' => ['nullable', 'string', 'max:2000'],
            // Whitelist http/https only — Laravel's default `url` rule accepts
            // schemes like `javascript:` and `file://` which would break the
            // launcher card semantics and pose XSS risk via target=_blank href.
            'url' => ['nullable', 'string', 'max:2048', 'regex:#^https?://#i'],
            'icon' => ['required', 'string', Rule::in(LauncherApp::ALLOWED_ICONS)],
            'open_mode' => ['required', 'string', Rule::in(AppRegistryService::OPEN_MODES)],
            'allowed_roles' => ['required', 'array', 'min:1'],
            'allowed_roles.*' => ['string', Rule::in(AppRegistryService::ROLES)],
            'is_active' => ['nullable'],
        ];

        if ($creating) {
            $rules['slug'] = [
                'required',
                'string',
                'max:64',
                // Anchor penuh dengan \A...\z agar slug seperti `valid-name-invalid`
                // tidak lolos validasi setelah admin lupa hapus karakter invalid.
                'regex:#\A[a-z0-9\-]+\z#',
                Rule::unique('apps', 'slug'),
            ];
        }

        $messages = [
            'name.required' => 'Nama aplikasi wajib diisi.',
            'name.min' => 'Nama aplikasi minimal 2 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung (-).',
            'slug.unique' => 'Slug sudah dipakai aplikasi lain.',
            'url.regex' => 'URL tujuan tidak valid. Gunakan format lengkap, contoh: https://perpustakaan.ut.ac.id/',
            'icon.required' => 'Pilih salah satu icon yang tersedia.',
            'icon.in' => 'Icon yang dipilih tidak dikenali.',
            'open_mode.required' => 'Pilih mode buka (tab baru atau tab yang sama).',
            'open_mode.in' => 'Mode buka tidak dikenali.',
            'allowed_roles.required' => 'Pilih minimal satu hak akses untuk aplikasi ini.',
            'allowed_roles.min' => 'Pilih minimal satu hak akses untuk aplikasi ini.',
        ];

        $data = $request->validate($rules, $messages);

        // Coerce slug to lowercase (defense-in-depth — regex check sudah cover ini,
        // tapi data tetap dinormalisasi untuk konsistensi DB).
        if (isset($data['slug'])) {
            $data['slug'] = strtolower(trim($data['slug']));
        }

        // Coerce is_active ke boolean
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
