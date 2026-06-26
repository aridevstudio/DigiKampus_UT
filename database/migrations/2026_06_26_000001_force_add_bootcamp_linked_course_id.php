<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Unconditionally backfill bootcamps.linked_course_id (and its FK) on
     * environments where the conditional 2026_05_17_000003 migration
     * skipped adding it (e.g. demo-digikampus_ut, where the column was
     * never created but the controller still queries it).
     */
    public function up(): void
    {
        // 1. Ensure the table exists at all (safety net for empty/stripped DBs).
        if (!Schema::hasTable('bootcamps')) {
            return;
        }

        // 2. Add the column if it is genuinely missing — no early exit so we
        //    can recover from any DB where it was never created.
        if (!Schema::hasColumn('bootcamps', 'linked_course_id')) {
            Schema::table('bootcamps', function (Blueprint $table) {
                $column = $table->unsignedBigInteger('linked_course_id')->nullable();

                if (Schema::hasColumn('bootcamps', 'published_at')) {
                    $column->after('published_at');
                }
            });
        }

        // 3. Make sure id_course on courses is BIGINT UNSIGNED to match the FK.
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'id_course')) {
            try {
                if (Schema::getConnection()->getDriverName() === 'mysql') {
                    DB::statement('ALTER TABLE courses MODIFY id_course BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
                }
            } catch (Throwable $exception) {
                // Keep migration non-destructive if the column can't be altered.
            }
        }

        // 4. Add the FK once we know the column is in place.
        if (
            Schema::hasColumn('bootcamps', 'linked_course_id')
            && !$this->foreignKeyExists('bootcamps_linked_course_id_foreign')
            && !$this->foreignKeyExistsOnColumn('bootcamps', 'linked_course_id')
        ) {
            Schema::table('bootcamps', function (Blueprint $table) {
                $table->foreign('linked_course_id', 'bootcamps_linked_course_id_foreign')
                    ->references('id_course')
                    ->on('courses')
                    ->nullOnDelete();
            });
        }

        // 5. Make sure an index exists for fast (linked_course_id) lookups in
        //    the cart flow — the FK already creates one, this is idempotent.
        if (Schema::hasColumn('bootcamps', 'linked_course_id')) {
            try {
                if (Schema::getConnection()->getDriverName() === 'mysql') {
                    $database = DB::getDatabaseName();
                    $indexExists = DB::selectOne(
                        'select index_name from information_schema.statistics where table_schema = ? and table_name = ? and column_name = ? and seq_in_index = 1 limit 1',
                        [$database, 'bootcamps', 'linked_course_id']
                    );
                    if (!$indexExists) {
                        DB::statement('CREATE INDEX bootcamps_linked_course_id_index ON bootcamps (linked_course_id)');
                    }
                }
            } catch (Throwable $exception) {
                // Index is optional; FK already covers lookups.
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('bootcamps')) {
            return;
        }

        if (!Schema::hasColumn('bootcamps', 'linked_course_id')) {
            return;
        }

        Schema::table('bootcamps', function (Blueprint $table) {
            if ($this->foreignKeyExists('bootcamps_linked_course_id_foreign')) {
                $table->dropForeign('bootcamps_linked_course_id_foreign');
            }
        });

        try {
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                DB::statement('DROP INDEX bootcamps_linked_course_id_index ON bootcamps');
            }
        } catch (Throwable $exception) {
            // Index might not exist; ignore.
        }

        Schema::table('bootcamps', function (Blueprint $table) {
            $table->dropColumn('linked_course_id');
        });
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return false;
        }

        $database = DB::getDatabaseName();
        $result = DB::selectOne(
            'select constraint_name from information_schema.table_constraints where constraint_schema = ? and constraint_type = ? and constraint_name = ? limit 1',
            [$database, 'FOREIGN KEY', $constraintName]
        );

        return $result !== null;
    }

    private function foreignKeyExistsOnColumn(string $table, string $column): bool
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return false;
        }

        $database = DB::getDatabaseName();
        $result = DB::selectOne(
            'select kcu.constraint_name from information_schema.key_column_usage kcu inner join information_schema.table_constraints tc on tc.constraint_schema = kcu.constraint_schema and tc.table_name = kcu.table_name and tc.constraint_name = kcu.constraint_name where kcu.constraint_schema = ? and kcu.table_name = ? and kcu.column_name = ? and tc.constraint_type = ? limit 1',
            [$database, $table, $column, 'FOREIGN KEY']
        );

        return $result !== null;
    }
};
