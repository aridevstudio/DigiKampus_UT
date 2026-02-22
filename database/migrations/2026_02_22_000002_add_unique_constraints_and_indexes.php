<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * P1 Fix: Add unique constraints and indexes to prevent data corruption
 * and improve query performance.
 *
 * Missing constraints found in audit:
 * - enrollments: no UNIQUE on (id_mahasiswa, id_course) → double enrollment
 * - carts: no UNIQUE on (id_mahasiswa, id_course) → duplicate cart entries
 * - favorites: no UNIQUE on (id_mahasiswa, id_course) → duplicate favorites
 * - course_ratings: no UNIQUE on (id_mahasiswa, id_course) → double ratings
 * - material_progress: no UNIQUE on (id_mahasiswa, id_material) → progress duplication
 *
 * Missing indexes:
 * - profiles.nim → used for mahasiswa login (full table scan without index)
 * - courses.id_dosen → dosen's course listings
 * - courses.status → active course filtering
 * - enrollments(id_mahasiswa, id_course) → enrollment lookups
 * - messages(id_sender, id_receiver) → conversation queries
 */
return new class extends Migration {
    public function up(): void
    {
        // Add unique constraints
        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['id_mahasiswa', 'id_course'], 'enrollments_unique_student_course');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['id_mahasiswa', 'id_course'], 'carts_unique_student_course');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->unique(['id_mahasiswa', 'id_course'], 'favorites_unique_student_course');
        });

        Schema::table('course_ratings', function (Blueprint $table) {
            $table->unique(['id_mahasiswa', 'id_course'], 'course_ratings_unique_student_course');
        });

        Schema::table('material_progress', function (Blueprint $table) {
            $table->unique(['id_mahasiswa', 'id_material'], 'material_progress_unique_student_material');
        });

        // Add performance indexes
        Schema::table('profiles', function (Blueprint $table) {
            $table->index('nim', 'profiles_nim_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index('id_dosen', 'courses_id_dosen_index');
            $table->index('status', 'courses_status_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['id_sender', 'id_receiver'], 'messages_sender_receiver_index');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('enrollments_unique_student_course');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('carts_unique_student_course');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('favorites_unique_student_course');
        });

        Schema::table('course_ratings', function (Blueprint $table) {
            $table->dropUnique('course_ratings_unique_student_course');
        });

        Schema::table('material_progress', function (Blueprint $table) {
            $table->dropUnique('material_progress_unique_student_material');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex('profiles_nim_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_id_dosen_index');
            $table->dropIndex('courses_status_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_sender_receiver_index');
        });
    }
};
