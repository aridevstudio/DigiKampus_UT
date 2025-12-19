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
        Schema::create('material_progress', function (Blueprint $table) {
            $table->id('id_progress');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_material');
            $table->boolean('is_completed')->default(false);
            $table->integer('watched_duration')->default(0)->comment('Duration in seconds');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            // Relasi ke tabel users (mahasiswa)
            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Relasi ke tabel course_materials
            $table->foreign('id_material')
                ->references('id_material')
                ->on('course_materials')
                ->onDelete('cascade');

            // Unique: 1 mahasiswa 1 material
            $table->unique(['id_mahasiswa', 'id_material'], 'unique_material_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_progress');
    }
};
