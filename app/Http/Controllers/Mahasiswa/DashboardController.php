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
        $user = Auth::guard('mahasiswa')->user();
        
        $notifications = \App\Models\Notification::where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = $notifications->where('is_read', false)->count();
        
        return view('pages.mahasiswa.notification', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markNotificationRead($id)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        $notification = \App\Models\Notification::where('id_mahasiswa', $user->id)
            ->where('id_notification', $id)
            ->first();

        if ($notification) {
            $notification->is_read = true;
            $notification->save();
        }

        return redirect()->route('mahasiswa.notification')->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::guard('mahasiswa')->user();
        
        \App\Models\Notification::where('id_mahasiswa', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('mahasiswa.notification')->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    /**
     * Show news & announcements page
     */
    public function news()
    {
        $news = News::active()
            ->published()
            ->orderBy('tanggal_publish', 'desc')
            ->take(50)
            ->get()
            ->map(function ($item) {
                return [
                    'id_news' => $item->id_news,
                    'judul' => $item->judul,
                    'konten' => $item->konten,
                    'thumbnail' => $item->thumbnail,
                    'thumbnail_url' => $item->thumbnail
                        ? asset('storage/' . $item->thumbnail)
                        : null,
                    'kategori' => $item->kategori,
                    'tanggal_publish' => $item->tanggal_publish?->toDateTimeString(),
                    'waktu_relatif' => $item->waktu_relatif,
                ];
            });

        return view('pages.mahasiswa.news', [
            'active' => 'news',
            'title' => 'News',
            'newsData' => $news,
        ]);
    }
}
