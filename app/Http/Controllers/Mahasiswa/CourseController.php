<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Exceptions\Bootcamp\BootcampFlowException;
use App\Http\Controllers\Controller;
use App\Models\AutomaticCertificate;
use App\Models\AssignmentSubmission;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\DosenNotification;
use App\Models\CourseRating;
use App\Models\CourseDiscussion;
use App\Models\CourseInstructorNote;
use App\Models\CourseMaterial;
use App\Models\Assignment;
use App\Models\BootcampLiveClassAttendance;
use App\Models\Notification;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\BootcampFlowService;
use App\Support\Bootcamp\BootcampTransition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Single source of truth for ALL bootcamp gating decisions.
     * Constructor-injected via Laravel container auto-resolution.
     */
    public function __construct(protected BootcampFlowService $bootcampFlow) {}
    /**
     * Show get courses page (course catalog)
     */
    public function index()
    {
        $search = request('search');
        $tipe = request('tipe');
        $user = Auth::guard('mahasiswa')->user();
        
        // Get IDs of courses user is already enrolled in
        $enrolledCourseIds = [];
        if ($user) {
            $enrolledCourseIds = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
                ->pluck('id_course')
                ->toArray();
        }
        
        // Get courses from database (exclude enrolled courses)
        $courses = Course::with(['dosen', 'jurusan', 'modules' => function ($q) {
                $q->orderBy('urutan');
            }, 'modules.materials' => function ($q) {
                $q->orderBy('urutan');
            }])
            ->withCount('ratings as real_jumlah_ulasan')
            ->withAvg('ratings as real_rating', 'rating')
            ->aktif() // Only active courses
            ->search($search) // Search by nama_course or deskripsi
            ->when($tipe && $tipe !== 'semua', function ($query) use ($tipe) {
                return $query->where('kategori', $tipe);
            })
            ->whereNotExists(function ($query) {
                // Keep only the latest active course for duplicate names per dosen.
                $query->select(DB::raw(1))
                    ->from('courses as newer_courses')
                    ->whereColumn('newer_courses.id_dosen', 'courses.id_dosen')
                    ->whereRaw('LOWER(TRIM(newer_courses.nama_course)) = LOWER(TRIM(courses.nama_course))')
                    ->where('newer_courses.status', 'aktif')
                    ->whereColumn('newer_courses.id_course', '>', 'courses.id_course');
            })
            ->when(!empty($enrolledCourseIds), function ($query) use ($enrolledCourseIds) {
                return $query->whereNotIn('id_course', $enrolledCourseIds);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('pages.mahasiswa.get-courses', [
            'courses' => $courses,
            'selectedTipe' => $tipe ?? 'semua',
            'searchQuery' => $search,
        ]);
    }

    /**
     * Dedicated bootcamp and ticket event catalog for mahasiswa.
     */
    public function bootcampCatalog()
    {
        $search = request('search');
        $filter = request('filter', 'semua');
        $user = Auth::guard('mahasiswa')->user();

        $enrolledCourseIds = [];
        if ($user) {
            $enrolledCourseIds = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
                ->pluck('id_course')
                ->toArray();
        }

        $bootcampCourses = Course::with(['dosen', 'jurusan'])
            ->withCount('ratings as real_jumlah_ulasan')
            ->withAvg('ratings as real_rating', 'rating')
            ->aktif()
            ->where('kategori', 'tiket')
            ->search($search)
            ->when($filter === 'mine' && !empty($enrolledCourseIds), function ($query) use ($enrolledCourseIds) {
                return $query->whereIn('id_course', $enrolledCourseIds);
            })
            ->when($filter === 'available' && !empty($enrolledCourseIds), function ($query) use ($enrolledCourseIds) {
                return $query->whereNotIn('id_course', $enrolledCourseIds);
            })
            ->orderByRaw('tanggal_webinar IS NULL, tanggal_webinar ASC')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.mahasiswa.bootcamp', [
            'bootcampCourses' => $bootcampCourses,
            'searchQuery' => $search,
            'selectedFilter' => $filter,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }

    /**
     * Show bootcamp detail page (wraps show with bootcamp context).
     */
    public function bootcampDetail($id)
    {
        $course = Course::with([
            'dosen',
            'jurusan',
            'materials' => fn ($query) => $query->orderBy('urutan'),
            'modules' => fn ($query) => $query->orderBy('urutan'),
            'modules.materials' => fn ($query) => $query->orderBy('urutan'),
            'assignments' => fn ($query) => $query->orderBy('deadline'),
            'ratings.mahasiswa.profile',
            'learningGoals',
            'instructorNotes' => fn ($query) => $query->where('is_active', true)->latest(),
        ])->findOrFail($id);

        $user = Auth::guard('mahasiswa')->user();
        $isEnrolled = false;
        $enrollment = null;

        if ($user) {
            $enrollment = $course->enrollments()
                ->where('id_mahasiswa', $user->id)
                ->first();
            $isEnrolled = $enrollment !== null;
        }

        if ($isEnrolled && $enrollment && in_array($enrollment->status, ['aktif', 'selesai', 'in_progress'], true)) {
            // Trigger assignment reminders on-demand
            $this->checkAndSendAssignmentReminders($user, $course);

            // Build tab dataset
            $data = $this->prepareBootcampDashboardData($user, $course, $enrollment);

            // Inject session-based bootcamp engine data: live-class sesi list,
            // per-sesi attendance map (already in $liveClassAttendances $data), and
            // capability snapshot for badge/CTA rendering. Single source of truth
            // stays in BootcampFlowService; this controller only projects it.
            $data['sesiList'] = $this->bootcampFlow->resolveActiveSesi($course);

            // Backward-compat fallback: if course has no real sesi rows AND legacy
            // liveClassAttendances only carries 'primary', still expose primary
            // virtual entry so blade keeps rendering legacy single-session until
            // admin migrates data via Kelola Sesi UI.
            if (empty($data['sesiList']) && !empty($course->tanggal_webinar)) {
                // resolveActiveSesi already returns the virtual primary for legacy
                // courses, so the conditional above is defensive only.
                $data['sesiList'] = $this->bootcampFlow->resolveActiveSesi($course);
            }

            // Per-sesi attendance lookup map keyed by session_key (sesi_<id> | primary | qa_*).
            $sesiIdByKey = [];
            foreach ($data['sesiList'] as $sesi) {
                $key = $sesi instanceof \App\Models\BootcampSession
                    ? $sesi->sessionKey()
                    : (string) ($sesi->session_key ?? '');
                if ($key !== '') {
                    $sesiIdByKey[$key] = $sesi;
                }
            }
            $data['sesiIdByKey'] = $sesiIdByKey;

            $data['capabilities'] = $this->bootcampFlow->capabilities($user, $course);

            // Experience-layer projection: timeline (used by Overview tab + Live Class feedback lookup)
            $data['sesiTimeline'] = $this->bootcampFlow->learningJourneyTimeline($user, $course);

            return view('pages.mahasiswa.bootcamp-detail', $data);
        }

        return $this->show($id, 'bootcamp');
    }

    /**
     * Show bootcamp learning page (wraps learn with bootcamp context).
     */
    public function bootcampLearn($id)
    {
        return $this->learn($id, 'bootcamp');
    }

    /**
     * Show user's enrolled bootcamps (Bootcamp Saya).
     */
    public function bootcampMy()
    {
        $user = Auth::guard('mahasiswa')->user();
        $sort = request('sort', 'terbaru');

        $query = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->with(['course.dosen', 'course.jurusan'])
            ->whereHas('course', function ($q) {
                $q->where('kategori', 'tiket');
            });

        switch ($sort) {
            case 'progress':
                $query->orderBy('progress', 'desc');
                break;
            case 'nama':
                $query->join('courses', 'enrollments.id_course', '=', 'courses.id_course')
                      ->orderBy('courses.nama_course', 'asc')
                      ->select('enrollments.*');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $enrollments = $query->paginate(4);

        return view('pages.mahasiswa.bootcamp-saya', [
            'enrollments' => $enrollments,
            'selectedSort' => $sort,
        ]);
    }

    /**
     * Resolve the detail route name based on context.
     */
    private function detailRouteName(string $context): string
    {
        return $context === 'bootcamp' ? 'mahasiswa.bootcamp-detail' : 'mahasiswa.course-detail';
    }

    /**
     * Show course detail page
     */
    public function show($id, string $context = 'course')
    {
        $course = Course::with([
            'dosen',
            'jurusan',
            'materials' => fn ($query) => $query->orderBy('urutan'),
            'modules' => fn ($query) => $query->orderBy('urutan'),
            'modules.materials' => fn ($query) => $query->orderBy('urutan'),
            'assignments' => fn ($query) => $query->orderBy('deadline'),
            'ratings.mahasiswa.profile',
            'learningGoals',
        ])
            ->findOrFail($id);
        
        // Get user's enrollment status if logged in
        $user = Auth::guard('mahasiswa')->user();
        $isEnrolled = false;
        $enrollment = null;
        $isFavorited = false;
        $issuedCertificate = null;
        
        if ($user) {
            $enrollment = $course->enrollments()
                ->where('id_mahasiswa', $user->id)
                ->first();
            $isEnrolled = $enrollment !== null;

            if ($isEnrolled && $enrollment) {
                $enrollment = $this->syncWebinarCompletionIfEligible($user, $course, $enrollment);
                $issuedCertificate = $this->issueCertificateForEnrollmentIfEligible(
                    $user,
                    $course,
                    $enrollment,
                    (int) round((float) ($enrollment->progress ?? 0))
                );
            }
            
            $isFavorited = \App\Models\Favorite::where('id_mahasiswa', $user->id)
                ->where('id_course', $course->id_course)
                ->exists();
        }
        
        $isBootcamp = $this->isBootcamp($course);

        return view('pages.mahasiswa.course-detail', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'enrollment' => $enrollment,
            'isFavorited' => $isFavorited,
            'issuedCertificate' => $issuedCertificate,
            'context' => $context,
            'isBootcamp' => $isBootcamp,
        ]);
    }

    /**
     * Show user's enrolled courses (Kursus Saya)
     */
    public function myCourses()
    {
        $user = Auth::guard('mahasiswa')->user();
        $tipe = request('tipe', 'all');
        $sort = request('sort', 'terbaru');
        
        // Get user's enrollments with course data
        $query = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->with(['course.dosen', 'course.jurusan']);
        
        // Filter by course category
        if ($tipe && $tipe !== 'all') {
            $query->whereHas('course', function($q) use ($tipe) {
                $q->where('kategori', $tipe);
            });
        }
        
        // Sorting
        switch ($sort) {
            case 'progress':
                $query->orderBy('progress', 'desc');
                break;
            case 'nama':
                $query->join('courses', 'enrollments.id_course', '=', 'courses.id_course')
                      ->orderBy('courses.nama_course', 'asc')
                      ->select('enrollments.*');
                break;
            default: // terbaru
                $query->orderBy('created_at', 'desc');
        }
        
        $enrollments = $query->paginate(4);
        
        // Get course type counts for filter badges
        $allCount = \App\Models\Enrollment::where('id_mahasiswa', $user->id)->count();
        $typeCounts = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->join('courses', 'enrollments.id_course', '=', 'courses.id_course')
            ->selectRaw('courses.kategori, COUNT(*) as count')
            ->groupBy('courses.kategori')
            ->pluck('count', 'kategori')
            ->toArray();
        
        // Get recommended courses (courses user hasn't enrolled in)
        $enrolledCourseIds = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->pluck('id_course');

        // Get user's active juridiction/ major to provide contextually-accurate recommendations.
        $userJurusanId = $user->profile->id_jurusan ?? null;
        
        $recommendedCourses = Course::with(['dosen'])
            ->whereNotIn('id_course', $enrolledCourseIds)
            ->aktif()
            ->when($userJurusanId, function ($query) use ($userJurusanId) {
                return $query->where('id_jurusan', $userJurusanId);
            })
            ->orderBy('rating', 'desc')
            ->limit(3)
            ->get();
            
        // If we didn't find enough recommendations for their specific major, backfill with highly-rated courses
        if ($recommendedCourses->count() < 3) {
            $existingIds = $recommendedCourses->pluck('id_course')->merge($enrolledCourseIds);
            $backfill = Course::with(['dosen'])
                ->whereNotIn('id_course', $existingIds)
                ->aktif()
                ->orderBy('rating', 'desc')
                ->limit(3 - $recommendedCourses->count())
                ->get();
                
            $recommendedCourses = $recommendedCourses->merge($backfill);
        }
        
        // Weekly target (simple calculation)
        $weeklyTarget = [
            'completed' => $enrollments->where('progress', '>=', 100)->count(),
            'total' => 3, // Target 3 modules per week
        ];
        
        return view('pages.mahasiswa.courses', [
            'enrollments' => $enrollments,
            'selectedTipe' => $tipe,
            'selectedSort' => $sort,
            'allCount' => $allCount,
            'typeCounts' => $typeCounts,
            'recommendedCourses' => $recommendedCourses,
            'weeklyTarget' => $weeklyTarget,
        ]);
    }

    /**
     * Show course learning page
     *
     * @param mixed $id
     * @param string $context 'course' or 'bootcamp'
     */
    public function learn($id, string $context = 'course')
    {
        $user = Auth::guard('mahasiswa')->user();
        
        $course = Course::with([
            'dosen',
            'materials',
            'assignments',
            'instructorNotes' => fn ($query) => $query->where('is_active', true)->latest(),
            'learningGoals',
        ])
            ->findOrFail($id);
        $courseId = $course->id_course;
        
        // Check if user is enrolled
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $id)
            ->first();
        
        if (!$enrollment) {
            return redirect()->route($this->detailRouteName($context), $id)
                ->with('error', $context === 'bootcamp' ? 'Anda harus terdaftar untuk mengakses bootcamp ini' : 'Anda harus terdaftar untuk mengakses kursus ini');
        }

        if ($enrollment->status === 'pending') {
            return redirect()->route($this->detailRouteName($context), $id)
                ->with('error', $context === 'bootcamp' ? 'Bootcamp berbayar ini masih menunggu konfirmasi pembayaran dari admin.' : 'Kursus berbayar ini masih menunggu konfirmasi pembayaran dari admin.');
        }

        $courseModules = CourseModule::where('id_course', $courseId)
            ->orderBy('urutan')
            ->get()
            ->keyBy('id_module');

        $courseQuizzes = Quiz::with('module')
            ->where('id_course', $courseId)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get()
            ->groupBy('id_module');

        $completedQuizAttempts = QuizAttempt::where('id_mahasiswa', $user->id)
            ->where('status', 'selesai')
            ->whereIn('id_quiz', $courseQuizzes->flatten(1)->pluck('id_quiz'))
            ->orderByDesc('waktu_selesai')
            ->orderByDesc('id_attempt')
            ->get()
            ->groupBy('id_quiz');

        // Get materials grouped by module
        $materials = $course->materials()->orderBy('urutan')->get();
        
        // Group materials by module number
        $modules = [];
        foreach ($materials as $material) {
            $moduleNum = $material->id_module ?? $material->modul ?? 1;
            $moduleEntity = $courseModules->get($moduleNum);
            $materialType = $this->normalizeMaterialType($material->tipe);
            $materialTitle = $material->judul_material ?? $material->judul ?? 'Materi';
            $materialQuiz = $materialType === 'kuis'
                ? $this->resolveQuizForLegacyMaterialLink((int) $courseId, $material)
                : null;
            $moduleQuiz = $materialQuiz
                ?? $courseQuizzes->get($moduleNum)?->first(fn (Quiz $quiz) => !$quiz->is_pretest)
                ?? $courseQuizzes->get($moduleNum)?->first();
            $moduleQuizAttempt = $moduleQuiz ? $completedQuizAttempts->get($moduleQuiz->id_quiz)?->first() : null;
            $moduleTopic = trim((string) ($moduleEntity?->judul_module ?? $material->topik ?? 'Materi'));
            $moduleTopic = preg_replace('/^modul\s+\d+\s*:\s*/i', '', $moduleTopic) ?: 'Materi';
            if (!isset($modules[$moduleNum])) {
                $modules[$moduleNum] = [
                    'title' => 'Modul ' . $moduleNum . ': ' . $moduleTopic,
                    'materials' => [],
                    'completed' => 0,
                    'total' => 0,
                    'quiz' => $moduleQuiz ? [
                        'id' => $moduleQuiz->id_quiz,
                        'title' => $moduleQuiz->judul ?: 'Kuis Akhir Modul',
                        'duration' => $moduleQuiz->durasi_menit,
                        'is_pretest' => (bool) $moduleQuiz->is_pretest,
                        'passing_score' => $moduleQuiz->passing_score,
                    ] : null,
                    'quizzes' => [],
                    'quiz_completed' => $moduleQuizAttempt !== null,
                    'feedback_available' => $moduleQuizAttempt !== null,
                    'assignment' => null,
                ];
            }
            
            // Check if material is completed
            $isCompleted = \App\Models\MaterialProgress::where('id_mahasiswa', $user->id)
                ->where('id_material', $material->id_material)
                ->where('is_completed', true)
                ->exists();

            $modules[$moduleNum]['materials'][] = [
                'id' => $material->id_material,
                'title' => $materialTitle,
                'type' => $materialType,
                'content' => $material->konten,
                'duration' => $material->durasi ?? '10 menit',
                'is_completed' => $isCompleted,
                'video_url' => $material->video_url,
                'quiz_id' => $materialType === 'kuis' ? ($materialQuiz?->id_quiz) : null,
            ];

            if ($materialType === 'kuis' && $materialQuiz) {
                $materialQuizAttempt = $completedQuizAttempts->get($materialQuiz->id_quiz)?->first();
                $quizData = [
                    'id' => $materialQuiz->id_quiz,
                    'title' => $materialTitle ?: 'Kuis Akhir Modul',
                    'duration' => max(5, (int) ($materialQuiz->durasi_menit ?? $material->durasi ?? 30)),
                    'is_pretest' => (bool) $materialQuiz->is_pretest,
                    'passing_score' => (int) ($materialQuiz->passing_score ?? 70),
                    'completed' => $materialQuizAttempt !== null,
                ];

                if (!collect($modules[$moduleNum]['quizzes'])->contains('id', $quizData['id'])) {
                    $modules[$moduleNum]['quizzes'][] = $quizData;
                }

                if (empty($modules[$moduleNum]['quiz'])) {
                    $modules[$moduleNum]['quiz'] = $quizData;
                    $modules[$moduleNum]['quiz_completed'] = $quizData['completed'];
                    $modules[$moduleNum]['feedback_available'] = $quizData['completed'];
                }
            } elseif ($materialType === 'kuis' && empty($modules[$moduleNum]['quiz'])) {
                $modules[$moduleNum]['quiz'] = [
                    'id' => $material->id_material,
                    'title' => $materialTitle ?: 'Kuis Akhir Modul',
                    'duration' => max(5, (int) ($material->durasi ?? 30)),
                    'is_pretest' => (bool) ($material->is_pretest ?? false),
                    'passing_score' => 70,
                    'completed' => false,
                ];
            }

            if ($materialType === 'tugas' && $modules[$moduleNum]['assignment'] === null) {
                $modules[$moduleNum]['assignment'] = [
                    'id' => $material->id_material,
                    'title' => $materialTitle,
                    'duration' => $material->durasi,
                ];
            }
            
            $modules[$moduleNum]['total']++;
            if ($isCompleted) {
                $modules[$moduleNum]['completed']++;
            }
        }
        
        if (!empty($modules)) {
            ksort($modules, SORT_NUMERIC);
        }

        $completedAssignments = session('completed_assignments', []);

        foreach ($modules as $moduleNum => &$module) {
            $assignmentKey = $courseId . '_' . $moduleNum;
            $module['assignment_completed'] = in_array($assignmentKey, $completedAssignments);
        }
        unset($module); // Break reference
        
        // Get current material from query (?material=ID) when available.
        $currentMaterial = null;
        $currentModuleIndex = 0;
        $requestedMaterialId = (int) request('material');

        if ($requestedMaterialId > 0) {
            foreach ($modules as $idx => $module) {
                foreach ($module['materials'] as $mat) {
                    if ((int) $mat['id'] === $requestedMaterialId) {
                        $currentMaterial = $mat;
                        $currentModuleIndex = $idx;
                        break 2;
                    }
                }
            }
        }

        if (!$currentMaterial) {
            foreach ($modules as $idx => $module) {
                foreach ($module['materials'] as $mat) {
                    if (!$mat['is_completed']) {
                        $currentMaterial = $mat;
                        $currentModuleIndex = $idx;
                        break 2;
                    }
                }
            }
        }
        
        // If all completed, show first material
        if (!$currentMaterial && !empty($modules)) {
            $firstModule = reset($modules);
            $currentMaterial = $firstModule['materials'][0] ?? null;
            $currentModuleIndex = array_key_first($modules);
        }
        
        // Calculate overall progress
        $totalMaterials = $materials->count();
        $completedMaterials = \App\Models\MaterialProgress::where('id_mahasiswa', $user->id)
            ->whereIn('id_material', $materials->pluck('id_material'))
            ->where('is_completed', true)
            ->count();
        $progressPercent = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
        
        // Update enrollment progress and status consistently.
        $nextStatus = $progressPercent >= 100
            ? 'selesai'
            : (in_array($enrollment->status, ['aktif', 'in_progress'], true) ? $enrollment->status : 'aktif');

        $enrollment->update([
            'progress' => $progressPercent,
            'status' => $nextStatus,
        ]);

        $enrollment = $enrollment->fresh() ?: $enrollment;
        $enrollment = $this->syncWebinarCompletionIfEligible($user, $course, $enrollment);
        $progressPercent = (int) round((float) ($enrollment->progress ?? $progressPercent));
        $issuedCertificate = $this->issueCertificateForEnrollmentIfEligible($user, $course, $enrollment, $progressPercent);
        
        $isBootcamp = $this->isBootcamp($course);

        return view('pages.mahasiswa.course-learn', [
            'course' => $course,
            'enrollment' => $enrollment,
            'modules' => $modules,
            'currentMaterial' => $currentMaterial,
            'currentModuleIndex' => $currentModuleIndex,
            'progressPercent' => $progressPercent,
            'completedMaterials' => $completedMaterials,
            'totalMaterials' => $totalMaterials,
            'issuedCertificate' => $issuedCertificate,
            'context' => $context,
            'isBootcamp' => $isBootcamp,
            'dosenNotes' => $course->instructorNotes->map(function (CourseInstructorNote $note) {
                return [
                    'id' => $note->id_course_instructor_note,
                    'title' => $note->judul ?: 'Catatan Dosen',
                    'content' => $note->konten,
                    'created_at' => optional($note->created_at)->diffForHumans(),
                ];
            })->values(),
        ]);
    }

    /**
     * Mark material as complete
     */
    public function completeMaterial($id)
    {
        $user = Auth::guard('mahasiswa')->user();

        $material = CourseMaterial::findOrFail($id);

        // R1/B-1 fix: route through BootcampFlowService. Backdoor bypass of
        // tugas/kuis materials via this endpoint is now closed.
        // BootcampFlowException is rendered to 403 JSON / back()->error by
        // the renderer registered in bootstrap/app.php.
        $this->bootcampFlow->assert(
            $user,
            Course::find($material->id_course),
            BootcampTransition::COMPLETE_MATERIAL,
            ['material' => $material],
        );

        // Check enrollment
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $material->id_course)
            ->first();
        
        if (!$enrollment) {
            return back()->with('error', 'Anda tidak terdaftar di kursus ini');
        }

        if ($enrollment->status === 'pending') {
            return back()->with('error', 'Akses materi belum dibuka karena pembayaran masih menunggu konfirmasi admin.');
        }
        
        // Create or update progress
        \App\Models\MaterialProgress::updateOrCreate(
            [
                'id_mahasiswa' => $user->id,
                'id_material' => $id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        // Recalculate aggregate progress and update enrollment status.
        $enrollment->recalculateProgress($user->id);
        $enrollment = $enrollment->fresh() ?: $enrollment;
        $course = $material->course ?: Course::find($material->id_course);
        $issuedCertificate = $course
            ? $this->issueCertificateForEnrollmentIfEligible($user, $course, $enrollment, (int) round((float) ($enrollment->progress ?? 0)))
            : null;

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Materi ditandai selesai.',
                'certificate_ready' => $issuedCertificate !== null,
            ]);
        }

        return back()->with('success', 'Materi ditandai selesai!');
    }
    
    /**
     * Show favorites page
     */
    public function favorites()
    {
        $user = Auth::guard('mahasiswa')->user();
        
        if (!$user) {
            return redirect()->route('mahasiswa.login');
        }
        
        $favorites = \App\Models\Favorite::with(['course', 'course.dosen'])
            ->whereHas('course', function ($query) {
                $query->where('status', 'aktif'); // Kursus menggunakan status 'aktif'
            })
            ->where('id_mahasiswa', $user->id)
            ->latest()
            ->get();
            
        return view('pages.mahasiswa.favorites', compact('favorites'));
    }
    
    /**
     * Add course to favorites
     */
    public function addToFavorite(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();
        $courseId = $request->input('id_course');
        
        if (!$user) {
            return redirect()->route('mahasiswa.login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Check if course exists
        $course = Course::find($courseId);
        if (!$course) {
            return back()->with('error', 'Kursus tidak ditemukan.');
        }
        
        // Check if already favorited
        $exists = \App\Models\Favorite::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();
            
        if ($exists) {
            return back()->with('info', 'Kursus sudah ada di favorit.');
        }
        
        // Add to favorites
        \App\Models\Favorite::create([
            'id_mahasiswa' => $user->id,
            'id_course' => $courseId,
        ]);
        
        return back()->with('success', 'Kursus berhasil ditambahkan ke favorit!');
    }
    
    /**
     * Remove course from favorites
     */
    public function removeFromFavorite($id)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        if (!$user) {
            return redirect()->route('mahasiswa.login');
        }
        
        $favorite = \App\Models\Favorite::where('id_mahasiswa', $user->id)
            ->where('id_course', $id)
            ->first();
            
        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Kursus berhasil dihapus dari favorit.');
        }
        
        return back()->with('error', 'Kursus tidak ditemukan di favorit.');
    }
    
    /**
     * Show quiz page for a course module
     */
    public function quiz($courseId, $quizId)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();

        if (!$enrollment) {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Anda belum terdaftar di kursus ini.');
        }

        if ($enrollment->status === 'pending') {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Webinar atau kursus ini masih menunggu konfirmasi pembayaran dari admin.');
        }

        $course = Course::findOrFail($courseId);
        $quizModel = $this->resolveCourseQuiz((int) $courseId, (int) $quizId);
        if ((int) $quizId !== (int) $quizModel->id_quiz) {
            return redirect()->route('mahasiswa.course-quiz', [
                'courseId' => $courseId,
                'quizId' => (int) $quizModel->id_quiz,
                'q' => (int) request('q', 1),
            ]);
        }

        $resolvedQuizId = (int) $quizModel->id_quiz;
        $orderedQuestions = $this->getQuizQuestionsInDisplayOrder($quizModel);

        if ($orderedQuestions->isEmpty()) {
            return redirect()->route('mahasiswa.course-learn', $courseId)
                ->with('error', 'Kuis ini belum memiliki soal.');
        }

        $sessionKey = $this->getQuizStartedAtSessionKey($resolvedQuizId);
        if (!session()->has($sessionKey)) {
            session([$sessionKey => now()->toIso8601String()]);
        }

        $currentQuestion = max(1, min((int) request('q', 1), $orderedQuestions->count()));
        $userAnswers = $this->getQuizSessionAnswers($resolvedQuizId);
        $flaggedQuestions = $this->getQuizSessionFlags($resolvedQuizId);

        return view('pages.mahasiswa.course-quiz', [
            'course' => $course,
            'quiz' => $this->buildQuizViewData($quizModel, $orderedQuestions),
            'questions' => $orderedQuestions
                ->values()
                ->mapWithKeys(fn (QuizQuestion $question, int $index) => [
                    $index + 1 => $this->buildQuizQuestionViewData($question),
                ])
                ->all(),
            'currentQuestion' => $currentQuestion,
            'userAnswers' => $userAnswers,
            'flaggedQuestions' => $flaggedQuestions,
        ]);
    }
    
    /**
     * Save quiz answer to session
     */
    public function saveQuizAnswer(Request $request, $courseId, $quizId)
    {
        $user = Auth::guard('mahasiswa')->user();
        if (!$this->isEnrolledInCourse($user->id, (int) $courseId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar di kursus ini.',
            ], 403);
        }

        $quiz = $this->resolveCourseQuiz((int) $courseId, (int) $quizId);
        $resolvedQuizId = (int) $quiz->id_quiz;
        $orderedQuestions = $this->getQuizQuestionsInDisplayOrder($quiz);
        $questionNumber = (int) $request->input('question');
        $answer = (string) $request->input('answer');

        if ($questionNumber < 1 || $questionNumber > $orderedQuestions->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor soal tidak valid.',
            ], 422);
        }

        $sessionKey = $this->getQuizAnswerSessionKey($resolvedQuizId);
        $answers = session($sessionKey, []);
        $answers[$questionNumber] = $answer;
        session([$sessionKey => $answers]);

        return response()->json([
            'success' => true,
            'answeredCount' => count($answers),
        ]);
    }
    
    /**
     * Toggle flag on a quiz question
     */
    public function toggleQuizFlag(Request $request, $courseId, $quizId)
    {
        $user = Auth::guard('mahasiswa')->user();
        if (!$this->isEnrolledInCourse($user->id, (int) $courseId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar di kursus ini.',
            ], 403);
        }

        $quiz = $this->resolveCourseQuiz((int) $courseId, (int) $quizId);
        $resolvedQuizId = (int) $quiz->id_quiz;
        $questionNumber = (int) $request->input('question');
        $totalQuestions = $this->getQuizQuestionsInDisplayOrder($quiz)->count();

        if ($questionNumber < 1 || $questionNumber > $totalQuestions) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor soal tidak valid.',
            ], 422);
        }

        $sessionKey = $this->getQuizFlagSessionKey($resolvedQuizId);
        $flagged = session($sessionKey, []);
        
        if (in_array($questionNumber, $flagged)) {
            $flagged = array_values(array_diff($flagged, [$questionNumber]));
            $isFlagged = false;
        } else {
            $flagged[] = $questionNumber;
            $isFlagged = true;
        }
        
        session([$sessionKey => $flagged]);
        
        return response()->json([
            'success' => true,
            'isFlagged' => $isFlagged,
            'flaggedCount' => count($flagged),
        ]);
    }
    
    /**
     * Reset quiz session data
     */
    public function resetQuiz(Request $request, $courseId, $quizId)
    {
        $quiz = $this->resolveCourseQuiz((int) $courseId, (int) $quizId);
        $resolvedQuizId = (int) $quiz->id_quiz;

        session()->forget($this->getQuizAnswerSessionKey($resolvedQuizId));
        session()->forget($this->getQuizFlagSessionKey($resolvedQuizId));
        session()->forget($this->getQuizStartedAtSessionKey($resolvedQuizId));
        session()->forget($this->getQuizQuestionOrderSessionKey($resolvedQuizId));
        
        return response()->json(['success' => true]);
    }

    /**
     * Finalize quiz attempt and persist real score.
     */
    public function submitQuiz(Request $request, $courseId, $quizId)
    {
        $user = Auth::guard('mahasiswa')->user();

        if (!$this->isEnrolledInCourse($user->id, (int) $courseId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar di kursus ini.',
            ], 403);
        }

        $quiz = $this->resolveCourseQuiz((int) $courseId, (int) $quizId);
        $resolvedQuizId = (int) $quiz->id_quiz;
        $orderedQuestions = $this->getQuizQuestionsInDisplayOrder($quiz);
        $sessionAnswers = $this->getQuizSessionAnswers($resolvedQuizId);

        if ($orderedQuestions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis ini belum memiliki soal.',
            ], 422);
        }

        if (empty($sessionAnswers)) {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban kuiz masih kosong.',
            ], 422);
        }

        $totalQuestions = $orderedQuestions->count();
        $unansweredCount = 0;
        for ($questionNumber = 1; $questionNumber <= $totalQuestions; $questionNumber++) {
            $hasAnswer = array_key_exists($questionNumber, $sessionAnswers)
                && $sessionAnswers[$questionNumber] !== null
                && $sessionAnswers[$questionNumber] !== '';

            if (!$hasAnswer) {
                $unansweredCount++;
            }
        }

        if ($unansweredCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Masih ada {$unansweredCount} soal yang belum dijawab. Selesaikan semua soal sebelum submit.",
                'unanswered_count' => $unansweredCount,
            ], 422);
        }

        $startedAt = session($this->getQuizStartedAtSessionKey($resolvedQuizId), now()->toIso8601String());
        $finishedAt = now();
        $attempt = null;

        DB::transaction(function () use ($quiz, $orderedQuestions, $sessionAnswers, $user, $startedAt, $finishedAt, &$attempt) {
            $attempt = QuizAttempt::create([
                'id_quiz' => $quiz->id_quiz,
                'id_mahasiswa' => $user->id,
                'skor' => 0,
                'total_poin' => 0,
                'persentase' => 0,
                'status' => 'selesai',
                'waktu_mulai' => $startedAt,
                'waktu_selesai' => $finishedAt,
            ]);

            $earnedPoints = 0;
            $totalPoints = 0;

            foreach ($orderedQuestions->values() as $index => $question) {
                $questionNumber = $index + 1;
                $selectedAnswer = array_key_exists($questionNumber, $sessionAnswers)
                    ? (string) $sessionAnswers[$questionNumber]
                    : null;

                $isCorrect = $this->isQuizAnswerCorrect($selectedAnswer, $question);
                $questionPoints = max(1, (int) ($question->bobot ?? 0));
                $earnedForQuestion = $isCorrect ? $questionPoints : 0;
                $earnedPoints += $earnedForQuestion;
                $totalPoints += $questionPoints;

                QuizAnswer::create([
                    'id_attempt' => $attempt->id_attempt,
                    'id_question' => $question->id_question,
                    'jawaban' => $selectedAnswer,
                    'is_correct' => $isCorrect,
                    'poin_diperoleh' => $earnedForQuestion,
                ]);
            }

            $attempt->update([
                'skor' => $earnedPoints,
                'total_poin' => $totalPoints,
                'persentase' => $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0,
            ]);
        });

        $this->markQuizMaterialAsCompleted((int) $courseId, $quiz, $user->id);
        if ($enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)->where('id_course', $courseId)->first()) {
            $enrollment->recalculateProgress($user->id);
            $enrollment = $enrollment->fresh() ?: $enrollment;
            if ($course = Course::find((int) $courseId)) {
                $this->issueCertificateForEnrollmentIfEligible($user, $course, $enrollment, (int) round((float) ($enrollment->progress ?? 0)));
            }
        }
        $this->clearQuizSession($resolvedQuizId);

        return response()->json([
            'success' => true,
            'message' => 'Kuis berhasil diselesaikan.',
            'redirect_url' => route('mahasiswa.quiz-result', ['courseId' => $courseId, 'quizId' => $resolvedQuizId]),
            'attempt_id' => $attempt?->id_attempt,
        ]);
    }
    
    /**
     * Show quiz result page
     */
    public function quizResult($courseId, $quizId)
    {
        $user = Auth::guard('mahasiswa')->user();
        $course = Course::findOrFail($courseId);
        $quiz = $this->resolveCourseQuiz((int) $courseId, (int) $quizId);
        $resolvedQuizId = (int) $quiz->id_quiz;
        $attempt = $this->getLatestCompletedQuizAttempt($resolvedQuizId, $user->id);

        if (!$attempt) {
            return redirect()->route('mahasiswa.course-quiz', ['courseId' => $courseId, 'quizId' => $resolvedQuizId])
                ->with('info', 'Silakan selesaikan kuiz terlebih dahulu.');
        }

        $attempt->loadMissing('answers');
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();
        $progressPercent = (int) round((float) ($enrollment->progress ?? 0));
        $issuedCertificate = $enrollment
            ? $this->issueCertificateForEnrollmentIfEligible($user, $course, $enrollment, $progressPercent)
            : null;
        $certificateEligible = (bool) ($course->sertifikat ?? false)
            && $enrollment
            && (($enrollment->status ?? null) === 'selesai' || $progressPercent >= 100)
            && $issuedCertificate !== null;
        $score = (int) round((float) $attempt->persentase);
        $durationText = $attempt->waktu_mulai && $attempt->waktu_selesai
            ? $attempt->waktu_mulai->diffForHumans($attempt->waktu_selesai, true, true, 2)
            : null;

        $result = [
            'quiz_id' => $quiz->id_quiz,
            'quiz_title' => $quiz->judul ?: 'Kuis Akhir Modul',
            'module_id' => (int) ($quiz->id_module ?? 0),
            'module_name' => $quiz->module?->judul_module ?: ('Modul ' . $quiz->id_module),
            'score' => $score,
            'total_score' => 100,
            'passing_score' => (int) ($quiz->passing_score ?? 0),
            'is_passed' => $score >= (int) ($quiz->passing_score ?? 0),
            'correct_answers' => $attempt->answers->where('is_correct', true)->count(),
            'total_questions' => $attempt->answers->count(),
            'time_taken' => $durationText,
            'earned_points' => (int) $attempt->skor,
            'max_points' => (int) $attempt->total_poin,
            'submitted_at' => $attempt->waktu_selesai,
        ];

        return view('pages.mahasiswa.quiz-result', [
            'course' => $course,
            'result' => $result,
            'quizId' => $resolvedQuizId,
            'certificateEligible' => $certificateEligible,
            'issuedCertificate' => $issuedCertificate,
        ]);
    }
    
    /**
     * Show assignment detail page
     */
    public function assignmentDetail($courseId, $assignmentId)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        // Check if enrolled
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();
            
        if (!$enrollment) {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Anda belum terdaftar di kursus ini.');
        }

        if ($enrollment->status === 'pending') {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Tugas belum dapat diakses karena pembayaran masih menunggu konfirmasi admin.');
        }
        
        // Get course info
        $course = \App\Models\Course::findOrFail($courseId);
        $assignmentMaterial = $this->resolveAssignmentMaterial((int) $courseId, (int) $assignmentId);
        if (!$assignmentMaterial) {
            return redirect()->route('mahasiswa.course-learn', $courseId)
                ->with('info', 'Modul ini tidak memiliki tugas akhir (opsional oleh dosen).');
        }

        $assignment = $this->buildAssignmentViewData($assignmentMaterial);
        
        return view('pages.mahasiswa.assignment-detail', [
            'course' => $course,
            'assignment' => $assignment,
        ]);
    }
    
    /**
     * Show assignment submission page
     */
    public function assignmentSubmission($courseId, $assignmentId)
    {
        $user = Auth::guard('mahasiswa')->user();

        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();
        if (!$enrollment) {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Anda belum terdaftar di kursus ini.');
        }

        if ($enrollment->status === 'pending') {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Tugas belum dapat diakses karena pembayaran masih menunggu konfirmasi admin.');
        }

        // Get course and assignment
        $course = \App\Models\Course::findOrFail($courseId);
        $assignmentMaterial = $this->resolveAssignmentMaterial((int) $courseId, (int) $assignmentId);
        if (!$assignmentMaterial) {
            return redirect()->route('mahasiswa.course-learn', $courseId)
                ->with('info', 'Modul ini tidak memiliki tugas akhir (opsional oleh dosen).');
        }

        $assignment = $this->buildAssignmentViewData($assignmentMaterial);

        $submission = AssignmentSubmission::where('id_material', $assignmentMaterial->id_material)
            ->where('id_mahasiswa', $user->id)
            ->latest('submitted_at')
            ->first();
        
        return view('pages.mahasiswa.assignment-submission', [
            'course' => $course,
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }
    
    /**
     * Show assignment status after submission
     */
    public function assignmentStatus($courseId, $assignmentId)
    {
        $user = Auth::guard('mahasiswa')->user();

        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();
        if (!$enrollment) {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Anda belum terdaftar di kursus ini.');
        }

        if ($enrollment->status === 'pending') {
            return redirect()->route('mahasiswa.course-detail', $courseId)
                ->with('error', 'Status kursus masih pending pembayaran, jadi tugas belum bisa diakses.');
        }

        $course = \App\Models\Course::findOrFail($courseId);
        $assignmentMaterial = $this->resolveAssignmentMaterial((int) $courseId, (int) $assignmentId);
        if (!$assignmentMaterial) {
            return redirect()->route('mahasiswa.course-learn', $courseId)
                ->with('info', 'Modul ini tidak memiliki tugas akhir (opsional oleh dosen).');
        }
        
        $submission = AssignmentSubmission::where('id_material', $assignmentMaterial->id_material)
            ->where('id_mahasiswa', $user->id)
            ->first();

        if (!$submission) {
            return redirect()->route('mahasiswa.assignment-submission', [
                'courseId' => $courseId,
                'assignmentId' => $assignmentId,
            ])->with('info', 'Tugas belum dikumpulkan.');
        }
        
        return view('pages.mahasiswa.assignment-status', [
            'course' => $course,
            'submission' => $submission,
        ]);
    }
    
    /**
     * Show module feedback and grade page
     */
    public function moduleFeedback($courseId, $moduleId)
    {
        $user = Auth::guard('mahasiswa')->user();
        $course = Course::findOrFail($courseId);
        $module = CourseModule::where('id_course', $courseId)
            ->where('id_module', $moduleId)
            ->firstOrFail();

        $quiz = Quiz::where('id_course', $courseId)
            ->where('id_module', $moduleId)
            ->where('is_active', true)
            ->orderBy('is_pretest')
            ->orderBy('urutan')
            ->first();

        if (!$quiz) {
            return redirect()->route('mahasiswa.course-learn', $courseId)
                ->with('info', 'Modul ini tidak memiliki kuiz.');
        }

        $attempt = $this->getLatestCompletedQuizAttempt((int) $quiz->id_quiz, $user->id);
        if (!$attempt) {
            return redirect()->route('mahasiswa.course-quiz', ['courseId' => $courseId, 'quizId' => $quiz->id_quiz])
                ->with('info', 'Silakan kerjakan kuiz terlebih dahulu.');
        }

        $attempt->loadMissing(['answers.question', 'quiz']);
        $score = (int) round((float) $attempt->persentase);
        $correctCount = $attempt->answers->where('is_correct', true)->count();
        $questionReviews = $attempt->answers
            ->sortBy(fn (QuizAnswer $answer) => $answer->question?->urutan ?? 0)
            ->values()
            ->map(function (QuizAnswer $answer, int $index) {
                $question = $answer->question;
                $options = $question ? array_values((array) ($question->opsi ?? [])) : [];
                $selectedMeta = $this->buildQuizAnswerDisplayMeta($answer->jawaban, $options);
                $correctMeta = $this->buildQuizAnswerDisplayMeta($question?->jawaban_benar, $options);

                return [
                    'number' => $index + 1,
                    'question' => $question?->pertanyaan ?? 'Soal tidak ditemukan.',
                    'type' => $this->formatQuizQuestionType($question?->tipe),
                    'selected_label' => $selectedMeta['label'],
                    'selected_text' => $selectedMeta['text'],
                    'correct_label' => $correctMeta['label'],
                    'correct_text' => $correctMeta['text'],
                    'is_correct' => (bool) $answer->is_correct,
                    'points' => (int) $answer->poin_diperoleh,
                    'max_points' => (int) ($question?->bobot ?? 0),
                    'explanation' => trim((string) ($question?->penjelasan ?? '')),
                ];
            })
            ->all();

        $feedback = [
            'module_name' => $module->judul_module ?: ('Modul ' . $moduleId),
            'quiz_title' => $quiz->judul ?: 'Kuis Akhir Modul',
            'quiz_id' => (int) $quiz->id_quiz,
            'score' => $score,
            'max_score' => 100,
            'is_passed' => $score >= (int) ($quiz->passing_score ?? 0),
            'passing_score' => (int) ($quiz->passing_score ?? 0),
            'correct_answers' => $correctCount,
            'total_questions' => count($questionReviews),
            'earned_points' => (int) $attempt->skor,
            'max_points' => (int) $attempt->total_poin,
            'submitted_at' => $attempt->waktu_selesai,
            'summary' => $this->buildQuizPerformanceSummary($score, $correctCount, count($questionReviews)),
            'question_reviews' => $questionReviews,
        ];

        return view('pages.mahasiswa.module-feedback', [
            'course' => $course,
            'feedback' => $feedback,
            'moduleId' => $moduleId,
        ]);
    }
    
    /**
     * Submit assignment and mark as completed
     */
    public function submitAssignment(Request $request, $courseId, $assignmentId)
    {
        $user = auth('mahasiswa')->user();
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar di kursus ini.',
            ], 403);
        }

        if ($enrollment->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran kursus masih menunggu konfirmasi admin.',
            ], 403);
        }

        $assignmentMaterial = $this->resolveAssignmentMaterial((int) $courseId, (int) $assignmentId);
        if (!$assignmentMaterial) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas akhir tidak tersedia pada modul ini.',
            ], 422);
        }

        // R2 refactor: server-side deadline + final-project gating moved to
        // BootcampFlowService::assertSubmitAssignment (single source of truth).
        // BootcampFlowException is rendered to 4xx JSON / back()->error by
        // the renderer registered in bootstrap/app.php.
        $assignmentPayload = $this->parseAssignmentPayload($assignmentMaterial->konten);
        $this->bootcampFlow->assert(
            $user,
            Course::find((int) $courseId),
            BootcampTransition::SUBMIT_ASSIGNMENT,
            ['material' => $assignmentMaterial, 'payload' => $assignmentPayload],
        );

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240|mimes:pdf,docx,doc,zip',
            'catatan' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $userId = $user->id;
        $fileName = "assignment_{$courseId}_{$assignmentId}_{$userId}_" . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('assignments', $fileName, 'public');

        $submission = AssignmentSubmission::where('id_material', $assignmentMaterial->id_material)
            ->where('id_mahasiswa', $user->id)
            ->first();

        if ($submission?->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }

        AssignmentSubmission::updateOrCreate(
            [
                'id_material' => $assignmentMaterial->id_material,
                'id_mahasiswa' => $user->id,
            ],
            [
                'id_course' => $courseId,
                'file_path' => $filePath,
                'original_file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'catatan_mahasiswa' => $request->string('catatan')->trim()->value(),
                'status' => 'submitted',
                'submitted_at' => now(),
                'reviewed_at' => null,
            ]
        );

        // Mark assignment as completed in session (use module key so it matches learn() logic)
        $completedAssignments = session('completed_assignments', []);
        $assignmentModule = $assignmentMaterial?->id_module ?: $assignmentId;
        $key = $courseId . '_' . $assignmentModule;
        if (!in_array($key, $completedAssignments)) {
            $completedAssignments[] = $key;
            session(['completed_assignments' => $completedAssignments]);
        }

        $course = Course::find($courseId);
        \App\Models\MaterialProgress::updateOrCreate(
            [
                'id_mahasiswa' => $user->id,
                'id_material' => $assignmentMaterial->id_material,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        $enrollment->recalculateProgress($user->id);
        $enrollment = $enrollment->fresh() ?: $enrollment;
        if ($course) {
            $this->issueCertificateForEnrollmentIfEligible($user, $course, $enrollment, (int) round((float) ($enrollment->progress ?? 0)));
        }

        // Notify dosen about assignment submission
        if ($course && $course->id_dosen) {
            $mahasiswa = auth('mahasiswa')->user();
            DosenNotification::notifyDosen(
                $course->id_dosen,
                'Tugas Dikumpulkan',
                ($mahasiswa->name ?? 'Mahasiswa') . ' mengumpulkan tugas di kursus ' . ($course->nama_course ?? 'Kursus'),
                'tugas',
                    'tugas',
                    '/dosen/kursus/' . $courseId
                );
        }

        Notification::notifyMahasiswa(
            $user->id,
            'Tugas berhasil dikumpulkan',
            'Tugas Anda untuk kursus ' . ($course->nama_course ?? 'Kursus') . ' sudah tersimpan dan menunggu review dosen.',
            'kursus_pembelajaran',
            'assignment',
            '#F97316'
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil disubmit.',
            'redirect' => route('mahasiswa.assignment-status', ['courseId' => $courseId, 'assignmentId' => $assignmentId])
        ]);
    }

    public function getDiscussions($courseId)
    {
        $user = Auth::guard('mahasiswa')->user();

        if (!$this->isEnrolledInCourse($user->id, (int) $courseId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke diskusi kursus ini.',
            ], 403);
        }

        if (!Schema::hasTable('course_discussions')) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Fitur diskusi belum aktif di server ini.',
            ]);
        }

        try {
            $messages = CourseDiscussion::with(['user.profile'])
                ->where('id_course', $courseId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn (CourseDiscussion $comment) => $this->formatDiscussionComment($comment));

            return response()->json([
                'success' => true,
                'data' => $messages,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal memuat diskusi kursus mahasiswa.', [
                'course_id' => (int) $courseId,
                'mahasiswa_id' => (int) $user->id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Diskusi kursus sedang bermasalah. Silakan coba lagi.',
            ], 500);
        }
    }

    public function sendDiscussion(Request $request, $courseId)
    {
        $user = Auth::guard('mahasiswa')->user();

        if (!$this->isEnrolledInCourse($user->id, (int) $courseId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke diskusi kursus ini.',
            ], 403);
        }

        if (!Schema::hasTable('course_discussions')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur diskusi belum aktif di server ini.',
            ], 503);
        }

        try {
            $validated = $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $message = trim($validated['message']);

            $discussion = CourseDiscussion::query()
                ->where('id_course', $courseId)
                ->where('id_user', $user->id)
                ->where('message', $message)
                ->where('created_at', '>=', now()->subSeconds(15))
                ->latest((new CourseDiscussion())->getKeyName())
                ->first();

            if (!$discussion) {
                $discussion = CourseDiscussion::create([
                    'id_course' => $courseId,
                    'id_user' => $user->id,
                    'message' => $message,
                ]);
            }

            $discussion->load(['user.profile']);

            $course = Course::query()
                ->select(['id_course', 'id_dosen', 'nama_course'])
                ->find($courseId);

            if ($course && (int) $course->id_dosen !== (int) $user->id) {
                try {
                    DosenNotification::notifyDosen(
                        (int) $course->id_dosen,
                        'Pertanyaan baru di diskusi kursus',
                        $user->name . ' mengirim pesan baru di kursus ' . $course->nama_course . '.',
                        'discussion',
                        'discussion',
                        route('dosen.kursus.detail', $course->id_course, false)
                    );
                } catch (\Throwable $notificationException) {
                    Log::warning('Notifikasi diskusi kursus ke dosen gagal dikirim.', [
                        'course_id' => (int) $courseId,
                        'mahasiswa_id' => (int) $user->id,
                        'dosen_id' => (int) $course->id_dosen,
                        'discussion_id' => (int) $discussion->getKey(),
                        'error' => $notificationException->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatDiscussionComment($discussion),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim diskusi kursus mahasiswa.', [
                'course_id' => (int) $courseId,
                'mahasiswa_id' => (int) $user->id,
                'payload' => [
                    'message_length' => strlen((string) $request->input('message')),
                ],
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Diskusi gagal dikirim. Server sedang bermasalah.',
            ], 500);
        }
    }

    /**
     * Submit a course review
     */
    public function submitCourseReview(Request $request, $courseId)
    {
        $user = Auth::guard('mahasiswa')->user();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'required|string|min:5',
        ]);

        $course = Course::find($courseId);
        $isBootcamp = $this->isBootcamp($course);
        $entityLabel = $isBootcamp ? 'bootcamp' : 'kursus';

        // Ensure student is enrolled
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();
            
        if (!$enrollment) {
            return back()->with('error', 'Anda harus terdaftar di ' . $entityLabel . ' ini untuk memberikan ulasan.');
        }

        if ($enrollment->status === 'pending') {
            return back()->with('error', 'Ulasan hanya bisa diberikan setelah pembayaran dikonfirmasi dan ' . $entityLabel . ' aktif.');
        }
        
        if ($enrollment->progress < 100) {
            return back()->with('error', 'Anda harus menyelesaikan ' . $entityLabel . ' terlebih dahulu untuk memberikan ulasan.');
        }
        
        $existingReview = CourseRating::where('id_course', $courseId)
            ->where('id_mahasiswa', $user->id)
            ->first();
            
        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);
        } else {
            CourseRating::create([
                'id_course' => $courseId,
                'id_mahasiswa' => $user->id,
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);
        }
        
        return back()->with('success', 'Terima kasih! Ulasan Anda telah tersimpan.');
    }

    private function resolveCourseQuiz(int $courseId, int $quizId): Quiz
    {
        $quiz = Quiz::with([
            'module',
            'questions' => fn ($query) => $query->orderBy('urutan')->orderBy('id_question'),
        ])
            ->where('id_course', $courseId)
            ->where('id_quiz', $quizId)
            ->where('is_active', true)
            ->first();

        if ($quiz) {
            return $quiz;
        }

        $legacyQuizMaterial = CourseMaterial::where('id_course', $courseId)
            ->where('id_material', $quizId)
            ->first();

        if (!$legacyQuizMaterial || $this->normalizeMaterialType($legacyQuizMaterial->tipe) !== 'kuis') {
            abort(404);
        }

        return $this->provisionQuizFromLegacyMaterial($courseId, $legacyQuizMaterial);
    }

    private function resolveQuizForLegacyMaterialLink(int $courseId, CourseMaterial $legacyQuizMaterial): ?Quiz
    {
        if ($this->normalizeMaterialType($legacyQuizMaterial->tipe) !== 'kuis' || empty($legacyQuizMaterial->id_module)) {
            return null;
        }

        try {
            return $this->provisionQuizFromLegacyMaterial($courseId, $legacyQuizMaterial);
        } catch (\Throwable $exception) {
            Log::warning('Gagal sinkronisasi material kuis ke quiz.', [
                'course_id' => $courseId,
                'material_id' => $legacyQuizMaterial->id_material,
                'module_id' => $legacyQuizMaterial->id_module,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function provisionQuizFromLegacyMaterial(int $courseId, CourseMaterial $legacyQuizMaterial): Quiz
    {
        $title = trim((string) ($legacyQuizMaterial->judul_material ?? $legacyQuizMaterial->judul ?? 'Kuis Akhir Modul'));
        $sourceMarker = 'legacy_material:' . (int) $legacyQuizMaterial->id_material;

        $existingQuiz = Quiz::where('id_course', $courseId)
            ->where('id_module', $legacyQuizMaterial->id_module)
            ->where('deskripsi', 'like', '%' . $sourceMarker . '%')
            ->orderByDesc('id_quiz')
            ->first();

        if (!$existingQuiz) {
            $sameTitleLegacyCount = CourseMaterial::where('id_course', $courseId)
                ->where('id_module', $legacyQuizMaterial->id_module)
                ->get()
                ->filter(function (CourseMaterial $material) use ($title) {
                    return $this->normalizeMaterialType($material->tipe) === 'kuis'
                        && strtolower(trim((string) ($material->judul_material ?? $material->judul ?? ''))) === strtolower($title);
                })
                ->count();

            if ($sameTitleLegacyCount <= 1) {
                $existingQuiz = Quiz::where('id_course', $courseId)
                    ->where('id_module', $legacyQuizMaterial->id_module)
                    ->whereRaw('LOWER(TRIM(judul)) = ?', [strtolower($title)])
                    ->orderByDesc('id_quiz')
                    ->first();
            }
        }

        if ($existingQuiz) {
            if (!$existingQuiz->is_active) {
                $existingQuiz->forceFill(['is_active' => true])->save();
            }
            if (!str_contains((string) $existingQuiz->deskripsi, $sourceMarker)) {
                $existingQuiz->forceFill([
                    'deskripsi' => trim((string) $existingQuiz->deskripsi . ' ' . $sourceMarker),
                ])->save();
            }
            if ($existingQuiz->questions()->count() === 0) {
                $this->seedQuizQuestionsFromLegacyMaterial($existingQuiz, $legacyQuizMaterial);
            }

            return $existingQuiz->load([
                'module',
                'questions' => fn ($query) => $query->orderBy('urutan')->orderBy('id_question'),
            ]);
        }

        $nextOrder = (int) Quiz::where('id_course', $courseId)
            ->where('id_module', $legacyQuizMaterial->id_module)
            ->max('urutan');

        $quiz = DB::transaction(function () use ($courseId, $legacyQuizMaterial, $title, $sourceMarker, $nextOrder) {
            $quiz = Quiz::create([
                'id_module' => $legacyQuizMaterial->id_module,
                'id_course' => $courseId,
                'judul' => $title,
                'deskripsi' => 'Kuis dibuat otomatis dari materi kuis lama. ' . $sourceMarker,
                'durasi_menit' => max(5, (int) ($legacyQuizMaterial->durasi ?? 30)),
                'is_pretest' => (bool) ($legacyQuizMaterial->is_pretest ?? false),
                'is_active' => true,
                'passing_score' => 70,
                'acak_soal' => false,
                'tampilkan_nilai' => true,
                'urutan' => $nextOrder + 1,
            ]);

            $this->seedQuizQuestionsFromLegacyMaterial($quiz, $legacyQuizMaterial);

            return $quiz;
        });

        return $quiz->load([
            'module',
            'questions' => fn ($query) => $query->orderBy('urutan')->orderBy('id_question'),
        ]);
    }

    private function seedQuizQuestionsFromLegacyMaterial(Quiz $quiz, CourseMaterial $legacyQuizMaterial): void
    {
        $legacyQuestions = $this->extractLegacyQuizQuestions($legacyQuizMaterial->konten);
        if (empty($legacyQuestions)) {
            return;
        }

        foreach ($legacyQuestions as $index => $legacyQuestion) {
            $questionText = trim((string) ($legacyQuestion['pertanyaan'] ?? $legacyQuestion['question'] ?? ''));
            if ($questionText === '') {
                continue;
            }

            $options = $legacyQuestion['opsi'] ?? $legacyQuestion['options'] ?? [];
            $options = is_array($options) ? array_values($options) : [];
            if (empty($options)) {
                continue;
            }

            $type = trim((string) ($legacyQuestion['tipe'] ?? $legacyQuestion['type'] ?? 'pilihan_ganda'));
            $questionType = in_array($type, ['benar_salah', 'pilihan_ganda'], true)
                ? $type
                : 'pilihan_ganda';

            $correctAnswer = $legacyQuestion['jawaban_benar']
                ?? $legacyQuestion['correctAnswer']
                ?? $legacyQuestion['kunci']
                ?? null;

            QuizQuestion::create([
                'id_quiz' => $quiz->id_quiz,
                'pertanyaan' => $questionText,
                'tipe' => $questionType,
                'opsi' => $options,
                'jawaban_benar' => is_scalar($correctAnswer) ? (string) $correctAnswer : null,
                'bobot' => max(1, (int) ($legacyQuestion['bobot'] ?? 10)),
                'penjelasan' => trim((string) ($legacyQuestion['penjelasan'] ?? $legacyQuestion['explanation'] ?? '')),
                'urutan' => $index + 1,
            ]);
        }
    }

    private function extractLegacyQuizQuestions(?string $content): array
    {
        if (!$content) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        if (array_is_list($decoded)) {
            return $decoded;
        }

        foreach (['questions', 'soal', 'items'] as $key) {
            if (isset($decoded[$key]) && is_array($decoded[$key])) {
                return array_values($decoded[$key]);
            }
        }

        return [];
    }

    private function buildQuizViewData(Quiz $quiz, $orderedQuestions): array
    {
        return [
            'id' => $quiz->id_quiz,
            'title' => $quiz->judul ?: 'Kuis Akhir Modul',
            'course_name' => $quiz->course?->nama_course ?? '',
            'module_name' => $quiz->module?->judul_module ?: ('Modul ' . $quiz->id_module),
            'total_questions' => $orderedQuestions->count(),
            'duration' => (int) ($quiz->durasi_menit ?? 0),
            'passing_score' => (int) ($quiz->passing_score ?? 0),
            'can_go_back' => false,
            'is_pretest' => (bool) $quiz->is_pretest,
        ];
    }

    private function buildQuizQuestionViewData(QuizQuestion $question): array
    {
        return [
            'id' => $question->id_question,
            'text' => $question->pertanyaan,
            'type' => $this->formatQuizQuestionType($question->tipe),
            'options' => array_values((array) ($question->opsi ?? [])),
        ];
    }

    private function getQuizQuestionsInDisplayOrder(Quiz $quiz)
    {
        $questions = $quiz->questions->values();
        if (!$quiz->acak_soal || $questions->count() <= 1) {
            return $questions;
        }

        $sessionKey = $this->getQuizQuestionOrderSessionKey((int) $quiz->id_quiz);
        $storedOrder = array_map('intval', session($sessionKey, []));
        $questionIds = $questions->pluck('id_question')->map(fn ($id) => (int) $id)->all();
        $hasSameQuestions = count($storedOrder) === count($questionIds)
            && empty(array_diff($storedOrder, $questionIds))
            && empty(array_diff($questionIds, $storedOrder));

        if (!$hasSameQuestions) {
            $storedOrder = $questionIds;
            shuffle($storedOrder);
            session([$sessionKey => $storedOrder]);
        }

        return collect($storedOrder)
            ->map(fn (int $questionId) => $questions->firstWhere('id_question', $questionId))
            ->filter()
            ->values();
    }

    private function getLatestCompletedQuizAttempt(int $quizId, int $mahasiswaId): ?QuizAttempt
    {
        return QuizAttempt::with(['answers.question', 'quiz.module'])
            ->where('id_quiz', $quizId)
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('status', 'selesai')
            ->orderByDesc('waktu_selesai')
            ->orderByDesc('id_attempt')
            ->first();
    }

    private function getQuizAnswerSessionKey(int $quizId): string
    {
        return 'quiz_' . $quizId . '_answers';
    }

    private function getQuizFlagSessionKey(int $quizId): string
    {
        return 'quiz_' . $quizId . '_flagged';
    }

    private function getQuizStartedAtSessionKey(int $quizId): string
    {
        return 'quiz_' . $quizId . '_started_at';
    }

    private function getQuizQuestionOrderSessionKey(int $quizId): string
    {
        return 'quiz_' . $quizId . '_question_order';
    }

    private function getQuizSessionAnswers(int $quizId): array
    {
        return session($this->getQuizAnswerSessionKey($quizId), []);
    }

    private function getQuizSessionFlags(int $quizId): array
    {
        return session($this->getQuizFlagSessionKey($quizId), []);
    }

    private function clearQuizSession(int $quizId): void
    {
        session()->forget($this->getQuizAnswerSessionKey($quizId));
        session()->forget($this->getQuizFlagSessionKey($quizId));
        session()->forget($this->getQuizStartedAtSessionKey($quizId));
        session()->forget($this->getQuizQuestionOrderSessionKey($quizId));
    }

    private function isQuizAnswerCorrect(?string $selectedAnswer, QuizQuestion $question): bool
    {
        if ($selectedAnswer === null || $selectedAnswer === '') {
            return false;
        }

        $normalizedSelected = $this->normalizeQuizComparableValue($selectedAnswer);
        $options = array_values((array) ($question->opsi ?? []));
        $comparables = [$question->jawaban_benar];

        if (is_numeric($question->jawaban_benar)) {
            $correctIndex = (int) $question->jawaban_benar;
            $comparables[] = (string) $correctIndex;
            $comparables[] = $this->quizOptionLabelFromIndex($correctIndex);
            if (array_key_exists($correctIndex, $options)) {
                $comparables[] = $options[$correctIndex];
            }
        } elseif (is_string($question->jawaban_benar) && strlen(trim($question->jawaban_benar)) === 1 && ctype_alpha(trim($question->jawaban_benar))) {
            $correctIndex = ord(strtoupper(trim($question->jawaban_benar))) - 65;
            $comparables[] = (string) $correctIndex;
            if (array_key_exists($correctIndex, $options)) {
                $comparables[] = $options[$correctIndex];
            }
        }

        foreach ($comparables as $candidate) {
            if ($normalizedSelected === $this->normalizeQuizComparableValue($candidate)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeQuizComparableValue(mixed $value): string
    {
        return mb_strtolower(trim((string) $value));
    }

    private function formatQuizQuestionType(?string $type): string
    {
        return match ($type) {
            'benar_salah' => 'Benar / Salah',
            'pilihan_ganda' => 'Pilihan Ganda',
            default => 'Soal',
        };
    }

    private function quizOptionLabelFromIndex(?int $index): ?string
    {
        if ($index === null || $index < 0 || $index > 25) {
            return null;
        }

        return chr(65 + $index);
    }

    private function buildQuizAnswerDisplayMeta(mixed $value, array $options): array
    {
        if ($value === null || $value === '') {
            return ['label' => '-', 'text' => 'Tidak dijawab'];
        }

        if (is_numeric($value)) {
            $index = (int) $value;
            return [
                'label' => $this->quizOptionLabelFromIndex($index) ?? (string) $value,
                'text' => $options[$index] ?? (string) $value,
            ];
        }

        $textValue = trim((string) $value);
        $matchedIndex = collect($options)->search(fn ($option) => trim((string) $option) === $textValue);
        if ($matchedIndex !== false) {
            return [
                'label' => $this->quizOptionLabelFromIndex((int) $matchedIndex) ?? (string) $matchedIndex,
                'text' => $options[$matchedIndex],
            ];
        }

        if (strlen($textValue) === 1 && ctype_alpha($textValue)) {
            $index = ord(strtoupper($textValue)) - 65;
            return [
                'label' => strtoupper($textValue),
                'text' => $options[$index] ?? strtoupper($textValue),
            ];
        }

        return ['label' => '-', 'text' => $textValue];
    }

    private function buildQuizPerformanceSummary(int $score, int $correctAnswers, int $totalQuestions): string
    {
        if ($totalQuestions <= 0) {
            return 'Belum ada jawaban yang dapat dievaluasi.';
        }

        if ($score >= 85) {
            return "Hasil Anda sangat baik. Anda menjawab {$correctAnswers} dari {$totalQuestions} soal dengan benar dan sudah memahami materi kuiz dengan kuat.";
        }

        if ($score >= 70) {
            return "Hasil Anda sudah memenuhi batas lulus. Anda menjawab {$correctAnswers} dari {$totalQuestions} soal dengan benar, namun masih ada beberapa bagian yang perlu ditinjau ulang.";
        }

        return "Hasil Anda belum mencapai nilai lulus. Anda menjawab {$correctAnswers} dari {$totalQuestions} soal dengan benar. Tinjau ulang penjelasan pada soal yang salah sebelum mencoba lagi.";
    }

    private function markQuizMaterialAsCompleted(int $courseId, Quiz $quiz, int $mahasiswaId): void
    {
        $moduleId = (int) ($quiz->id_module ?? 0);
        if ($moduleId <= 0) {
            return;
        }

        $quizMaterials = CourseMaterial::where('id_course', $courseId)
            ->where('id_module', $moduleId)
            ->get()
            ->filter(fn (CourseMaterial $material) => $this->normalizeMaterialType($material->tipe) === 'kuis');

        $quizTitle = Str::lower(trim((string) ($quiz->judul ?? '')));
        $matchedMaterials = $quizMaterials->filter(function (CourseMaterial $material) use ($quizTitle) {
            return $quizTitle !== ''
                && Str::lower(trim((string) ($material->judul_material ?? $material->judul ?? ''))) === $quizTitle;
        });

        if ($matchedMaterials->isEmpty() && $quizMaterials->count() === 1) {
            $matchedMaterials = $quizMaterials;
        }

        foreach ($matchedMaterials as $material) {
            \App\Models\MaterialProgress::updateOrCreate(
                [
                    'id_mahasiswa' => $mahasiswaId,
                    'id_material' => $material->id_material,
                ],
                [
                    'is_completed' => true,
                    'completed_at' => now(),
                ]
            );
        }
    }

    private function normalizeMaterialType(?string $type): string
    {
        return match ($type) {
            'video' => 'video',
            'kuis', 'quiz' => 'kuis',
            'tugas', 'assignment', 'tugas_akhir' => 'tugas',
            'bacaan', 'text' => 'bacaan',
            default => 'bacaan',
        };
    }

    private function resolveAssignmentMaterial(int $courseId, int $assignmentId): ?CourseMaterial
    {
        $material = CourseMaterial::where('id_material', $assignmentId)
            ->where('id_course', $courseId)
            ->first();

        if (!$material) {
            return null;
        }

        return $this->normalizeMaterialType($material->tipe) === 'tugas' ? $material : null;
    }

    private function buildAssignmentViewData(CourseMaterial $assignmentMaterial): array
    {
        $payload = $this->parseAssignmentPayload($assignmentMaterial->konten);
        $description = trim((string) ($payload['deskripsi'] ?? ''));
        $instructions = trim((string) ($payload['instruksi'] ?? ''));

        return [
            'id' => $assignmentMaterial->id_material,
            'title' => $assignmentMaterial->judul_material ?? $assignmentMaterial->judul ?? 'Tugas Akhir',
            'description' => $description !== ''
                ? $description
                : ($assignmentMaterial->konten ?: 'Kerjakan tugas akhir sesuai instruksi dosen pada modul ini.'),
            'deadline' => $this->parseAssignmentDeadline($payload['deadline'] ?? null),
            'weight' => 40,
            'format' => $this->formatAssignmentFormat($payload['format'] ?? null),
            'max_size' => '10 MB',
            'learning_objectives' => [
                'Mampu menganalisis sistem informasi perusahaan',
                'Mengidentifikasi masalah dan memberikan solusi',
                'Menyusun rekomendasi implementasi',
            ],
            'steps' => $this->buildAssignmentSteps($instructions),
            'grading_criteria' => [
                ['name' => 'Analisis Mendalam', 'percentage' => 30],
                ['name' => 'Identifikasi Masalah', 'percentage' => 25],
                ['name' => 'Solusi & Rekomendasi', 'percentage' => 25],
                ['name' => 'Kualitas Laporan', 'percentage' => 20],
            ],
            'instructor_note' => !empty($payload['allowLinks'])
                ? 'Anda boleh menambahkan link pendukung jika memang relevan dengan tugas.'
                : 'Pastikan analisis didukung dengan data yang valid dan referensi yang relevan.',
        ];
    }

    private function parseAssignmentPayload(?string $content): array
    {
        if (!$content) {
            return [];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) && !empty($decoded['is_tugas']) ? $decoded : [];
    }

    private function parseAssignmentDeadline(mixed $deadline): \Illuminate\Support\Carbon
    {
        if (is_string($deadline) && trim($deadline) !== '') {
            try {
                return \Illuminate\Support\Carbon::parse($deadline);
            } catch (\Throwable $exception) {
                // fall back to the default placeholder deadline below
            }
        }

        return now()->addDays(7);
    }

    private function formatAssignmentFormat(mixed $format): string
    {
        if (is_array($format)) {
            $items = array_filter(array_map(fn ($item) => strtoupper(trim((string) $item)), $format));

            return !empty($items) ? implode(', ', $items) : 'PDF, DOCX, ZIP';
        }

        $value = strtoupper(trim((string) $format));

        return $value !== '' ? str_replace('|', ', ', $value) : 'PDF, DOCX, ZIP';
    }

    private function buildAssignmentSteps(?string $instructions): array
    {
        $instructions = trim((string) $instructions);

        if ($instructions === '') {
            return [
                'Pilih perusahaan yang akan dianalisis',
                'Lakukan observasi dan pengumpulan data',
                'Analisis sistem informasi yang ada',
                'Identifikasi masalah dan solusi',
                'Susun laporan dan rekomendasi',
            ];
        }

        $normalized = preg_replace("/\r\n|\r/", "\n", $instructions) ?? $instructions;
        $segments = preg_split('/\n+|(?=\d+\.\s+)/', $normalized) ?: [];
        $steps = [];

        foreach ($segments as $segment) {
            $step = trim(preg_replace('/^(\d+\.\s*|[-*]\s*)/', '', trim($segment)) ?? '');
            if ($step !== '') {
                $steps[] = $step;
            }
        }

        return !empty($steps) ? $steps : [$instructions];
    }

    private function isEnrolledInCourse(int $mahasiswaId, int $courseId): bool
    {
        return \App\Models\Enrollment::where('id_mahasiswa', $mahasiswaId)
            ->where('id_course', $courseId)
            ->accessible()
            ->exists();
    }

    /**
     * Single source of truth: is this Course object a bootcamp (tiket)?
     * Re-derives in many blade templates; keep in sync if you refactor.
     */
    private function isBootcamp(?Course $course): bool
    {
        return $course !== null
            && strtolower((string) ($course->kategori ?? '')) === 'tiket';
    }

    /**
     * Strict final-project prerequisite gate (server-enforced — UI may also
     * hide the upload form, but a raw POST to submitAssignment() must not be
     * able to bypass this).
     *
     * Returns true only if:
     *  - all material progresses complete for this course
     *  - all non-final assignment materials have a submission (any status)
     *  - all active quizzes for the course have a completed attempt passing passing_score
     *  - all live-class sessions for the course are attendance-verified
     *
     * Read-only helpers; safe to call repeatedly in dashboard renders.
     */
    /**
     * Compute the set of `session_key` strings currently exposed by the
     * bootcamp live class dashboard for a given course. Used by
     * `finalProjectGate` and by the dashboard data
     * pre-fetch in `prepareBootcampDashboardData` to keep keys in sync.
     */
    private function collectLiveClassSessionKeys(Course $course): array
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
            ? \Illuminate\Support\Carbon::parse($course->tanggal_webinar->format('Y-m-d') . ' ' . ($course->jam_mulai_webinar ?: '08:00:00'))
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

    /**
     * Upload attendance proof for a single live-class session.
     * Server-side enforced:
     *   - enrollment valid & active
     *   - course is a bootcamp (kategori='tiket')
     *   - session_key belongs to this course
     *   - the session end_time has already passed
     *   - file upload is image/pdf only
     *
     * Upsert semantics: one row per (user, course, session_key).
     */
    public function storeLiveClassAttendance(Request $request, int $courseId): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = auth('mahasiswa')->user();
        if (!$user) {
            abort(401);
        }

        // R5 refactor: 5 inline gating checks (enrollment/bootcamp/session-key/
        // time-window/verified-guard) consolidated into
        // BootcampFlowService::assertUploadAttendance. BootcampFlowException
        // is rendered to 4xx JSON / back()->error by bootstrap/app.php.
        $course = Course::find($courseId);
        $sessionKey = trim((string) $request->input('session_key', ''));
        $this->bootcampFlow->assert(
            $user,
            $course,
            BootcampTransition::UPLOAD_ATTENDANCE,
            ['session_key' => $sessionKey],
        );

        $validator = Validator::make($request->all(), [
            'proof_file' => 'required|file|max:5120|mimes:jpg,jpeg,png,webp,pdf',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'proof_file.required' => 'Pilih file bukti kehadiran terlebih dahulu.',
            'proof_file.mimes' => 'Format bukti harus JPG, PNG, WEBP, atau PDF.',
            'proof_file.max' => 'Ukuran bukti kehadiran maksimal 5 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('proof_file');
        $fileName = 'att_' . $courseId . '_' . $user->id . '_' . substr(sha1($sessionKey), 0, 8) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('attendance', $fileName, 'public');

        // SECURITY: jangan izinkan re-upload setelah bukti terverifikasi;
        // kalau dibiarkan, final-project gate akan bisa dibalik ke pending.
        $existing = BootcampLiveClassAttendance::where('id_user', $user->id)
            ->where('id_course', $courseId)
            ->where('session_key', $sessionKey)
            ->first();
        if ($existing && $existing->isVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti kehadiran sudah terverifikasi dan tidak dapat diganti.',
            ], 403);
        }

        // Upsert: if rejected previously, allow re-submit by replacing row.
        $attendance = $existing;

        if ($attendance?->proof_file) {
            Storage::disk('public')->delete($attendance->proof_file);
        }

        BootcampLiveClassAttendance::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_course' => $courseId,
                'session_key' => $sessionKey,
            ],
            [
                'proof_file' => $filePath,
                'catatan_mahasiswa' => $request->string('catatan')->trim()->value() ?: null,
                'status' => BootcampLiveClassAttendance::STATUS_PENDING,
                'catatan_reviewer' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bukti kehadiran berhasil diunggah. Menunggu verifikasi mentor/admin.',
            ]);
        }

        return back()->with('success', 'Bukti kehadiran berhasil diunggah. Menunggu verifikasi.');
    }

    /**
     * Resolve the end timestamp (Carbon) for a given session_key.
     * Returns null if the key is unknown (caller must reject).
     */
    private function resolveLiveClassEndTime(Course $course, string $sessionKey): ?\Illuminate\Support\Carbon
    {
        if (!$course->tanggal_webinar) {
            return null;
        }

        $sessionEnd = \Illuminate\Support\Carbon::parse(
            $course->tanggal_webinar->format('Y-m-d') . ' ' . ($course->jam_selesai_webinar ?: '17:00:00')
        );

        if ($sessionKey === 'primary') {
            return $sessionEnd;
        }

        // qa_<module_id>_<start_iso>
        if (preg_match('/^qa_(\d+)_(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})$/', $sessionKey, $m) === 1) {
            $moduleId = (int) $m[1];
            $module = CourseModule::where('id_course', $course->id_course)
                ->where('id_module', $moduleId)
                ->first();
            if (!$module) {
                return null;
            }
            try {
                $start = \Illuminate\Support\Carbon::parse($m[2]);
            } catch (\Throwable) {
                return null;
            }
            return $start->copy()->addMinutes(90);
        }

        return null;
    }

    private function formatDiscussionComment(CourseDiscussion $comment): array
    {
        $avatar = $comment->user?->profile?->foto_profile
            ? asset('storage/' . $comment->user->profile->foto_profile)
            : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user?->name ?? 'Mahasiswa') . '&background=0D9488&color=fff';

        return [
            'id' => $comment->id_course_discussion,
            'name' => $comment->user?->name ?? 'Mahasiswa',
            'role' => $comment->user?->role === 'dosen' ? 'Pengajar' : 'Mahasiswa',
            'text' => $comment->message,
            'time' => optional($comment->created_at)->diffForHumans(),
            'avatar' => $avatar,
            'created_at' => optional($comment->created_at)->toISOString(),
        ];
    }

    private function notifyCertificateReadyIfEligible($mahasiswa, Course $course, $enrollment, int $progressPercent): void
    {
        $this->issueCertificateForEnrollmentIfEligible($mahasiswa, $course, $enrollment, $progressPercent);
    }

    private function issueCertificateForEnrollmentIfEligible($mahasiswa, Course $course, $enrollment, int $progressPercent): ?array
    {
        if (!($course->sertifikat ?? false)) {
            return null;
        }

        $isCompleted = (($enrollment->status ?? null) === 'selesai') || $progressPercent >= 100;
        if (!$isCompleted) {
            return null;
        }

        $certificate = $this->resolveIssuedCertificateModel($mahasiswa, $course);
        if (!$certificate) {
            $template = $this->resolvePreferredCertificateTemplate();
            if (!$template) {
                return null;
            }

            $attributes = [
                'certificate_template_id' => $template->id,
                'nomor_sertifikat' => $this->generateNextCertificateNumber($course),
                'nama_peserta' => trim((string) ($mahasiswa->name ?? 'Mahasiswa')),
                'nama_program' => trim((string) ($course->nama_course ?? 'Kursus')),
                'tanggal_terbit' => now()->toDateString(),
            ];

            if ($this->automaticCertificateHasDirectMapping()) {
                $attributes['id_mahasiswa'] = (int) $mahasiswa->id;
                $attributes['id_course'] = (int) $course->id_course;
                $attributes['source'] = 'auto';
            }

            $certificate = AutomaticCertificate::create($attributes);
        }

        $this->notifyCertificateAvailableOnce($mahasiswa, $course);

        return $this->formatIssuedCertificatePayload($certificate->loadMissing('template'));
    }

    private function syncWebinarCompletionIfEligible($mahasiswa, Course $course, $enrollment)
    {
        if (($course->kategori ?? null) !== 'webinar') {
            return $enrollment;
        }

        if (!in_array((string) ($enrollment->status ?? ''), ['aktif', 'in_progress', 'selesai'], true)) {
            return $enrollment;
        }

        if ((string) ($enrollment->status ?? '') === 'selesai' || (float) ($enrollment->progress ?? 0) >= 100) {
            return $enrollment;
        }

        if (!$this->hasWebinarEnded($course)) {
            return $enrollment;
        }

        $enrollment->update([
            'progress' => 100,
            'status' => 'selesai',
        ]);

        return $enrollment->fresh() ?: $enrollment;
    }

    private function hasWebinarEnded(Course $course): bool
    {
        if (!$course->tanggal_webinar) {
            return false;
        }

        $date = $course->tanggal_webinar instanceof \DateTimeInterface
            ? $course->tanggal_webinar->format('Y-m-d')
            : (string) $course->tanggal_webinar;
        $time = $course->jam_selesai_webinar ?: '23:59:59';

        try {
            return \Carbon\Carbon::parse(trim($date . ' ' . $time), config('app.timezone'))->lte(now());
        } catch (\Throwable) {
            return false;
        }
    }

    private function notifyCertificateAvailableOnce($mahasiswa, Course $course): void
    {
        $courseName = trim((string) ($course->nama_course ?? 'Kursus'));
        $programType = ($course->kategori ?? null) === 'webinar' ? 'webinar' : 'kursus';

        $alreadyNotified = Notification::query()
            ->where('id_mahasiswa', $mahasiswa->id)
            ->where('tipe', 'pencapaian')
            ->where('icon', 'certificate')
            ->where('judul', 'Sertifikat Kursus Tersedia')
            ->where('konten', 'like', '%' . $courseName . '%')
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        Notification::notifyMahasiswa(
            $mahasiswa->id,
            'Sertifikat Kursus Tersedia',
            'Sertifikat untuk ' . $programType . ' "' . $courseName . '" sudah tersedia. Buka halaman kursus dan klik Download Sertifikat.',
            'pencapaian',
            'certificate',
            '#10B981'
        );
    }

    private function resolveIssuedCertificateModel($mahasiswa, Course $course): ?AutomaticCertificate
    {
        if ($this->automaticCertificateHasDirectMapping()) {
            $certificate = AutomaticCertificate::query()
                ->with('template')
                ->where('id_mahasiswa', (int) $mahasiswa->id)
                ->where('id_course', (int) $course->id_course)
                ->orderByDesc('tanggal_terbit')
                ->orderByDesc('id')
                ->first();

            if ($certificate) {
                return $certificate;
            }
        }

        $certificate = $this->findCertificateByNames($mahasiswa->name ?? '', $course->nama_course ?? '');

        if ($certificate && $this->automaticCertificateHasDirectMapping()) {
            $updates = [];
            if (!$certificate->id_mahasiswa) {
                $updates['id_mahasiswa'] = (int) $mahasiswa->id;
            }
            if (!$certificate->id_course) {
                $updates['id_course'] = (int) $course->id_course;
            }
            if (!($certificate->source ?? null)) {
                $updates['source'] = 'manual';
            }
            if ($updates) {
                $certificate->update($updates);
            }
        }

        return $certificate;
    }

    private function findCertificateByNames(string $mahasiswaName, string $courseName): ?AutomaticCertificate
    {
        $normalizedName = Str::lower(trim($mahasiswaName));
        $normalizedCourse = Str::lower(trim($courseName));

        if ($normalizedName === '' || $normalizedCourse === '') {
            return null;
        }

        return AutomaticCertificate::query()
            ->with('template')
            ->whereRaw('LOWER(TRIM(nama_peserta)) = ?', [$normalizedName])
            ->whereRaw('LOWER(TRIM(nama_program)) = ?', [$normalizedCourse])
            ->orderByDesc('tanggal_terbit')
            ->orderByDesc('id')
            ->first();
    }

    private function automaticCertificateHasDirectMapping(): bool
    {
        return Schema::hasColumn('automatic_certificates', 'id_mahasiswa')
            && Schema::hasColumn('automatic_certificates', 'id_course');
    }

    private function generateNextCertificateNumber(?Course $course = null): string
    {
        $currentYear = now()->year;
        $prefix = ($course?->kategori ?? null) === 'webinar' ? 'WEB' : 'SRT';

        $maxNumber = AutomaticCertificate::query()
            ->where('nomor_sertifikat', 'like', "{$prefix}-{$currentYear}-%")
            ->get(['nomor_sertifikat'])
            ->map(function (AutomaticCertificate $certificate): int {
                if (preg_match('/(\d+)$/', $certificate->nomor_sertifikat, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        return $prefix . '-' . $currentYear . '-' . str_pad((string) ($maxNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    private function resolvePreferredCertificateTemplate(): ?CertificateTemplate
    {
        return CertificateTemplate::query()
            ->latest('id')
            ->get()
            ->first(fn (CertificateTemplate $template) => $this->isCertificateTemplateAvailable($template));
    }

    private function isCertificateTemplateAvailable(?CertificateTemplate $template): bool
    {
        if (!$template) {
            return false;
        }

        if ($template->background_type !== 'image') {
            return true;
        }

        return filled($template->background_image_path)
            && Storage::disk('public')->exists($template->background_image_path);
    }

    private function formatIssuedCertificatePayload(AutomaticCertificate $certificate): array
    {
        return [
            'id' => $certificate->id,
            'number' => $certificate->nomor_sertifikat,
            'issued_date' => optional($certificate->tanggal_terbit)->format('d F Y'),
            'issued_date_iso' => optional($certificate->tanggal_terbit)->format('Y-m-d'),
            'participant_name' => $certificate->nama_peserta,
            'program_name' => $certificate->nama_program,
            'template' => $this->buildCertificateTemplatePayload($certificate->template),
        ];
    }

    private function resolveIssuedCertificateForMahasiswa(string $mahasiswaName, string $courseName): ?array
    {
        $certificate = $this->findCertificateByNames($mahasiswaName, $courseName);

        if (!$certificate) {
            return null;
        }

        return $this->formatIssuedCertificatePayload($certificate);
    }

    private function buildCertificateTemplatePayload(?CertificateTemplate $template): array
    {
        $settings = $this->resolveCertificateTemplateSettings($template);
        $imageUrl = null;
        $kind = 'gradient';
        $gradient = 'linear-gradient(145deg, #ffffff 0%, #f8fafc 55%, #eef2ff 100%)';

        if ($template) {
            if ($template->background_type === 'image'
                && filled($template->background_image_path)
                && Storage::disk('public')->exists($template->background_image_path)
            ) {
                $kind = 'image';
                $imageUrl = asset('storage/' . ltrim((string) $template->background_image_path, '/'));
            } elseif ($template->background_type === 'gradient' && filled($template->background_gradient)) {
                $gradient = (string) $template->background_gradient;
            }
        }

        return [
            'name' => $template?->name ?? 'Template Sertifikat',
            'kind' => $kind,
            'image' => $imageUrl,
            'gradient' => $gradient,
            'settings' => [
                'nomor' => [
                    'x' => (float) $settings['nomor_x'],
                    'y' => (float) $settings['nomor_y'],
                    'size' => (int) $settings['nomor_size'],
                ],
                'nama' => [
                    'x' => (float) $settings['nama_x'],
                    'y' => (float) $settings['nama_y'],
                    'size' => (int) $settings['nama_size'],
                ],
                'program' => [
                    'x' => (float) $settings['program_x'],
                    'y' => (float) $settings['program_y'],
                    'size' => (int) $settings['program_size'],
                ],
                'tanggal' => [
                    'x' => (float) $settings['tanggal_x'],
                    'y' => (float) $settings['tanggal_y'],
                    'size' => (int) $settings['tanggal_size'],
                ],
            ],
        ];
    }

    private function resolveCertificateTemplateSettings(?CertificateTemplate $template): array
    {
        $defaults = $this->defaultCertificateTemplateSettings();

        if (!$template) {
            return $defaults;
        }

        $settings = [
            'nomor_x' => (float) ($template->nomor_x ?? $defaults['nomor_x']),
            'nomor_y' => (float) ($template->nomor_y ?? $defaults['nomor_y']),
            'nomor_size' => (int) ($template->nomor_size ?? $defaults['nomor_size']),
            'nama_x' => (float) ($template->nama_x ?? $defaults['nama_x']),
            'nama_y' => (float) ($template->nama_y ?? $defaults['nama_y']),
            'nama_size' => (int) ($template->nama_size ?? $defaults['nama_size']),
            'program_x' => (float) ($template->program_x ?? $defaults['program_x']),
            'program_y' => (float) ($template->program_y ?? $defaults['program_y']),
            'program_size' => (int) ($template->program_size ?? $defaults['program_size']),
            'tanggal_x' => (float) ($template->tanggal_x ?? $defaults['tanggal_x']),
            'tanggal_y' => (float) ($template->tanggal_y ?? $defaults['tanggal_y']),
            'tanggal_size' => (int) ($template->tanggal_size ?? $defaults['tanggal_size']),
        ];

        $hasInvalidCoordinate = collect([
            $settings['nomor_x'],
            $settings['nomor_y'],
            $settings['nama_x'],
            $settings['nama_y'],
            $settings['program_x'],
            $settings['program_y'],
            $settings['tanggal_x'],
            $settings['tanggal_y'],
        ])->contains(fn ($value) => $value <= 0);

        return $hasInvalidCoordinate ? $defaults : $settings;
    }

    private function defaultCertificateTemplateSettings(): array
    {
        return [
            'nomor_x' => 50.00,
            'nomor_y' => 24.00,
            'nomor_size' => 26,
            'nama_x' => 50.00,
            'nama_y' => 43.00,
            'nama_size' => 42,
            'program_x' => 50.00,
            'program_y' => 58.00,
            'program_size' => 18,
            'tanggal_x' => 50.00,
            'tanggal_y' => 72.00,
            'tanggal_size' => 14,
        ];
    }

    /**
     * Single source of truth for "is this course a bootcamp (tiket)?"
     * Used by show(), learn(), and submitCourseReview() so the UI shape,
     * route labels, and review-guard semantics never drift apart.
     *
     * If a future refactor moves this onto the Course model (e.g. an `is_bootcamp`
     * accessor), update this helper AND the `@php` re-derivation inside
     * course-detail.blade.php / course-learn.blade.php together.
     */
    private function prepareBootcampDashboardData($user, Course $course, $enrollment): array
    {
        // 1. Gather all course materials
        $materials = $course->materials()->orderBy('urutan')->get();
        $totalMaterials = $materials->count();
        
        // 2. Load all module structures
        $courseModules = CourseModule::where('id_course', $course->id_course)
            ->orderBy('urutan')
            ->get();
            
        // Get completed materials list for the user
        $completedMaterialsIds = \App\Models\MaterialProgress::where('id_mahasiswa', $user->id)
            ->whereIn('id_material', $materials->pluck('id_material'))
            ->where('is_completed', true)
            ->pluck('id_material')
            ->toArray();
            
        // 3. Quiz attempt stats
        $quizzes = Quiz::where('id_course', $course->id_course)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();
            
        $completedQuizAttempts = QuizAttempt::where('id_mahasiswa', $user->id)
            ->where('status', 'selesai')
            ->whereIn('id_quiz', $quizzes->pluck('id_quiz'))
            ->get()
            ->groupBy('id_quiz');
            
        // 4. Assignments
        $assignments = $course->materials()
            ->whereIn('tipe', ['tugas', 'assignment', 'tugas_akhir'])
            ->orderBy('urutan')
            ->get();
            
        $submissions = AssignmentSubmission::where('id_mahasiswa', $user->id)
            ->where('id_course', $course->id_course)
            ->get()
            ->keyBy('id_material');
            
        // 5. Final Project heuristic
        $finalProjectMaterial = null;
        foreach ($assignments as $assignment) {
            $title = Str::lower(trim((string) $assignment->judul_material));
            if (Str::contains($title, ['tugas akhir', 'final', 'project akhir', 'ujian akhir'])) {
                $finalProjectMaterial = $assignment;
                break;
            }
        }
        
        // Determine bootcamp module progression mode
        // course id % 3: 0 -> sequential, 1 -> scheduled, 2 -> open
        $moduleMode = 'sequential';
        if ($course->id_course % 3 === 1) {
            $moduleMode = 'scheduled';
        } elseif ($course->id_course % 3 === 2) {
            $moduleMode = 'open';
        }
        
        // Build modules data with locked/completed status
        $modulesData = [];
        $previousModuleCompleted = true;
        $enrollmentDate = $enrollment->created_at ?? now();
        
        $totalMandatoryActivities = 0;
        $completedMandatoryActivities = 0;
        
        foreach ($courseModules as $index => $module) {
            $moduleNum = $module->id_module;
            $moduleMaterials = $materials->where('id_module', $moduleNum);
            $moduleQuizzes = $quizzes->where('id_module', $moduleNum);
            
            // Exclude final project assignment from normal module prerequisites if it's the final project module
            $isFinalProjectInModule = $finalProjectMaterial && ($finalProjectMaterial->id_module == $moduleNum);
            
            $moduleMaterialsCount = $moduleMaterials->count();
            $completedModuleMaterialsCount = $moduleMaterials->whereIn('id_material', $completedMaterialsIds)->count();
            
            $moduleQuizzesCount = $moduleQuizzes->count();
            $completedQuizzesCount = 0;
            foreach ($moduleQuizzes as $quiz) {
                if ($completedQuizAttempts->has($quiz->id_quiz)) {
                    $completedQuizzesCount++;
                }
            }
            
            $totalMandatoryActivities += $moduleMaterialsCount + $moduleQuizzesCount;
            $completedMandatoryActivities += $completedModuleMaterialsCount + $completedQuizzesCount;
            
            // Check status
            $status = 'Available';
            $lockReason = null;
            
            if ($moduleMode === 'sequential' && !$previousModuleCompleted) {
                $status = 'Locked';
                $lockReason = 'Modul sebelumnya belum selesai.';
            } elseif ($moduleMode === 'scheduled') {
                $daysOffset = $index * 7; // 1 week per module
                $releaseDate = $enrollmentDate->copy()->addDays($daysOffset);
                if (now()->lessThan($releaseDate)) {
                    $status = 'Locked';
                    $lockReason = 'Modul akan dirilis pada tanggal ' . $releaseDate->format('d M Y') . '.';
                }
            }
            
            if ($status !== 'Locked') {
                $totalItems = $moduleMaterialsCount + $moduleQuizzesCount;
                $completedItems = $completedModuleMaterialsCount + $completedQuizzesCount;
                
                if ($completedItems === $totalItems && $totalItems > 0) {
                    $status = 'Completed';
                } elseif ($completedItems > 0) {
                    $status = 'In Progress';
                }
            }
            
            // Update sequential tracker for NEXT module
            $isThisModuleFinished = ($status === 'Completed');
            // If the module only contains the final project and it is locked, don't count it for previous module completion yet
            if ($isFinalProjectInModule) {
                $isThisModuleFinished = true;
            }
            $previousModuleCompleted = $isThisModuleFinished;
            
            $modulesData[$moduleNum] = [
                'id' => $moduleNum,
                'title' => $module->judul_module ?: 'Modul ' . ($index + 1),
                'description' => $module->deskripsi,
                'status' => $status,
                'lock_reason' => $lockReason,
                'materials' => $moduleMaterials->map(function ($mat) use ($completedMaterialsIds) {
                    return [
                        'id' => $mat->id_material,
                        'title' => $mat->judul_material ?: 'Materi',
                        'type' => $this->normalizeMaterialType($mat->tipe),
                        'content' => $mat->konten,
                        'video_url' => $mat->video_url,
                        'lampiran_path' => $mat->lampiran_path,
                        'sumber_referensi' => $mat->sumber_referensi,
                        'is_completed' => in_array($mat->id_material, $completedMaterialsIds),
                    ];
                }),
                'quizzes' => $moduleQuizzes,
                'completed_count' => $completedModuleMaterialsCount + $completedQuizzesCount,
                'total_count' => $moduleMaterialsCount + $moduleQuizzesCount,
            ];
        }
        
        // 6. Assignments Tab Data
        $assignmentsData = [];
        $assignmentCount = 0;
        $completedAssignmentsCount = 0;
        
        foreach ($assignments as $mat) {
            // Skip final project if we treat it separately
            if ($finalProjectMaterial && $mat->id_material == $finalProjectMaterial->id_material) {
                continue;
            }
            
            $payload = $this->parseAssignmentPayload($mat->konten);
            $deadline = $this->parseAssignmentDeadline($payload['deadline'] ?? null);
            $submission = $submissions->get($mat->id_material);
            
            $assignmentCount++;
            if ($submission && in_array($submission->status, ['submitted', 'approved', 'reviewed'], true)) {
                $completedAssignmentsCount++;
            }
            
            $status = 'Belum Dikerjakan';
            if ($submission) {
                if ($submission->status === 'draft') {
                    $status = 'Draft';
                } elseif ($submission->status === 'submitted') {
                    $status = 'Menunggu Review';
                } elseif ($submission->status === 'revision' || $submission->status === 'perlu_revisi') {
                    $status = 'Perlu Revisi';
                } elseif ($submission->status === 'approved' || $submission->status === 'reviewed') {
                    $status = 'Disetujui';
                }
                
                if ($submission->submitted_at && $submission->submitted_at->greaterThan($deadline)) {
                    $status .= ' (Terlambat)';
                }
            } else {
                if (now()->greaterThan($deadline)) {
                    $status = 'Terlambat';
                }
            }
            
            $assignmentsData[] = [
                'material_id' => $mat->id_material,
                'title' => $mat->judul_material ?: 'Tugas',
                'description' => $payload['deskripsi'] ?? $mat->konten,
                'instructions' => $payload['instruksi'] ?? 'Ikuti petunjuk pengerjaan yang diberikan.',
                'deadline' => $deadline,
                'countdown' => now()->lessThan($deadline) ? now()->diffForHumans($deadline, true) : 'Tenggat Waktu Lewat',
                'submission' => $submission,
                'status' => $status,
                'allow_late' => (bool) ($payload['late_submission'] ?? $payload['allow_late'] ?? true),
                'lampiran_path' => $mat->lampiran_path,
            ];
        }
        
        // Include assignments & final project in the total progress calculation
        $totalMandatoryActivities += $assignmentCount;
        $completedMandatoryActivities += $completedAssignmentsCount;
        
        if ($finalProjectMaterial) {
            $totalMandatoryActivities++;
            $fpSubmission = $submissions->get($finalProjectMaterial->id_material);
            if ($fpSubmission && ($fpSubmission->status === 'approved' || $fpSubmission->status === 'reviewed')) {
                $completedMandatoryActivities++;
            }
        }
        
        $progressPercent = $totalMandatoryActivities > 0 
            ? round(($completedMandatoryActivities / $totalMandatoryActivities) * 100) 
            : 0;
            
        // Sync progress back to enrollment
        $enrollment->update([
            'progress' => $progressPercent,
            'status' => $progressPercent >= 100 ? 'selesai' : 'aktif',
        ]);
        
        // 7. Live Classes
        $liveClasses = [];
        if ($course->tanggal_webinar) {
            $startStr = $course->tanggal_webinar->format('Y-m-d') . ' ' . ($course->jam_mulai_webinar ?: '08:00:00');
            $endStr = $course->tanggal_webinar->format('Y-m-d') . ' ' . ($course->jam_selesai_webinar ?: '17:00:00');
            $startTime = Carbon::parse($startStr);
            $endTime = Carbon::parse($endStr);
            
            $isActive = now()->greaterThanOrEqualTo($startTime->copy()->subMinutes(15)) 
                && now()->lessThanOrEqualTo($endTime);
                
            $recordingUrl = now()->greaterThan($endTime) ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null;
            
            $liveClasses[] = [
                'title' => 'Sesi Utama: ' . $course->nama_course,
                'mentor' => $course->dosen?->name ?: 'Mentor Utama',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'link' => 'https://zoom.us/j/9998887771',
                'is_active' => $isActive,
                'countdown' => now()->lessThan($startTime) ? now()->diffForHumans($startTime, true) : 'Kelas Dimulai',
                'recording_url' => $recordingUrl,
            ];
        }
        
        foreach ($courseModules as $index => $module) {
            $startTime = $enrollmentDate->copy()->addDays(($index * 7) + 2)->hour(19)->minute(0)->second(0);
            $endTime = $startTime->copy()->addHours(2);
            $isActive = now()->greaterThanOrEqualTo($startTime->copy()->subMinutes(15)) 
                && now()->lessThanOrEqualTo($endTime);
            $recordingUrl = now()->greaterThan($endTime) ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null;
            
            $liveClasses[] = [
                'title' => 'Live Q&A: ' . ($module->judul_module ?: 'Modul ' . ($index + 1)),
                'mentor' => $course->dosen?->name ?: 'Mentor Utama',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'link' => 'https://meet.google.com/abc-defg-hij',
                'is_active' => $isActive,
                'countdown' => now()->lessThan($startTime) ? now()->diffForHumans($startTime, true) : 'Kelas Dimulai',
                'recording_url' => $recordingUrl,
            ];
        }
        
        // 8. Quizzes Tab Data
        $quizzesData = [];
        foreach ($quizzes as $quiz) {
            $quizAttempts = QuizAttempt::where('id_mahasiswa', $user->id)
                ->where('id_quiz', $quiz->id_quiz)
                ->orderByDesc('skor')
                ->get();
                
            $attemptsCount = $quizAttempts->count();
            $bestScore = $quizAttempts->max('skor') ?? 0;
            
            $status = 'Belum Mulai';
            if ($attemptsCount > 0) {
                $status = $bestScore >= ($quiz->passing_score ?? 70) ? 'Lulus' : 'Gagal';
            }
            
            $quizzesData[] = [
                'id_quiz' => $quiz->id_quiz,
                'title' => $quiz->judul ?: 'Kuis Modul',
                'description' => $quiz->deskripsi ?: 'Uji pemahaman Anda terhadap modul ini.',
                'duration' => $quiz->durasi_menit ?: 30,
                'question_count' => QuizQuestion::where('id_quiz', $quiz->id_quiz)->count(),
                'status' => $status,
                'score' => $bestScore,
                'attempts' => $attemptsCount,
                'passing_score' => $quiz->passing_score ?? 70,
            ];
        }
        
        // 9. Forum diskus khusus bootcamp
        $forumTopics = ForumTopic::where('status', 'published')
            ->where('judul', 'like', '[Bootcamp #' . $course->id_course . ']%')
            ->withCount(['publishedComments'])
            ->orderByDesc('last_activity_at')
            ->get();
            
        // 10. Announcements
        $announcements = CourseInstructorNote::where('id_course', $course->id_course)
            ->where('is_active', true)
            ->with('dosen')
            ->latest()
            ->get();
            
        // 11. Final Project
        $finalProjectData = null;
        $prerequisitesMet = true;
        
        foreach ($modulesData as $modId => $mod) {
            if ($finalProjectMaterial && $finalProjectMaterial->id_module == $modId) {
                continue;
            }
            if ($mod['status'] !== 'Completed') {
                $prerequisitesMet = false;
            }
        }
        
        if ($finalProjectMaterial) {
            $fpSubmission = $submissions->get($finalProjectMaterial->id_material);
            $fpStatus = 'Belum Mulai';
            if ($fpSubmission) {
                if ($fpSubmission->status === 'draft') {
                    $fpStatus = 'Draft';
                } elseif ($fpSubmission->status === 'submitted') {
                    $fpStatus = 'Submitted';
                } elseif ($fpSubmission->status === 'revision' || $fpSubmission->status === 'perlu_revisi') {
                    $fpStatus = 'Revisi';
                } elseif ($fpSubmission->status === 'approved' || $fpSubmission->status === 'reviewed') {
                    $fpStatus = 'Lulus';
                }
            }
            
            $finalProjectData = [
                'material_id' => $finalProjectMaterial->id_material,
                'title' => $finalProjectMaterial->judul_material ?: 'Proyek Akhir',
                'description' => $finalProjectMaterial->konten ?: 'Selesaikan Proyek Akhir untuk kelulusan Bootcamp.',
                'status' => $fpStatus,
                'submission' => $fpSubmission,
                'prerequisites_met' => $prerequisitesMet,
            ];
        }
        
        // 12. Certificate Eligibility Checklist
        $certificateChecklist = [
            'modules_completed' => $completedMandatoryActivities >= ($totalMandatoryActivities - ($finalProjectMaterial ? 1 : 0)),
            'assignments_completed' => $completedAssignmentsCount >= $assignmentCount,
            'final_project_passed' => $finalProjectData ? ($finalProjectData['status'] === 'Lulus') : true,
            'score_met' => true,
        ];
        
        $avgQuizScore = 0;
        if (!empty($quizzesData)) {
            $totalQuizScores = 0;
            foreach ($quizzesData as $quizData) {
                $totalQuizScores += $quizData['score'];
            }
            $avgQuizScore = $totalQuizScores / count($quizzesData);
            if ($avgQuizScore < 70) {
                $certificateChecklist['score_met'] = false;
            }
        }
        
        $certificateEligible = !in_array(false, $certificateChecklist, true);
        
        $issuedCertificate = null;
        if ($certificateEligible) {
            $issuedCertificate = $this->issueCertificateForEnrollmentIfEligible(
                $user,
                $course,
                $enrollment,
                $progressPercent
            );
        }
        
        $latestAnnouncement = $announcements->first();
        
        $closestDeadline = null;
        foreach ($assignmentsData as $aData) {
            if ($aData['status'] === 'Belum Dikerjakan' && ($closestDeadline === null || $aData['deadline']->lessThan($closestDeadline))) {
                $closestDeadline = $aData['deadline'];
            }
        }
        
        $nextLiveClass = null;
        foreach ($liveClasses as $lc) {
            if (now()->lessThan($lc['start_time']) && ($nextLiveClass === null || $lc['start_time']->lessThan($nextLiveClass['start_time']))) {
                $nextLiveClass = $lc;
            }
        }
        
        $lastOpenedModule = reset($modulesData) ?: null;
        foreach ($modulesData as $mod) {
            if ($mod['status'] === 'In Progress') {
                $lastOpenedModule = $mod;
                break;
            }
        }
        
        return [
            'course' => $course,
            'enrollment' => $enrollment,
            'modules' => $modulesData,
            'assignments' => $assignmentsData,
            'liveClasses' => $liveClasses,
            'quizzes' => $quizzesData,
            'forumTopics' => $forumTopics,
            'announcements' => $announcements,
            'finalProject' => $finalProjectData,
            'certificateChecklist' => $certificateChecklist,
            'certificateEligible' => $certificateEligible,
            'issuedCertificate' => $issuedCertificate,
            'progressPercent' => $progressPercent,
            'avgQuizScore' => $avgQuizScore,
            'dashboardStats' => [
                'next_class' => $nextLiveClass,
                'latest_announcement' => $latestAnnouncement,
                'closest_deadline' => $closestDeadline,
                'last_module' => $lastOpenedModule,
            ],
            'moduleMode' => $moduleMode,
        ];
    }
    
    private function checkAndSendAssignmentReminders($user, $course): void
    {
        foreach ($course->materials as $material) {
            $type = match ($material->tipe) {
                'tugas', 'assignment', 'tugas_akhir' => 'tugas',
                default => null,
            };
            if ($type !== 'tugas') {
                continue;
            }
            $payload = $this->parseAssignmentPayload($material->konten);
            if (empty($payload)) {
                continue;
            }
            
            $deadline = $this->parseAssignmentDeadline($payload['deadline'] ?? null);
            
            $hasSubmitted = AssignmentSubmission::where('id_material', $material->id_material)
                ->where('id_mahasiswa', $user->id)
                ->exists();
                
            if ($hasSubmitted) {
                continue;
            }
            
            $now = now();
            $hoursDiff = $now->diffInHours($deadline, false);
            
            $reminderType = null;
            if ($hoursDiff > 48 && $hoursDiff <= 72) {
                $reminderType = 'H-3';
            } elseif ($hoursDiff > 12 && $hoursDiff <= 24) {
                $reminderType = 'H-1';
            } elseif ($hoursDiff > 0 && $hoursDiff <= 12) {
                $reminderType = 'Hari H';
            }
            
            if ($reminderType) {
                $notifTitle = "Reminder {$reminderType}: " . ($material->judul_material ?: 'Tugas');
                $alreadyNotified = Notification::where('id_mahasiswa', $user->id)
                    ->where('judul', $notifTitle)
                    ->exists();
                    
                if (!$alreadyNotified) {
                    Notification::notifyMahasiswa(
                        $user->id,
                        $notifTitle,
                        "Tugas \"" . ($material->judul_material ?: 'Tugas') . "\" pada Bootcamp \"" . $course->nama_course . "\" mendekati deadline. Segera selesaikan!",
                        'kursus_pembelajaran',
                        'bell',
                        '#F59E0B'
                    );
                }
            }
        }
    }

    public function storeBootcampForumTopic(Request $request, $id)
    {
        $user = Auth::guard('mahasiswa')->user();
        if (!$user) {
            return redirect()->route('mahasiswa.login');
        }

        $course = Course::findOrFail($id);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'isi' => ['required', 'string', 'min:5', 'max:8000'],
        ]);

        $category = \App\Models\ForumCategory::where('is_active', true)->first();
        $categoryId = $category ? $category->id_forum_category : 1;

        $prefixedTitle = '[Bootcamp #' . $course->id_course . '] ' . trim((string) $data['judul']);

        \App\Models\ForumTopic::create([
            'category_id' => $categoryId,
            'user_id' => $user->id,
            'judul' => $prefixedTitle,
            'isi' => trim((string) $data['isi']),
            'status' => 'published',
            'last_activity_at' => now(),
        ]);

        return redirect()->to(route('mahasiswa.bootcamp-detail', ['id' => $course->id_course]) . '?tab=forum')
            ->with('success', 'Diskusi baru berhasil ditambahkan pada Forum Bootcamp ini.');
    }

}
