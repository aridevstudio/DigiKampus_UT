<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('courses', 'tanggal_webinar')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->date('tanggal_webinar')->nullable()->after('kategori');
                $table->time('jam_mulai_webinar')->nullable()->after('tanggal_webinar');
                $table->time('jam_selesai_webinar')->nullable()->after('jam_mulai_webinar');
                $table->unsignedInteger('kuota_peserta')->nullable()->after('jam_selesai_webinar');
            });
        }

        if (!Schema::hasColumn('courses', 'approval_status')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('approval_status', 30)->default('tidak_perlu')->after('status');
                $table->text('approval_notes')->nullable()->after('approval_status');
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_notes');
                $table->timestamp('approved_at')->nullable()->after('approved_by');
                $table->index('approval_status', 'courses_approval_status_index');
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('draft', 'aktif', 'nonaktif', 'selesai') DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'approval_status')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropForeign(['approved_by']);
                $table->dropIndex('courses_approval_status_index');
                $table->dropColumn(['approval_status', 'approval_notes', 'approved_by', 'approved_at']);
            });
        }

        if (Schema::hasColumn('courses', 'tanggal_webinar')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn([
                    'tanggal_webinar',
                    'jam_mulai_webinar',
                    'jam_selesai_webinar',
                    'kuota_peserta',
                ]);
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('draft', 'aktif', 'selesai') DEFAULT 'draft'");
        }
    }
};
