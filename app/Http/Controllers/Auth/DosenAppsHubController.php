<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AppRegistryService;
use Illuminate\Http\Request;

/**
 * Apps Hub Launcher — kartu untuk role Dosen.
 *
 * Dosen tidak punya akses CRUD terhadap aplikasi; hanya melihat launcher
 * yang difilter oleh service `AppRegistryService::activeForUser('dosen')`.
 *
 * Hanya route GET — tidak ada method show() per app. Klik kartu langsung
 * membuka URL eksternal sesuai open_mode (new_tab / same_tab).
 */
class DosenAppsHubController extends Controller
{
    public function __construct(private AppRegistryService $registry)
    {
    }

    public function index(Request $request)
    {
        // Service sudah melakukan filter is_active + role di SQL.
        $items = $this->registry->activeForUser('dosen');

        return view('pages.dosen.apps', [
            'active'       => 'apps',
            'title'        => 'Apps Hub',
            'items'        => $items,
            'visibleTotal' => $items->count(),
        ]);
    }
}
