<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DosenProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_profile_page_is_displayed(): void
    {
        $dosen = User::factory()->dosen()->create();

        $response = $this
            ->actingAs($dosen, 'dosen')
            ->get(route('dosen.profile'));

        $response->assertOk();
    }

    public function test_dosen_can_update_profile_data_and_photo(): void
    {
        Storage::fake('public');

        $dosen = User::factory()->dosen()->create();

        $response = $this
            ->actingAs($dosen, 'dosen')
            ->put(route('dosen.profile.update'), [
                'name' => 'Dosen Update',
                'email' => 'dosen.update@example.com',
                'no_hp' => '081234567890',
                'bio' => 'Ini bio dosen update.',
                'foto' => UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dosen.profile'));

        $dosen->refresh();

        $this->assertSame('Dosen Update', $dosen->name);
        $this->assertSame('dosen.update@example.com', $dosen->email);
        $this->assertNotNull($dosen->profile);
        $this->assertSame('081234567890', $dosen->profile->no_hp);
        $this->assertSame('Ini bio dosen update.', $dosen->profile->bio);
        $this->assertNotNull($dosen->profile->foto_profile);
        Storage::disk('public')->assertExists($dosen->profile->foto_profile);
    }

    public function test_dosen_can_update_password_with_valid_current_password(): void
    {
        $dosen = User::factory()->dosen()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this
            ->actingAs($dosen, 'dosen')
            ->put(route('dosen.profile.update'), [
                'name' => $dosen->name,
                'email' => $dosen->email,
                'current_password' => 'password',
                'new_password' => 'NewPassword123',
                'new_password_confirmation' => 'NewPassword123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dosen.profile'));

        $this->assertTrue(Hash::check('NewPassword123', $dosen->fresh()->password));
    }
}
