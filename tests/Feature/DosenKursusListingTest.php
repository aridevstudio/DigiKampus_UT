<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenKursusListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_kursus_saya_excludes_bootcamp_owned_by_the_dosen(): void
    {
        $dosen = User::factory()->dosen()->create();

        Course::create([
            'kode_course' => 'KRS-REG-01',
            'nama_course' => 'Kursus Reguler Dosen',
            'deskripsi' => 'Kursus reguler yang harus tampil.',
            'id_dosen' => $dosen->id,
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        Course::create([
            'kode_course' => 'TKT-BTC-01',
            'nama_course' => 'Bootcamp Khusus Dosen',
            'deskripsi' => 'Bootcamp harus tampil di Bootcamp Saya, bukan Kursus Saya.',
            'id_dosen' => $dosen->id,
            'status' => 'aktif',
            'tipe' => 'berbayar',
            'kategori' => 'tiket',
            'harga' => 100000,
        ]);

        $this
            ->actingAs($dosen, 'dosen')
            ->get(route('dosen.kursus'))
            ->assertOk()
            ->assertSee('Kursus Reguler Dosen')
            ->assertDontSee('Bootcamp Khusus Dosen');
    }
}
