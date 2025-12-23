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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->string('judul');
            $table->text('konten');
            $table->enum('tipe', ['kursus_pembelajaran', 'jadwal_ujian', 'pencapaian', 'umum'])->default('umum');
            $table->string('icon')->nullable();
            $table->string('icon_color')->default('#3B82F6');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
