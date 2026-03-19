<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dosen_jurusan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->timestamps();

            $table->unique(['profile_id', 'jurusan_id']);
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id_jurusan')->on('jurusans')->onDelete('cascade');
        });

        $rows = DB::table('profiles')
            ->join('users', 'users.id', '=', 'profiles.user_id')
            ->where('users.role', 'dosen')
            ->whereNotNull('profiles.id_jurusan')
            ->select('profiles.id as profile_id', 'profiles.id_jurusan as jurusan_id')
            ->get()
            ->map(fn ($row) => [
                'profile_id' => $row->profile_id,
                'jurusan_id' => $row->jurusan_id,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        if (!empty($rows)) {
            DB::table('dosen_jurusan')->insertOrIgnore($rows);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_jurusan');
    }
};
