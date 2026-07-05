<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCT BEHAVIOR
 *  Event mode "Online" wajib menyimpan meeting link utama di level course.
 *  Sebelumnya link meeting disimpan di `youtube_playlist` (hack legacy utk
 *  webinar) atau per-sesi di `bootcamp_sessions.link_zoom/link_meet`. Kolom
 *  baru ini menjadi rujukan resmi untuk event online course-level.
 *
 * BACKWARD-COMPATIBLE
 *  - Kolom NULLABLE → tidak mengubah shape table.
 *  - Tidak menyentuh kolom lama (youtube_playlist, lokasi_event, dll).
 *  - Controller/view fallback ke `youtube_playlist` jika `online_link` kosong,
 *    sehingga course legacy tidak rusak.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'online_link')) {
                $table->string('online_link', 500)->nullable()->after('youtube_playlist');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'online_link')) {
                $table->dropColumn('online_link');
            }
        });
    }
};
