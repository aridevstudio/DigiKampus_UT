<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropForeign(['id_mahasiswa']);
        });

        DB::statement('ALTER TABLE `support_tickets` MODIFY `id_mahasiswa` BIGINT UNSIGNED NULL');

        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets', 'id_dosen')) {
                $table->unsignedBigInteger('id_dosen')->nullable()->after('id_mahasiswa');
            }

            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('id_dosen')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['id_dosen', 'status']);
        });
    }

    public function down(): void
    {
        DB::table('support_tickets')->whereNull('id_mahasiswa')->delete();

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropForeign(['id_mahasiswa']);
            $table->dropForeign(['id_dosen']);
            $table->dropIndex(['id_dosen', 'status']);
            $table->dropColumn('id_dosen');
        });

        DB::statement('ALTER TABLE `support_tickets` MODIFY `id_mahasiswa` BIGINT UNSIGNED NOT NULL');

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
