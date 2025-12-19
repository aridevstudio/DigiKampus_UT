<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_ratings', function (Blueprint $table) {
            $table->id('id_rating');
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->tinyInteger('rating')->unsigned()->comment('Rating 1-5');
            $table->text('ulasan')->nullable();
            $table->timestamps();

            // Relasi ke tabel courses
            $table->foreign('id_course')
                ->references('id_course')
                ->on('courses')
                ->onDelete('cascade');

            // Relasi ke tabel users (mahasiswa)
            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Unique constraint: 1 mahasiswa hanya bisa rating 1x per course
            $table->unique(['id_course', 'id_mahasiswa'], 'unique_course_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_ratings');
    }
};
