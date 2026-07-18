<?php

namespace Tests\Unit;

use App\Models\BootcampSession;
use App\Services\MahasiswaAgendaService;
use Carbon\Carbon;
use Tests\TestCase;

class MahasiswaAgendaServiceTest extends TestCase
{
    public function test_bootcamp_session_is_mapped_to_a_student_agenda_item(): void
    {
        $session = new BootcampSession([
            'id_course' => 41,
            'judul_sesi' => 'Hands-on Laravel',
            'tanggal_sesi' => '2026-07-23',
            'jam_mulai' => '19:30:00',
            'mode_event' => 'online',
            'is_active' => true,
        ]);
        $session->id_bootcamp_session = 99;

        $item = app(MahasiswaAgendaService::class)->mapBootcampSession($session);

        $this->assertSame('bootcamp-session-99', $item->id_agenda);
        $this->assertSame(41, $item->id_course);
        $this->assertSame('Hands-on Laravel', $item->judul);
        $this->assertTrue($item->tanggal->equalTo(Carbon::parse('2026-07-23')));
        $this->assertSame('19:30:00', $item->waktu_mulai);
        $this->assertSame('webinar', $item->tipe);
    }
}
