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

        // Gate 2 — sesi masih dalam rentang live, ATAU baru lewat (late-join default 15 menit)
        $now = now();
        $lateJoinAllowance = (int) config('bootcamp.late_join_minutes', 15);
        $joinWindowEnd = $sesi->end_at->copy()->addMinutes($lateJoinAllowance);
        $joinable = $now->greaterThanOrEqualTo($sesi->start_at)
            && $now->lessThanOrEqualTo($joinWindowEnd);
        if (!$joinable) {
            return back()->with('error', 'Sesi belum dimulai atau sudah berakhir lebih dari ' . $lateJoinAllowance . ' menit. Bukti kehadiran dapat diunggah lewat menu Live Class.');
        }

        // Gate 3 — kapasitas tidak penuh (jika ada limit)
        if (!$this->bootcampFlow->incrementCapacity($course)) {
            return back()->with('error', 'Kapasitas sesi sudah penuh. Hubungi admin.');
        }

        // Insert/Update attendance row.
        $sessionKey = $sesi->sessionKey();
        $row = BootcampLiveClassAttendance::firstOrNew([
            'id_course' => (int) $course->id_course,
            'id_user' => (int) $user->id,
            'session_key' => $sessionKey,
        ]);

        $row->proof_file = $row->proof_file ?? '';
        $row->catatan_mahasiswa = $row->catatan_mahasiswa ?? ('Auto-check-in via JOIN tombol pada ' . $now->format('d M Y H:i'));
        $row->status = $now->greaterThan($sesi->end_at->copy()->addMinutes(5))
            ? BootcampLiveClassAttendance::STATUS_VERIFIED
            : BootcampLiveClassAttendance::STATUS_PENDING;
        $row->save();

        // Catat heartbeat langsung pada join — user aktif di sesi live.
        $this->bootcampFlow->recordHeartbeat((int) $user->id, (int) $course->id_course, (int) $sesiId, $now);

        $msg = $row->isVerified()
            ? 'Berhasil tercatat hadir untuk sesi ini (auto-verified).'
            : 'Check-in tercatat. Unggah bukti kehadiran di tab Live Class agar admin dapat memverifikasi.';

        return back()->with('success', $msg);
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
        $this->bootcampFlow->setUserFeedback((int) $user->id, $courseId, $sesiId, $feedback);
        return back()->with('success', $feedback === 'paham'
            ? 'Terima kasih! Senang Anda memahami materi ini.'
            : 'Terima kasih masukannya. Materi akan kami perjelas di kesempatan berikutnya.');
    }
}
