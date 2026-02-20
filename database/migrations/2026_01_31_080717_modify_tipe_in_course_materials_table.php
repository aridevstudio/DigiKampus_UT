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
        Schema::table('course_materials', function (Blueprint $table) {
            // We need to use raw SQL statement because changing enum using change() is tricky with Doctrine
            // Alternatively, we can just change it to string if strict enum is not required
            $table->string('tipe')->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            // Revert back to limited enum if needed, but string is safer for now
             $table->enum('tipe', ['video', 'file', 'text'])->change();
        });
    }
};
