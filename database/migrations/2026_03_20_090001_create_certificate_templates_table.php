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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('background_type', ['image', 'gradient'])->default('gradient');
            $table->string('background_image_path')->nullable();
            $table->text('background_gradient')->nullable();
            $table->decimal('nomor_x', 5, 2)->default(82.00);
            $table->decimal('nomor_y', 5, 2)->default(10.50);
            $table->unsignedSmallInteger('nomor_size')->default(28);
            $table->decimal('nama_x', 5, 2)->default(50.00);
            $table->decimal('nama_y', 5, 2)->default(54.00);
            $table->unsignedSmallInteger('nama_size')->default(52);
            $table->decimal('program_x', 5, 2)->default(50.00);
            $table->decimal('program_y', 5, 2)->default(71.00);
            $table->unsignedSmallInteger('program_size')->default(18);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
