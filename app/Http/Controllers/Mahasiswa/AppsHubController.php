<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LauncherApp;
use App\Services\AppRegistryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Apps Hub Launcher — kartu yang ketika di-klik membuka URL eksternal.
 *
 * Tidak ada lagi method show() per app — klik kartu langsung membuka URL
 * (new_tab atau same_tab sesuai setting di DB).
 */
class AppsHubController extends Controller
{
    public function __construct(private AppRegistryService $registry)
    {
    }

    public function index(Request $request)
    {
        // Resolve role user dari guard aktif.
        $user = Auth::guard('mahasiswa')->user();
        $userRole = $user?->role ?? 'mahasiswa';

        // Service sudah melakukan filter is_active + role di SQL.
        $items = $this->registry->activeForUser($userRole);

        $totalAll = LauncherApp::query()->count();

        return view('pages.mahasiswa.apps', [
            'active'       => 'apps',
            'title'        => 'Apps Hub',
            'items'        => $items,
            'visibleTotal' => $items->count(),
            'totalApps'    => $totalAll,
        ]);
    }
}
