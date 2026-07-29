<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\News;
use App\Models\Agenda;
use App\Models\Notification;
use App\Services\MahasiswaAgendaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Show mahasiswa dashboard
     */
    public function index(MahasiswaAgendaService $agendaService)
    {
        $user = Auth::guard('mahasiswa')->user();

        // Get enrollments for progress calculation
        // PENTING: Pisahkan kursus reguler (`kategori='kursus'`) dari event tiket
        // (`kategori='tiket'` atau `tipe_event` IN bootcamp/webinar/workshop/seminar)
        // karena kursus dan event mengikuti flow yang benar-benar berbeda dan jangan
        // dirender jadi satu section di dashboard.
        // Eager-load `course` agar filter isEventCourse() tidak menjadi N+1.
        $enrollments = Enrollment::with('course')
            ->where('id_mahasiswa', $user->id)
            ->get();

        $activeStatuses = ['aktif', 'in_progress'];
        $activeEnrollments = $enrollments->filter(function ($enrollment) use ($activeStatuses) {
            return in_array($enrollment->status, $activeStatuses, true)
                && (float) $enrollment->progress < 100;
        });
        $completedEnrollments = $enrollments->filter(function ($enrollment) {
            return $enrollment->status === 'selesai' || (float) $enrollment->progress >= 100;
        });
        $activeCourseIds = $activeEnrollments->pluck('id_course')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $activeBootcampCourseIds = $activeEnrollments
            ->filter(fn (Enrollment $enrollment) => $enrollment->course?->kategori === 'tiket')
            ->pluck('id_course')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Calculate progress statistics (kursus reguler saja — agar angka di donut
        // chart konsisten dengan section "Kursus yang Sedang Kamu Ikuti" di bawah).
        $kursusEnrollmentsForStats = $enrollments->filter(function ($enrollment) {
            return !self::isEventCourse($enrollment->course);
        });
        $kursusActiveEnrollments = $kursusEnrollmentsForStats->filter(function ($enrollment) use ($activeStatuses) {
            return in_array($enrollment->status, $activeStatuses, true)
                && (float) $enrollment->progress < 100;
        });
        $kursusCompletedEnrollments = $kursusEnrollmentsForStats->filter(function ($enrollment) {
            return $enrollment->status === 'selesai' || (float) $enrollment->progress >= 100;
        });
        $kursusAktif = $kursusActiveEnrollments->count();
        $kursusSelesai = $kursusCompletedEnrollments->count();
        $kursusSedangDipelajari = $kursusActiveEnrollments->where('progress', '>', 0)->count();
        $kursusTertunda = $kursusActiveEnrollments->where('progress', '<', 50)->count();

        // Calculate total progress (average of active kursus enrollments — event
        // style tidak dihitung ke progress karena completion-nya multi-sesi dan
        // dihitung terpisah via BootcampFlowService → attendance verification).
        $totalProgress = $kursusActiveEnrollments->count() > 0
            ? round($kursusActiveEnrollments->avg('progress'), 0)
            : 0;

        // Get enrolled KURSUS (kategori='kursus' ATAU non-event) untuk section
        // "Kursus yang Sedang Kamu Ikuti" — limit 3 untuk dashboard.
        $kursusEnrollments = Enrollment::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->whereIn('status', $activeStatuses)
            ->where('progress', '<', 100)
            ->whereDoesntHave('course', function ($q) {
                $q->where('kategori', 'tiket');
            })
            ->orderByRaw('CASE WHEN progress > 0 THEN 0 ELSE 1 END')
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        // Get enrolled EVENT (bootcamp / webinar / workshop / seminar) untuk section
        // "Event yang Sedang Kamu Ikuti" — limit 3 untuk dashboard.
        $eventEnrollments = Enrollment::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->whereIn('status', $activeStatuses)
            ->whereHas('course', function ($q) {
                $q->where('kategori', 'tiket');
            })
            ->orderByRaw('CASE WHEN progress > 0 THEN 0 ELSE 1 END')
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        // Backwards-compat alias — blade lama yang masih baca $enrolledCourses
        // akan resolve ke kursus saja supaya tidak ndak-include event.
        $enrolledCourses = $kursusEnrollments;

        // Prefer kursus with existing progress for "continue learning" CTA.
        // Pakai DB query langsung (bukan chained Collection sort yang fragile).
        // Event punya CTA sendiri di section event (lihat blade).
        $nextEnrollment = Enrollment::query()
            ->where('id_mahasiswa', $user->id)
            ->whereIn('status', $activeStatuses)
            ->where('progress', '<', 100)
            ->whereDoesntHave('course', function ($q) {
                $q->where('kategori', 'tiket');
            })
            ->orderByRaw('CASE WHEN progress > 0 THEN 0 ELSE 1 END')
            ->orderByDesc('updated_at')
            ->first();
        $continueLearningUrl = $nextEnrollment
            ? route('mahasiswa.course-learn', $nextEnrollment->id_course)
            : route('mahasiswa.get-courses');

        // Get latest news (limit 3)
        $news = News::where('is_active', true)
            ->where('tanggal_publish', '<=', now())
            ->orderBy('tanggal_publish', 'desc')
            ->take(3)
            ->get();

        // Agenda mahasiswa: jadwal pribadi/course aktif + BootcampSession dari
        // bootcamp yang sudah dibeli. Jangan membaca mirror Agenda dosen karena
        // BootcampSession adalah source of truth untuk peserta.
        $agenda = $agendaService->forPeriod(
            $user->id,
            $activeCourseIds,
            $activeBootcampCourseIds,
            now()->startOfMonth(),
            now()->endOfMonth(),
        );

        $dashboardNotifications = Notification::where('id_mahasiswa', $user->id)
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        return view('pages.mahasiswa.dashboard', [
            'totalProgress' => $totalProgress,
            'kursusAktif' => $kursusAktif,
            'kursusSelesai' => $kursusSelesai,
            'kursusSedangDipelajari' => $kursusSedangDipelajari,
            'kursusTertunda' => $kursusTertunda,
            'continueLearningUrl' => $continueLearningUrl,
            'enrolledCourses' => $enrolledCourses,
            'eventEnrollments' => $eventEnrollments,
            'news' => $news,
            'agenda' => $agenda,
            'dashboardNotifications' => $dashboardNotifications
                ->map(fn (Notification $notification) => $this->mapNotificationForMahasiswa($notification, $user->id))
                ->values(),
            'dashboardNotificationUnreadCount' => $dashboardNotifications->where('is_read', false)->count(),
        ]);
    }

    /**
     * Show calendar page with all agenda
     */
    public function calendar(MahasiswaAgendaService $agendaService)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        // Get month and year from request or use current
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        $periodStart = now()->setYear((int) $year)->setMonth((int) $month)->startOfMonth();
        $activeEnrollments = Enrollment::with('course')
            ->where('id_mahasiswa', $user->id)
            ->whereIn('status', ['aktif', 'in_progress', 'selesai'])
            ->get();
        $activeCourseIds = $activeEnrollments->pluck('id_course')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $activeBootcampCourseIds = $activeEnrollments
            ->filter(fn (Enrollment $enrollment) => $enrollment->course?->kategori === 'tiket' || !empty($enrollment->course?->tipe_event))
            ->pluck('id_course')
            ->filter()
            ->unique()
            ->values()
            ->all();
        
        $agenda = $agendaService->forPeriod(
            $user->id,
            $activeCourseIds,
            $activeBootcampCourseIds,
            $periodStart,
            $periodStart->copy()->endOfMonth(),
        );
        
        // Get same agenda for sidebar list (all events for the month, same as dashboard)
        $upcomingAgenda = $agenda;
        
        return view('pages.mahasiswa.calendar', [
            'agenda' => $agenda,
            'upcomingAgenda' => $upcomingAgenda,
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }
    
    /**
     * Store personal agenda for mahasiswa
     */
    public function storePersonalAgenda(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:webinar,workshop,deadline,quiz',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|string',
        ]);
        
        Agenda::create([
            'id_mahasiswa' => $user->id,
            'id_dosen' => null,
            'id_course' => null,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'tipe' => $request->tipe,
            'warna' => Agenda::getColorByType($request->tipe),
        ]);
        
        return back()->with('success', 'Jadwal pribadi berhasil ditambahkan!');
    }

    /**
     * Show notification page
     */
    public function notification()
    {
        $user = Auth::guard('mahasiswa')->user();
        
        $notifications = Notification::where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = $notifications->where('is_read', false)->count();
        
        return view('pages.mahasiswa.notification', [
            'notifications' => $notifications,
            'notificationPayload' => $notifications
                ->map(fn (Notification $notification) => $this->mapNotificationForMahasiswa($notification, $user->id))
                ->values(),
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markNotificationRead(Request $request, $id)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        $notification = \App\Models\Notification::where('id_mahasiswa', $user->id)
            ->where('id_notification', $id)
            ->first();

        if ($notification) {
            $notification->is_read = true;
            $notification->save();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca',
            ]);
        }

        return redirect()->route('mahasiswa.notification')->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::guard('mahasiswa')->user();
        
        Notification::where('id_mahasiswa', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('mahasiswa.notification')->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    private function mapNotificationForMahasiswa(Notification $notification, int $mahasiswaId): array
    {
        $action = $this->resolveNotificationAction($notification, $mahasiswaId);

        return [
            'id' => $notification->id_notification,
            'judul' => $notification->judul,
            'konten' => $notification->konten,
            'tipe' => $notification->tipe,
            'icon' => $notification->icon,
            'icon_color' => $notification->icon_color,
            'is_read' => $notification->is_read,
            'waktu_relatif' => $notification->created_at->diffForHumans(),
            'action_url' => $action['url'] ?? null,
            'action_label' => $action['label'] ?? null,
            'action_variant' => $action['variant'] ?? null,
            'course_name' => $action['course_name'] ?? null,
        ];
    }

    private function resolveNotificationAction(Notification $notification, int $mahasiswaId): array
    {
        if (trim((string) $notification->judul) !== 'Sertifikat Kursus Tersedia') {
            return [];
        }

        $courseName = $this->extractCourseNameFromNotification($notification->konten);
        if (!$courseName) {
            return [];
        }

        $normalizedCourseName = Str::lower(trim($courseName));

        $enrollment = Enrollment::query()
            ->where('id_mahasiswa', $mahasiswaId)
            ->whereHas('course', function ($query) use ($normalizedCourseName) {
                $query->whereRaw('LOWER(TRIM(nama_course)) = ?', [$normalizedCourseName]);
            })
            ->with('course:id_course,nama_course')
            ->orderByDesc('updated_at')
            ->first();

        if (!$enrollment || !$enrollment->course) {
            return [];
        }

        return [
            'url' => route('mahasiswa.course-learn', [
                'id' => $enrollment->course->id_course,
                'certificate' => 'download',
            ]) . '#course-certificate-panel',
            'label' => 'Download Sertifikat',
            'variant' => 'certificate',
            'course_name' => $enrollment->course->nama_course,
        ];
    }

    private function extractCourseNameFromNotification(?string $content): ?string
    {
        if (!filled($content)) {
            return null;
        }

        if (preg_match('/"([^"]+)"/u', $content, $matches)) {
            return trim((string) ($matches[1] ?? '')) ?: null;
        }

        return null;
    }

    /**
     * Cek apakah Course entry adalah event (bootcamp/webinar/workshop/seminar).
     *
     * - Legacy: pakai kolom `kategori='tiket'`.
     * - Baru: pakai kolom `tipe_event` dengan nilai dalam {@see Course::EVENT_TIPE_VALUES}.
     * - Nilai lain dari `kategori` (mis. 'kursus', 'webinar' legacy) bukan event.
     *
     * Dipakai oleh {@see DashboardController::index()} untuk memisahkan statistik
     * kursus dari statistik event di dashboard mahasiswa — sehingga dua jenis
     * konten tidak dicampur jadi satu section.
     */
    private static function isEventCourse(?Course $course): bool
    {
        if (!$course) {
            return false;
        }

        return $course->kategori === 'tiket';
    }

    /**
     * Show news & announcements page
     */
    public function news()
    {
        $news = News::active()
            ->published()
            ->orderBy('tanggal_publish', 'desc')
            ->take(50)
            ->get()
            ->map(function ($item) {
                return [
                    'id_news' => $item->id_news,
                    'judul' => $item->judul,
                    'konten' => $item->konten,
                    'thumbnail' => $item->thumbnail,
                    'thumbnail_url' => $item->thumbnail
                        ? asset('storage/' . $item->thumbnail)
                        : null,
                    'kategori' => $item->kategori,
                    'tanggal_publish' => $item->tanggal_publish?->toDateTimeString(),
                    'waktu_relatif' => $item->waktu_relatif,
                ];
            });

        return view('pages.mahasiswa.news', [
            'active' => 'news',
            'title' => 'News',
            'newsData' => $news,
        ]);
    }
}
