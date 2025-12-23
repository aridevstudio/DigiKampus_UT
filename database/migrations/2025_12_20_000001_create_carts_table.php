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
        Schema::create('carts', function (Blueprint $table) {
            $table->id('id_cart');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_course');
            $table->timestamps();

            $table->foreign('id_mahasiswa')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('id_course')
                ->references('id_course')
                ->on('courses')
                ->onDelete('cascade');

            $table->unique(['id_mahasiswa', 'id_course'], 'unique_cart_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
