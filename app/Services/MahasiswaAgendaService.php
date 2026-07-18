<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\BootcampSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Menyatukan agenda mahasiswa dari dua sumber yang memang berbeda:
 * agenda pribadi/jadwal course, serta sesi BootcampSession yang canonical.
 */
class MahasiswaAgendaService
{
    private const BOOTCAMP_MIRROR_PREFIX = 'BOOTCAMP_SESSION|';

    /**
     * @param array<int, int|string> $activeCourseIds
     * @param array<int, int|string> $activeBootcampCourseIds
     */
    public function forPeriod(
        int $mahasiswaId,
        array $activeCourseIds,
        array $activeBootcampCourseIds,
        Carbon $from,
        Carbon $until,
    ): Collection {
        $agendaItems = Agenda::query()
            ->visibleToMahasiswa($mahasiswaId, $activeCourseIds)
            ->whereDate('tanggal', '>=', $from->toDateString())
            ->whereDate('tanggal', '<=', $until->toDateString())
            ->where(function ($query) {
                $query->whereNull('deskripsi')
                    ->orWhere('deskripsi', 'not like', self::BOOTCAMP_MIRROR_PREFIX . '%');
            })
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get()
            ->map(fn (Agenda $agenda) => $this->mapAgenda($agenda));

        $bootcampItems = empty($activeBootcampCourseIds)
            ? collect()
            : BootcampSession::query()
                ->whereIn('id_course', $activeBootcampCourseIds)
                ->where('is_active', true)
                ->whereDate('tanggal_sesi', '>=', $from->toDateString())
                ->whereDate('tanggal_sesi', '<=', $until->toDateString())
                ->orderBy('tanggal_sesi')
                ->orderBy('jam_mulai')
                ->get()
                ->map(fn (BootcampSession $session) => $this->mapBootcampSession($session));

        return $agendaItems
            ->concat($bootcampItems)
            ->sortBy(fn (object $item) => $item->tanggal->format('Y-m-d') . ' ' . ($item->waktu_mulai ?: '00:00:00'))
            ->values();
    }

    public function mapBootcampSession(BootcampSession $session): object
    {
        $isOffline = $session->isOffline();
        $type = $isOffline ? 'workshop' : 'webinar';

        return (object) [
            'id_agenda' => 'bootcamp-session-' . $session->id_bootcamp_session,
            'id_course' => (int) $session->id_course,
            'judul' => $session->judul_sesi,
            'deskripsi' => $session->deskripsi_sesi,
            'tanggal' => $session->tanggal_sesi->copy(),
            'waktu_mulai' => $session->jam_mulai,
            'waktu_selesai' => $session->jam_selesai,
            'tipe' => $type,
            'warna' => Agenda::getColorByType($type),
            'source' => 'bootcamp_session',
        ];
    }

    private function mapAgenda(Agenda $agenda): object
    {
        return (object) [
            'id_agenda' => (string) $agenda->id_agenda,
            'id_course' => $agenda->id_course,
            'judul' => $agenda->judul,
            'deskripsi' => $agenda->deskripsi,
            'tanggal' => $agenda->tanggal->copy(),
            'waktu_mulai' => $agenda->waktu_mulai,
            'waktu_selesai' => $agenda->waktu_selesai,
            'tipe' => $agenda->tipe,
            'warna' => $agenda->warna,
            'source' => 'agenda',
        ];
    }
}
