<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\User;
use App\Models\YoutubePlaylistVideo;
use App\Services\YoutubePlaylistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminYoutubeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    private function actingAsAdmin(): static
    {
        Auth::guard('admin')->login($this->admin);
        return $this->withSession(['admin_login' => true]);
    }

    public function test_sync_fails_for_nonexistent_course(): void
    {
        $response = $this->actingAsAdmin()
            ->postJson(route('admin.kursus.syncPlaylist', 99999));

        $response->assertStatus(404)
            ->assertJson(['error' => 'Kursus tidak ditemukan.']);
    }

    public function test_sync_fails_without_playlist_url(): void
    {
        $course = Course::create([
            'kode_course' => 'CS101',
            'nama_course' => 'Test Course',
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.kursus.syncPlaylist', $course->id_course));

        $response->assertStatus(422)
            ->assertJson(['error' => 'URL playlist YouTube belum diisi.']);
    }

    public function test_sync_fails_with_invalid_url(): void
    {
        $course = Course::create([
            'kode_course' => 'CS102',
            'nama_course' => 'Test Course 2',
            'youtube_playlist' => 'https://www.google.com',
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.kursus.syncPlaylist', $course->id_course));

        $response->assertStatus(422)
            ->assertJsonPath('error', fn($v) => str_contains($v, 'tidak valid'));
    }

    public function test_get_youtube_videos_returns_empty_for_new_course(): void
    {
        $course = Course::create([
            'kode_course' => 'CS103',
            'nama_course' => 'No Videos',
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        $response = $this->actingAsAdmin()
            ->getJson(route('admin.kursus.youtubeVideos', $course->id_course));

        $response->assertOk()
            ->assertJson(['videos' => []]);
    }

    public function test_get_youtube_videos_returns_existing_records(): void
    {
        $course = Course::create([
            'kode_course' => 'CS104',
            'nama_course' => 'With Videos',
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        YoutubePlaylistVideo::create([
            'id_course' => $course->id_course,
            'youtube_id' => 'abc123',
            'title' => 'Video 1',
            'thumbnail_url' => 'https://img.youtube.com/vi/abc123/mqdefault.jpg',
            'urutan' => 0,
            'duration_seconds' => 120,
        ]);

        $response = $this->actingAsAdmin()
            ->getJson(route('admin.kursus.youtubeVideos', $course->id_course));

        $response->assertOk();
        $this->assertCount(1, $response->json('videos'));
        $this->assertEquals('abc123', $response->json('videos.0.youtube_id'));
    }

    public function test_sync_to_database(): void
    {
        $course = Course::create([
            'kode_course' => 'CS105',
            'nama_course' => 'Sync Test',
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        $videos = [
            ['youtube_id' => 'v1', 'title' => 'First', 'thumbnail_url' => '', 'urutan' => 0, 'duration_seconds' => 60],
            ['youtube_id' => 'v2', 'title' => 'Second', 'thumbnail_url' => '', 'urutan' => 1, 'duration_seconds' => 120],
        ];

        $result = YoutubePlaylistService::syncToDatabase($course->id_course, $videos);

        $this->assertEquals(2, $result['added']);
        $this->assertEquals(0, $result['removed']);
        $this->assertEquals(2, YoutubePlaylistVideo::where('id_course', $course->id_course)->count());
    }

    public function test_sync_to_database_removes_stale_videos(): void
    {
        $course = Course::create([
            'kode_course' => 'CS106',
            'nama_course' => 'Stale Test',
            'status' => 'aktif',
            'tipe' => 'gratis',
            'kategori' => 'kursus',
            'harga' => 0,
        ]);

        // Pre-existing video
        YoutubePlaylistVideo::create([
            'id_course' => $course->id_course,
            'youtube_id' => 'old_video',
            'title' => 'Old Video',
            'urutan' => 0,
            'duration_seconds' => 0,
        ]);

        // Sync with new set (old_video is not included)
        $videos = [
            ['youtube_id' => 'new_video', 'title' => 'New', 'thumbnail_url' => '', 'urutan' => 0, 'duration_seconds' => 60],
        ];

        $result = YoutubePlaylistService::syncToDatabase($course->id_course, $videos);

        $this->assertEquals(1, $result['added']);
        $this->assertEquals(1, $result['removed']);
        $this->assertDatabaseMissing('youtube_playlist_videos', ['youtube_id' => 'old_video']);
        $this->assertDatabaseHas('youtube_playlist_videos', ['youtube_id' => 'new_video']);
    }
}
