<?php

namespace Tests\Feature\Admin;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
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

    public function test_get_notifications_returns_json(): void
    {
        AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Notifikasi Test',
            'konten' => 'Detail notifikasi',
            'tipe' => 'info',
            'icon' => 'info',
            'is_read' => false,
        ]);

        $response = $this->actingAsAdmin()
            ->getJson(route('admin.notifications'));

        $response->assertOk()
            ->assertJsonStructure([
                'count',
                'items' => [
                    '*' => ['id', 'type', 'icon', 'message', 'detail', 'is_read', 'time'],
                ],
            ]);

        $this->assertEquals(1, $response->json('count'));
        $this->assertCount(1, $response->json('items'));
    }

    public function test_get_notification_count(): void
    {
        AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Unread',
            'konten' => '',
            'tipe' => 'info',
            'is_read' => false,
        ]);
        AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Read',
            'konten' => '',
            'tipe' => 'info',
            'is_read' => true,
        ]);

        $response = $this->actingAsAdmin()
            ->getJson(route('admin.notifications.count'));

        $response->assertOk()
            ->assertJson(['count' => 1]);
    }

    public function test_mark_single_notification_as_read(): void
    {
        $notif = AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'To Read',
            'konten' => '',
            'tipe' => 'info',
            'is_read' => false,
        ]);

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.notifications.read', $notif->id));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_mark_all_notifications_as_read(): void
    {
        AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'N1',
            'konten' => '',
            'tipe' => 'info',
            'is_read' => false,
        ]);
        AdminNotification::create([
            'admin_id' => $this->admin->id,
            'judul' => 'N2',
            'konten' => '',
            'tipe' => 'warning',
            'is_read' => false,
        ]);

        $response = $this->actingAsAdmin()
            ->postJson(route('admin.notifications.readAll'));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(0, AdminNotification::where('admin_id', $this->admin->id)->unread()->count());
    }

    public function test_cannot_read_other_admins_notification(): void
    {
        $otherAdmin = User::factory()->admin()->create();
        $notif = AdminNotification::create([
            'admin_id' => $otherAdmin->id,
            'judul' => 'Other Admin Notif',
            'konten' => '',
            'tipe' => 'info',
            'is_read' => false,
        ]);

        $this->actingAsAdmin()
            ->postJson(route('admin.notifications.read', $notif->id));

        // The other admin's notification should still be unread
        $this->assertFalse($notif->fresh()->is_read);
    }

    public function test_notify_all_admins_creates_for_each(): void
    {
        User::factory()->admin()->count(2)->create();
        $adminCount = User::where('role', 'admin')->count(); // includes $this->admin

        AdminNotification::notifyAllAdmins('Test', 'Konten', 'info', 'info', null);

        $this->assertEquals($adminCount, AdminNotification::count());
    }
}
