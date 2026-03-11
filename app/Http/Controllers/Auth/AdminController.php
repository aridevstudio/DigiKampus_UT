<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\YoutubePlaylistVideo;
use App\Services\ExcelImportService;
use App\Services\YoutubePlaylistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('Auth.admin.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('alert', 'Email tidak terdaftar sebagai admin.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->with('alert', 'Password salah. Silakan coba lagi.');
        }

        if ($user->status !== 'aktif') {
            return back()
                ->withInput()
                ->with('alert', 'Akun Anda sedang tidak aktif. Hubungi super admin.');
        }

        // Login using Laravel Auth guard
        Auth::guard('admin')->login($user, $request->boolean('remember'));

        return redirect()->route('admin.dashboard')
            ->with('status', 'Login berhasil. Selamat datang!');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('Auth.admin.forgot-password');
    }

    /**
     * Send forgot password OTP
     */
    public function sendForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->where('role', 'admin')->first();
        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak ditemukan sebagai admin.']);
        }

        // Generate OTP 4 digit
        $otp = rand(1000, 9999);

        // Save OTP to database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        // Send Email
        try {
            Mail::to($email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi nanti.']);
        }

        session(['admin_reset_email' => $email]);

        return redirect()->route('admin.verify-otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Show verify OTP form
     */
    public function showVerifyOtpForm()
    {
        if (!session('admin_reset_email')) {
            return redirect()->route('admin.forgot-password');
        }
        return view('Auth.admin.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:4'],
        ]);

        $email = session('admin_reset_email');
        $otp = $request->otp;

        if (!$email) {
            return redirect()->route('admin.forgot-password')
                ->with('alert', 'Session expired. Silakan ulangi proses.');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Permintaan tidak ditemukan. Silakan ulangi proses.']);
        }

        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan kirim ulang.']);
        }

        if (!Hash::check($otp, $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali.']);
        }

        // Generate verification token
        $verificationToken = Str::random(64);

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($verificationToken),
                'created_at' => now()
            ]);

        session(['admin_reset_token' => $verificationToken]);

        return redirect()->route('admin.reset-password')
            ->with('status', 'OTP valid. Silakan masukkan password baru.');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm()
    {
        if (!session('admin_reset_email') || !session('admin_reset_token')) {
            return redirect()->route('admin.forgot-password');
        }
        return view('Auth.admin.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = session('admin_reset_email');
        $token = session('admin_reset_token');

        if (!$email || !$token) {
            return redirect()->route('admin.forgot-password')
                ->with('alert', 'Session expired. Silakan ulangi proses.');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return redirect()->route('admin.forgot-password')
                ->with('alert', 'Token tidak valid. Silakan ulangi proses.');
        }

        $user = User::where('email', $email)->where('role', 'admin')->first();

        if (!$user) {
            return redirect()->route('admin.forgot-password')
                ->with('alert', 'User tidak ditemukan.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        session()->forget(['admin_reset_email', 'admin_reset_token']);

        return redirect()->route('admin.login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    /**
     * Show dashboard
     */
    public function showDashboard()
    {
        $admin = Auth::guard('admin')->user();

        $totalDosen = User::where('role', 'dosen')->where('status', 'aktif')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->where('status', 'aktif')->count();
        $kursusAktif = \App\Models\Course::where('status', 'aktif')->count();
        $totalKursus = \App\Models\Course::count();
        $pendaftaranBulanIni = \App\Models\Enrollment::whereMonth('tanggal_daftar', now()->month)
            ->whereYear('tanggal_daftar', now()->year)
            ->count();

        // Weekly enrollment data for chart (last 7 days, grouped by day)
        $startOfWeek = now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $endOfWeek = now()->endOfWeek(\Carbon\Carbon::SUNDAY);
        $weeklyEnrollments = \App\Models\Enrollment::selectRaw('DAYOFWEEK(tanggal_daftar) as day_num, COUNT(*) as total')
            ->whereBetween('tanggal_daftar', [$startOfWeek, $endOfWeek])
            ->groupByRaw('DAYOFWEEK(tanggal_daftar)')
            ->pluck('total', 'day_num')
            ->toArray();

        // DAYOFWEEK: 1=Sunday, 2=Monday, ..., 7=Saturday
        // Map to [Sen, Sel, Rab, Kam, Jum, Sab, Min]
        $chartData = [
            $weeklyEnrollments[2] ?? 0, // Sen (Monday)
            $weeklyEnrollments[3] ?? 0, // Sel (Tuesday)
            $weeklyEnrollments[4] ?? 0, // Rab (Wednesday)
            $weeklyEnrollments[5] ?? 0, // Kam (Thursday)
            $weeklyEnrollments[6] ?? 0, // Jum (Friday)
            $weeklyEnrollments[7] ?? 0, // Sab (Saturday)
            $weeklyEnrollments[1] ?? 0, // Min (Sunday)
        ];

        // Recent activities from real data
        $recentActivities = collect();

        // Recent enrollments
        $recentEnrollments = \App\Models\Enrollment::with(['mahasiswa', 'course'])
            ->orderByDesc('tanggal_daftar')
            ->limit(5)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'type' => 'enrollment',
                    'icon_color' => 'bg-green-500',
                    'initials' => strtoupper(substr($enrollment->mahasiswa->name ?? 'M', 0, 2)),
                    'title' => 'Mahasiswa mendaftar kursus',
                    'description' => ($enrollment->mahasiswa->name ?? 'Mahasiswa') . ' - ' . ($enrollment->course->nama_course ?? 'Kursus'),
                    'time' => $enrollment->tanggal_daftar,
                ];
            });
        $recentActivities = $recentActivities->merge($recentEnrollments);

        // Recent new users (dosen & mahasiswa)
        $recentUsers = User::whereIn('role', ['dosen', 'mahasiswa'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                $color = $user->role === 'dosen' ? 'bg-blue-500' : 'bg-indigo-500';
                $label = $user->role === 'dosen' ? 'Dosen baru bergabung' : 'Mahasiswa baru bergabung';
                return [
                    'type' => 'new_user',
                    'icon_color' => $color,
                    'initials' => strtoupper(substr($user->name ?? 'U', 0, 2)),
                    'title' => $label,
                    'description' => $user->name . ' - ' . $user->email,
                    'time' => $user->created_at,
                ];
            });
        $recentActivities = $recentActivities->merge($recentUsers);

        // Recent courses created
        $recentCourses = \App\Models\Course::with('dosen')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function ($course) {
                return [
                    'type' => 'new_course',
                    'icon_color' => 'bg-purple-500',
                    'initials' => strtoupper(substr($course->nama_course ?? 'K', 0, 2)),
                    'title' => 'Kursus baru dibuat',
                    'description' => $course->nama_course . ($course->dosen ? ' - ' . $course->dosen->name : ''),
                    'time' => $course->created_at,
                ];
            });
        $recentActivities = $recentActivities->merge($recentCourses);

        // Sort all activities by time descending, take latest 6
        $recentActivities = $recentActivities->sortByDesc('time')->take(6)->values();

        // Recent announcements/news
        $recentNews = \App\Models\News::where('is_active', true)
            ->orderByDesc('tanggal_publish')
            ->limit(3)
            ->get();

        // Unread notification count for badge
        $unreadNotifCount = AdminNotification::where('admin_id', $admin->id)->unread()->count();

        return view('Auth.admin.dashboard', [
            'admin' => $admin,
            'totalDosen' => $totalDosen,
            'totalMahasiswa' => $totalMahasiswa,
            'kursusAktif' => $kursusAktif,
            'totalKursus' => $totalKursus,
            'pendaftaranBulanIni' => $pendaftaranBulanIni,
            'chartData' => $chartData,
            'recentActivities' => $recentActivities,
            'recentNews' => $recentNews,
            'unreadNotifCount' => $unreadNotifCount,
        ]);
    }

    /**
     * Show Kelola Dosen page
     */
    public function showDosen(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $perPage = 5;

        // Query dosen from users table with role 'dosen'
        $query = User::where('role', 'dosen')



            ->with('profile.jurusan');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function($pq) use ($search) {
                      $pq->where('nim', 'like', "%{$search}%"); // NIP stored in nim field for now
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Program Studi (Jurusan) filter
        if ($request->has('jurusan') && $request->jurusan !== 'all') {
            $jurusanId = $request->jurusan;
            $query->whereHas('profile', function($pq) use ($jurusanId) {
                // Adjusting filter depending on whether the backend implements it as JSON or directly if still currently a scalar ID
                $pq->where('id_jurusan', $jurusanId)
                   ->orWhereJsonContains('id_jurusan', $jurusanId)
                   ->orWhereJsonContains('id_jurusan', (string)$jurusanId);
            });
        }

        $dosenPaginated = $query->paginate($perPage);

        // Transform data for view
        $dosenList = $dosenPaginated->map(function($dosen) {
            return [
                'id' => $dosen->id,
                'foto' => $dosen->profile?->foto_profile,
                'nama' => $dosen->name,
                'nip' => $dosen->profile?->nim ?? '-',
                'program_studi' => $dosen->profile?->jurusan?->nama_jurusan ?? '-',
                'email' => $dosen->email,
                'no_telepon' => $dosen->profile?->no_hp ?? '-',
                'status' => match($dosen->status) {
                    'aktif'   => 'Aktif',
                    'pending' => 'Pending',
                    default   => 'Nonaktif',
                },
            ];
        });

        // Get jurusan list for dropdown
        $jurusanList = \App\Models\Jurusan::all();

        // Stats counts (all dosen, not just current page)
        $dosenAktifCount = User::where('role', 'dosen')->where('status', 'aktif')->count();
        $dosenNonaktifCount = User::where('role', 'dosen')->where('status', 'nonaktif')->count();
        $dosenPendingCount = User::where('role', 'dosen')->where('status', 'pending')->count();


        return view('Auth.admin.dosen', [
            'admin' => $admin,
            'dosenList' => $dosenList,
            'dosenPaginated' => $dosenPaginated,
            'totalDosen' => $dosenPaginated->total(),
            'dosenAktifCount' => $dosenAktifCount,
            'dosenNonaktifCount' => $dosenNonaktifCount,
            'dosenPendingCount' => $dosenPendingCount,
            'currentPage' => $dosenPaginated->currentPage(),
            'perPage' => $perPage,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
            'jurusanFilter' => $request->jurusan ?? 'all',
            'jurusanList' => $jurusanList,
        ]);
    }

    /**
     * Store new Dosen
     */
    public function storeDosen(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|string|max:50|unique:profiles,nim',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890 atau +62 812-3456-7890).',
        ]);

        // Create user (P0 FIX: Generate secure random password instead of hardcoded)
        $defaultPassword = \Illuminate\Support\Str::random(12);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($defaultPassword),
            'role' => 'dosen',
            'status' => $request->status,
        ]);

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('dosen-photos', 'public');
        }

        // Create profile
        $user->profile()->create([
            'nim' => $request->nip,
            'id_jurusan' => $request->id_jurusan,
            'no_hp' => $request->no_hp,
            'foto_profile' => $fotoPath,
        ]);

        return redirect()->route('admin.dosen')
            ->with('success', "Dosen berhasil ditambahkan! Password default: {$defaultPassword} (catat sekarang, tidak ditampilkan lagi)");
    }

    /**
     * Get Dosen data for edit
     */
    public function getDosen($id)
    {
        $dosen = User::with('profile')->find($id);
        
        if (!$dosen || $dosen->role !== 'dosen') {
            return response()->json(['error' => 'Dosen tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $dosen->id,
            'name' => $dosen->name,
            'email' => $dosen->email,
            'status' => $dosen->status,
            'nip' => $dosen->profile?->nim,
            'id_jurusan' => $dosen->profile?->id_jurusan,
            'no_hp' => $dosen->profile?->no_hp,
            'foto' => $dosen->profile?->foto_profile,
        ]);
    }

    /**
     * Update Dosen
     */
    public function updateDosen(Request $request, $id)
    {
        $dosen = User::find($id);
        
        if (!$dosen || $dosen->role !== 'dosen') {
            return redirect()->route('admin.dosen')
                ->with('error', 'Dosen tidak ditemukan');
        }

        $profileId = $dosen->profile?->id;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'required|string|max:50|unique:profiles,nim,' . ($profileId ?? 'NULL') . ',id',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890 atau +62 812-3456-7890).',
        ]);

        // Update user
        $dosen->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        // Handle photo upload
        $fotoPath = $dosen->profile?->foto_profile;
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($fotoPath && \Storage::disk('public')->exists($fotoPath)) {
                \Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('dosen-photos', 'public');
        }

        // Update or create profile
        $dosen->profile()->updateOrCreate(
            ['user_id' => $dosen->id],
            [
                'nim' => $request->nip,
                'id_jurusan' => $request->id_jurusan,
                'no_hp' => $request->no_hp,
                'foto_profile' => $fotoPath,
            ]
        );

        return redirect()->route('admin.dosen')
            ->with('success', 'Data dosen berhasil diperbarui!');
    }

    /**
     * Delete Dosen
     */
    public function deleteDosen($id)
    {
        $dosen = User::find($id);
        
        if (!$dosen || $dosen->role !== 'dosen') {
            return redirect()->route('admin.dosen')
                ->with('error', 'Dosen tidak ditemukan');
        }

        // Delete photo if exists
        if ($dosen->profile?->foto_profile && \Storage::disk('public')->exists($dosen->profile->foto_profile)) {
            \Storage::disk('public')->delete($dosen->profile->foto_profile);
        }

        // Delete profile first
        $dosen->profile()?->delete();
        
        // Delete user
        $dosen->delete();

        return redirect()->route('admin.dosen')
            ->with('success', 'Dosen berhasil dihapus!');
    }

    // ==================== MAHASISWA MANAGEMENT ====================

    /**
     * Show Mahasiswa page
     */
    public function showMahasiswa(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $perPage = 5;

        // Query users with role mahasiswa
        $query = User::where('role', 'mahasiswa')
            ->with(['profile.jurusan']);

        // Search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function($pq) use ($search) {
                      $pq->where('nim', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Prodi filter
        if ($request->filled('prodi')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('id_jurusan', $request->prodi);
            });
        }

        $mahasiswaPaginated = $query->paginate($perPage);
        $mahasiswaPaginated->appends($request->only(['search', 'status', 'prodi']));
        $mahasiswaList = $mahasiswaPaginated->map(function($mhs) {
            return [
                'id' => $mhs->id,
                'foto' => $mhs->profile?->foto_profile,
                'nama' => $mhs->name,
                'nim' => $mhs->profile?->nim ?? '-',
                'program_studi' => $mhs->profile?->jurusan?->nama_jurusan ?? '-',
                'email' => $mhs->email,
                'no_telepon' => $mhs->profile?->no_hp ?? '-',
                'status' => $mhs->status === 'aktif' ? 'Aktif' : 'Nonaktif',
            ];
        });

        // Get jurusan list for dropdown
        $jurusanList = \App\Models\Jurusan::all();
        
        // Get active courses for enrollment dropdown
        $courseList = \App\Models\Course::where('status', 'aktif')->get();

        // Stats
        $totalAll = User::where('role', 'mahasiswa')->count();
        $totalAktif = User::where('role', 'mahasiswa')->where('status', 'aktif')->count();
        $totalNonaktif = User::where('role', 'mahasiswa')->where('status', 'nonaktif')->count();
        $totalBaru = User::where('role', 'mahasiswa')->where('created_at', '>=', now()->subDays(30))->count();

        return view('Auth.admin.mahasiswa', [
            'admin' => $admin,
            'mahasiswaList' => $mahasiswaList,
            'mahasiswaPaginated' => $mahasiswaPaginated,
            'totalMahasiswa' => $mahasiswaPaginated->total(),
            'currentPage' => $mahasiswaPaginated->currentPage(),
            'perPage' => $perPage,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
            'prodiFilter' => $request->prodi ?? '',
            'jurusanList' => $jurusanList,
            'courseList' => $courseList,
            'totalAll' => $totalAll,
            'totalAktif' => $totalAktif,
            'totalNonaktif' => $totalNonaktif,
            'totalBaru' => $totalBaru,
        ]);
    }

    /**
     * Store new Mahasiswa
     */
    public function storeMahasiswa(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|max:50|unique:profiles,nim',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nim.unique' => 'NIM sudah terdaftar di sistem.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890 atau +62 812-3456-7890).',
        ]);

        // Create user (P0 FIX: Generate secure random password)
        $defaultPassword = \Illuminate\Support\Str::random(12);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($defaultPassword),
            'role' => 'mahasiswa',
            'status' => $request->status,
        ]);

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('mahasiswa-photos', 'public');
        }

        // Create profile
        $user->profile()->create([
            'nim' => $request->nim,
            'id_jurusan' => $request->id_jurusan,
            'no_hp' => $request->no_hp,
            'foto_profile' => $fotoPath,
        ]);

        // Enroll in selected courses
        if ($request->has('courses') && is_array($request->courses)) {
            foreach ($request->courses as $courseId) {
                \App\Models\Enrollment::create([
                    'id_mahasiswa' => $user->id,
                    'id_course' => $courseId,
                    'tanggal_daftar' => now(),
                    'progress' => 0,
                    'status' => 'aktif',
                ]);
            }
        }

        return redirect()->route('admin.mahasiswa')
            ->with('success', "Mahasiswa berhasil ditambahkan! Password default: {$defaultPassword} (catat sekarang, tidak ditampilkan lagi)");
    }

    /**
     * Get Mahasiswa data for edit
     */
    public function getMahasiswa($id)
    {
        $mhs = User::with('profile')->find($id);
        
        if (!$mhs || $mhs->role !== 'mahasiswa') {
            return response()->json(['error' => 'Mahasiswa tidak ditemukan'], 404);
        }

        // Get enrolled courses with progress
        $enrolledCourses = [];
        $enrollments = \App\Models\Enrollment::where('id_mahasiswa', $mhs->id)
            ->with('course')
            ->get();
        
        foreach ($enrollments as $enrollment) {
            if ($enrollment->course) {
                $enrolledCourses[] = [
                    'id' => $enrollment->course->id_course,
                    'name' => $enrollment->course->nama_course,
                    'progress' => $enrollment->progress ?? 0,
                ];
            }
        }

        return response()->json([
            'id' => $mhs->id,
            'name' => $mhs->name,
            'email' => $mhs->email,
            'status' => $mhs->status,
            'nim' => $mhs->profile?->nim,
            'id_jurusan' => $mhs->profile?->id_jurusan,
            'no_hp' => $mhs->profile?->no_hp,
            'foto' => $mhs->profile?->foto_profile,
            'enrolled_courses' => $enrolledCourses,
        ]);
    }

    /**
     * Update Mahasiswa
     */
    public function updateMahasiswa(Request $request, $id)
    {
        $mhs = User::find($id);
        
        if (!$mhs || $mhs->role !== 'mahasiswa') {
            return redirect()->route('admin.mahasiswa')
                ->with('error', 'Mahasiswa tidak ditemukan');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nim' => 'required|string|max:50|unique:profiles,nim,' . $id . ',user_id',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nim.unique' => 'NIM sudah terdaftar di sistem.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890 atau +62 812-3456-7890).',
        ]);

        // Update user
        $mhs->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        // Handle photo upload
        $fotoPath = $mhs->profile?->foto_profile;
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($fotoPath && \Storage::disk('public')->exists($fotoPath)) {
                \Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('mahasiswa-photos', 'public');
        }

        // Update or create profile
        $mhs->profile()->updateOrCreate(
            ['user_id' => $mhs->id],
            [
                'nim' => $request->nim,
                'id_jurusan' => $request->id_jurusan,
                'no_hp' => $request->no_hp,
                'foto_profile' => $fotoPath,
            ]
        );

        return redirect()->route('admin.mahasiswa')
            ->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    /**
     * Delete Mahasiswa
     */
    public function deleteMahasiswa($id)
    {
        $mhs = User::find($id);
        
        if (!$mhs || $mhs->role !== 'mahasiswa') {
            return redirect()->route('admin.mahasiswa')
                ->with('error', 'Mahasiswa tidak ditemukan');
        }

        // Delete photo if exists
        if ($mhs->profile?->foto_profile && \Storage::disk('public')->exists($mhs->profile->foto_profile)) {
            \Storage::disk('public')->delete($mhs->profile->foto_profile);
        }

        // Delete profile first
        $mhs->profile()?->delete();
        
        // Delete user
        $mhs->delete();

        return redirect()->route('admin.mahasiswa')
            ->with('success', 'Mahasiswa berhasil dihapus!');
    }

    // ==========================================
    // KURSUS MANAGEMENT
    // ==========================================

    /**
     * Show Kursus Management Page
     */
    public function showKursus(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $query = \App\Models\Course::with(['dosen', 'jurusan', 'modules' => function ($q) {
            $q->orderBy('urutan');
        }, 'modules.materials' => function ($q) {
            $q->orderBy('urutan');
        }])->withCount('enrollments');
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by tipe (pricing)
        if ($request->tipe && $request->tipe !== 'all') {
            $query->where('tipe', $request->tipe);
        }
        
        // Filter by kategori (format)
        if ($request->kategori && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_course', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_course', 'like', '%' . $request->search . '%');
            });
        }
        
        $perPage = 10;
        $kursusPaginated = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Transform data for view
        $kursusList = $kursusPaginated->map(function($kursus) {
        // Extract first module video thumbnail
        $videoThumb = null;
        $moduleCount = $kursus->modules->count();
        foreach ($kursus->modules as $module) {
            $firstVideo = $module->materials->where('tipe', 'video')->whereNotNull('video_url')->first();
            if ($firstVideo && $firstVideo->video_url) {
                $videoId = null;
                if (str_contains($firstVideo->video_url, 'youtube.com/watch?v=')) {
                    parse_str(parse_url($firstVideo->video_url, PHP_URL_QUERY), $params);
                    $videoId = $params['v'] ?? null;
                } elseif (str_contains($firstVideo->video_url, 'youtu.be/')) {
                    $videoId = basename(parse_url($firstVideo->video_url, PHP_URL_PATH));
                }
                if ($videoId) {
                    $videoThumb = "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg";
                    break;
                }
            }
        }

        return [
            'id' => $kursus->id_course,
            'kode' => $kursus->kode_course,
            'nama' => $kursus->nama_course,
            'thumbnail' => $kursus->thumbnail,
            'video_thumbnail' => $videoThumb,
            'module_count' => $moduleCount,
                'tipe' => $kursus->tipe,
                'kategori' => $kursus->kategori,
                'harga' => $kursus->harga,
                'diskon' => $kursus->diskon,
                'status' => $kursus->status,
                'rating' => $kursus->rating,
                'jumlah_ulasan' => $kursus->jumlah_ulasan,
                'enrollments_count' => $kursus->enrollments_count ?? 0,
                'has_youtube' => !empty($kursus->youtube_playlist),
            ];
        });
        
        // Get dosen list for dropdown
        $dosenList = User::where('role', 'dosen')->where('status', 'aktif')->get();
        $jurusanList = \App\Models\Jurusan::all();
        
        return view('Auth.admin.kursus', [
            'admin' => $admin,
            'kursusList' => $kursusList,
            'kursusPaginated' => $kursusPaginated,
            'totalKursus' => $kursusPaginated->total(),
            'currentPage' => $kursusPaginated->currentPage(),
            'perPage' => $perPage,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
            'tipeFilter' => $request->tipe ?? 'all',
            'kategoriFilter' => $request->kategori ?? 'all',
            'dosenList' => $dosenList,
            'jurusanList' => $jurusanList,
        ]);
    }

    /**
     * Store new Kursus
     */
    public function storeKursus(Request $request)
    {
        $request->validate([
            'nama_course' => 'required|string|max:255',
            'kode_course' => 'required|string|max:50|unique:courses,kode_course',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'id_dosen' => 'nullable|exists:users,id',
            'id_jurusan' => 'nullable|exists:jurusans,id_jurusan',
            'tipe' => 'required|in:gratis,berbayar',
            'kategori' => 'required|in:webinar,tiket,kursus',
            'harga' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:aktif,draft,nonaktif',
            'level' => 'nullable|in:Pemula,Menengah,Mahir',
            'estimasi_waktu' => 'nullable|numeric|min:0',
            'durasi_satuan' => 'nullable|in:Jam,Minggu',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'youtube_playlist' => 'nullable|url|max:500',
        ]);

        // Handle status from button or toggle
        $status = $request->status;
        if ($request->has('add_status_btn')) {
            $status = $request->add_status_btn;
        }

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        \App\Models\Course::create([
            'kode_course' => $request->kode_course,
            'nama_course' => $request->nama_course,
            'deskripsi' => $request->deskripsi,
            'persyaratan' => $request->persyaratan,
            'id_dosen' => $request->id_dosen,
            'id_jurusan' => $request->id_jurusan,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'harga' => $request->tipe === 'berbayar' ? ($request->harga ?? 0) : 0,
            'diskon' => $request->tipe === 'berbayar' ? ($request->diskon ?? 0) : 0,
            'status' => $status,
            'level' => $request->level,
            'estimasi_waktu' => $request->estimasi_waktu,
            'durasi_satuan' => $request->durasi_satuan ?? 'Jam',
            'thumbnail' => $thumbnailPath,
            'youtube_playlist' => $request->youtube_playlist,
            'rating' => 0,
            'jumlah_ulasan' => 0,
        ]);

        return redirect()->route('admin.kursus')
            ->with('success', 'Kursus berhasil ditambahkan!');
    }

    /**
     * Get Kursus data for edit
     */
    public function getKursus($id)
    {
        $kursus = \App\Models\Course::with(['dosen', 'jurusan'])->find($id);
        
        if (!$kursus) {
            return response()->json(['error' => 'Kursus tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $kursus->id_course,
            'kode_course' => $kursus->kode_course,
            'nama_course' => $kursus->nama_course,
            'deskripsi' => $kursus->deskripsi,
            'persyaratan' => $kursus->persyaratan,
            'id_dosen' => $kursus->id_dosen,
            'id_jurusan' => $kursus->id_jurusan,
            'tipe' => $kursus->tipe,
            'kategori' => $kursus->kategori,
            'harga' => $kursus->harga,
            'diskon' => $kursus->diskon ?? 0,
            'status' => $kursus->status,
            'level' => $kursus->level,
            'estimasi_waktu' => $kursus->estimasi_waktu,
            'durasi_satuan' => $kursus->durasi_satuan,
            'thumbnail' => $kursus->thumbnail,
            'youtube_playlist' => $kursus->youtube_playlist,
            'sertifikat' => (bool) $kursus->sertifikat,
            'akses_publik' => (bool) $kursus->akses_publik,
        ]);
    }

    /**
     * Update Kursus
     */
    public function updateKursus(Request $request, $id)
    {
        $kursus = \App\Models\Course::find($id);
        
        if (!$kursus) {
            return redirect()->route('admin.kursus')
                ->with('error', 'Kursus tidak ditemukan');
        }

        $request->validate([
            'nama_course' => 'required|string|max:255',
            'kode_course' => 'required|string|max:50|unique:courses,kode_course,' . $id . ',id_course',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'id_dosen' => 'nullable|exists:users,id',
            'id_jurusan' => 'nullable|exists:jurusans,id_jurusan',
            'tipe' => 'required|in:gratis,berbayar',
            'kategori' => 'required|in:webinar,tiket,kursus',
            'harga' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:aktif,draft,nonaktif',
            'level' => 'nullable|in:Pemula,Menengah,Mahir',
            'estimasi_waktu' => 'nullable|numeric|min:0',
            'durasi_satuan' => 'nullable|in:Jam,Minggu',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'youtube_playlist' => 'nullable|url|max:500',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($kursus->thumbnail) {
                Storage::disk('public')->delete($kursus->thumbnail);
            }
            $kursus->thumbnail = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $kursus->update([
            'kode_course' => $request->kode_course,
            'nama_course' => $request->nama_course,
            'deskripsi' => $request->deskripsi,
            'persyaratan' => $request->persyaratan,
            'id_dosen' => $request->id_dosen,
            'id_jurusan' => $request->id_jurusan,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'harga' => $request->tipe === 'berbayar' ? ($request->harga ?? 0) : 0,
            'diskon' => $request->tipe === 'berbayar' ? ($request->diskon ?? 0) : 0,
            'status' => $request->status,
            'level' => $request->level,
            'estimasi_waktu' => $request->estimasi_waktu,
            'durasi_satuan' => $request->durasi_satuan ?? 'Jam',
            'youtube_playlist' => $request->youtube_playlist,
        ]);

        return redirect()->route('admin.kursus')
            ->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Delete Kursus
     */
    public function deleteKursus($id)
    {
        $kursus = \App\Models\Course::find($id);
        
        if (!$kursus) {
            return redirect()->route('admin.kursus')
                ->with('error', 'Kursus tidak ditemukan');
        }

        // Delete thumbnail
        if ($kursus->thumbnail) {
            Storage::disk('public')->delete($kursus->thumbnail);
        }

        $kursus->delete();

        return redirect()->route('admin.kursus')
            ->with('success', 'Kursus berhasil dihapus!');
    }

    /**
     * Show Kelola Modul page
     */
    public function showKelolaModul($id)
    {
        $course = \App\Models\Course::where('id_course', $id)
            ->with([
                'enrollments',
                'modules' => function($q) {
                    $q->orderBy('urutan', 'asc');
                },
                'modules.materials' => function($q) {
                    $q->orderBy('urutan', 'asc');
                },
                'modules.quizzes' => function($q) {
                    $q->orderBy('urutan', 'asc');
                },
                'modules.quizzes.questions',
            ])
            ->first();

        if (!$course) {
            return redirect()->route('admin.kursus')->with('error', 'Kursus tidak ditemukan');
        }

        $modules = $course->modules->map(function($module) {
            $videoCount = $module->materials->where('tipe', 'video')->count();
            $bacaanCount = $module->materials->where('tipe', 'bacaan')->count();
            $pretest = $module->quizzes->where('is_pretest', true)->first();
            $quizzes = $module->quizzes->where('is_pretest', false)->values();

            return [
                'id' => $module->id_module,
                'judul' => $module->judul_module,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'video_count' => $videoCount,
                'bacaan_count' => $bacaanCount,
                'has_pretest' => $pretest !== null,
                'pretest' => $pretest ? [
                    'id' => $pretest->id_quiz,
                    'judul' => $pretest->judul,
                    'jumlah_soal' => $pretest->questions->count(),
                    'durasi' => $pretest->durasi_menit,
                    'total_bobot' => $pretest->questions->sum('bobot'),
                ] : null,
                'quizzes' => $quizzes->map(function($quiz) {
                    return [
                        'id' => $quiz->id_quiz,
                        'judul' => $quiz->judul,
                        'jumlah_soal' => $quiz->questions->count(),
                        'durasi' => $quiz->durasi_menit,
                        'total_bobot' => $quiz->questions->sum('bobot'),
                    ];
                })->values()->all(),
                'materials' => $module->materials->map(function($m) {
                    return [
                        'id' => $m->id_material,
                        'judul' => $m->judul_material,
                        'tipe' => $m->tipe,
                        'durasi' => $m->durasi,
                        'video_url' => $m->video_url,
                    ];
                })->values()->all(),
            ];
        });

        return view('Auth.admin.kelola-modul', [
            'course' => [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'kode' => $course->kode_course,
                'status' => $course->status,
                'mahasiswa_count' => $course->enrollments->count(),
            ],
            'modules' => $modules,
        ]);
    }

    /**
     * Get Modul/Material detail
     */
    public function getMaterialDetail($courseId, $materialId)
    {
        $material = \App\Models\CourseMaterial::where('id_material', $materialId)
            ->where('id_course', $courseId)
            ->first();

        if (!$material) {
            return response()->json(['error' => 'Material tidak ditemukan'], 404);
        }

        return response()->json([
            'id_material' => $material->id_material,
            'judul_material' => $material->judul_material,
            'tipe' => $material->tipe,
            'konten' => $material->konten,
            'video_url' => $material->video_url,
            'urutan' => $material->urutan,
            'durasi' => $material->durasi,
        ]);
    }

    /**
     * Store new module
     */
    public function storeModule(Request $request, $id)
    {
        $course = \App\Models\Course::where('id_course', $id)->first();

        if (!$course) {
            return back()->with('error', 'Kursus tidak ditemukan');
        }

        $request->validate([
            'judul_module' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $lastOrder = \App\Models\CourseModule::where('id_course', $id)->max('urutan') ?? 0;

        \App\Models\CourseModule::create([
            'id_course' => $id,
            'judul_module' => $request->judul_module,
            'deskripsi' => $request->deskripsi,
            'urutan' => $lastOrder + 1,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan');
    }

    /**
     * Update module
     */
    public function updateModule(Request $request, $courseId, $moduleId)
    {
        $module = \App\Models\CourseModule::where('id_module', $moduleId)
            ->where('id_course', $courseId)
            ->first();

        if (!$module) {
            return back()->with('error', 'Modul tidak ditemukan');
        }

        $request->validate([
            'judul_module' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $module->update([
            'judul_module' => $request->judul_module,
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Modul berhasil diperbarui');
    }

    /**
     * Delete module
     */
    public function deleteModule($courseId, $moduleId)
    {
        $module = \App\Models\CourseModule::where('id_module', $moduleId)
            ->where('id_course', $courseId)
            ->first();

        if (!$module) {
            return back()->with('error', 'Modul tidak ditemukan');
        }

        $module->delete();

        return back()->with('success', 'Modul berhasil dihapus');
    }

     /**
     * Reorder modules
     */
    public function reorderModules(Request $request, $courseId)
    {
        $course = \App\Models\Course::where('id_course', $courseId)->first();

        if (!$course) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $order = $request->input('order');
        
        if (!is_array($order)) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        foreach ($order as $index => $moduleId) {
            \App\Models\CourseModule::where('id_module', $moduleId)
                ->where('id_course', $courseId)
                ->update(['urutan' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Store new material
     */
    public function storeMaterial(Request $request, $id)
    {
        $course = \App\Models\Course::where('id_course', $id)->first();

        if (!$course) {
            return back()->with('error', 'Kursus tidak ditemukan');
        }

        $request->validate([
            'judul_material' => 'required|string|max:255',
            'tipe' => 'required|in:video,bacaan,kuis,tugas',
            'konten' => 'nullable|string',
            'video_url' => 'nullable|url',
            'durasi' => 'nullable|integer|min:0',
            'id_module' => 'nullable|exists:course_modules,id_module',
        ]);

        // Auto-create default module if none provided (for flat material pages)
        $moduleId = $request->id_module;
        if (!$moduleId) {
            $defaultModule = \App\Models\CourseModule::where('id_course', $id)->first();
            if (!$defaultModule) {
                $defaultModule = \App\Models\CourseModule::create([
                    'id_course' => $id,
                    'judul_module' => 'Modul Utama',
                    'deskripsi' => 'Modul default untuk materi kursus',
                    'urutan' => 1,
                ]);
            }
            $moduleId = $defaultModule->id_module;
        }

        $lastOrder = \App\Models\CourseMaterial::where('id_module', $moduleId)->max('urutan') ?? 0;

        \App\Models\CourseMaterial::create([
            'id_course' => $id,
            'id_module' => $moduleId,
            'judul_material' => $request->judul_material,
            'tipe' => $request->tipe,
            'konten' => $request->konten,
            'video_url' => $request->video_url,
            'urutan' => $lastOrder + 1,
            'durasi' => $request->durasi ?? 0,
        ]);

        return back()->with('success', 'Material berhasil ditambahkan');
    }

    /**
     * Update material
     */
    public function updateMaterial(Request $request, $courseId, $materialId)
    {
        $material = \App\Models\CourseMaterial::where('id_material', $materialId)
            ->where('id_course', $courseId)
            ->first();

        if (!$material) {
            return back()->with('error', 'Material tidak ditemukan');
        }

        $request->validate([
            'judul_material' => 'required|string|max:255',
            'tipe' => 'required|in:video,bacaan,kuis,tugas',
            'konten' => 'nullable|string',
            'video_url' => 'nullable|url',
            'durasi' => 'nullable|integer|min:0',
        ]);

        $material->update([
            'judul_material' => $request->judul_material,
            'tipe' => $request->tipe,
            'konten' => $request->konten,
            'video_url' => $request->video_url,
            'durasi' => $request->durasi ?? $material->durasi,
        ]);

        return back()->with('success', 'Material berhasil diperbarui');
    }

    /**
     * Reorder materials
     */
    public function reorderMaterials(Request $request, $courseId)
    {
        $course = \App\Models\Course::where('id_course', $courseId)->first();

        if (!$course) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $order = $request->input('order');
        
        if (!is_array($order)) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        foreach ($order as $index => $materialId) {
            \App\Models\CourseMaterial::where('id_material', $materialId)
                ->where('id_course', $courseId)
                ->update(['urutan' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete material
     */
    public function deleteMaterial($courseId, $materialId)
    {
        $material = \App\Models\CourseMaterial::where('id_material', $materialId)
            ->where('id_course', $courseId)
            ->first();

        if (!$material) {
            return back()->with('error', 'Material tidak ditemukan');
        }

        $material->delete();

        return back()->with('success', 'Material berhasil dihapus');
    }

    // ========================================
    // Quiz Management
    // ========================================

    /**
     * Show quiz management page for a module
     */
    public function showKelolaQuiz($courseId, $moduleId)
    {
        $course = \App\Models\Course::where('id_course', $courseId)->first();
        $module = \App\Models\CourseModule::where('id_module', $moduleId)
            ->where('id_course', $courseId)
            ->first();

        if (!$course || !$module) {
            return redirect()->route('admin.kursus')->with('error', 'Data tidak ditemukan');
        }

        $quizzes = \App\Models\Quiz::where('id_module', $moduleId)
            ->with('questions')
            ->orderBy('urutan')
            ->get()
            ->map(function($quiz) {
                return [
                    'id' => $quiz->id_quiz,
                    'judul' => $quiz->judul,
                    'deskripsi' => $quiz->deskripsi,
                    'durasi_menit' => $quiz->durasi_menit,
                    'is_pretest' => $quiz->is_pretest,
                    'is_active' => $quiz->is_active,
                    'passing_score' => $quiz->passing_score,
                    'acak_soal' => $quiz->acak_soal,
                    'tampilkan_nilai' => $quiz->tampilkan_nilai,
                    'total_bobot' => $quiz->questions->sum('bobot'),
                    'jumlah_soal' => $quiz->questions->count(),
                    'questions' => $quiz->questions->map(function($q) {
                        return [
                            'id' => $q->id_question,
                            'pertanyaan' => $q->pertanyaan,
                            'tipe' => $q->tipe,
                            'opsi' => $q->opsi,
                            'jawaban_benar' => $q->jawaban_benar,
                            'bobot' => $q->bobot,
                            'penjelasan' => $q->penjelasan,
                            'urutan' => $q->urutan,
                        ];
                    })->values()->all(),
                ];
            });

        return view('Auth.admin.kelola-quiz', [
            'course' => [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
            ],
            'module' => [
                'id' => $module->id_module,
                'judul' => $module->judul_module,
            ],
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Store a new quiz for a module
     */
    public function storeQuiz(Request $request, $courseId, $moduleId)
    {
        $module = \App\Models\CourseModule::where('id_module', $moduleId)
            ->where('id_course', $courseId)
            ->first();

        if (!$module) {
            return response()->json(['error' => 'Modul tidak ditemukan'], 404);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi_menit' => 'nullable|integer|min:1|max:300',
            'is_pretest' => 'nullable|boolean',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'acak_soal' => 'nullable|boolean',
            'tampilkan_nilai' => 'nullable|boolean',
        ]);

        // Jika is_pretest, pastikan belum ada pretest lain di modul ini
        if ($request->boolean('is_pretest')) {
            $existingPretest = \App\Models\Quiz::where('id_module', $moduleId)
                ->where('is_pretest', true)->first();
            if ($existingPretest) {
                return response()->json(['error' => 'Modul ini sudah memiliki pretest. Hapus yang lama terlebih dahulu.'], 422);
            }
        }

        $lastOrder = \App\Models\Quiz::where('id_module', $moduleId)->max('urutan') ?? 0;

        $quiz = \App\Models\Quiz::create([
            'id_module' => $moduleId,
            'id_course' => $courseId,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'durasi_menit' => $request->durasi_menit ?? 15,
            'is_pretest' => $request->boolean('is_pretest'),
            'passing_score' => $request->passing_score ?? 60,
            'acak_soal' => $request->boolean('acak_soal'),
            'tampilkan_nilai' => $request->boolean('tampilkan_nilai', true),
            'urutan' => $lastOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz berhasil dibuat.',
            'data' => [
                'id' => $quiz->id_quiz,
                'judul' => $quiz->judul,
            ],
        ], 201);
    }

    /**
     * Get quiz detail with questions
     */
    public function getQuiz($courseId, $quizId)
    {
        $quiz = \App\Models\Quiz::where('id_quiz', $quizId)
            ->where('id_course', $courseId)
            ->with('questions')
            ->first();

        if (!$quiz) {
            return response()->json(['error' => 'Quiz tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $quiz->id_quiz,
            'judul' => $quiz->judul,
            'deskripsi' => $quiz->deskripsi,
            'durasi_menit' => $quiz->durasi_menit,
            'is_pretest' => $quiz->is_pretest,
            'is_active' => $quiz->is_active,
            'passing_score' => $quiz->passing_score,
            'acak_soal' => $quiz->acak_soal,
            'tampilkan_nilai' => $quiz->tampilkan_nilai,
            'total_bobot' => $quiz->questions->sum('bobot'),
            'questions' => $quiz->questions->map(function($q) {
                return [
                    'id' => $q->id_question,
                    'pertanyaan' => $q->pertanyaan,
                    'tipe' => $q->tipe,
                    'opsi' => $q->opsi,
                    'jawaban_benar' => $q->jawaban_benar,
                    'bobot' => $q->bobot,
                    'penjelasan' => $q->penjelasan,
                    'urutan' => $q->urutan,
                ];
            })->values()->all(),
        ]);
    }

    /**
     * Update quiz settings
     */
    public function updateQuiz(Request $request, $courseId, $quizId)
    {
        $quiz = \App\Models\Quiz::where('id_quiz', $quizId)
            ->where('id_course', $courseId)
            ->first();

        if (!$quiz) {
            return response()->json(['error' => 'Quiz tidak ditemukan'], 404);
        }

        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi_menit' => 'nullable|integer|min:1|max:300',
            'is_pretest' => 'nullable|boolean',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'acak_soal' => 'nullable|boolean',
            'tampilkan_nilai' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $quiz->update($request->only([
            'judul', 'deskripsi', 'durasi_menit', 'is_pretest',
            'passing_score', 'acak_soal', 'tampilkan_nilai', 'is_active',
        ]));

        return response()->json(['success' => true, 'message' => 'Quiz berhasil diperbarui.']);
    }

    /**
     * Delete quiz
     */
    public function deleteQuiz($courseId, $quizId)
    {
        $quiz = \App\Models\Quiz::where('id_quiz', $quizId)
            ->where('id_course', $courseId)
            ->first();

        if (!$quiz) {
            return response()->json(['error' => 'Quiz tidak ditemukan'], 404);
        }

        $quiz->delete();

        return response()->json(['success' => true, 'message' => 'Quiz berhasil dihapus.']);
    }

    // ========================================
    // Quiz Question Management
    // ========================================

    /**
     * Store a new question to a quiz
     */
    public function storeQuestion(Request $request, $courseId, $quizId)
    {
        $quiz = \App\Models\Quiz::where('id_quiz', $quizId)
            ->where('id_course', $courseId)
            ->first();

        if (!$quiz) {
            return response()->json(['error' => 'Quiz tidak ditemukan'], 404);
        }

        $request->validate([
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:pilihan_ganda,benar_salah',
            'opsi' => 'required_if:tipe,pilihan_ganda|array|min:2',
            'opsi.*' => 'required_if:tipe,pilihan_ganda|string|max:1000',
            'jawaban_benar' => 'required|string',
            'bobot' => 'nullable|integer|min:1',
            'penjelasan' => 'nullable|string',
        ]);

        $lastOrder = \App\Models\QuizQuestion::where('id_quiz', $quizId)->max('urutan') ?? 0;

        $opsi = $request->tipe === 'benar_salah' ? ['Benar', 'Salah'] : $request->opsi;

        $question = \App\Models\QuizQuestion::create([
            'id_quiz' => $quizId,
            'pertanyaan' => $request->pertanyaan,
            'tipe' => $request->tipe,
            'opsi' => $opsi,
            'jawaban_benar' => $request->jawaban_benar,
            'bobot' => $request->bobot ?? 10,
            'penjelasan' => $request->penjelasan,
            'urutan' => $lastOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil ditambahkan.',
            'data' => [
                'id' => $question->id_question,
                'pertanyaan' => $question->pertanyaan,
                'tipe' => $question->tipe,
                'opsi' => $question->opsi,
                'jawaban_benar' => $question->jawaban_benar,
                'bobot' => $question->bobot,
                'penjelasan' => $question->penjelasan,
                'urutan' => $question->urutan,
            ],
        ], 201);
    }

    /**
     * Update a question
     */
    public function updateQuestion(Request $request, $courseId, $quizId, $questionId)
    {
        $question = \App\Models\QuizQuestion::where('id_question', $questionId)
            ->whereHas('quiz', function($q) use ($courseId) {
                $q->where('id_course', $courseId);
            })
            ->where('id_quiz', $quizId)
            ->first();

        if (!$question) {
            return response()->json(['error' => 'Soal tidak ditemukan'], 404);
        }

        $request->validate([
            'pertanyaan' => 'sometimes|required|string',
            'tipe' => 'sometimes|required|in:pilihan_ganda,benar_salah',
            'opsi' => 'required_if:tipe,pilihan_ganda|array|min:2',
            'opsi.*' => 'required_if:tipe,pilihan_ganda|string|max:1000',
            'jawaban_benar' => 'sometimes|required|string',
            'bobot' => 'nullable|integer|min:1',
            'penjelasan' => 'nullable|string',
        ]);

        $data = $request->only(['pertanyaan', 'tipe', 'jawaban_benar', 'bobot', 'penjelasan']);

        if ($request->has('tipe') && $request->tipe === 'benar_salah') {
            $data['opsi'] = ['Benar', 'Salah'];
        } elseif ($request->has('opsi')) {
            $data['opsi'] = $request->opsi;
        }

        $question->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil diperbarui.',
            'data' => [
                'id' => $question->id_question,
                'pertanyaan' => $question->pertanyaan,
                'tipe' => $question->tipe,
                'opsi' => $question->opsi,
                'jawaban_benar' => $question->jawaban_benar,
                'bobot' => $question->bobot,
                'penjelasan' => $question->penjelasan,
                'urutan' => $question->urutan,
            ],
        ]);
    }

    /**
     * Delete a question
     */
    public function deleteQuestion($courseId, $quizId, $questionId)
    {
        $question = \App\Models\QuizQuestion::where('id_question', $questionId)
            ->whereHas('quiz', function($q) use ($courseId) {
                $q->where('id_course', $courseId);
            })
            ->where('id_quiz', $quizId)
            ->first();

        if (!$question) {
            return response()->json(['error' => 'Soal tidak ditemukan'], 404);
        }

        $question->delete();

        return response()->json(['success' => true, 'message' => 'Soal berhasil dihapus.']);
    }

    /**
     * Reorder questions in a quiz
     */
    public function reorderQuestions(Request $request, $courseId, $quizId)
    {
        $quiz = \App\Models\Quiz::where('id_quiz', $quizId)
            ->where('id_course', $courseId)
            ->first();

        if (!$quiz) {
            return response()->json(['error' => 'Quiz tidak ditemukan'], 404);
        }

        $order = $request->input('order');
        if (!is_array($order)) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        foreach ($order as $index => $questionId) {
            \App\Models\QuizQuestion::where('id_question', $questionId)
                ->where('id_quiz', $quizId)
                ->update(['urutan' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle pretest for a module (create or remove)
     */
    public function togglePretest(Request $request, $courseId, $moduleId)
    {
        $module = \App\Models\CourseModule::where('id_module', $moduleId)
            ->where('id_course', $courseId)
            ->first();

        if (!$module) {
            return response()->json(['error' => 'Modul tidak ditemukan'], 404);
        }

        $existingPretest = \App\Models\Quiz::where('id_module', $moduleId)
            ->where('is_pretest', true)
            ->first();

        if ($existingPretest) {
            // Hapus pretest
            $existingPretest->delete();
            return response()->json([
                'success' => true,
                'message' => 'Pretest berhasil dihapus.',
                'has_pretest' => false,
            ]);
        }

        // Buat pretest baru
        $quiz = \App\Models\Quiz::create([
            'id_module' => $moduleId,
            'id_course' => $courseId,
            'judul' => 'Pretest - ' . $module->judul_module,
            'deskripsi' => 'Pretest untuk modul ' . $module->judul_module,
            'durasi_menit' => 15,
            'is_pretest' => true,
            'passing_score' => 0,
            'urutan' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pretest berhasil dibuat. Silakan tambahkan soal.',
            'has_pretest' => true,
            'quiz_id' => $quiz->id_quiz,
        ]);
    }

    /**
     * Show admin profile page
     */
    public function showProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('Auth.admin.profile', ['admin' => $admin]);
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ], [
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'foto.image' => 'File harus berupa gambar.',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('admin-photos', 'public');
            // Update or create profile
            if ($admin->profile) {
                $admin->profile->update(['foto_profile' => $fotoPath]);
            } else {
                $admin->profile()->create(['foto_profile' => $fotoPath]);
            }
        }

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
            }
            $admin->password = Hash::make($request->new_password);
        }

        $admin->save();

        return redirect()->route('admin.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Import mahasiswa from CSV file
     */
    public function importMahasiswa(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return redirect()->route('admin.mahasiswa')
                ->with('error', 'Gagal membaca file CSV.');
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return redirect()->route('admin.mahasiswa')
                ->with('error', 'File CSV kosong atau format header tidak valid.');
        }

        // Normalize header
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $jurusanMap = \App\Models\Jurusan::pluck('id_jurusan', 'nama_jurusan')->toArray();
        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }

            $data = array_combine($header, array_pad($row, count($header), ''));
            $nama = $data['nama'] ?? '';
            $nim = $data['nim'] ?? '';
            $email = $data['email'] ?? '';
            $jurusanName = $data['jurusan'] ?? '';
            $noHp = $data['no_hp'] ?? '';

            if (empty($nama) || empty($nim) || empty($email)) {
                $skipped++;
                continue;
            }

            // Check duplicates
            if (User::where('email', $email)->exists()) {
                $errors[] = "Email {$email} sudah terdaftar.";
                $skipped++;
                continue;
            }

            // Match jurusan by name
            $idJurusan = null;
            foreach ($jurusanMap as $name => $id) {
                if (stripos($name, $jurusanName) !== false || stripos($jurusanName, $name) !== false) {
                    $idJurusan = $id;
                    break;
                }
            }

            try {
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make(\Illuminate\Support\Str::random(12)),
                    'role' => 'mahasiswa',
                    'status' => 'aktif',
                ]);

                $user->profile()->create([
                    'nim' => $nim,
                    'id_jurusan' => $idJurusan,
                    'no_hp' => $noHp ?: null,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Gagal import {$nama}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "{$imported} mahasiswa berhasil diimport.";
        if ($skipped > 0) $message .= " {$skipped} data dilewati.";
        if (!empty($errors)) $message .= " Errors: " . implode('; ', array_slice($errors, 0, 3));

        return redirect()->route('admin.mahasiswa')
            ->with($imported > 0 ? 'success' : 'error', $message);
    }

    /**
     * Import dosen from CSV file
     */
    public function importDosen(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return redirect()->route('admin.dosen')
                ->with('error', 'Gagal membaca file CSV.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return redirect()->route('admin.dosen')
                ->with('error', 'File CSV kosong atau format header tidak valid.');
        }

        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $jurusanMap = \App\Models\Jurusan::pluck('id_jurusan', 'nama_jurusan')->toArray();
        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }

            $data = array_combine($header, array_pad($row, count($header), ''));
            $nama = $data['nama'] ?? '';
            $nip = $data['nip'] ?? '';
            $email = $data['email'] ?? '';
            $jurusanName = $data['jurusan'] ?? '';
            $noHp = $data['no_hp'] ?? '';

            if (empty($nama) || empty($nip) || empty($email)) {
                $skipped++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Email {$email} sudah terdaftar.";
                $skipped++;
                continue;
            }

            $idJurusan = null;
            foreach ($jurusanMap as $name => $id) {
                if (stripos($name, $jurusanName) !== false || stripos($jurusanName, $name) !== false) {
                    $idJurusan = $id;
                    break;
                }
            }

            try {
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make(\Illuminate\Support\Str::random(12)),
                    'role' => 'dosen',
                    'status' => 'aktif',
                ]);

                $user->profile()->create([
                    'nim' => $nip,
                    'id_jurusan' => $idJurusan,
                    'no_hp' => $noHp ?: null,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Gagal import {$nama}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "{$imported} dosen berhasil diimport.";
        if ($skipped > 0) $message .= " {$skipped} data dilewati.";
        if (!empty($errors)) $message .= " Errors: " . implode('; ', array_slice($errors, 0, 3));

        return redirect()->route('admin.dosen')
            ->with($imported > 0 ? 'success' : 'error', $message);
    }

    /**
     * Get notifications (JSON API) — real persistent notifications
     */
    public function getNotifications()
    {
        $admin = Auth::guard('admin')->user();

        $notifications = AdminNotification::where('admin_id', $admin->id)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->tipe,
                    'icon' => $n->icon ?? 'info',
                    'message' => $n->judul,
                    'detail' => $n->konten,
                    'link' => $n->link,
                    'is_read' => $n->is_read,
                    'time' => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at,
                ];
            });

        $unreadCount = AdminNotification::where('admin_id', $admin->id)->unread()->count();

        return response()->json([
            'count' => $unreadCount,
            'items' => $notifications,
        ]);
    }

    /**
     * Get unread notification count (for badge polling)
     */
    public function getNotificationCount()
    {
        $admin = Auth::guard('admin')->user();
        $count = AdminNotification::where('admin_id', $admin->id)->unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a single notification as read
     */
    public function markNotificationRead($id)
    {
        $admin = Auth::guard('admin')->user();
        AdminNotification::where('id', $id)->where('admin_id', $admin->id)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $admin = Auth::guard('admin')->user();
        AdminNotification::where('admin_id', $admin->id)->unread()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // ==========================================
    // YOUTUBE PLAYLIST SYNC (Task 1)
    // ==========================================

    /**
     * Sync YouTube playlist for a course
     */
    public function syncYoutubePlaylist(Request $request, $id)
    {
        $course = \App\Models\Course::find($id);

        if (!$course) {
            return response()->json(['error' => 'Kursus tidak ditemukan.'], 404);
        }

        $playlistUrl = $request->input('youtube_playlist', $course->youtube_playlist);

        if (empty($playlistUrl)) {
            return response()->json(['error' => 'URL playlist YouTube belum diisi.'], 422);
        }

        $playlistId = YoutubePlaylistService::extractPlaylistId($playlistUrl);
        if (!$playlistId) {
            return response()->json(['error' => 'URL playlist tidak valid. Gunakan format: https://www.youtube.com/playlist?list=PLxxxxxxx'], 422);
        }

        try {
            $result = YoutubePlaylistService::fetchPlaylistVideos($playlistId);

            if (empty($result['videos'])) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Playlist valid tetapi tidak ada video ditemukan.',
                    'sync' => null,
                ]);
            }

            $sync = YoutubePlaylistService::syncToDatabase($course->id_course, $result['videos']);

            // Update playlist URL on course if changed
            if ($course->youtube_playlist !== $playlistUrl) {
                $course->update(['youtube_playlist' => $playlistUrl]);
            }

            // Notify admin
            AdminNotification::notifyAllAdmins(
                "YouTube Playlist disinkronkan",
                "Kursus \"{$course->nama_course}\": {$sync['added']} video ditambah, {$sync['updated']} diperbarui, {$sync['removed']} dihapus.",
                'success',
                'youtube',
                route('admin.kursus')
            );

            return response()->json([
                'success' => true,
                'message' => "Sinkronisasi selesai: {$sync['added']} ditambah, {$sync['updated']} diperbarui, {$sync['removed']} dihapus.",
                'sync' => $sync,
                'videos' => YoutubePlaylistVideo::where('id_course', $course->id_course)
                    ->orderBy('urutan')
                    ->get(),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('YouTube sync error', ['course' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal sinkronisasi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get YouTube videos for a course (JSON)
     */
    public function getYoutubeVideos($id)
    {
        $videos = YoutubePlaylistVideo::where('id_course', $id)
            ->orderBy('urutan')
            ->get();

        return response()->json(['videos' => $videos]);
    }

    // ==========================================
    // EXCEL IMPORT (Task 4+5)
    // ==========================================

    /**
     * Preview Excel import
     */
    public function previewImport(Request $request, string $type)
    {
        if (!in_array($type, ['mahasiswa', 'dosen'])) {
            return response()->json(['error' => 'Tipe import tidak valid.'], 422);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.mimes' => 'Format file harus Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $filePath = $request->file('file')->getRealPath();
            $preview = ExcelImportService::preview($filePath, $type);

            // Store file temporarily for confirm step
            $tempPath = $request->file('file')->store('imports/temp', 'local');
            session(["import_temp_{$type}" => $tempPath]);
            session(["import_preview_{$type}" => $preview]);

            return response()->json([
                'success' => true,
                'preview' => $preview,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membaca file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Confirm and execute Excel import
     */
    public function confirmImport(Request $request, string $type)
    {
        if (!in_array($type, ['mahasiswa', 'dosen'])) {
            return response()->json(['error' => 'Tipe import tidak valid.'], 422);
        }

        $strategy = $request->input('strategy', 'skip');
        if (!in_array($strategy, ['skip', 'update', 'stop'])) {
            return response()->json(['error' => 'Strategi duplikasi tidak valid.'], 422);
        }

        $preview = session("import_preview_{$type}");
        if (!$preview || empty($preview['valid_rows'])) {
            return response()->json(['error' => 'Tidak ada data preview. Upload file terlebih dahulu.'], 422);
        }

        $admin = Auth::guard('admin')->user();

        try {
            $result = ExcelImportService::executeImport(
                $preview['valid_rows'],
                $type,
                $strategy,
                $admin->id
            );

            // Clear session
            $tempPath = session("import_temp_{$type}");
            if ($tempPath && Storage::disk('local')->exists($tempPath)) {
                Storage::disk('local')->delete($tempPath);
            }
            session()->forget(["import_temp_{$type}", "import_preview_{$type}"]);

            $label = $type === 'mahasiswa' ? 'Mahasiswa' : 'Dosen';
            $message = "{$result['imported']} {$label} berhasil diimport.";
            if ($result['updated'] > 0) $message .= " {$result['updated']} diperbarui.";
            if ($result['skipped'] > 0) $message .= " {$result['skipped']} dilewati.";

            if ($result['status'] === 'stopped') {
                return response()->json([
                    'success' => false,
                    'message' => $result['errors'][0] ?? 'Import dihentikan karena data duplikat.',
                    'result' => $result,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal import: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate(string $type)
    {
        if (!in_array($type, ['mahasiswa', 'dosen'])) {
            abort(404);
        }

        try {
            $path = ExcelImportService::generateTemplate($type);
            $filename = "template_import_{$type}.xlsx";

            return response()->download($path, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate template: ' . $e->getMessage());
        }
    }

    // ========================
    // News/Pengumuman Management
    // ========================
    // PRODI (JURUSAN) MANAGEMENT
    // ========================

    public function showProdi(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $perPage = 10;

        $query = \App\Models\Jurusan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_jurusan', 'like', "%{$search}%")
                  ->orWhere('kode_jurusan', 'like', "%{$search}%")
                  ->orWhere('fakultas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('fakultas')) {
            $query->where('fakultas', $request->fakultas);
        }

        $prodiPaginated = $query->withCount('profiles')->orderBy('nama_jurusan')->paginate($perPage);
        $prodiPaginated->appends($request->only(['search', 'jenjang', 'fakultas']));

        $totalAll = \App\Models\Jurusan::count();
        $jenjangList = \App\Models\Jurusan::select('jenjang')->distinct()->orderBy('jenjang')->pluck('jenjang');
        $fakultasList = \App\Models\Jurusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');

        return view('Auth.admin.prodi', [
            'admin' => $admin,
            'prodiPaginated' => $prodiPaginated,
            'totalAll' => $totalAll,
            'search' => $request->search ?? '',
            'jenjangFilter' => $request->jenjang ?? '',
            'fakultasFilter' => $request->fakultas ?? '',
            'jenjangList' => $jenjangList,
            'fakultasList' => $fakultasList,
        ]);
    }

    public function storeProdi(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'jenjang' => 'required|string|max:10',
        ]);

        \App\Models\Jurusan::create($request->only(['kode_jurusan', 'nama_jurusan', 'fakultas', 'jenjang']));

        return redirect()->route('admin.prodi')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function getProdi($id)
    {
        $prodi = \App\Models\Jurusan::findOrFail($id);
        return response()->json($prodi);
    }

    public function updateProdi(Request $request, $id)
    {
        $prodi = \App\Models\Jurusan::findOrFail($id);

        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusans,kode_jurusan,' . $id . ',id_jurusan',
            'nama_jurusan' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'jenjang' => 'required|string|max:10',
        ]);

        $prodi->update($request->only(['kode_jurusan', 'nama_jurusan', 'fakultas', 'jenjang']));

        return redirect()->route('admin.prodi')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function deleteProdi($id)
    {
        $prodi = \App\Models\Jurusan::findOrFail($id);

        $mahasiswaCount = $prodi->profiles()->count();
        if ($mahasiswaCount > 0) {
            return redirect()->route('admin.prodi')->with('error', "Tidak dapat menghapus prodi ini karena masih memiliki {$mahasiswaCount} mahasiswa terdaftar.");
        }

        $prodi->delete();

        return redirect()->route('admin.prodi')->with('success', 'Program Studi berhasil dihapus.');
    }

    // ========================
    // PENGUMUMAN MANAGEMENT
    // ========================

    /**
     * Show Pengumuman page
     */
    public function showPengumuman(Request $request)
    {
        $query = \App\Models\News::query();

        // Filter by kategori
        $kategoriFilter = $request->get('kategori', 'all');
        if ($kategoriFilter !== 'all') {
            $query->where('kategori', $kategoriFilter);
        }

        // Filter by status
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter === 'aktif') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'nonaktif') {
            $query->where('is_active', false);
        }

        // Search
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        $newsPaginated = $query->orderByDesc('tanggal_publish')->paginate(10)->withQueryString();

        return view('Auth.admin.pengumuman', [
            'newsList' => $newsPaginated,
            'totalNews' => \App\Models\News::count(),
            'kategoriFilter' => $kategoriFilter,
            'statusFilter' => $statusFilter,
            'search' => $search,
        ]);
    }

    /**
     * Store new pengumuman
     */
    public function storePengumuman(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'required|in:pengumuman,berita,event',
            'tanggal_publish' => 'required|date',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable',
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'konten.required' => 'Konten wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'tanggal_publish.required' => 'Tanggal publish wajib diisi.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
        ]);

        $data = [
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kategori' => $request->kategori,
            'tanggal_publish' => $request->tanggal_publish,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('news-thumbnails', 'public');
        }

        \App\Models\News::create($data);

        return redirect()->route('admin.pengumuman')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Get pengumuman data (JSON)
     */
    public function getPengumuman($id)
    {
        $news = \App\Models\News::findOrFail($id);
        return response()->json([
            'id' => $news->id_news,
            'judul' => $news->judul,
            'konten' => $news->konten,
            'kategori' => $news->kategori,
            'tanggal_publish' => $news->tanggal_publish->format('Y-m-d\TH:i'),
            'thumbnail' => $news->thumbnail,
            'is_active' => $news->is_active,
        ]);
    }

    /**
     * Update pengumuman
     */
    public function updatePengumuman(Request $request, $id)
    {
        $news = \App\Models\News::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'required|in:pengumuman,berita,event',
            'tanggal_publish' => 'required|date',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable',
        ]);

        $news->judul = $request->judul;
        $news->konten = $request->konten;
        $news->kategori = $request->kategori;
        $news->tanggal_publish = $request->tanggal_publish;
        $news->is_active = $request->has('is_active');

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($news->thumbnail && Storage::disk('public')->exists($news->thumbnail)) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            $news->thumbnail = $request->file('thumbnail')->store('news-thumbnails', 'public');
        }

        $news->save();

        return redirect()->route('admin.pengumuman')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Delete pengumuman
     */
    public function deletePengumuman($id)
    {
        $news = \App\Models\News::findOrFail($id);

        // Delete thumbnail if exists
        if ($news->thumbnail && Storage::disk('public')->exists($news->thumbnail)) {
            Storage::disk('public')->delete($news->thumbnail);
        }

        $news->delete();

        return redirect()->route('admin.pengumuman')->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Export Dosen or Mahasiswa to Excel
     */
    public function exportExcel($type)
    {
        if (!in_array($type, ['dosen', 'mahasiswa'])) {
            abort(404);
        }

        $users = \App\Models\User::where('role', $type)
            ->with('profile.jurusan')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $isMahasiswa = ($type === 'mahasiswa');
        $sheet->setTitle(ucfirst($type));

        // Headers
        $headers = $isMahasiswa
            ? ['No', 'Nama', 'NIM', 'Email', 'Program Studi', 'No. Telepon', 'Status']
            : ['No', 'Nama', 'NIP', 'Email', 'Program Studi', 'No. Telepon', 'Status'];

        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header row
        $lastCol = chr(65 + count($headers) - 1);
        $headerRange = "A1:{$lastCol}1";
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF3B82F6'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $rowNum = 2;
        foreach ($users as $index => $user) {
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $user->name);
            $sheet->setCellValueExplicit('C' . $rowNum, $user->profile?->nim ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $rowNum, $user->email);
            $sheet->setCellValue('E' . $rowNum, $user->profile?->jurusan?->nama_jurusan ?? '-');
            $sheet->setCellValueExplicit('F' . $rowNum, $user->profile?->no_hp ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('G' . $rowNum, ucfirst($user->status));
            $rowNum++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Export_" . ucfirst($type) . "_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('status', 'Logout berhasil.');
    }
}
