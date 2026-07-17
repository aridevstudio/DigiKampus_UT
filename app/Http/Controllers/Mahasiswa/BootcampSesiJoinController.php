<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\BootcampLiveClassAttendance;
use App\Models\BootcampSession;
use App\Models\Course;
use App\Models\EnrollBootcamp;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\BootcampFlowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Aksi JOIN sesi untuk mahasiswa.
 *
 * Memisahkan logic check-in dari controller monolith lain. Aksi utama:
 *   - joinSesi(Request, $courseId, $sesiId) → POST /mahasiswa/bootcamp/{courseId}/sesi/{sesiId}/join
 *   - recordHeartbeat(Request, $courseId, $sesiId) → POST /mahasiswa/bootcamp/{courseId}/sesi/{sesiId}/heartbeat
 *   - setSesiFeedback(Request, $courseId, $sesiId) → POST /mahasiswa/bootcamp/{courseId}/sesi/{sesiId}/feedback
 */
class BootcampSesiJoinController extends Controller
{
    public function __construct(private readonly BootcampFlowService $bootcampFlow) {}

    public function joinSesi(Request $request, int $courseId, int $sesiId): RedirectResponse
    {
        $user = Auth::guard('mahasiswa')->user() ?? Auth::user();
        if (!$user instanceof User) {
            return back()->with('error', 'Sesi login tidak valid.');
        }
        $course = Course::findOrFail($courseId);
        $sesi = BootcampSession::where('id_course', $course->id_course)
            ->where('id_bootcamp_session', $sesiId)
            ->firstOrFail();

        // Gate 1 — enrollment aktif untuk course ini
        $enrollment = $this->bootcampFlow->resolveAccessibleEnrollment($user, $course);
        if (!$enrollment) {
            return back()->with('error', 'Anda tidak terdaftar aktif pada bootcamp ini.');
        }

        // Gate 1.5 — sesi OFFLINE tidak punya join URL. Tampilkan info lokasi saja.
        if ($sesi->isOffline()) {
            $lokasi = $sesi->effectiveLocation() ?: 'lokasi akan diinformasikan oleh admin';
            return back()->with('error', "Sesi ini bersifat Offline di {$lokasi}. Kehadiran akan dicatat oleh admin di lokasi, atau gunakan form Unggah Bukti Kehadiran setelah sesi berakhir.");
        }

        // Gate 2 — JOIN hanya tersedia selama sesi benar-benar live. Late join
        // setelah sesi berakhir bukan bukti kehadiran dan tidak boleh mengubah
        // state attendance.
        $now = now();
        $joinable = $now->greaterThanOrEqualTo($sesi->start_at)
            && $now->lessThanOrEqualTo($sesi->end_at);
        if (!$joinable) {
            return back()->with('error', 'Sesi belum dimulai atau sudah berakhir. Bukti kehadiran dapat diunggah lewat menu Live Class setelah sesi berakhir.');
        }

        $joinUrl = $sesi->joinUrl();
        if (!$joinUrl) {
            return back()->with('error', 'Link Zoom/Google Meet untuk sesi ini belum tersedia. Hubungi mentor atau admin.');
        }

        // Check-in adalah attendance pending, bukan approval. Jangan pernah
        // mengubah verified/rejected row saat peserta menekan JOIN ulang.
        $sessionKey = $sesi->sessionKey();
        $row = BootcampLiveClassAttendance::firstOrNew([
            'id_course' => (int) $course->id_course,
            'id_user' => (int) $user->id,
            'session_key' => $sessionKey,
        ]);

        if (!$row->exists) {
            $row->proof_file = '';
            $row->catatan_mahasiswa = 'Check-in via tombol JOIN pada ' . $now->format('d M Y H:i');
            $row->status = BootcampLiveClassAttendance::STATUS_PENDING;
            $row->save();
        }

        // Catat heartbeat langsung pada join — user aktif di sesi live.
        $this->bootcampFlow->recordHeartbeat((int) $user->id, (int) $course->id_course, (int) $sesiId, $now);

        // Check-in lalu pindahkan peserta ke provider meeting. URL berasal dari
        // data sesi yang dikelola mentor/admin, bukan dari request mahasiswa.
        return redirect()->away($joinUrl)->with('success', $row->isVerified()
            ? 'Kehadiran sesi ini sudah terverifikasi.'
            : 'Check-in tercatat. Anda sedang diarahkan ke Live Class.');
    }

    /**
     * Heartbeat ping — dipanggil dari UI setiap ~60 detik saat user berada di
     * halaman Live Class. Menandai user "aktif" di sesi. Tidak ada efek ke
     * attendance status; murni untuk telemetry "activity heartbeat".
     */
    public function recordHeartbeat(Request $request, int $courseId, int $sesiId): JsonResponse
    {
        $user = Auth::guard('mahasiswa')->user() ?? Auth::user();
        if (!$user instanceof User) {
            return response()->json(['success' => false, 'message' => 'Sesi login tidak valid.'], 401);
        }
        $course = Course::findOrFail($courseId);
        $sesi = BootcampSession::where('id_course', $course->id_course)
            ->where('id_bootcamp_session', $sesiId)
            ->firstOrFail();
        if (!$this->bootcampFlow->resolveAccessibleEnrollment($user, $course)) {
            abort(403);
        }
        if (now()->lessThan($sesi->start_at) || now()->greaterThan($sesi->end_at)) {
            return response()->json(['success' => false, 'message' => 'Heartbeat hanya dapat dicatat saat sesi berlangsung.'], 422);
        }

        $ts = $this->bootcampFlow->recordHeartbeat((int) $user->id, $courseId, $sesiId);
        return response()->json(['success' => true, 'last_heartbeat' => $ts->toIso8601String()]);
    }

    /**
     * Self-reported pemahaman user terhadap sesi: paham | belum.
     * Disimpan di cache, bukan DB (per experience-layer spec: no migration).
     * Hanya boleh diset kalau user punya attendance verified ATAU sesi sudah
     * lewat (recap window).
     */
    public function setSesiFeedback(Request $request, int $courseId, int $sesiId): RedirectResponse
    {
        $user = Auth::guard('mahasiswa')->user() ?? Auth::user();
        if (!$user instanceof User) {
            return back()->with('error', 'Sesi login tidak valid.');
        }
        $feedback = trim((string) $request->input('feedback', ''));
        if (!in_array($feedback, ['paham', 'belum'], true)) {
            return back()->with('error', 'Feedback tidak valid.');
        }
        $course = Course::findOrFail($courseId);
        if (!$this->bootcampFlow->resolveAccessibleEnrollment($user, $course)) {
            abort(403);
        }
        BootcampSession::where('id_course', $course->id_course)
            ->where('id_bootcamp_session', $sesiId)
            ->firstOrFail();
        $this->bootcampFlow->setUserFeedback((int) $user->id, $courseId, $sesiId, $feedback);
        return back()->with('success', $feedback === 'paham'
            ? 'Terima kasih! Senang Anda memahami materi ini.'
            : 'Terima kasih masukannya. Materi akan kami perjelas di kesempatan berikutnya.');
    }
}
