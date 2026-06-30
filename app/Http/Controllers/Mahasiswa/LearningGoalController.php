<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LearningGoalController extends Controller
{
    /**
     * Show the student-side Learning Goals dashboard.
     *
     * Aggregates per-course learning goals (`course_learning_goals`) over every
     * enrollment the student can still access and derives the achievement
     * percentage from each enrollment's progress percentage using the same
     * formula as `resources/views/pages/mahasiswa/course-learn.blade.php` so
     * the per-course widget and this dashboard never disagree:
     *
     *     achievedCount = intdiv(safeProgress * totalGoals, 100)
     *     achievedIndex = achievedCount - 1
     *
     * The first N goals (sorted by `urutan` ASC) flip to "achieved".
     */
    public function index(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();

        $enrollments = Enrollment::with([
                'course:id_course,nama_course,kategori,thumbnail,sertifikat',
                'course.learningGoals',
            ])
            ->where('id_mahasiswa', $user->id)
            ->accessible()
            ->get();

        $totalEnrollments = $enrollments->count();

        $courseGoals = $enrollments
            ->filter(fn (Enrollment $enrollment) => $enrollment->course !== null)
            ->map(function (Enrollment $enrollment) {
                $course = $enrollment->course;
                $goals = $course->learningGoals;

                $totalGoals = (int) $goals->count();
                $safeProgress = (int) max(0, min(100, (int) $enrollment->progress));
                // PM spec §6: enrollment.status is the primary completion signal.
                // progress===100 is kept as a fallback so legacy data (where status
                // hasn't been re-synced yet) still flips Learning Goals to achieved.
                $enrollmentCompleted = ($enrollment->status ?? null) === 'selesai' || $safeProgress >= 100;
                $achievedCount = $totalGoals > 0
                    ? ($enrollmentCompleted ? $totalGoals : intdiv($safeProgress * $totalGoals, 100))
                    : 0;

                $isBootcamp = strtolower((string) ($course->kategori ?? '')) === 'tiket';
                $statusBadge = match (true) {
                    $enrollmentCompleted => 'selesai',
                    $safeProgress === 0 => 'belum_mulai',
                    default => 'sedang_berjalan',
                };

                return [
                    'course' => $course,
                    'enrollment' => $enrollment,
                    'progress_percent' => $safeProgress,
                    'total_goals' => $totalGoals,
                    'achieved_count' => $achievedCount,
                    'all_achieved' => $totalGoals > 0 && $achievedCount >= $totalGoals,
                    'is_bootcamp' => $isBootcamp,
                    'status_badge' => $statusBadge,
                    'has_goals' => $totalGoals > 0,
                    'name_sort' => Str::lower((string) ($course->nama_course ?? '')),
                    'goals' => $goals
                        ->map(function ($goal, $index) use ($achievedCount) {
                            return [
                                'judul' => $goal->judul_goal,
                                'deskripsi' => $goal->deskripsi,
                                'urutan' => (int) $goal->urutan,
                                'is_achieved' => $index < $achievedCount,
                            ];
                        })
                        ->values(),
                ];
            })
            // Only surface courses whose dosen/admin actually defined goals for.
            ->filter(fn (array $row) => $row['has_goals'])
            // Highest progress first, then most achievements as a tie-breaker,
            // then alphabetical fallback that doesn't need dot-notation through
            // Laravel's Arr::sort plumbing.
            ->sortBy([
                ['progress_percent', 'desc'],
                ['achieved_count', 'desc'],
                ['name_sort', 'asc'],
            ])
            ->values();

        $summaryCoursesCount = $courseGoals->count();
        $summaryTotalGoals = $courseGoals->sum('total_goals');
        $summaryTotalAchieved = $courseGoals->sum('achieved_count');
        $summaryFullyAchieved = $courseGoals->where('all_achieved', true)->count();
        $summaryInProgress = $summaryCoursesCount - $summaryFullyAchieved;
        $summaryAchievementRate = $summaryTotalGoals > 0
            ? (int) round(($summaryTotalAchieved / $summaryTotalGoals) * 100)
            : 0;

        return view('pages.mahasiswa.learning-goals', [
            'active' => 'learning-goals',
            'title' => 'Learning Goals',
            'courseGoals' => $courseGoals,
            'totalEnrollments' => $totalEnrollments,
            'summary' => [
                'courses_count' => $summaryCoursesCount,
                'total_goals' => $summaryTotalGoals,
                'total_achieved' => $summaryTotalAchieved,
                'fully_achieved_courses' => $summaryFullyAchieved,
                'in_progress_courses' => $summaryInProgress,
                'achievement_rate' => $summaryAchievementRate,
            ],
        ]);
    }
}
