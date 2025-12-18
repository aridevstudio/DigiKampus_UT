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
       Schema::create('assignments', function (Blueprint $table) {
                $table->id('id_assignment');
                $table->unsignedBigInteger('id_course');
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->dateTime('deadline')->nullable();
                $table->timestamps();

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
        Schema::dropIfExists('assigments');
    }
};
