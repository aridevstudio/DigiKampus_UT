<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `apps` adalah single source of truth untuk menu Apps Hub.
 *
 * Sebelumnya apps ditentukan oleh `AppRegistryService::CATALOG` (hardcoded)
 * dengan `app_registry_overrides` sebagai layer kustomisasi admin.
 * Setelah refactor ini:
 *   - Tidak ada hardcoded catalog di code
 *   - Admin punya CRUD penuh via panel admin
 *   - Override tabel lama di-drop oleh migration `2026_07_12_*`
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);
            $table->string('slug', 64)->unique(); // folder-safe, lowercase, hyphenated

            $table->string('description', 2000)->nullable();

            // Nullable URL — kosong = kartu disabled ("Aplikasi belum tersedia.")
            // Validasi http/https dilakukan di FormRequest (defense-in-depth).
            $table->string('url', 2048)->nullable();

            // Catalog icon terbatas (8 jenis) — lihat AppRegistryService::ICONS.
            // Tidak menerima SVG kustom untuk menghindari injeksi / sanitization overhead.
            $table->string('icon', 32)->default('globe');

            $table->boolean('is_active')->default(true);

            // JSON array: salah satu / kombinasi dari [mahasiswa, dosen, admin, all].
            // "all" = wildcard — cek di service layer (lihat AppRegistryService).
            $table->json('allowed_roles')->nullable();

            // "new_tab" (target=_blank rel=noopener noreferrer) atau
            // "same_tab" (link biasa, tidak membuka tab baru).
            $table->string('open_mode', 16)->default('new_tab');

            $table->timestamps();

            // Index untuk query launcher (filter aktif + urutkan).
            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
