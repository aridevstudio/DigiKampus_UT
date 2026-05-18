<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('external_mentors')) {
            Schema::create('external_mentors', function (Blueprint $table) {
                $table->id('id_external_mentor');
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('expertise')->nullable();
                $table->string('institution')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['status', 'name']);
            });
        } else {
            Schema::table('external_mentors', function (Blueprint $table) {
                if (!Schema::hasColumn('external_mentors', 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn('external_mentors', 'email')) {
                    $table->string('email')->nullable();
                }
                if (!Schema::hasColumn('external_mentors', 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (!Schema::hasColumn('external_mentors', 'expertise')) {
                    $table->string('expertise')->nullable();
                }
                if (!Schema::hasColumn('external_mentors', 'institution')) {
                    $table->string('institution')->nullable();
                }
                if (!Schema::hasColumn('external_mentors', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }
                if (!Schema::hasColumn('external_mentors', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('external_mentors', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('external_mentors', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        Schema::table('bootcamps', function (Blueprint $table) {
            if (!Schema::hasColumn('bootcamps', 'price')) {
                $table->unsignedBigInteger('price')->default(0)->after('price_label');
            }
            if (!Schema::hasColumn('bootcamps', 'seat_capacity')) {
                $table->unsignedInteger('seat_capacity')->default(0)->after('seats_label');
            }
            if (!Schema::hasColumn('bootcamps', 'schedule_date')) {
                $table->date('schedule_date')->nullable()->after('schedule_label');
            }
            if (!Schema::hasColumn('bootcamps', 'start_time')) {
                $table->time('start_time')->nullable()->after('schedule_date');
            }
            if (!Schema::hasColumn('bootcamps', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('bootcamps', 'sales_opened_at')) {
                $table->timestamp('sales_opened_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bootcamps', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('sales_opened_at');
            }
            if (!Schema::hasColumn('bootcamps', 'linked_course_id')) {
                $table->unsignedBigInteger('linked_course_id')->nullable()->after('published_at');
            }
        });

        if (
            Schema::hasColumn('bootcamps', 'linked_course_id')
            && ! $this->foreignKeyExists('bootcamps_linked_course_id_foreign')
            && ! $this->foreignKeyExistsOnColumn('bootcamps', 'linked_course_id')
        ) {
            Schema::table('bootcamps', function (Blueprint $table) {
                $table->foreign('linked_course_id', 'bootcamps_linked_course_id_foreign')
                    ->references('id_course')
                    ->on('courses')
                    ->nullOnDelete();
            });
        }

        Schema::table('bootcamp_mentors', function (Blueprint $table) {
            if (!Schema::hasColumn('bootcamp_mentors', 'mentor_type')) {
                $table->enum('mentor_type', ['internal', 'external'])->default('internal')->after('id_bootcamp');
            }
            if (!Schema::hasColumn('bootcamp_mentors', 'id_external_mentor')) {
                $table->unsignedBigInteger('id_external_mentor')->nullable()->after('id_user');
            }
        });

        if (Schema::hasColumn('bootcamp_mentors', 'id_user')) {
            try {
                if (Schema::getConnection()->getDriverName() === 'mysql') {
                    DB::statement('ALTER TABLE bootcamp_mentors MODIFY id_user BIGINT UNSIGNED NULL');
                }
            } catch (Throwable $exception) {
                // Keep migration non-destructive if the platform already changed the column.
            }
        }

        if (
            Schema::hasColumn('bootcamp_mentors', 'id_external_mentor')
            && ! $this->foreignKeyExists('bootcamp_mentors_external_mentor_foreign')
            && ! $this->foreignKeyExistsOnColumn('bootcamp_mentors', 'id_external_mentor')
        ) {
            Schema::table('bootcamp_mentors', function (Blueprint $table) {
                $table->foreign('id_external_mentor', 'bootcamp_mentors_external_mentor_foreign')
                    ->references('id_external_mentor')
                    ->on('external_mentors')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('bootcamps', 'price') && Schema::hasColumn('bootcamps', 'price_label')) {
            DB::table('bootcamps')->orderBy('id_bootcamp')->each(function ($bootcamp) {
                $price = (int) preg_replace('/\D+/', '', (string) ($bootcamp->price_label ?? '0'));
                $seatCapacity = 0;
                if (preg_match('/\d+\s*\/\s*(\d+)/', (string) ($bootcamp->seats_label ?? ''), $matches)) {
                    $seatCapacity = (int) $matches[1];
                }

                DB::table('bootcamps')
                    ->where('id_bootcamp', $bootcamp->id_bootcamp)
                    ->update([
                        'price' => $price,
                        'seat_capacity' => $seatCapacity,
                    ]);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bootcamp_mentors', 'id_external_mentor')) {
            Schema::table('bootcamp_mentors', function (Blueprint $table) {
                if ($this->foreignKeyExists('bootcamp_mentors_external_mentor_foreign')) {
                    $table->dropForeign('bootcamp_mentors_external_mentor_foreign');
                }
                $table->dropColumn('id_external_mentor');
            });
        }

        if (Schema::hasColumn('bootcamp_mentors', 'mentor_type')) {
            Schema::table('bootcamp_mentors', function (Blueprint $table) {
                $table->dropColumn('mentor_type');
            });
        }

        if (Schema::hasColumn('bootcamps', 'linked_course_id')) {
            Schema::table('bootcamps', function (Blueprint $table) {
                if ($this->foreignKeyExists('bootcamps_linked_course_id_foreign')) {
                    $table->dropForeign('bootcamps_linked_course_id_foreign');
                }
                $table->dropColumn('linked_course_id');
            });
        }

        Schema::table('bootcamps', function (Blueprint $table) {
            $columns = [];
            foreach (['price', 'seat_capacity', 'schedule_date', 'start_time', 'end_time', 'sales_opened_at', 'published_at'] as $column) {
                if (Schema::hasColumn('bootcamps', $column)) {
                    $columns[] = $column;
                }
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('external_mentors');
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
