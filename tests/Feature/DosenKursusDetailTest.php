<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenKursusDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_kursus_detail_route_renders_blade_for_browser_request(): void
    {
        $dosen = User::factory()->dosen()->create();

        $course = Course::create([
            'kode_course' => 'C-DETAIL-01',
            'nama_course' => 'Kursus Detail Test',
            'deskripsi' => 'Deskripsi kursus detail',
            'id_dosen' => $dosen->id,
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        $response = $this
            ->actingAs($dosen, 'dosen')
            ->get(route('dosen.kursus.detail', $course->id_course));

        $response
            ->assertOk()
            ->assertViewIs('Auth.dosen.detail-kursus')
            ->assertSee('Kursus Detail Test');
    }

    public function test_dosen_kursus_detail_route_returns_json_for_json_request(): void
    {
        $dosen = User::factory()->dosen()->create();

        $course = Course::create([
            'kode_course' => 'C-DETAIL-02',
            'nama_course' => 'Kursus Detail JSON',
            'deskripsi' => 'Deskripsi kursus json',
            'id_dosen' => $dosen->id,
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        $response = $this
            ->actingAs($dosen, 'dosen')
            ->getJson(route('dosen.kursus.detail', $course->id_course));

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id_course' => $course->id_course,
                'nama_course' => 'Kursus Detail JSON',
            ]);
    }
}

