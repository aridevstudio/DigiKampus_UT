<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCT BEHAVIOR
 *  Setiap sesi live-class pada bootcamp (kategori='tiket') meminta mahasiswa
 *  untuk mengunggah bukti kehadiran. Bukti diverifikasi admin/dosen →
 *  status 'verified' | 'rejected' | 'pending'.
 *
 * SCHEMA DESIGN
 *  • Tidak ada kolom `id_material`. Sesi live-class tidak terikat 1:1 ke
 *    CourseMaterial; mereka adalah slot waktu hasil penjadwalan dinamis dari
 *    Course.tanggal_webinar / CourseModule. Identifikasi sesi memakai
 *    `session_key` (string) yang konkatenasi di CourseController:
 *      - 'primary' untuk sesi utama bootcamp
 *      - 'qa_<course_module.id>_<start_iso>' untuk sesi Q&A per-modul
 *  • Unique index (id_user, id_course, session_key) mencegah double-upload
 *    lewat request berulang.
 *  • Kolom bukti/upload disimpan via Storage::disk('public').
 *
 * NON-BREAKING
 *  Tabel baru, tidak menyentuh schema assignment/quiz/live-class yang ada.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('bootcamp_live_class_attendances', function (Blueprint $table) {
            $table->bigIncrements('id_bootcamp_live_class_attendance');

            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_course');
            $table->string('session_key', 120);

            $table->string('proof_file', 255);
            $table->text('catatan_mahasiswa')->nullable();

            $table->string('status', 16)->default('pending');
            $table->text('catatan_reviewer')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->unique(['id_user', 'id_course', 'session_key'], 'blca_user_course_session_unique');
            $table->index(['id_course', 'status'], 'blca_course_status_index');

            $table->foreign('id_user')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bootcamp_live_class_attendances', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_course']);
            $table->dropForeign(['reviewed_by']);
        });

        Schema::dropIfExists('bootcamp_live_class_attendances');
    }
};
