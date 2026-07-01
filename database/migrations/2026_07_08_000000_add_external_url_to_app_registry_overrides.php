<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Append-only addition: lets admin override the destination URL of any
     * "launcher" app (default fallback is `AppRegistryService::CATALOG` per
     * slug). Used primarily by E-Library so the perpustakaan URL can be
     * updated without a code deploy.
     *
     * - 2048 chars to comfortably fit long SharePoint/library portal URLs.
     * - Nullable: empty/null means "fall back to catalog URL".
     * - Stored AFTER `description` so the table reads logically
     *   (slug → display → desc → url → active → roles → timestamps).
     */
    public function up(): void
    {
        Schema::table('app_registry_overrides', function (Blueprint $table) {
            $table->string('external_url', 2048)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('app_registry_overrides', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });
    }
};
