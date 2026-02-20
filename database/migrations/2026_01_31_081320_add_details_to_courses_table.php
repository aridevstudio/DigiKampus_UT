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
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('estimasi_waktu')->nullable()->after('deskripsi');
            $table->string('durasi_satuan')->nullable()->after('estimasi_waktu');
            $table->string('level')->nullable()->after('durasi_satuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['estimasi_waktu', 'durasi_satuan', 'level']);
        });
    }
};
