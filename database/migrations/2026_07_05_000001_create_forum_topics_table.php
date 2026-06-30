<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id('id_forum_topic');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id');
            $table->string('judul', 200);
            $table->string('slug', 220)->unique();
            $table->longText('isi');
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->enum('status', ['published', 'hidden', 'deleted'])->default('published');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id_forum_category')->on('forum_categories')
                ->cascadeOnDelete();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->index(['status', 'is_pinned', 'last_activity_at']);
            $table->index(['category_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};
