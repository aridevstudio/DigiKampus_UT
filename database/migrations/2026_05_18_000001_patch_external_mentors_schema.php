<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            });

            return;
        }

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

    public function down(): void
    {
        // This migration is a production schema patch. Do not drop columns that
        // may already contain external mentor data.
    }
};
