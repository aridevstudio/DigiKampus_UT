<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Cleanup migration — menurunkan tabel AI yang ditinggalkan oleh refactor
 * launcher (sebelumnya ada model + controller untuk embedded AI chat).
 *
 * Drops TIDAK dilakukan oleh migrasi awal yang sudah di-stub (yang berisi
 * komentar saja), jadi tabel `ai_conversations`, `ai_messages`,
 * `ai_usage_logs` menjadi orphan di env yang terlanjur pernah migrate.
 * Migration ini menjatuhkannya secara FK-safe berurutan.
 *
 * Hanya perlu dijalankan SATU KALI dan reversible via `up` di-seed ulang
 * dengan isi tabel kosong. `down()` dibuat eksplisit agar developer
 * tidak sengaja resurrect tabel tanpa sengaja.
 */
return new class extends Migration
{
    public function up(): void
    {
        // FK-safe order: drop child tables first.
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
    }

    public function down(): void
    {
        // Recreate schemas sederhana tanpa constraints. Tidak mengembalikan
        // data — hanya struktur skeleton supaya `migrate:rollback` tidak
        // crash jika user ingin kembali ke epoch pre-cleanup.
        Schema::create('ai_conversations', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('user_role')->nullable();
            $table->string('title')->nullable();
            $table->text('system_prompt')->nullable();
            $table->string('model')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_messages', function ($table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id')->index();
            $table->string('role', 32);
            $table->text('content');
            $table->unsignedInteger('tokens_prompt')->nullable();
            $table->unsignedInteger('tokens_completion')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();
            $table->string('model')->nullable();
            $table->string('finish_reason')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_usage_logs', function ($table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id')->nullable()->index();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('tokens_prompt')->nullable();
            $table->unsignedInteger('tokens_completion')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedTinyInteger('http_status')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }
};
