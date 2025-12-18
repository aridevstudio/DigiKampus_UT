<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('course_materials', function (Blueprint $table) {
                $table->id('id_material');
                $table->unsignedBigInteger('id_course');
                $table->string('judul_material');
                $table->enum('tipe', ['video', 'file', 'text']);
                $table->text('konten')->nullable();
                $table->integer('urutan')->default(1);
                $table->timestamps();

                // Relasi ke tabel courses
                $table->foreign('id_course')
                    ->references('id_course')
                    ->on('courses')
                    ->onDelete('cascade'); // hapus otomatis kalau course dihapus
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};
