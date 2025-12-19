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
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('tipe', ['webinar', 'tiket', 'kursus'])->default('kursus')->after('status');
            $table->decimal('harga', 12, 2)->default(0)->after('tipe');
            $table->decimal('rating', 2, 1)->default(0)->after('harga');
            $table->integer('jumlah_ulasan')->default(0)->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'harga', 'rating', 'jumlah_ulasan']);
        });
    }
};
