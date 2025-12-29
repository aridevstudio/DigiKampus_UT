<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            ->aktif() // Only active courses
            ->search($search) // Search by nama_course or deskripsi
            ->when($tipe && $tipe !== 'semua', function ($query) use ($tipe) {
                return $query->where('tipe', $tipe);
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
        $course = Course::with(['dosen', 'jurusan', 'materials', 'assignments', 'ratings.mahasiswa'])
            ->findOrFail($id);
        
        // Get user's enrollment status if logged in
        $user = Auth::guard('mahasiswa')->user();
        $isEnrolled = false;
        $enrollment = null;
        
        if ($user) {
            $enrollment = $course->enrollments()
                ->where('id_mahasiswa', $user->id)
                ->first();
            $isEnrolled = $enrollment !== null;
        }
        
        return view('pages.mahasiswa.course-detail', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'enrollment' => $enrollment,
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
        
        // Filter by course type
        if ($tipe && $tipe !== 'all') {
            $query->whereHas('course', function($q) use ($tipe) {
                $q->where('tipe', $tipe);
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
            ->selectRaw('courses.tipe, COUNT(*) as count')
            ->groupBy('courses.tipe')
            ->pluck('count', 'tipe')
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
            if (!isset($modules[$moduleNum])) {
                $modules[$moduleNum] = [
                    'title' => 'Modul ' . $moduleNum . ': ' . ($material->topik ?? 'Materi'),
                    'materials' => [],
                    'completed' => 0,
                    'total' => 0,
                ];
            }
            
            // Check if material is completed
            $isCompleted = \App\Models\MaterialProgress::where('id_mahasiswa', $user->id)
                ->where('id_material', $material->id_material)
                ->where('is_completed', true)
                ->exists();
            
            $modules[$moduleNum]['materials'][] = [
                'id' => $material->id_material,
                'title' => $material->judul,
                'type' => $material->tipe ?? 'document', // video, document, quiz
                'content' => $material->konten,
                'duration' => $material->durasi ?? '10 menit',
                'is_completed' => $isCompleted,
            ];
            
            $modules[$moduleNum]['total']++;
            if ($isCompleted) {
                $modules[$moduleNum]['completed']++;
            }
        }
        
        // Get current material (first incomplete material)
        $currentMaterial = null;
        $currentModuleIndex = 0;
        foreach ($modules as $idx => $module) {
            foreach ($module['materials'] as $mat) {
                if (!$mat['is_completed']) {
                    $currentMaterial = $mat;
                    $currentModuleIndex = $idx;
                    break 2;
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
        
        // Update enrollment progress
        $enrollment->update(['progress' => $progressPercent]);
        
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
        $enrolled = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $material->id_course)
            ->exists();
        
        if (!$enrolled) {
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
            ->where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
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
}

