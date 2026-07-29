<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LearningGoalController extends Controller
{
    /**
     * Show the student-side Dashboard Capaian Pembelajaran & Learning Goals.
     * Aggregates real learning data from enrollments, course goals, quiz attempts,
     * assignment submissions, and certificate completions.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();

        // 1. Fetch All Student Enrollments
        $enrollments = Enrollment::with([
                'course:id_course,nama_course,kategori,thumbnail,sertifikat,tipe_event',
                'course.learningGoals',
            ])
            ->where('id_mahasiswa', $user->id)
            ->accessible()
            ->get();

        $totalEnrollments = $enrollments->count();

        // 2. Fetch Quiz Attempts for Current Student
        $quizAttempts = QuizAttempt::with(['quiz.course'])
            ->where('id_mahasiswa', $user->id)
            ->orderByDesc('created_at')
            ->get();

        // 3. Fetch Assignment Submissions for Current Student
        $assignmentSubmissions = AssignmentSubmission::with(['course', 'material'])
            ->where('id_mahasiswa', $user->id)
            ->orderByDesc('submitted_at')
            ->get();

        // 4. Calculate Detailed Course Achievements & Competencies
        $courseGoals = $enrollments
            ->filter(fn (Enrollment $enrollment) => $enrollment->course !== null)
            ->map(function (Enrollment $enrollment) use ($quizAttempts, $assignmentSubmissions) {
                $course = $enrollment->course;
                $goals = $course->learningGoals;

                $totalGoals = (int) $goals->count();
                $safeProgress = (int) max(0, min(100, (int) $enrollment->progress));
                $enrollmentCompleted = ($enrollment->status ?? null) === 'selesai' || $safeProgress >= 100;

                $achievedCount = $totalGoals > 0
                    ? ($enrollmentCompleted ? $totalGoals : intdiv($safeProgress * $totalGoals, 100))
                    : 0;

                $isBootcamp = strtolower((string) ($course->kategori ?? '')) === 'tiket' || !empty($course->tipe_event);
                $statusBadge = match (true) {
                    $enrollmentCompleted => 'selesai',
                    $safeProgress === 0 => 'belum_mulai',
                    default => 'sedang_berjalan',
                };

                // Filter quizzes & assignments for this course
                $courseQuizzes = $quizAttempts->filter(fn ($attempt) => $attempt->quiz?->id_course === $course->id_course);
                $avgQuizScore = $courseQuizzes->count() > 0 ? round($courseQuizzes->avg('persentase'), 1) : null;

                $courseSubmissions = $assignmentSubmissions->filter(fn ($sub) => $sub->id_course === $course->id_course);

                return [
                    'course' => $course,
                    'enrollment' => $enrollment,
                    'progress_percent' => $safeProgress,
                    'total_goals' => $totalGoals,
                    'achieved_count' => $achievedCount,
                    'all_achieved' => $enrollmentCompleted || ($totalGoals > 0 && $achievedCount >= $totalGoals),
                    'is_bootcamp' => $isBootcamp,
                    'status_badge' => $statusBadge,
                    'has_goals' => $totalGoals > 0,
                    'avg_quiz_score' => $avgQuizScore,
                    'quizzes_count' => $courseQuizzes->count(),
                    'submissions_count' => $courseSubmissions->count(),
                    'name_sort' => Str::lower((string) ($course->nama_course ?? '')),
                    'goals' => $goals
                        ->map(function ($goal, $index) use ($achievedCount, $enrollmentCompleted) {
                            return [
                                'judul' => $goal->judul_goal,
                                'deskripsi' => $goal->deskripsi,
                                'urutan' => (int) $goal->urutan,
                                'is_achieved' => $enrollmentCompleted || ($index < $achievedCount),
                            ];
                        })
                        ->values(),
                ];
            })
            ->sortBy([
                ['progress_percent', 'desc'],
                ['achieved_count', 'desc'],
                ['name_sort', 'asc'],
            ])
            ->values();

        // 5. Aggregate Global Summary Statistics
        $coursesRunning = $courseGoals->whereIn('status_badge', ['sedang_berjalan', 'belum_mulai'])->count();
        $coursesCompleted = $courseGoals->where('status_badge', 'selesai')->count();
        $overallProgressAvg = $totalEnrollments > 0 ? (int) round($courseGoals->avg('progress_percent')) : 0;

        $allQuizScores = $quizAttempts->pluck('persentase')->filter();
        $overallQuizScoreAvg = $allQuizScores->count() > 0 ? round($allQuizScores->avg(), 1) : null;

        $totalAchievedCompetencies = $courseGoals->sum('achieved_count');
        $earnedCertificatesCount = $courseGoals->where('status_badge', 'selesai')->count();

        $summary = [
            'total_enrollments' => $totalEnrollments,
            'courses_running' => $coursesRunning,
            'courses_completed' => $coursesCompleted,
            'overall_progress_avg' => $overallProgressAvg,
            'overall_quiz_score_avg' => $overallQuizScoreAvg,
            'total_competencies_achieved' => $totalAchievedCompetencies,
            'earned_certificates_count' => $earnedCertificatesCount,
            'total_quizzes_taken' => $quizAttempts->count(),
            'total_assignments_submitted' => $assignmentSubmissions->count(),
        ];

        return view('pages.mahasiswa.learning-goals', [
            'active' => 'learning-goals',
            'title' => 'Dashboard Capaian Pembelajaran',
            'courseGoals' => $courseGoals,
            'quizAttempts' => $quizAttempts,
            'assignmentSubmissions' => $assignmentSubmissions,
            'totalEnrollments' => $totalEnrollments,
            'summary' => $summary,
        ]);
    }
}
