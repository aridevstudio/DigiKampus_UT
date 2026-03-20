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
        Schema::create('automatic_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_template_id')
                ->constrained('certificate_templates')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nomor_sertifikat')->unique();
            $table->string('nama_peserta');
            $table->string('nama_program');
            $table->date('tanggal_terbit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automatic_certificates');
    }
};
