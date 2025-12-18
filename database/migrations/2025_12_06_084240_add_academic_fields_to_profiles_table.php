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
        Schema::table('profiles', function (Blueprint $table) {
            $table->decimal('ipk', 3, 2)->nullable()->after('id_jurusan'); // IPK (0.00 - 4.00)
            $table->integer('total_sks')->nullable()->after('ipk'); // Total SKS
            $table->enum('status_akademik', ['Aktif', 'Cuti', 'Non-Aktif', 'Lulus'])->default('Aktif')->after('total_sks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['ipk', 'total_sks', 'status_akademik']);
        });
    }
};
