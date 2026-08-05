<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE agendas MODIFY id_mahasiswa BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fallbackUserId = (int) (DB::table('users')->min('id') ?? 1);

        DB::table('agendas')
            ->whereNull('id_mahasiswa')
            ->update(['id_mahasiswa' => $fallbackUserId]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE agendas MODIFY id_mahasiswa BIGINT UNSIGNED NOT NULL');
        }
    }
};

