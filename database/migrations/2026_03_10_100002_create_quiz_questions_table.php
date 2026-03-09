<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id('id_question');
            $table->unsignedBigInteger('id_quiz');
            $table->text('pertanyaan');
            $table->enum('tipe', ['pilihan_ganda', 'benar_salah'])->default('pilihan_ganda');
            $table->json('opsi')->nullable(); // array of option strings
            $table->string('jawaban_benar', 255); // index (0,1,2,3) atau "true"/"false"
            $table->integer('bobot')->default(10); // poin per soal
            $table->text('penjelasan')->nullable(); // penjelasan jawaban benar
            $table->integer('urutan')->default(1);
            $table->timestamps();

            $table->foreign('id_quiz')->references('id_quiz')->on('quizzes')->cascadeOnDelete();
            $table->index(['id_quiz', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
