<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * P0 Security Fix: Add OTP attempt tracking to prevent brute-force attacks.
 *
 * The password_reset_tokens table previously had no attempt counting.
 * With a 4-digit OTP and no rate limiting, an attacker could brute-force
 * all 10,000 combinations. This migration adds:
 * - `attempts` column for server-side lockout after 5 failed attempts
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->default(0)->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('attempts');
        });
    }
};
