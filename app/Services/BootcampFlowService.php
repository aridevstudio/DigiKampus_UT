<?php

namespace App\Services;

use App\Exceptions\Bootcamp\BootcampFlowException;
use App\Models\BootcampLiveClassAttendance;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\MaterialProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Support\Bootcamp\BootcampCapabilities;
use App\Support\Bootcamp\BootcampProgression;
use App\Support\Bootcamp\BootcampState;
use App\Support\Bootcamp\BootcampTransition;
use Illuminate\Support\Carbon;

/**
 * SINGLE SOURCE OF TRUTH for all bootcamp gating decisions.
 *
 * Refer to docs/Kerjain/adr-0002-bootcamp-flow-engine.md.
 *
 * Every user-action transition (submit assignment, upload attendance, access
 * final project, issue certificate, etc.) MUST be validated via
 * {@see assert()} before any side-effect (file upload, model create/update,
 * notification dispatch) runs in the controller. Controllers and blade views
 * MUST NOT contain bootcamp business-logic decisions — they call into this
 * service and propagate BootcampFlowException back to the framework renderer.
 *
 * No migration required. All state is computed on read from existing tables:
 *   - enrollments            (status, progress)
 *   - material_progresses    (is_completed)
 *   - quiz_attempts          (persentase, status)
 *   - bootcamp_live_class_attendances (status per session_key)
 *   - courses                (tanggal_webinar / jam_mulai/selesai_webinar)
 */
class BootcampFlowService
{
    // ─────────────────────────────────────────────────────────────────────
    // PUBLIC API — the only methods controllers should call
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Validate a user-action transition. Throws BootcampFlowException on
     * rejection. NEVER call before this method in a controller flow.
     *
     * @param  array<string,mixed> $context  Per-transition context (material, payload, session_key, …)
     * @throws BootcampFlowException  HTTP-403 (or 404/422 for specific transitions)
     */
    public function assert(User $user, Course $course, string $transition, array $context = []): void
    {
        match ($transition) {
            BootcampTransition::COMPLETE_MATERIAL    => $this->assertCompleteMaterial($user, $course, $context),
            BootcampTransition::SUBMIT_ASSIGNMENT     => $this->assertSubmitAssignment($user, $course, $context),
            BootcampTransition::SUBMIT_QUIZ          => $this->assertSubmitQuiz($user, $course, $context),
            BootcampTransition::JOIN_LIVE_CLASS      => $this->assertJoinLiveClass($user, $course, $context),
            BootcampTransition::UPLOAD_ATTENDANCE    => $this->assertUploadAttendance($user, $course, $context),
            BootcampTransition::ACCESS_FINAL_PROJECT => $this->assertAccessFinalProject($user, $course, $context),
            BootcampTransition::ISSUE_CERTIFICATE    => $this->assertIssueCertificate($user, $course, $context),
            default => throw BootcampFlowException::forTransition(
                $transition,
                "Transisi tidak dikenali: {$transition}",
                500,
            ),
        };
    }

    /**
     * Final-project gate. Returns true ONLY if:
     *   - enrollment active for this course
     *   - all non-quiz/assignment materials complete
     *   - all non-pretest quizzes passed (>= passing_score)
     *   - for bootcamp courses: every live-class session_key has an attendance row in 'verified' status
     *
     * Read-only. Safe to call repeatedly for badge rendering in views.
     */
    public function finalProjectGate(User $user, Course $course): bool
    {
        $enrollment = $this->resolveAccessibleEnrollment($user, $course);
        if (!$enrollment) {
            return false;
        }

        // 1) All material progress complete
        $totalMaterials = $course->materials()->count();
        if ($totalMaterials === 0) {
            return false;
        }

        $completedMaterials = MaterialProgress::where('id_mahasiswa', $user->id)
            ->whereIn('id_material', $course->materials()->pluck('id_material'))
            ->where('is_completed', true)
            ->count();
        if ($completedMaterials < $totalMaterials) {
            return false;
        }

        // 2) Every GRADED quiz passed (exclude pretest which is placement-only)
        $quizzes = Quiz::where('id_course', $course->id_course)
            ->where('is_active', true)
            ->where('is_pretest', false)
            ->get();
        foreach ($quizzes as $quiz) {
            $bestAttempt = QuizAttempt::where('id_quiz', $quiz->id_quiz)
                ->where('id_mahasiswa', $user->id)
                ->where('status', 'selesai')
                ->orderByDesc('persentase')
                ->orderByDesc('waktu_selesai')
                ->first();
            $score = $bestAttempt ? (float) $bestAttempt->persentase : 0.0;
            $passing = (int) ($quiz->passing_score ?? 0);
            if ($score < $passing) {
                return false;
            }
        }

        // 3) For bootcamp only: every live-class session is attendance-verified
        if ($this->isBootcamp($course)) {
            $sessionKeys = $this->collectLiveClassSessionKeys($course);
            if (!empty($sessionKeys)) {
                $verifiedKeys = BootcampLiveClassAttendance::where('id_user', $user->id)
                    ->where('id_course', $course->id_course)
                    ->whereIn('session_key', $sessionKeys)
                    ->where('status', BootcampLiveClassAttendance::STATUS_VERIFIED)
                    ->pluck('session_key')
                    ->all();
                $missing = array_diff($sessionKeys, $verifiedKeys);
                if (!empty($missing)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Compute the current BootcampProgression for the given (user, course).
     * Pure read — never persisted. Cheap to call repeatedly inside one request.
     */
    public function progression(User $user, Course $course): BootcampProgression
    {
        $enrollment = $this->resolveAccessibleEnrollment($user, $course);
        if (!$enrollment) {
            return new BootcampProgression(BootcampState::REGISTERED, [], ['reason' => 'no_accessible_enrollment']);
        }

        $gateOpen = $this->finalProjectGate($user, $course);
        $completed = ((string) ($enrollment->status ?? '')) === 'selesai'
            || (float) ($enrollment->progress ?? 0) >= 100.0;

        if ($completed) {
            return new BootcampProgression(BootcampState::COMPLETED, ['enrollment_status' => 'selesai'], []);
        }

        if ($gateOpen && $this->isBootcamp($course)) {
            return new BootcampProgression(BootcampState::FINAL_PROJECT_UNLOCKED, [], []);
        }

        if ($this->isBootcamp($course)) {
            return $this->computeBootcampMidFlow($user, $course);
        }

        // Non-bootcamp courses: simple learning/in-progress/completed ladder
        $state = ((float) ($enrollment->progress ?? 0)) > 0
            ? BootcampState::LEARNING
            : BootcampState::ENROLLED;
        return new BootcampProgression($state, [], ['enrollment_status' => $enrollment->status]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PRIVATE — transition guards (one per transition verb)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * B-1 fix: block backdoor completion of tugas/kuis materials.
     * These types MUST be submitted via submitAssignment / submitQuiz.
     */
    private function assertCompleteMaterial(User $user, Course $course, array $ctx): void
    {
        $material = $ctx['material'] ?? null;
        if (!$material instanceof CourseMaterial) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::COMPLETE_MATERIAL,
                'Material tidak ditemukan.',
                422,
            );
        }

        // C2 fix: use passed Course (defense: verify id_course matches material.id_course).
        if (!$course || (int) $course->id_course !== (int) $material->id_course) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::COMPLETE_MATERIAL,
                'Course untuk material ini tidak cocok atau tidak ditemukan.',
                422,
            );
        }

        if (!$this->resolveAccessibleEnrollment($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::COMPLETE_MATERIAL,
                'Anda tidak terdaftar aktif pada kursus ini.',
                403,
            );
        }

        $type = $this->normalizeMaterialType($material->tipe);
        if (in_array($type, ['tugas', 'kuis'], true)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::COMPLETE_MATERIAL,
                'Materi ini wajib diselesaikan melalui submission/quiz resmi, bukan endpoint ini.',
                403,
            );
        }
    }

    private function assertSubmitAssignment(User $user, Course $course, array $ctx): void
    {
        $material = $ctx['material'] ?? null;
        if (!$material instanceof CourseMaterial) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::SUBMIT_ASSIGNMENT,
                'Material tugas tidak ditemukan.',
                422,
            );
        }

        if (!$this->resolveAccessibleEnrollment($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::SUBMIT_ASSIGNMENT,
                'Anda tidak terdaftar aktif pada kursus ini atau pembayaran belum dikonfirmasi admin.',
                403,
            );
        }

        $payload = $ctx['payload'] ?? [];
        $isFinalProject = (bool) ($payload['is_final_project'] ?? false);

        // Fail-closed: misconfigured assignment (no deadline AND not flagged final)
        if (empty($payload['deadline']) && !$isFinalProject) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::SUBMIT_ASSIGNMENT,
                'Konfigurasi tugas tidak valid (tidak ada deadline dan bukan final project). Hubungi admin.',
                422,
            );
        }

        // Deadline check (skip if final-project without explicit deadline cap)
        if (!empty($payload['deadline'])) {
            $deadline = $this->parseAssignmentDeadline($payload['deadline']);
            $allowAfterDeadline = (bool) ($payload['allow_after_deadline'] ?? false);
            if (now()->greaterThan($deadline) && !$allowAfterDeadline) {
                throw BootcampFlowException::forTransition(
                    BootcampTransition::SUBMIT_ASSIGNMENT,
                    "Tenggat waktu pengumpulan tugas telah berakhir ({$deadline->format('d M Y, H:i')}).",
                    403,
                );
            }
        }

        // Final-project requires all gate conditions met.
        if ($isFinalProject && !$this->finalProjectGate($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::SUBMIT_ASSIGNMENT,
                'Prasyarat pengerjaan proyek akhir belum terpenuhi (modul/kuis/kehadiran live class).',
                403,
            );
        }
    }

    private function assertSubmitQuiz(User $user, Course $course, array $ctx): void
    {
        if (!$this->resolveAccessibleEnrollment($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::SUBMIT_QUIZ,
                'Anda tidak terdaftar aktif pada kursus ini atau pembayaran belum dikonfirmasi admin.',
                403,
            );
        }
        // Quiz-level hard deadline not enforced (kept async-pacing friendly);
        // gating happens via finalProjectGate's quiz-passed check.
    }

    private function assertJoinLiveClass(User $user, Course $course, array $ctx): void
    {
        if (!$this->isBootcamp($course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::JOIN_LIVE_CLASS,
                'Live class hanya tersedia untuk bootcamp.',
                404,
            );
        }
        if (!$this->resolveAccessibleEnrollment($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::JOIN_LIVE_CLASS,
                'Anda tidak terdaftar aktif pada bootcamp ini.',
                403,
            );
        }
        // Time window: mirror capabilities() — reject join after session ends.
        if ($course->tanggal_webinar === null) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::JOIN_LIVE_CLASS,
                'Jadwal live class belum ditentukan.',
                422,
            );
        }
        $primaryEnd = $this->resolveLiveClassEndTime($course, 'primary');
        if ($primaryEnd !== null && Carbon::now()->greaterThan($primaryEnd)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::JOIN_LIVE_CLASS,
                'Sesi live class sudah berakhir.',
                403,
            );
        }
    }

    private function assertUploadAttendance(User $user, Course $course, array $ctx): void
    {
        $sessionKey = trim((string) ($ctx['session_key'] ?? ''));
        if ($sessionKey === '') {
            throw BootcampFlowException::forTransition(
                BootcampTransition::UPLOAD_ATTENDANCE,
                'session_key wajib diisi.',
                422,
            );
        }

        if (!$this->isBootcamp($course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::UPLOAD_ATTENDANCE,
                'Halaman ini hanya untuk bootcamp (kategori tiket).',
                404,
            );
        }
        if (!$this->resolveAccessibleEnrollment($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::UPLOAD_ATTENDANCE,
                'Anda tidak terdaftar aktif pada bootcamp ini.',
                403,
            );
        }

        $validKeys = $this->collectLiveClassSessionKeys($course);
        if (!in_array($sessionKey, $validKeys, true)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::UPLOAD_ATTENDANCE,
                'Sesi live class tidak dikenali.',
                422,
            );
        }

        $sessionEnded = $this->resolveLiveClassEndTime($course, $sessionKey);
        if ($sessionEnded === null || now()->lessThan($sessionEnded)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::UPLOAD_ATTENDANCE,
                'Bukti kehadiran baru dapat diunggah setelah sesi live class berakhir.',
                403,
            );
        }

        $existing = BootcampLiveClassAttendance::where('id_user', $user->id)
            ->where('id_course', $course->id_course)
            ->where('session_key', $sessionKey)
            ->first();
        if ($existing && $existing->isVerified()) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::UPLOAD_ATTENDANCE,
                'Bukti kehadiran sudah terverifikasi dan tidak dapat diganti.',
                403,
            );
        }
    }

    private function assertAccessFinalProject(User $user, Course $course, array $ctx): void
    {
        if (!$this->finalProjectGate($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::ACCESS_FINAL_PROJECT,
                'Final project masih terkunci. Selesaikan semua modul, kuis, dan kehadiran live class terlebih dahulu.',
                403,
            );
        }
    }

    private function assertIssueCertificate(User $user, Course $course, array $ctx): void
    {
        if (!($course->sertifikat ?? false)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::ISSUE_CERTIFICATE,
                'Kursus ini tidak menyediakan sertifikat.',
                422,
            );
        }
        if (!$this->finalProjectGate($user, $course)) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::ISSUE_CERTIFICATE,
                'Selesaikan bootcamp terlebih dahulu untuk membuka sertifikat.',
                403,
            );
        }
        // Mirror capabilities() requirement: certificate only after full completion
        // (enrollment.status='selesai' OR progress>=100). Prevents early issue
        // when coursework has passed but enrollment book-keeping lags.
        $enrollment = $this->resolveAccessibleEnrollment($user, $course);
        $isCompleted = $enrollment !== null
            && (
                ((string) ($enrollment->status ?? '')) === 'selesai'
                || (float) ($enrollment->progress ?? 0) >= 100.0
            );
        if (!$isCompleted) {
            throw BootcampFlowException::forTransition(
                BootcampTransition::ISSUE_CERTIFICATE,
                'Progress belum 100% atau enrollment belum selesai.',
                403,
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // PRIVATE — shared helpers (mirrored from CourseController, kept in sync)
    // adding helpers here DOES NOT delete controller copies yet (Phase 4 cleanup).
    // ─────────────────────────────────────────────────────────────────────

    public function resolveAccessibleEnrollment(User $user, Course $course): ?Enrollment
    {
        return Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $course->id_course)
            ->accessible()
            ->first();
    }

    /**
     * Single forward-compatible source for live-class session_key strings.
     * Reading from Course webinar date + CourseModule ordered list.
     */
    public function collectLiveClassSessionKeys(Course $course): array
    {
        if (!$this->isBootcamp($course)) {
            return [];
        }

        $keys = [];
        if ($course->tanggal_webinar) {
            $keys[] = 'primary';
        }

        $modules = CourseModule::where('id_course', $course->id_course)
            ->orderBy('urutan')
            ->get();

        $start = $course->tanggal_webinar
            ? Carbon::parse($course->tanggal_webinar->format('Y-m-d') . ' ' . ($course->jam_mulai_webinar ?: '08:00:00'))
            : null;

        $index = 0;
        foreach ($modules as $module) {
            $slot = $start?->copy()->addMinutes(90 * $index);
            if (!$slot) {
                break;
            }
            $keys[] = 'qa_' . (int) $module->id_module . '_' . $slot->format('Y-m-d\TH:i');
            $index++;
        }

        return $keys;
    }

    public function resolveLiveClassEndTime(Course $course, string $sessionKey): ?Carbon
    {
        if (!$course->tanggal_webinar) {
            return null;
        }

        $sessionEnd = Carbon::parse(
            $course->tanggal_webinar->format('Y-m-d') . ' ' . ($course->jam_selesai_webinar ?: '17:00:00')
        );

        if ($sessionKey === 'primary') {
            return $sessionEnd;
        }

        if (preg_match('/^qa_(\d+)_(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})$/', $sessionKey, $m) === 1) {
            $moduleId = (int) $m[1];
            $module = CourseModule::where('id_course', $course->id_course)
                ->where('id_module', $moduleId)
                ->first();
            if (!$module) {
                return null;
            }
            try {
                $start = Carbon::parse($m[2]);
            } catch (\Throwable) {
                return null;
            }
            return $start->copy()->addMinutes(90);
        }

        return null;
    }

    public function parseAssignmentDeadline(mixed $deadline): Carbon
    {
        if (is_string($deadline) && trim($deadline) !== '') {
            try {
                return Carbon::parse($deadline);
            } catch (\Throwable) {
                // fall through to default
            }
        }
        return now()->addDays(7);
    }

    public function normalizeMaterialType(?string $type): string
    {
        return match ($type) {
            'video' => 'video',
            'kuis', 'quiz' => 'kuis',
            'tugas', 'assignment', 'tugas_akhir' => 'tugas',
            'bacaan', 'text' => 'bacaan',
            default => 'bacaan',
        };
    }

    public function isBootcamp(?Course $course): bool
    {
        return $course !== null
            && strtolower((string) ($course->kategori ?? '')) === 'tiket';
    }

    /**
     * Sub-routine for mid-flow bootcamp states (between enrolled and final gate).
     */
    private function computeBootcampMidFlow(User $user, Course $course): BootcampProgression
    {
        if (!$course->tanggal_webinar) {
            return new BootcampProgression(BootcampState::LEARNING, [], ['reason' => 'no_webinar_date']);
        }

        $sessionKeys = $this->collectLiveClassSessionKeys($course);
        $now = Carbon::now();

        $primaryEnd = $this->resolveLiveClassEndTime($course, 'primary');
        if ($primaryEnd !== null && $now->lessThan($this->resolveLiveClassStartTime($course))) {
            return new BootcampProgression(BootcampState::LEARNING, [], ['reason' => 'before_live_class']);
        }
        if ($primaryEnd !== null && $now->lessThanOrEqualTo($primaryEnd)) {
            return new BootcampProgression(BootcampState::LIVE_CLASS_ACTIVE, [], ['stage' => 'primary']);
        }
        if ($primaryEnd !== null && $now->greaterThan($primaryEnd)) {
            $afterWindow = $primaryEnd->copy()->addHour();
            if ($now->lessThan($afterWindow)) {
                return new BootcampProgression(
                    BootcampState::LIVE_CLASS_COMPLETED,
                    ['primary_ended' => true],
                    ['stage' => 'primary', 'next' => 'upload_attendance'],
                );
            }
        }

        // After primary window: inspect per-session attendance rows
        if (!empty($sessionKeys)) {
            $rows = BootcampLiveClassAttendance::where('id_user', $user->id)
                ->where('id_course', $course->id_course)
                ->whereIn('session_key', $sessionKeys)
                ->get()
                ->keyBy('session_key');

            $rejected = $rows->contains(fn ($r) => $r->isRejected());
            $pending = $rows->contains(fn ($r) => $r->isPending());
            $verifiedCount = $rows->where('status', BootcampLiveClassAttendance::STATUS_VERIFIED)->count();

            if ($rejected) {
                return new BootcampProgression(
                    BootcampState::ATTENDANCE_REJECTED,
                    ['rejected_present' => true, 'verified_count' => $verifiedCount],
                    ['rows' => $rows->keys()->all()],
                );
            }
            if ($pending) {
                return new BootcampProgression(
                    BootcampState::ATTENDANCE_PENDING,
                    ['pending_present' => true, 'verified_count' => $verifiedCount],
                    ['rows' => $rows->keys()->all()],
                );
            }
            if ($verifiedCount >= count($sessionKeys)) {
                return new BootcampProgression(
                    BootcampState::FINAL_PROJECT_LOCKED,
                    ['all_sessions_verified' => true],
                    ['next' => 'attempt_final_project'],
                );
            }
        }

        return new BootcampProgression(BootcampState::LEARNING, [], []);
    }

    private function resolveLiveClassStartTime(Course $course): ?Carbon
    {
        if (!$course->tanggal_webinar) {
            return null;
        }
        return Carbon::parse(
            $course->tanggal_webinar->format('Y-m-d') . ' ' . ($course->jam_mulai_webinar ?: '08:00:00')
        );
    }

    /**
     * Per-gate capability snapshot for (user, course). Designed for view/badge
     * rendering \u2014 controllers fetch once and pass to blade. Final per-resource
     * decisions (e.g. specific assignment's deadline) still route through
     * {@see assert()} with the relevant context.
     *
     * Returns human-readable lockedReasons keyed by transition name. Reasons
     * are in Bahasa Indonesia to match backend error messages.
     */
    public function capabilities(User $user, Course $course): BootcampCapabilities
    {
        $reasons = [];
        $enrollment = $this->resolveAccessibleEnrollment($user, $course);
        $hasEnrollment = (bool) $enrollment;

        // --- canCompleteMaterial ---
        $canCompleteMaterial = $hasEnrollment;
        if (!$canCompleteMaterial) {
            $reasons[BootcampTransition::COMPLETE_MATERIAL] = 'Anda tidak terdaftar aktif pada kursus ini.';
        }

        // --- canSubmitAssignment / canSubmitQuiz ---
        $canSubmitAssignment = $hasEnrollment;
        $canSubmitQuiz = $hasEnrollment;
        if (!$hasEnrollment) {
            $shared = 'Anda tidak terdaftar aktif pada kursus ini atau pembayaran belum dikonfirmasi admin.';
            $reasons[BootcampTransition::SUBMIT_ASSIGNMENT] = $shared;
            $reasons[BootcampTransition::SUBMIT_QUIZ] = $shared;
        }

        // --- canJoinLiveClass ---
        $canJoinLiveClass = false;
        if (!$hasEnrollment) {
            $reasons[BootcampTransition::JOIN_LIVE_CLASS] = 'Anda tidak terdaftar aktif pada bootcamp ini.';
        } elseif (!$this->isBootcamp($course)) {
            $reasons[BootcampTransition::JOIN_LIVE_CLASS] = 'Live class hanya tersedia untuk bootcamp.';
        } elseif ($course->tanggal_webinar === null) {
            $reasons[BootcampTransition::JOIN_LIVE_CLASS] = 'Jadwal live class belum ditentukan.';
        } else {
            $primaryEnd = $this->resolveLiveClassEndTime($course, 'primary');
            $canJoinLiveClass = $primaryEnd !== null && Carbon::now()->lessThanOrEqualTo($primaryEnd);
            if (!$canJoinLiveClass) {
                $reasons[BootcampTransition::JOIN_LIVE_CLASS] = 'Sesi live class sudah berakhir.';
            }
        }

        // --- canUploadLiveClassAttendance ---
        $canUploadLiveClassAttendance = false;
        if (!$hasEnrollment) {
            $reasons[BootcampTransition::UPLOAD_ATTENDANCE] = 'Anda tidak terdaftar aktif pada bootcamp ini.';
        } elseif (!$this->isBootcamp($course)) {
            $reasons[BootcampTransition::UPLOAD_ATTENDANCE] = 'Halaman ini hanya untuk bootcamp (kategori tiket).';
        } elseif ($course->tanggal_webinar === null) {
            $reasons[BootcampTransition::UPLOAD_ATTENDANCE] = 'Jadwal live class belum ditentukan.';
        } else {
            $sessionKeys = $this->collectLiveClassSessionKeys($course);
            $uploadableKeys = array_filter($sessionKeys, function (string $key) use ($course): bool {
                $endTime = $this->resolveLiveClassEndTime($course, $key);
                return $endTime !== null && Carbon::now()->greaterThan($endTime);
            });
            $canUploadLiveClassAttendance = !empty($uploadableKeys);
            if (!$canUploadLiveClassAttendance) {
                $reasons[BootcampTransition::UPLOAD_ATTENDANCE] = 'Belum ada sesi live class yang berakhir untuk diunggah.';
            }
        }

        // --- canAccessFinalProject (composite reason built from prerequisites) ---
        $canAccessFinalProject = $this->finalProjectGate($user, $course);
        if (!$canAccessFinalProject) {
            $reasonParts = [];
            $totalMaterials = $course->materials()->count();
            if ($totalMaterials > 0) {
                $completedMaterials = MaterialProgress::where('id_mahasiswa', $user->id)
                    ->whereIn('id_material', $course->materials()->pluck('id_material'))
                    ->where('is_completed', true)
                    ->count();
                if ($completedMaterials < $totalMaterials) {
                    $reasonParts[] = 'belum menyelesaikan semua materi';
                }
            } else {
                $reasonParts[] = 'course belum memiliki materi';
            }

            $allQuizzesPassed = true;
            $quizzes = Quiz::where('id_course', $course->id_course)
                ->where('is_active', true)
                ->where('is_pretest', false)
                ->get();
            foreach ($quizzes as $quiz) {
                $bestAttempt = QuizAttempt::where('id_quiz', $quiz->id_quiz)
                    ->where('id_mahasiswa', $user->id)
                    ->where('status', 'selesai')
                    ->orderByDesc('persentase')
                    ->first();
                if (!$bestAttempt || (float) $bestAttempt->persentase < (float) ($quiz->passing_score ?? 0)) {
                    $allQuizzesPassed = false;
                    break;
                }
            }
            if (!$allQuizzesPassed) {
                $reasonParts[] = 'ada kuis yang belum lulus';
            }

            if ($this->isBootcamp($course)) {
                $sessionKeys = $this->collectLiveClassSessionKeys($course);
                if (!empty($sessionKeys)) {
                    $verifiedCount = BootcampLiveClassAttendance::where('id_user', $user->id)
                        ->where('id_course', $course->id_course)
                        ->whereIn('session_key', $sessionKeys)
                        ->where('status', BootcampLiveClassAttendance::STATUS_VERIFIED)
                        ->count();
                    if ($verifiedCount < count($sessionKeys)) {
                        $reasonParts[] = 'belum semua kehadiran live class terverifikasi';
                    }
                }
            }

            $reasons[BootcampTransition::ACCESS_FINAL_PROJECT] = empty($reasonParts)
                ? 'Prasyarat final project belum terpenuhi.'
                : 'Final project masih terkunci: ' . implode(', ', $reasonParts) . '.';
        }

        // --- canIssueCertificate ---
        $canIssueCertificate = false;
        if (!($course->sertifikat ?? false)) {
            $reasons[BootcampTransition::ISSUE_CERTIFICATE] = 'Kursus ini tidak menyediakan sertifikat.';
        } elseif (!$canAccessFinalProject) {
            $reasons[BootcampTransition::ISSUE_CERTIFICATE] = 'Selesaikan bootcamp terlebih dahulu untuk membuka sertifikat.';
        } elseif ($enrollment !== null) {
            $isCompleted = ((string) ($enrollment->status ?? '')) === 'selesai'
                || (float) ($enrollment->progress ?? 0) >= 100.0;
            if ($isCompleted) {
                $canIssueCertificate = true;
            } else {
                $reasons[BootcampTransition::ISSUE_CERTIFICATE] = 'Progress belum 100% atau enrollment belum selesai.';
            }
        } else {
            $reasons[BootcampTransition::ISSUE_CERTIFICATE] = 'Enrollment tidak ditemukan.';
        }

        return new BootcampCapabilities(
            canCompleteMaterial: $canCompleteMaterial,
            canSubmitAssignment: $canSubmitAssignment,
            canSubmitQuiz: $canSubmitQuiz,
            canJoinLiveClass: $canJoinLiveClass,
            canUploadLiveClassAttendance: $canUploadLiveClassAttendance,
            canAccessFinalProject: $canAccessFinalProject,
            canIssueCertificate: $canIssueCertificate,
            lockedReasons: $reasons,
        );
    }
}
