<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\DosenNotification;
use App\Models\CourseRating;
use App\Models\CourseDiscussion;
use App\Models\CourseInstructorNote;
use App\Models\CourseMaterial;
use App\Models\Assignment;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
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
     * Show course detail page
     */
    public function show($id)
    {
        $course = Course::with([
            'dosen',
            'jurusan',
            'materials' => fn ($query) => $query->orderBy('urutan'),
            'modules' => fn ($query) => $query->orderBy('urutan'),
            'modules.materials' => fn ($query) => $query->orderBy('urutan'),
            'assignments' => fn ($query) => $query->orderBy('deadline'),
            'ratings.mahasiswa.profile',
        ])
            ->findOrFail($id);
        
        // Get user's enrollment status if logged in
        $user = Auth::guard('mahasiswa')->user();
        $isEnrolled = false;
        $enrollment = null;
        $isFavorited = false;
        
        if ($user) {
            $enrollment = $course->enrollments()
                ->where('id_mahasiswa', $user->id)
                ->first();
            $isEnrolled = $enrollment !== null;
            
            $isFavorited = \App\Models\Favorite::where('id_mahasiswa', $user->id)
                ->where('id_course', $course->id_course)
                ->exists();
        }
        
        return view('pages.mahasiswa.course-detail', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'enrollment' => $enrollment,
            'isFavorited' => $isFavorited,
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
     */
    public function learn($id)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        $course = Course::with([
            'dosen',
            'materials',
            'assignments',
            'instructorNotes' => fn ($query) => $query->where('is_active', true)->latest(),
        ])
            ->findOrFail($id);
        $courseId = $course->id_course;
        
        // Check if user is enrolled
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $id)
            ->first();
        
        if (!$enrollment) {
            return redirect()->route('mahasiswa.course-detail', $id)
                ->with('error', 'Anda harus terdaftar untuk mengakses kursus ini');
        }

        if ($enrollment->status === 'pending') {
            return redirect()->route('mahasiswa.course-detail', $id)
                ->with('error', 'Kursus berbayar ini masih menunggu konfirmasi pembayaran dari admin.');
        }
        
        // Get materials grouped by module
        $materials = $course->materials()->orderBy('urutan')->get();
        
        // Group materials by module number
        $modules = [];
        foreach ($materials as $material) {
            $moduleNum = $material->id_module ?? $material->modul ?? 1;
            $moduleTopic = trim((string) ($material->topik ?? 'Materi'));
            $moduleTopic = preg_replace('/^modul\s+\d+\s*:\s*/i', '', $moduleTopic) ?: 'Materi';
            if (!isset($modules[$moduleNum])) {
                $modules[$moduleNum] = [
                    'title' => 'Modul ' . $moduleNum . ': ' . $moduleTopic,
                    'materials' => [],
                    'completed' => 0,
                    'total' => 0,
                    'quiz' => null,
                    'assignment' => null,
                ];
            }
            
            // Check if material is completed
            $isCompleted = \App\Models\MaterialProgress::where('id_mahasiswa', $user->id)
                ->where('id_material', $material->id_material)
                ->where('is_completed', true)
                ->exists();

            $materialType = $this->normalizeMaterialType($material->tipe);
            $materialTitle = $material->judul_material ?? $material->judul ?? 'Materi';
            
            $modules[$moduleNum]['materials'][] = [
                'id' => $material->id_material,
                'title' => $materialTitle,
                'type' => $materialType,
                'content' => $material->konten,
                'duration' => $material->durasi ?? '10 menit',
                'is_completed' => $isCompleted,
                'video_url' => $material->video_url,
            ];

            if ($materialType === 'kuis' && $modules[$moduleNum]['quiz'] === null) {
                $modules[$moduleNum]['quiz'] = [
                    'id' => $material->id_material,
                    'title' => $materialTitle,
                    'duration' => $material->durasi,
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

        // Get completion status from session
        $completedQuizzes = session('completed_quizzes', []);
        $completedAssignments = session('completed_assignments', []);
        
        // Add quiz and assignment completion status to each module
        foreach ($modules as $moduleNum => &$module) {
            $quizKey = $courseId . '_' . $moduleNum;
            $assignmentKey = $courseId . '_' . $moduleNum;
            $module['quiz_completed'] = in_array($quizKey, $completedQuizzes);
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
        
        return view('pages.mahasiswa.course-learn', [
            'course' => $course,
            'enrollment' => $enrollment,
            'modules' => $modules,
            'currentMaterial' => $currentMaterial,
            'currentModuleIndex' => $currentModuleIndex,
            'progressPercent' => $progressPercent,
            'completedMaterials' => $completedMaterials,
            'totalMaterials' => $totalMaterials,
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

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Materi ditandai selesai.',
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
                ->with('error', 'Webinar atau kursus ini masih menunggu konfirmasi pembayaran dari admin.');
        }
        
        // Get course info
        $course = \App\Models\Course::findOrFail($courseId);
        
        // For now, generate dummy quiz data
        // In real implementation, this would come from a Quiz model
        $quiz = [
            'id' => $quizId,
            'title' => 'Kuis Akhir Modul',
            'course_name' => $course->nama_course,
            'module_name' => 'Modul ' . $quizId . ': Materi Pembelajaran',
            'total_questions' => 10,
            'duration' => 30,
            'passing_score' => 70,
            'can_go_back' => false,
            'current_question' => request('q', 1),
        ];
        
        // Dummy questions
        $questions = [
            1 => [
                'text' => 'Dalam sistem operasi, apa yang dimaksud dengan dan mengapa hal ini penting context switching dalam manajemen proses?',
                'type' => 'Pilihan Ganda',
                'options' => [
                    'A' => 'Proses mengganti data dalam memori utama dengan data dari storage sekunder',
                    'B' => 'Proses menyimpan state dari proses yang sedang berjalan dan memuat state proses lain untuk dieksekusi',
                    'C' => 'Proses komunikasi antara dua proses yang berbeda melalui shared memory',
                    'D' => 'Proses mengubah prioritas eksekusi proses berdasarkan algoritma scheduling',
                ],
            ],
            2 => [
                'text' => 'Apa perbedaan utama antara proses dan thread?',
                'type' => 'Pilihan Ganda',
                'options' => [
                    'A' => 'Thread memiliki address space sendiri, proses tidak',
                    'B' => 'Proses memiliki address space sendiri, thread berbagi dengan parent',
                    'C' => 'Thread tidak bisa berkomunikasi dengan proses lain',
                    'D' => 'Tidak ada perbedaan fundamental',
                ],
            ],
            3 => [
                'text' => 'Apa yang dimaksud dengan deadlock dalam sistem operasi?',
                'type' => 'Pilihan Ganda',
                'options' => [
                    'A' => 'Kondisi dimana CPU tidak memiliki proses untuk dieksekusi',
                    'B' => 'Kondisi dimana dua atau lebih proses saling menunggu resource yang dipegang proses lain',
                    'C' => 'Kondisi dimana proses berjalan terlalu lambat',
                    'D' => 'Kondisi dimana memory tidak mencukupi',
                ],
            ],
        ];
        
        // Generate remaining dummy questions
        for ($i = 4; $i <= 10; $i++) {
            $questions[$i] = [
                'text' => 'Soal nomor ' . $i . ' tentang sistem operasi dan manajemen proses...',
                'type' => 'Pilihan Ganda',
                'options' => [
                    'A' => 'Opsi jawaban A',
                    'B' => 'Opsi jawaban B',
                    'C' => 'Opsi jawaban C',
                    'D' => 'Opsi jawaban D',
                ],
            ];
        }
        
        // Simulated user answers progress
        $userAnswers = session('quiz_' . $quizId . '_answers', []);
        $flaggedQuestions = session('quiz_' . $quizId . '_flagged', []);
        
        return view('pages.mahasiswa.course-quiz', [
            'course' => $course,
            'quiz' => $quiz,
            'questions' => $questions,
            'currentQuestion' => (int) $quiz['current_question'],
            'userAnswers' => $userAnswers,
            'flaggedQuestions' => $flaggedQuestions,
        ]);
    }
    
    /**
     * Save quiz answer to session
     */
    public function saveQuizAnswer(Request $request, $courseId, $quizId)
    {
        $questionNumber = $request->input('question');
        $answer = $request->input('answer');
        
        // Get existing answers from session
        $sessionKey = 'quiz_' . $quizId . '_answers';
        $answers = session($sessionKey, []);
        
        // Save the answer
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
        $questionNumber = $request->input('question');
        
        // Get existing flagged questions from session
        $sessionKey = 'quiz_' . $quizId . '_flagged';
        $flagged = session($sessionKey, []);
        
        // Toggle flag
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
        session()->forget('quiz_' . $quizId . '_answers');
        session()->forget('quiz_' . $quizId . '_flagged');
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Show quiz result page
     */
    public function quizResult($courseId, $quizId)
    {
        $course = \App\Models\Course::findOrFail($courseId);
        
        // Dummy result data
        $result = [
            'quiz_id' => $quizId,
            'quiz_title' => 'Kuis Akhir Modul',
            'module_name' => 'Modul ' . $quizId . ': Manajemen Proses',
            'score' => 80,
            'total_score' => 100,
            'passing_score' => 70,
            'is_passed' => true,
            'correct_answers' => 8,
            'total_questions' => 10,
            'time_taken' => '15 Menit',
        ];
        
        // Clear quiz answer session data
        session()->forget('quiz_' . $quizId . '_answers');
        session()->forget('quiz_' . $quizId . '_flagged');
        
        // Mark quiz as completed in session
        $completedQuizzes = session('completed_quizzes', []);
        $key = $courseId . '_' . $quizId;
        if (!in_array($key, $completedQuizzes)) {
            $completedQuizzes[] = $key;
            session(['completed_quizzes' => $completedQuizzes]);
        }
        
        return view('pages.mahasiswa.quiz-result', [
            'course' => $course,
            'result' => $result,
            'quizId' => $quizId,
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
        $course = \App\Models\Course::findOrFail($courseId);

        $hasAssignment = CourseMaterial::where('id_course', $courseId)
            ->where('id_module', $moduleId)
            ->get()
            ->contains(fn ($material) => $this->normalizeMaterialType($material->tipe) === 'tugas');

        $gradeBreakdown = [
            ['name' => 'Kuis', 'weight' => $hasAssignment ? 40 : 100, 'score' => 80, 'max_score' => 100],
        ];
        if ($hasAssignment) {
            $gradeBreakdown[] = ['name' => 'Tugas Akhir', 'weight' => 60, 'score' => 88, 'max_score' => 100];
        }
        
        // Dummy feedback data
        $feedback = [
            'module_id' => $moduleId,
            'module_name' => 'Modul ' . $moduleId . ' - Database Design',
            'instructor' => [
                'name' => 'Dr. Ahmad Wijaya, M.Kom',
                'avatar' => null,
            ],
            'total_score' => 85,
            'max_score' => 100,
            'is_passed' => true,
            'has_assignment' => $hasAssignment,
            'grade_breakdown' => $gradeBreakdown,
            'instructor_feedback' => [
                'date' => now()->subDays(5),
                'text' => 'Pemahaman Anda terhadap konsep database design sudah sangat baik. Khususnya dalam menerapkan normalisasi dan ERD. Namun, perlu ditingkatkan pada bagian analisis studi kasus dan implementasi query optimization. Untuk kedepannya, saya sarankan untuk lebih banyak berlatih dengan kasus nyata dan memahami best practices dalam database performance tuning.',
                'tags' => [
                    ['text' => 'Konsep Dasar Kuat', 'type' => 'positive'],
                    ['text' => 'Perlu Latihan Query', 'type' => 'warning'],
                ],
            ],
            'personal_notes' => 'Modul ini cukup menantang terutama di bagian normalisasi database. Perlu lebih banyak latihan untuk memahami konsep 3NF dan BCNF. Akan fokus belajar query optimization untuk modul selanjutnya.',
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

        // Notify dosen about assignment submission
        $course = Course::find($courseId);
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

            $discussion = CourseDiscussion::create([
                'id_course' => $courseId,
                'id_user' => $user->id,
                'message' => trim($validated['message']),
            ]);

            $discussion->load(['user.profile']);

            $course = Course::query()
                ->select(['id_course', 'id_dosen', 'nama_course'])
                ->find($courseId);

            if ($course && (int) $course->id_dosen !== (int) $user->id) {
                DosenNotification::notifyDosen(
                    (int) $course->id_dosen,
                    'Pertanyaan baru di diskusi kursus',
                    $user->name . ' mengirim pesan baru di kursus ' . $course->nama_course . '.',
                    'discussion',
                    'discussion',
                    route('dosen.kursus.detail', $course->id_course, false)
                );
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
        
        // Ensure student is enrolled
        $enrollment = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->first();
            
        if (!$enrollment) {
            return back()->with('error', 'Anda harus terdaftar di kursus ini untuk memberikan ulasan.');
        }

        if ($enrollment->status === 'pending') {
            return back()->with('error', 'Ulasan hanya bisa diberikan setelah pembayaran dikonfirmasi dan kursus aktif.');
        }
        
        if ($enrollment->progress < 100) {
            return back()->with('error', 'Anda harus menyelesaikan kursus terlebih dahulu untuk memberikan ulasan.');
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

    private function normalizeMaterialType(?string $type): string
    {
        return match ($type) {
            'video' => 'video',
            'kuis', 'quiz' => 'kuis',
            'tugas' => 'tugas',
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
}
