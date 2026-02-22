<?php

namespace Tests\Feature\Admin;

use App\Models\AdminNotification;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
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
        return $this->withSession([
            'admin_login' => true,
        ]);
    }

    public function test_dashboard_requires_auth(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_loads_for_admin(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertViewHas('totalDosen')
            ->assertViewHas('totalMahasiswa')
            ->assertViewHas('kursusAktif')
            ->assertViewHas('unreadNotifCount');
    }

    public function test_dashboard_counts_only_active_dosen(): void
    {
        User::factory()->dosen()->count(3)->create(['status' => 'aktif']);
        User::factory()->dosen()->count(2)->create(['status' => 'nonaktif']);

        $response = $this->actingAsAdmin()
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $this->assertEquals(3, $response->viewData('totalDosen'));
    }

    public function test_dashboard_counts_only_active_mahasiswa(): void
    {
        User::factory()->mahasiswa()->count(5)->create(['status' => 'aktif']);
        User::factory()->mahasiswa()->count(4)->create(['status' => 'nonaktif']);

        $response = $this->actingAsAdmin()
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $this->assertEquals(5, $response->viewData('totalMahasiswa'));
    }

    public function test_dashboard_shows_unread_notification_count(): void
    {
        AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Test',
            'konten' => 'Content',
            'tipe' => 'info',
            'is_read' => false,
        ]);
        AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Read One',
            'konten' => 'Content',
            'tipe' => 'info',
            'is_read' => true,
        ]);

        $response = $this->actingAsAdmin()
            ->get(route('admin.dashboard'));

        $this->assertEquals(1, $response->viewData('unreadNotifCount'));
    }
}
