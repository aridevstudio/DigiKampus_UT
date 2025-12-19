<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\News;
use App\Models\Agenda;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show mahasiswa dashboard
     */
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user();
        
        // Get enrollments for progress calculation
        $enrollments = Enrollment::where('id_mahasiswa', $user->id)->get();
        
        // Calculate progress statistics
        $kursusAktif = $enrollments->where('status', 'aktif')->count();
        $kursusSelesai = $enrollments->where('status', 'selesai')->count();
        $kursusTertunda = $enrollments->where('status', 'aktif')->where('progress', '<', 50)->count();
        
        // Calculate total progress (average of active enrollments)
        $activeEnrollments = $enrollments->where('status', 'aktif');
        $totalProgress = $activeEnrollments->count() > 0 
            ? round($activeEnrollments->avg('progress'), 0) 
            : 0;
        
        // Get enrolled courses with course and dosen info (limit 3 for dashboard)
        $enrolledCourses = Enrollment::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->where('status', 'aktif')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();
        
        // Get latest news (limit 3)
        $news = News::where('is_active', true)
            ->where('tanggal_publish', '<=', now())
            ->orderBy('tanggal_publish', 'desc')
            ->take(3)
            ->get();
        
        // Get current month agenda
        $agenda = Agenda::where('id_mahasiswa', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->orderBy('tanggal', 'asc')
            ->get();
        
        return view('pages.mahasiswa.dashboard', [
            'totalProgress' => $totalProgress,
            'kursusAktif' => $kursusAktif,
            'kursusSelesai' => $kursusSelesai,
            'kursusTertunda' => $kursusTertunda,
            'enrolledCourses' => $enrolledCourses,
            'news' => $news,
            'agenda' => $agenda,
        ]);
    }

    /**
     * Show calendar page with all agenda
     */
    public function calendar()
    {
        $user = Auth::guard('mahasiswa')->user();
        
        // Get month and year from request or use current
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        
        // Get agenda for selected month (for calendar markers)
        $agenda = Agenda::where('id_mahasiswa', $user->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'asc')
            ->get();
        
        // Get same agenda for sidebar list (all events for the month, same as dashboard)
        $upcomingAgenda = $agenda;
        
        return view('pages.mahasiswa.calendar', [
            'agenda' => $agenda,
            'upcomingAgenda' => $upcomingAgenda,
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }

    /**
     * Show notification page
     */
    public function notification()
    {
        return view('pages.mahasiswa.notification');
    }
}
