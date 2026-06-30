<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id('id_forum_category');
            $table->string('nama', 120);
            $table->string('slug', 140)->unique();
            $table->string('deskripsi', 500)->nullable();
            $table->string('icon', 80)->default('tag');
            $table->string('warna', 20)->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_categories');
    }
};
