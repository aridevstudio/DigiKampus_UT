<?php

use Illuminate\Database\Migrations\Migration;

/**
 * No-op stub — modul AI Assistant dihapus per refactor PM (Apps = launcher).
 *
 * Awalnya migration ini membuat tabel `ai_conversations`. Sekarang sengaja
 * tidak membuat apa-apa agar fresh install tidak punya tabel orphan.
 *
 * Cleanup tabel yang terlanjur ada di-jatuhkan oleh migration berikutnya:
 * `2026_07_09_000000_drop_ai_tables.php` (FK-safe ORDER).
 *
 * Class harus berupa **named class** (bukan anonymous return) supaya Laravel
 * dapat me-resolve `CreateAiConversationsTable` dari nama file. Sebelumnya
 * file ini berisi anonymous-class return + kosong; artisan migrate gagal
 * dengan "Class CreateAiConversationsTable not found".
 *
 * CATATAN rollback: `down()` juga no-op. Jika perlu rollback sebelum drop
 * migration berjalan, gunakan `migrate:fresh`, bukan `migrate:rollback`.
 */
class CreateAiConversationsTable extends Migration
{
    public function up(): void
    {
        // Intentional no-op — lihat class docblock.
    }

    public function down(): void
    {
        // Intentional no-op — rollback untuk cleanup ditangani di drop migration.
    }
}
