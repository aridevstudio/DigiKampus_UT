<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_learning_goals', function (Blueprint $table) {
            $table->id('id_goal');
            $table->unsignedBigInteger('id_course');
            $table->string('judul_goal');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('id_course')
                ->references('id_course')->on('courses')
                ->onDelete('cascade');

            $table->index(['id_course', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_learning_goals');
    }
};
