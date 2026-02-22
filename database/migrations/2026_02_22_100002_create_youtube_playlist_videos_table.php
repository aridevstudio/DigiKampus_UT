<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_playlist_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_course');
            $table->string('youtube_id', 20);
            $table->string('title', 500);
            $table->string('thumbnail_url', 500)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->timestamps();

            $table->foreign('id_course')->references('id_course')->on('courses')->cascadeOnDelete();
            $table->unique(['id_course', 'youtube_id']);
            $table->index(['id_course', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_playlist_videos');
    }
};
