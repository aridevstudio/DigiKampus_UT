<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id('id_answer');
            $table->unsignedBigInteger('id_attempt');
            $table->unsignedBigInteger('id_question');
            $table->string('jawaban', 255)->nullable(); // jawaban mahasiswa
            $table->boolean('is_correct')->default(false);
            $table->integer('poin_diperoleh')->default(0);
            $table->timestamps();

            $table->foreign('id_attempt')->references('id_attempt')->on('quiz_attempts')->cascadeOnDelete();
            $table->foreign('id_question')->references('id_question')->on('quiz_questions')->cascadeOnDelete();
            $table->unique(['id_attempt', 'id_question']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
