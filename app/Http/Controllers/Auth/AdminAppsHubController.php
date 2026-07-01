<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AppRegistryService;
use Illuminate\Http\Request;

/**
 * Apps Hub Launcher — kartu untuk role Admin (TIDAK untuk CRUD).
 *
 * Berbeda dengan `AdminAppsController` yang mengurus create/edit/delete
 * aplikasi, controller ini hanya menampilkan versi launcher agar admin
 * bisa men-test aplikasi yang dia konfigurasi.
 *
 * Klik kartu membuka URL eksternal sesuai open_mode (new_tab / same_tab).
 */
class AdminAppsHubController extends Controller
{
    public function __construct(private AppRegistryService $registry)
    {
    }

    public function index(Request $request)
    {
        $items = $this->registry->activeForUser('admin');

        return view('pages.admin.apps-hub', [
            'active'       => 'apps-hub',
            'title'        => 'Apps Hub',
            'items'        => $items,
            'visibleTotal' => $items->count(),
        ]);
    }
}
