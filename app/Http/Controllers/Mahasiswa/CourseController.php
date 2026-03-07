<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Assignment;
use App\Models\DosenNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $courses = Course::with(['dosen', 'jurusan'])
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
        $recommendedCourses = Course::with(['dosen'])
            ->whereNotIn('id_course', $enrolledCourseIds)
            ->aktif()
            ->orderBy('rating', 'desc')
            ->limit(3)
            ->get();
        
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
        
        $course = Course::with(['dosen', 'materials', 'assignments'])
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
        
        // Get materials grouped by module
        $materials = $course->materials()->orderBy('urutan')->get();
        
        // Group materials by module number
        $modules = [];
        foreach ($materials as $material) {
            $moduleNum = $material->modul ?? 1;
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
        
        // Get course info
        $course = \App\Models\Course::findOrFail($courseId);
        
        // Dummy assignment data
        $assignment = [
            'id' => $assignmentId,
            'title' => 'Analisis Sistem Informasi Perusahaan',
            'description' => 'Buatlah analisis mendalam tentang sistem informasi pada perusahaan pilihan Anda. Fokus pada identifikasi masalah, solusi yang direkomendasikan, dan implementasi yang dapat diterapkan.',
            'deadline' => now()->addDays(7),
            'weight' => 40,
            'format' => 'PDF, DOCX, ZIP',
            'max_size' => '10 MB',
            'learning_objectives' => [
                'Mampu menganalisis sistem informasi perusahaan',
                'Mengidentifikasi masalah dan memberikan solusi',
                'Menyusun rekomendasi implementasi',
            ],
            'steps' => [
                'Pilih perusahaan yang akan dianalisis',
                'Lakukan observasi dan pengumpulan data',
                'Analisis sistem informasi yang ada',
                'Identifikasi masalah dan solusi',
                'Susun laporan dan rekomendasi',
            ],
            'grading_criteria' => [
                ['name' => 'Analisis Mendalam', 'percentage' => 30],
                ['name' => 'Identifikasi Masalah', 'percentage' => 25],
                ['name' => 'Solusi & Rekomendasi', 'percentage' => 25],
                ['name' => 'Kualitas Laporan', 'percentage' => 20],
            ],
            'instructor_note' => 'Pastikan analisis didukung dengan data yang valid dan referensi yang relevan.',
        ];
        
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
        
        // Get course and assignment
        $course = \App\Models\Course::findOrFail($courseId);
        
        $assignment = [
            'id' => $assignmentId,
            'title' => 'Analisis Sistem Informasi Perusahaan',
            'deadline' => now()->addDays(7),
            'weight' => 40,
            'format' => 'PDF, DOCX, ZIP',
            'max_size' => '10 MB',
        ];
        
        return view('pages.mahasiswa.assignment-submission', [
            'course' => $course,
            'assignment' => $assignment,
        ]);
    }
    
    /**
     * Show assignment status after submission
     */
    public function assignmentStatus($courseId, $assignmentId)
    {
        $user = Auth::guard('mahasiswa')->user();
        $course = \App\Models\Course::findOrFail($courseId);
        
        // Dummy submission data
        $submission = [
            'id' => 1,
            'assignment_id' => $assignmentId,
            'assignment_title' => 'Analisis Sistem Informasi Perusahaan',
            'status' => 'pending', // pending, graded
            'submitted_at' => now()->subDays(2),
            'file_name' => 'Analisis_SI.pdf',
            'file_size' => '2.4 MB',
            'notes' => 'Tugas sudah saya kerjakan dengan baik.',
            // Grading (if graded)
            'grade' => null,
            'feedback' => null,
            'graded_at' => null,
        ];
        
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
            'grade_breakdown' => [
                ['name' => 'Kuis', 'weight' => 40, 'score' => 80, 'max_score' => 100],
                ['name' => 'Tugas Akhir', 'weight' => 60, 'score' => 88, 'max_score' => 100],
            ],
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
        // Store uploaded file if present
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|file|max:10240|mimes:pdf,docx,doc,zip',
            ]);

            $file = $request->file('file');
            $userId = auth('mahasiswa')->id();
            $fileName = "assignment_{$courseId}_{$assignmentId}_{$userId}_" . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('assignments', $fileName, 'public');
        }

        // Mark assignment as completed in session
        $completedAssignments = session('completed_assignments', []);
        $key = $courseId . '_' . $assignmentId;
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
        
        return response()->json([
            'success' => true,
            'redirect' => route('mahasiswa.assignment-status', ['courseId' => $courseId, 'assignmentId' => $assignmentId])
        ]);
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
}
