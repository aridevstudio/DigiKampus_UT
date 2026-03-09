<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id('id_attempt');
            $table->unsignedBigInteger('id_quiz');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->integer('skor')->default(0); // total poin yang diperoleh
            $table->integer('total_poin')->default(0); // total poin maksimum
            $table->decimal('persentase', 5, 2)->default(0);
            $table->enum('status', ['sedang_mengerjakan', 'selesai', 'timeout'])->default('sedang_mengerjakan');
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->timestamps();

            $table->foreign('id_quiz')->references('id_quiz')->on('quizzes')->cascadeOnDelete();
            $table->foreign('id_mahasiswa')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['id_quiz', 'id_mahasiswa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
