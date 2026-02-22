<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    /**
     * Separate course category (webinar/tiket/kursus) from pricing type (gratis/berbayar).
     *
     * Before: tipe ENUM('webinar','tiket','kursus','gratis','berbayar')
     * After:  tipe ENUM('gratis','berbayar') — pricing model
     *         kategori ENUM('webinar','tiket','kursus') — course format/category
     */
    public function up(): void
    {
        // 1. Add kategori column
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('kategori', ['webinar', 'tiket', 'kursus'])->default('kursus')->after('tipe');
        });

        // 2. Migrate data: copy tipe → kategori for courses that have category values
        DB::statement("UPDATE courses SET kategori = tipe WHERE tipe IN ('webinar', 'tiket', 'kursus')");

        // 3. Convert those rows' tipe to pricing: if harga > 0 → berbayar, else → gratis
        DB::statement("UPDATE courses SET tipe = CASE WHEN harga > 0 THEN 'berbayar' ELSE 'gratis' END WHERE tipe IN ('webinar', 'tiket', 'kursus')");

        // 4. Shrink tipe enum to just pricing values
        DB::statement("ALTER TABLE courses MODIFY COLUMN tipe ENUM('gratis', 'berbayar') DEFAULT 'gratis'");
    }

    /**
     * Reverse: merge kategori back into tipe.
     */
    public function down(): void
    {
        // Expand tipe enum back
        DB::statement("ALTER TABLE courses MODIFY COLUMN tipe ENUM('webinar', 'tiket', 'kursus', 'gratis', 'berbayar') DEFAULT 'kursus'");

        // Restore: copy kategori back to tipe for courses that were originally webinar/tiket/kursus
        DB::statement("UPDATE courses SET tipe = kategori WHERE kategori IN ('webinar', 'tiket', 'kursus')");

        // Drop kategori column
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
