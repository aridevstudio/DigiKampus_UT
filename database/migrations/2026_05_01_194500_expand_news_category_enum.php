<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE news SET kategori = 'keuangan' WHERE kategori = 'keungan'");

        DB::statement("
            ALTER TABLE news
            MODIFY kategori ENUM(
                'pengumuman',
                'berita',
                'event',
                'umum',
                'akademik',
                'keuangan',
                'registrasi',
                'kemahasiswaan'
            ) NOT NULL DEFAULT 'pengumuman'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE news
            SET kategori = 'pengumuman'
            WHERE kategori IN ('umum', 'akademik', 'keuangan', 'registrasi', 'kemahasiswaan')
        ");

        DB::statement("
            ALTER TABLE news
            MODIFY kategori ENUM(
                'pengumuman',
                'berita',
                'event'
            ) NOT NULL DEFAULT 'pengumuman'
        ");
    }
};
