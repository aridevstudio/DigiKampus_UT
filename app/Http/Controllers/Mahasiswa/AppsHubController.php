<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\AppRegistryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppsHubController extends Controller
{
    public function __construct(private AppRegistryService $registry)
    {
    }

    /**
     * Halaman Apps Hub — menampilkan kartu-kartu sebagai launcher.
     * Klik kartu membuka URL eksternal di tab baru (target=_blank).
     * Tidak ada lagi method show() karena tidak ada per-app Blade view.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();
        $items = $this->registry->all();

        $visible = [];
        foreach ($items as $slug => $entry) {
            if (!($entry['is_active'] ?? true)) {
                continue;
            }
            if (!$this->userHasAccess($entry, $user)) {
                continue;
            }
            $visible[$slug] = $entry;
        }

        $grouped = [];
        foreach ($visible as $slug => $entry) {
            $cat = $entry['category'] ?? 'Lainnya';
            $grouped[$cat][] = array_merge(['key' => $slug], $entry);
        }
        uksort($grouped, function ($a, $b) {
            $oa = AppRegistryService::CATEGORY_ORDER[$a] ?? 999;
            $ob = AppRegistryService::CATEGORY_ORDER[$b] ?? 999;
            return $oa <=> $ob;
        });

        return view('pages.mahasiswa.apps', [
            'active'       => 'apps',
            'title'        => 'Apps Hub',
            'categoryApps' => $grouped,
            'catalogTotal' => count($this->registry->all()),
            'visibleTotal' => count($visible),
        ]);
    }

    private function userHasAccess(array $app, $user): bool
    {
        $roles = $app['access_roles'] ?? ['mahasiswa'];
        if (!is_array($roles)) {
            $roles = ['mahasiswa'];
        }
        if (in_array('mahasiswa', $roles, true)) {
            return true;
        }
        if ($user && method_exists($user, 'getAttribute')) {
            $role = $user->getAttribute('role');
            return in_array($role, $roles, true);
        }
        return false;
    }
}
