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
        Schema::create('courses', function (Blueprint $table) {
            $table->id('id_course');
            $table->string('kode_course')->unique();
            $table->string('nama_course');
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('id_dosen')->nullable();
            $table->unsignedBigInteger('id_jurusan')->nullable();
            $table->text('thumbnail')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->timestamps();

            // Relasi ke tabel users (dosen)
            $table->foreign('id_dosen')->references('id')->on('users')->onDelete('set null');

            // Relasi ke tabel jurusans
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
        $table->dropForeign(['id_course']); // nama kolom FK di assignments
        });
        Schema::dropIfExists('courses');
    }
};
