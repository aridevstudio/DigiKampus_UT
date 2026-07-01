<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Sumber kebenaran (source of truth) untuk Apps Hub dipindahkan dari
 * `app_registry_overrides` ke tabel baru `apps` (lihat migration
 * `2026_07_11_create_apps_table`). Karena `apps` sudah berisi semua
 * kolom yang sebelumnya ada di overrides + kolom baru (icon, open_mode,
 * url native), tabel overrides menjadi obsolete dan aman untuk di-drop.
 *
 * Data historis: tabel overrides hanya berisi customization dari admin
 * (display_name, description, external_url, is_active, access_roles).
 * Tidak ada data user-generated di sini — aman untuk kehilangan data
 * historis karena apps table adalah lapisan konfigurasi murni.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('app_registry_overrides');
    }

    public function down(): void
    {
        // Recreate skeleton agar rollback aman — ke stage sebelum apps table.
        // Definisi HARUS cocok dengan `2026_07_06_create_app_registry_overrides`
        // + `2026_07_08_add_external_url` (gabungan dua migration).
        Schema::create('app_registry_overrides', function ($table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('access_roles')->nullable();
            $table->timestamps();
            $table->index('slug');
        });
    }
};
