<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('profiles', 'nim') && !Schema::hasColumn('profiles', 'nomor_induk')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->renameColumn('nim', 'nomor_induk');
            });
        }

        $driver = DB::getDriverName();

        if (!Schema::hasColumn('profiles', 'nomor_induk')) {
            return;
        }

        if ($driver === 'mysql') {
            if ($this->mysqlIndexExists('profiles', 'profiles_nim_index')) {
                DB::statement('ALTER TABLE profiles DROP INDEX profiles_nim_index');
            }

            if (!$this->mysqlIndexExists('profiles', 'profiles_nomor_induk_index')) {
                DB::statement('ALTER TABLE profiles ADD INDEX profiles_nomor_induk_index (nomor_induk)');
            }

            DB::statement('ALTER TABLE profiles MODIFY nomor_induk VARCHAR(50) NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('profiles', 'nomor_induk') && Schema::hasColumn('profiles', 'nim')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql' && Schema::hasColumn('profiles', 'nomor_induk')) {
            if ($this->mysqlIndexExists('profiles', 'profiles_nomor_induk_index')) {
                DB::statement('ALTER TABLE profiles DROP INDEX profiles_nomor_induk_index');
            }

            if (!$this->mysqlIndexExists('profiles', 'profiles_nim_index')) {
                DB::statement('ALTER TABLE profiles ADD INDEX profiles_nim_index (nomor_induk)');
            }
        }

        if (Schema::hasColumn('profiles', 'nomor_induk') && !Schema::hasColumn('profiles', 'nim')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->renameColumn('nomor_induk', 'nim');
            });
        }

        if ($driver === 'mysql' && Schema::hasColumn('profiles', 'nim')) {
            DB::statement('ALTER TABLE profiles MODIFY nim INT NULL');
        }
    }

    private function mysqlIndexExists(string $table, string $indexName): bool
    {
        $rows = DB::select("SHOW INDEX FROM `{$table}`");

        foreach ($rows as $row) {
            if (($row->Key_name ?? null) === $indexName) {
                return true;
            }
        }

        return false;
    }
};
