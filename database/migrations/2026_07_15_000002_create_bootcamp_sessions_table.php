<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCT BEHAVIOR
 *  Setiap event bootcamp-style (tipe_event ∈ bootcamp|webinar|workshop|seminar)
 *  dapat memiliki satu atau beberapa sesi terjadwal. Sesi menentukan:
 *
 *    - judul, tanggal, jam_mulai/selesai
 *    - link Zoom / Google Meet (wajib utk bootcamp/webinar online)
 *    - link rekaman (opsional, diisi setelah sesi berakhir)
 *    - materi sesi (file upload atau URL)
 *    - status aktif/non-aktif (admin toggle)
 *
 * BACKWARD-COMPATIBLE
 *  - Kursus yang dibuat SEBELUM migration ini tetap valid karena
 *    {@see \App\Services\BootcampFlowService::resolveActiveSessions()}
 *    mem-virtualisasi 1 sesi default dari Course.tanggal_webinar/jam_*
 *    bila tidak ada baris di tabel ini.
 *  - Tidak menyentuh bootcamp_live_class_attendances; sesi baru di-
 *    kontribusikan ke tabel attendance lewat session_key='sesi_<id>'
 *    (lihat {@see \App\Models\BootcampSession::sessionKey()}).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('bootcamp_sessions', function (Blueprint $table) {
            $table->bigIncrements('id_bootcamp_session');

            $table->unsignedBigInteger('id_course');

            $table->string('judul_sesi', 120);
            $table->date('tanggal_sesi');
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();

            $table->string('link_zoom', 500)->nullable();
            $table->string('link_meet', 500)->nullable();
            $table->string('link_rekaman', 500)->nullable();

            $table->string('materi_file', 255)->nullable();
            $table->string('materi_url', 500)->nullable();

            $table->text('deskripsi_sesi')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['id_course', 'urutan'], 'bootcamp_sessions_course_urutan_index');
            $table->index(['id_course', 'tanggal_sesi'], 'bootcamp_sessions_course_date_index');
            $table->index(['id_course', 'is_active'], 'bootcamp_sessions_course_active_index');

            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bootcamp_sessions', function (Blueprint $table) {
            $table->dropForeign(['id_course']);
        });
        Schema::dropIfExists('bootcamp_sessions');
    }
};
