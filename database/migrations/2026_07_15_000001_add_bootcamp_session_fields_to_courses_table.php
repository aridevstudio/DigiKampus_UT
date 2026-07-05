<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCT BEHAVIOR
 *  Kursus (kategori='tiket' / bootcamp-style) mendapat kolom tambahan untuk
 *  dukung struktur event berbasis sesi:
 *
 *    tipe_event          bootcamp|webinar|workshop|seminar (format operasional)
 *    mode_event          online|onsite|hybrid (penyampaian acara)
 *    lokasi_event        untuk onsite (alamat lokasi)
 *    peta_event          link Google Maps / share location
 *    kapasitas_maksimal  slot & anti-overbook (opsional, wajib utk seminar onsite)
 *    slot_terisi         counter enrollment aktual
 *    checkin_required    onsite-required check-in toggle
 *
 * BACKWARD COMPATIBILITY
 *  - Semua kolom NULLABLE / memiliki default → tidak ada perubahan shape.
 *  - Tidak menyentuh kategori/kategori_id/tanggal_webinar/jam_*_webinar/
 *    kuota_peserta yang sudah ada → legacy bootcamp tetap valid (akan
 *    divirtualisasi sebagai 1 sesi default di BootcampFlowService).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'tipe_event')) {
                $table->string('tipe_event', 24)->default('bootcamp')->after('kategori');
                $table->index('tipe_event', 'courses_tipe_event_index');
            }

            if (!Schema::hasColumn('courses', 'mode_event')) {
                $table->string('mode_event', 24)->default('online')->after('tipe_event');
            }

            if (!Schema::hasColumn('courses', 'lokasi_event')) {
                $table->string('lokasi_event', 255)->nullable()->after('mode_event');
            }

            if (!Schema::hasColumn('courses', 'peta_event')) {
                $table->string('peta_event', 500)->nullable()->after('lokasi_event');
            }

            if (!Schema::hasColumn('courses', 'kapasitas_maksimal')) {
                $table->unsignedInteger('kapasitas_maksimal')->nullable()->after('kuota_peserta');
            }

            if (!Schema::hasColumn('courses', 'slot_terisi')) {
                $table->unsignedInteger('slot_terisi')->default(0)->after('kapasitas_maksimal');
            }

            if (!Schema::hasColumn('courses', 'checkin_required')) {
                $table->boolean('checkin_required')->default(false)->after('slot_terisi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $cols = [
                'tipe_event', 'mode_event', 'lokasi_event', 'peta_event',
                'kapasitas_maksimal', 'slot_terisi', 'checkin_required',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('courses', $col)) {
                    $table->dropColumn($col);
                }
            }
            // index drop if exists
            try {
                $table->dropIndex('courses_tipe_event_index');
            } catch (\Throwable) {
                // ignore
            }
        });
    }
};
