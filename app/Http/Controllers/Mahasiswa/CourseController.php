<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Show get courses page (course catalog)
     */
    public function index()
    {
        $search = request('search');
        $tipe = request('tipe');
        
        // Get courses from database
        $courses = Course::with(['dosen', 'jurusan'])
            ->aktif() // Only active courses
            ->search($search) // Search by nama_course or deskripsi
            ->when($tipe && $tipe !== 'semua', function ($query) use ($tipe) {
                return $query->where('tipe', $tipe);
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
}

