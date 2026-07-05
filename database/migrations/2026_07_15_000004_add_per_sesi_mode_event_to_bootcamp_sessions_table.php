<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCT BEHAVIOR
 *  Mode event (Online/Offline) sebelumnya hanya ada di level course
 *  (lihat migration 2026_07_15_000001). Spec 2026-07-15 mewajibkan mode
 *  event bisa ditentukan PER SESI sehingga admin dapat mencampur sesi
 *  online dan offline dalam satu bootcamp.
 *
 *  Field baru per sesi:
 *    mode_event         online|offline (default 'online', override course-level)
 *    lokasi_event       alamat lokasi (khusus sesi offline)
 *    peta_event         link Google Maps / share location
 *    kapasitas_sesi     slot & anti-overbook per sesi (khusus offline)
 *
 * BACKWARD-COMPATIBLE
 *  - Semua kolom NULLABLE / memiliki default → tidak ada perubahan shape.
 *  - Sesi yang dibuat SEBELUM migration ini akan dianggap 'online' dan tetap
 *    memakai link_zoom/link_meet seperti biasa (lihat AccessMode::fromNullable).
 *  - Tidak menyentuh kolom mode_event/lokasi_event di tabel courses.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('bootcamp_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('bootcamp_sessions', 'mode_event')) {
                $table->string('mode_event', 24)->default('online')->after('jam_selesai');
                $table->index('mode_event', 'bootcamp_sessions_mode_event_index');
            }
            if (!Schema::hasColumn('bootcamp_sessions', 'lokasi_event')) {
                $table->string('lokasi_event', 255)->nullable()->after('mode_event');
            }
            if (!Schema::hasColumn('bootcamp_sessions', 'peta_event')) {
                $table->string('peta_event', 500)->nullable()->after('lokasi_event');
            }
            if (!Schema::hasColumn('bootcamp_sessions', 'kapasitas_sesi')) {
                $table->unsignedInteger('kapasitas_sesi')->nullable()->after('peta_event');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bootcamp_sessions', function (Blueprint $table) {
            $cols = ['mode_event', 'lokasi_event', 'peta_event', 'kapasitas_sesi'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bootcamp_sessions', $col)) {
                    $table->dropColumn($col);
                }
            }
            try {
                $table->dropIndex('bootcamp_sessions_mode_event_index');
            } catch (\Throwable) {
                // ignore
            }
        });
    }
};
