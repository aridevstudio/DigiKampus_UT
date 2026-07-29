<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Assignment;
use App\Models\BootcampSession;
use App\Models\Course;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Menyatukan agenda & aktivitas pembelajaran mahasiswa dari lima sumber data nyata sistem:
 * 1. Agenda pribadi & master jadwal mengajar dosen (Model Agenda)
 * 2. Sesi bootcamp / live class (Model BootcampSession)
 * 3. Jadwal webinar / event tunggal (Model Course)
 * 4. Deadline tugas perkuliahan & bootcamp (Model Assignment)
 * 5. Jadwal & deadline kuis modul (Model Quiz)
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
        $allEnrolledCourseIds = array_values(array_unique(array_merge($activeCourseIds, $activeBootcampCourseIds)));

        // 1. Personal Agendas & Master Dosen Teaching Schedules
        $agendaItems = Agenda::query()
            ->with('course')
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

        // 2. Bootcamp Live Class Sessions (Multi-Session Events)
        $bootcampItems = empty($activeBootcampCourseIds)
            ? collect()
            : BootcampSession::query()
                ->with('course')
                ->whereIn('id_course', $activeBootcampCourseIds)
                ->where('is_active', true)
                ->whereDate('tanggal_sesi', '>=', $from->toDateString())
                ->whereDate('tanggal_sesi', '<=', $until->toDateString())
                ->orderBy('tanggal_sesi')
                ->orderBy('jam_mulai')
                ->get()
                ->map(fn (BootcampSession $session) => $this->mapBootcampSession($session));

        // 3. Single-Session Webinar / Event Dates
        $courseWebinarItems = empty($activeBootcampCourseIds)
            ? collect()
            : Course::query()
                ->whereIn('id_course', $activeBootcampCourseIds)
                ->whereNotNull('tanggal_webinar')
                ->whereDate('tanggal_webinar', '>=', $from->toDateString())
                ->whereDate('tanggal_webinar', '<=', $until->toDateString())
                ->get()
                ->filter(function (Course $course) {
                    return $course->sessions()->count() === 0;
                })
                ->map(fn (Course $course) => $this->mapCourseWebinar($course));

        // 4. Assignment Deadlines
        $assignmentItems = empty($allEnrolledCourseIds)
            ? collect()
            : Assignment::query()
                ->with('course')
                ->whereIn('id_course', $allEnrolledCourseIds)
                ->whereNotNull('deadline')
                ->whereDate('deadline', '>=', $from->toDateString())
                ->whereDate('deadline', '<=', $until->toDateString())
                ->orderBy('deadline')
                ->get()
                ->map(fn (Assignment $assignment) => $this->mapAssignmentDeadline($assignment));

        // 5. Quizzes
        $quizItems = empty($allEnrolledCourseIds)
            ? collect()
            : Quiz::query()
                ->with('course')
                ->whereIn('id_course', $allEnrolledCourseIds)
                ->where('is_active', true)
                ->where(function ($q) use ($from, $until) {
                    $q->whereBetween('created_at', [$from, $until]);
                })
                ->get()
                ->map(fn (Quiz $quiz) => $this->mapQuizItem($quiz));

        // Combine and deduplicate
        return $agendaItems
            ->concat($bootcampItems)
            ->concat($courseWebinarItems)
            ->concat($assignmentItems)
            ->concat($quizItems)
            ->unique(function (object $item) {
                $courseKey = $item->id_course ?? '0';
                $dateKey = $item->tanggal->format('Y-m-d');
                $titleKey = Str::slug($item->judul);
                return "{$courseKey}_{$dateKey}_{$titleKey}";
            })
            ->sortBy(fn (object $item) => $item->tanggal->format('Y-m-d') . ' ' . ($item->waktu_mulai ?: '00:00:00'))
            ->values();
    }

    public function mapBootcampSession(BootcampSession $session): object
    {
        $isOffline = $session->isOffline();
        $type = $isOffline ? 'workshop' : 'bootcamp';

        return (object) [
            'id_agenda' => 'bootcamp-session-' . $session->id_bootcamp_session,
            'id_course' => (int) $session->id_course,
            'nama_course' => $session->course?->nama_course,
            'judul' => $session->judul_sesi,
            'deskripsi' => $session->deskripsi_sesi,
            'tanggal' => $session->tanggal_sesi->copy(),
            'waktu_mulai' => $session->jam_mulai ? Carbon::parse($session->jam_mulai)->format('H:i') : null,
            'waktu_selesai' => $session->jam_selesai ? Carbon::parse($session->jam_selesai)->format('H:i') : null,
            'tipe' => $type,
            'warna' => Agenda::getColorByType('workshop'),
            'source' => 'bootcamp_session',
            'link' => route('mahasiswa.bootcamp-learn', $session->id_course),
        ];
    }

    private function mapCourseWebinar(Course $course): object
    {
        return (object) [
            'id_agenda' => 'course-webinar-' . $course->id_course,
            'id_course' => (int) $course->id_course,
            'nama_course' => $course->nama_course,
            'judul' => 'Webinar: ' . $course->nama_course,
            'deskripsi' => $course->deskripsi,
            'tanggal' => $course->tanggal_webinar->copy(),
            'waktu_mulai' => $course->jam_mulai_webinar ? Carbon::parse($course->jam_mulai_webinar)->format('H:i') : null,
            'waktu_selesai' => $course->jam_selesai_webinar ? Carbon::parse($course->jam_selesai_webinar)->format('H:i') : null,
            'tipe' => 'event',
            'warna' => Agenda::getColorByType('webinar'),
            'source' => 'course_webinar',
            'link' => route('mahasiswa.bootcamp-learn', $course->id_course),
        ];
    }

    private function mapAssignmentDeadline(Assignment $assignment): object
    {
        return (object) [
            'id_agenda' => 'assignment-deadline-' . $assignment->id_assignment,
            'id_course' => (int) $assignment->id_course,
            'nama_course' => $assignment->course?->nama_course,
            'judul' => 'Deadline Tugas: ' . $assignment->judul,
            'deskripsi' => $assignment->deskripsi,
            'tanggal' => $assignment->deadline->copy(),
            'waktu_mulai' => $assignment->deadline->format('H:i'),
            'waktu_selesai' => null,
            'tipe' => 'deadline',
            'warna' => '#EF4444',
            'source' => 'assignment_tugas',
            'link' => route('mahasiswa.assignment-detail', ['courseId' => $assignment->id_course, 'assignmentId' => $assignment->id_assignment]),
        ];
    }

    private function mapQuizItem(Quiz $quiz): object
    {
        return (object) [
            'id_agenda' => 'quiz-item-' . $quiz->id_quiz,
            'id_course' => (int) $quiz->id_course,
            'nama_course' => $quiz->course?->nama_course,
            'judul' => 'Quiz: ' . $quiz->judul,
            'deskripsi' => $quiz->deskripsi ?? 'Kuis evaluasi modul perkuliahan (Durasi: ' . ($quiz->durasi_menit ?? 30) . ' menit)',
            'tanggal' => $quiz->created_at->copy(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => null,
            'tipe' => 'quiz',
            'warna' => '#F59E0B',
            'source' => 'quiz_evaluasi',
            'link' => route('mahasiswa.course-learn', $quiz->id_course),
        ];
    }

    private function mapAgenda(Agenda $agenda): object
    {
        $type = match($agenda->tipe) {
            'deadline' => 'deadline',
            'quiz' => 'quiz',
            'workshop', 'bootcamp' => 'bootcamp',
            'webinar' => 'event',
            default => 'akademik',
        };

        return (object) [
            'id_agenda' => (string) $agenda->id_agenda,
            'id_course' => $agenda->id_course,
            'nama_course' => $agenda->course?->nama_course,
            'judul' => $agenda->judul,
            'deskripsi' => $agenda->deskripsi,
            'tanggal' => $agenda->tanggal->copy(),
            'waktu_mulai' => $agenda->waktu_mulai ? Carbon::parse($agenda->waktu_mulai)->format('H:i') : null,
            'waktu_selesai' => $agenda->waktu_selesai ? Carbon::parse($agenda->waktu_selesai)->format('H:i') : null,
            'tipe' => $type,
            'warna' => $agenda->warna ?? Agenda::getColorByType($agenda->tipe ?? 'webinar'),
            'source' => 'agenda_pribadi',
            'link' => $agenda->id_course ? route('mahasiswa.course-detail', $agenda->id_course) : null,
        ];
    }
}
