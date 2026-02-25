<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_dosen')->nullable()->after('id_mahasiswa');
            $table->unsignedBigInteger('id_course')->nullable()->after('id_dosen');

            $table->index(['id_dosen', 'tanggal'], 'agendas_dosen_tanggal_index');
            $table->index('id_course', 'agendas_id_course_index');

            $table->foreign('id_dosen')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('id_course')
                ->references('id_course')
                ->on('courses')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropForeign(['id_dosen']);
            $table->dropForeign(['id_course']);
            $table->dropIndex('agendas_dosen_tanggal_index');
            $table->dropIndex('agendas_id_course_index');
            $table->dropColumn(['id_dosen', 'id_course']);
        });
    }
};

