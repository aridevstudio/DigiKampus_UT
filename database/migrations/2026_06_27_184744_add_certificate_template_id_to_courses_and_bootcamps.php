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
            $table->unsignedBigInteger('certificate_template_id')->nullable()->after('kategori');
            $table->foreign('certificate_template_id')->references('id')->on('certificate_templates')->nullOnDelete();
        });

        Schema::table('bootcamps', function (Blueprint $table) {
            $table->unsignedBigInteger('certificate_template_id')->nullable()->after('status');
            $table->foreign('certificate_template_id')->references('id')->on('certificate_templates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_and_bootcamps', function (Blueprint $table) {
            //
        });
    }
};
