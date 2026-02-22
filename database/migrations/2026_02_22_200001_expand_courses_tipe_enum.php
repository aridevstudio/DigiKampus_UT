<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Expand the 'tipe' enum on courses table to include 'gratis' and 'berbayar'.
     * Original values: webinar, tiket, kursus
     * Added values: gratis, berbayar
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE courses MODIFY COLUMN tipe ENUM('webinar', 'tiket', 'kursus', 'gratis', 'berbayar') DEFAULT 'kursus'");
    }

    /**
     * Revert back to original enum values.
     * WARNING: Any rows with 'gratis' or 'berbayar' will fail if those values exist.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE courses MODIFY COLUMN tipe ENUM('webinar', 'tiket', 'kursus') DEFAULT 'kursus'");
    }
};
