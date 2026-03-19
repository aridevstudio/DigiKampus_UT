<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE enrollments MODIFY status ENUM('pending', 'aktif', 'in_progress', 'selesai') NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        DB::table('enrollments')
            ->whereIn('status', ['pending', 'in_progress'])
            ->update(['status' => 'aktif']);

        DB::statement("ALTER TABLE enrollments MODIFY status ENUM('aktif', 'selesai') NOT NULL DEFAULT 'aktif'");
    }
};
