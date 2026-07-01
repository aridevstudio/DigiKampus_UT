<?php

namespace App\Models;

use RuntimeException;

/**
 * STUB — AppRegistryOverride sudah deprecated.
 *
 * Tabel `app_registry_overrides` di-drop oleh migration
 * `2026_07_12_drop_app_registry_overrides_table.php`. Sumber kebenaran
 * tunggal untuk Apps Hub adalah tabel `apps` (model LauncherApp).
 *
 * Class declaration sengaja dipertahankan agar PSR-4 autoloader tetap
 * punya target — jika ada referensi lama yang lolos review karena lupa
 * di-rewrite, instantiate class ini akan throw exception loud dengan
 * pesan yang mengarah ke LauncherApp (lebih baik daripada error samar
 * "class not found" dari autoloader).
 *
 * Setelah `composer dump-autoload` dijalankan di deployment berikutnya,
 * referensi ke class ini akan dibersihkan oleh IDE / classmap statis.
 */
class AppRegistryOverride
{
    public function __construct()
    {
        throw new RuntimeException(
            'App\Models\AppRegistryOverride is deprecated. AppRegistryService sudah fully DB-driven. ' .
            'Gunakan App\Models\LauncherApp dari tabel apps, dan hapus semua reference ke AppRegistryOverride. ' .
            'Lihat migration 2026_07_12_* dan App\Services\AppRegistryService untuk konteks refactor.'
        );
    }
}
