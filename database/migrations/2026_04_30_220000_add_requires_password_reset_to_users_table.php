<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'requires_password_reset')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('requires_password_reset')
                    ->default(false)
                    ->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'requires_password_reset')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('requires_password_reset');
            });
        }
    }
};
