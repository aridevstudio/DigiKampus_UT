<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AppRegistryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAppsController extends Controller
{
    public function __construct(private AppRegistryService $registry)
    {
    }

    public function index()
    {
        $apps = $this->registry->getAdminList();
        return view('Auth.admin.apps', [
            'active' => 'apps',
            'title' => 'Apps Management',
            'apps' => $apps,
        ]);
    }

    public function edit(string $slug)
    {
        $app = $this->registry->get($slug);
        if (!$app) {
            abort(404, 'Aplikasi tidak ditemukan di registry.');
        }
        return view('Auth.admin.apps-edit', [
            'active' => 'apps',
            'title' => 'Edit ' . $app['display_name'],
            'app' => $app,
            'slug' => $slug,
        ]);
    }

    public function update(Request $request, string $slug)
    {
        if (!$this->registry->isKnownSlug($slug)) {
            abort(404);
        }

        $data = $request->validate([
            'display_name' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            // Whitelist http/https only — Laravel's default `url` rule accepts
            // schemes like `javascript:` and `file://` which would break the
            // launcher card semantics and pose XSS risk via target=_blank href.
            'external_url' => ['nullable', 'string', 'max:2048', 'regex:/^https?:\\/\\/.+/i'],
            'is_active' => ['nullable'],
            'access_roles' => ['required', 'array', 'min:1'],
            'access_roles.*' => ['string', Rule::in(['mahasiswa', 'dosen', 'admin'])],
        ], [
            'external_url.regex' => 'URL tujuan tidak valid. Gunakan format lengkap, contoh: https://perpustakaan.ut.ac.id/',
            'access_roles.required' => 'Pilih minimal satu hak akses untuk aplikasi ini.',
            'access_roles.min' => 'Pilih minimal satu hak akses untuk aplikasi ini.',
        ]);

        $this->registry->saveOverride($slug, [
            'display_name' => $data['display_name'] ?? null,
            'description'  => $data['description'] ?? null,
            'external_url' => $data['external_url'] ?? null,
            'is_active'    => $request->has('is_active'),
            'access_roles' => $data['access_roles'] ?? null,
        ]);

        return redirect()
            ->route('admin.apps.edit', ['slug' => $slug])
            ->with('success', 'Konfigurasi aplikasi berhasil disimpan.');
    }

    public function toggle(Request $request, string $slug)
    {
        if (!$this->registry->isKnownSlug($slug)) {
            abort(404);
        }
        $current = $this->registry->get($slug);
        $this->registry->setEnabled($slug, !(bool) ($current['is_active'] ?? true));

        return redirect()
            ->back()
            ->with('success', 'Status aktif aplikasi berhasil diperbarui.');
    }
}
