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
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->decimal('tanggal_x', 5, 2)->default(50.00)->after('program_size');
            $table->decimal('tanggal_y', 5, 2)->default(72.00)->after('tanggal_x');
            $table->unsignedSmallInteger('tanggal_size')->default(14)->after('tanggal_y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->dropColumn(['tanggal_x', 'tanggal_y', 'tanggal_size']);
        });
    }
};
