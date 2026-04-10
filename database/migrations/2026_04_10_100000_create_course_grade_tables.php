<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_grade_settings', function (Blueprint $table) {
            $table->id('id_course_grade_setting');
            $table->unsignedBigInteger('id_course')->unique();
            $table->unsignedTinyInteger('pretest_weight')->default(20);
            $table->unsignedTinyInteger('assignment_weight')->default(35);
            $table->unsignedTinyInteger('final_weight')->default(45);
            $table->boolean('has_final_assignment')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamp('draft_saved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
        });

        Schema::create('course_grade_records', function (Blueprint $table) {
            $table->id('id_course_grade_record');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->decimal('pretest_score', 5, 2)->nullable();
            $table->decimal('assignment_score', 5, 2)->nullable();
            $table->decimal('final_assignment_score', 5, 2)->nullable();
            $table->enum('status', ['perlu_review', 'lengkap', 'revisi_tugas'])->default('perlu_review');
            $table->text('note')->nullable();
            $table->timestamp('last_update_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['id_course', 'id_mahasiswa'], 'course_grade_records_course_student_unique');
            $table->index(['id_course', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_grade_records');
        Schema::dropIfExists('course_grade_settings');
    }
};

