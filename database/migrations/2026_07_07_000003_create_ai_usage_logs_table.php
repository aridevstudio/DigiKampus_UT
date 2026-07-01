<?php

use Illuminate\Database\Migrations\Migration;

/**
 * No-op stub — lihat C-000001 untuk konteks. Modul AI Assistant dihapus.
 * Tabel `ai_usage_logs` tidak lagi dibuat; orphan cleanup ada di drop migration.
 * Harus named-class (bukan anonymous) untuk kompatibilitas dengan Laravel's
 * filename → class-name resolver.
 *
 * CATATAN rollback: `down()` juga no-op — gunakan `migrate:fresh` jika perlu
 * rollback sebelum drop migration berjalan.
 */
class CreateAiUsageLogsTable extends Migration
{
    public function up(): void
    {
        // Intentional no-op.
    }

    public function down(): void
    {
        // Intentional no-op.
    }
}
