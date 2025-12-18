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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id('id_enroll');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_course');
            $table->dateTime('tanggal_daftar')->useCurrent();
            $table->float('progress')->default(0);
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();

            // Relasi ke tabel users (mahasiswa)
            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Relasi ke tabel courses
            $table->foreign('id_course')
                ->references('id_course')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
