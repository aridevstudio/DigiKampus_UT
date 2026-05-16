<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automatic_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('automatic_certificates', 'id_mahasiswa')) {
                $table->unsignedBigInteger('id_mahasiswa')->nullable()->after('certificate_template_id')->index();
            }

            if (!Schema::hasColumn('automatic_certificates', 'id_course')) {
                $table->unsignedBigInteger('id_course')->nullable()->after('id_mahasiswa')->index();
            }

            if (!Schema::hasColumn('automatic_certificates', 'source')) {
                $table->string('source', 30)->default('manual')->after('tanggal_terbit')->index();
            }
        });

        Schema::table('automatic_certificates', function (Blueprint $table) {
            $table->unique(['id_mahasiswa', 'id_course'], 'automatic_certificates_mahasiswa_course_unique');
        });
    }

    public function down(): void
    {
        Schema::table('automatic_certificates', function (Blueprint $table) {
            $table->dropUnique('automatic_certificates_mahasiswa_course_unique');

            if (Schema::hasColumn('automatic_certificates', 'source')) {
                $table->dropColumn('source');
            }

            if (Schema::hasColumn('automatic_certificates', 'id_course')) {
                $table->dropColumn('id_course');
            }

            if (Schema::hasColumn('automatic_certificates', 'id_mahasiswa')) {
                $table->dropColumn('id_mahasiswa');
            }
        });
    }
};
