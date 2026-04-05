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
        Schema::create('bootcamp_mentors', function (Blueprint $table) {
            $table->id('id_bootcamp_mentor');
            $table->unsignedBigInteger('id_bootcamp');
            $table->unsignedBigInteger('id_user');
            $table->string('role_label')->default('mentor');
            $table->text('assignment_note')->nullable();
            $table->timestamps();

            $table->unique(['id_bootcamp', 'id_user']);
            $table->foreign('id_bootcamp')->references('id_bootcamp')->on('bootcamps')->cascadeOnDelete();
            $table->foreign('id_user')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bootcamp_mentors');
    }
};

