<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id('id_quiz');
            $table->unsignedBigInteger('id_module');
            $table->unsignedBigInteger('id_course');
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->integer('durasi_menit')->default(15);
            $table->boolean('is_pretest')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('passing_score')->default(60); // persentase minimum lulus
            $table->boolean('acak_soal')->default(false);
            $table->boolean('tampilkan_nilai')->default(true);
            $table->integer('urutan')->default(1);
            $table->timestamps();

            $table->foreign('id_module')->references('id_module')->on('course_modules')->cascadeOnDelete();
            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->index(['id_module', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
