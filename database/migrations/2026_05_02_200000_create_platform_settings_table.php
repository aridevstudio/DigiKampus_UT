<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('value_type', 20)->default('string');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('platform_settings')->insert([
            'key' => 'course_service_fee',
            'value' => '5000',
            'value_type' => 'int',
            'description' => 'Biaya layanan global untuk checkout kursus.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
