<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id('id_forum_comment');
            $table->unsignedBigInteger('topic_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->longText('isi');
            $table->enum('status', ['published', 'hidden', 'deleted'])->default('published');
            $table->timestamps();

            $table->foreign('topic_id')
                ->references('id_forum_topic')->on('forum_topics')
                ->cascadeOnDelete();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
            $table->foreign('parent_id')
                ->references('id_forum_comment')->on('forum_comments')
                ->nullOnDelete();

            $table->index(['topic_id', 'status', 'parent_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
    }
};
