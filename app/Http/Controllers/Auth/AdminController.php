<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Bootcamp;
use App\Models\BootcampMentor;
use App\Models\AutomaticCertificate;
use App\Models\Category;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\DosenNotification;
use App\Models\Jurusan;
use App\Models\Message;
use App\Models\Notification;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionItem;
use App\Models\PlatformSetting;
use App\Models\Profile;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\YoutubePlaylistVideo;
use App\Services\DeviceSessionLimitService;
use App\Services\ExcelImportService;
use App\Services\YoutubePlaylistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    private const NEWS_ALLOWED_CATEGORIES = [
        'pengumuman',
        'berita',
        'event',
        'umum',
        'akademik',
        'keuangan',
        'registrasi',
        'kemahasiswaan',
        'keungan',
    ];

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
    public function login(Request $request, DeviceSessionLimitService $deviceSessionLimitService)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('role', 'admin')
            ->where('email', strtolower(trim($request->email)))
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

        if ($deviceSessionLimitService->hasReachedWebLoginLimit($user)) {
            return back()
                ->withInput()
                ->with('alert', $deviceSessionLimitService->limitMessage());
        }

        // Login using Laravel Auth guard
        Auth::guard('admin')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $deviceSessionLimitService->bindCurrentSessionToUser($request, (int) $user->id);

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

    public function showBootcampTiket()
    {
        $bootcamps = Bootcamp::query()
            ->with(['mentorAssignments.user'])
            ->latest('id_bootcamp')
            ->get();

        $bootcampPrograms = $bootcamps->map(function (Bootcamp $bootcamp) {
            $isTicket = $bootcamp->program_type === 'ticketed_event';

            return [
                'id' => (string) $bootcamp->id_bootcamp,
                'title' => $bootcamp->title,
                'type' => $isTicket ? 'Tiket Event' : 'Bootcamp',
                'batch' => $bootcamp->batch_label,
                'status' => $this->bootcampStatusLabel($bootcamp->status),
                'status_key' => $bootcamp->status,
                'mentor' => $bootcamp->mentor_label,
                'seats' => $bootcamp->seats_label,
                'price' => $bootcamp->price_label,
                'schedule' => $bootcamp->schedule_label ?: 'Jadwal belum diatur',
                'risk' => $bootcamp->risk_note ?: 'Belum ada catatan risiko',
                'accent' => $isTicket
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20'
                    : 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:border-sky-500/20',
            ];
        })->values()->all();

        $activeBootcampCount = $bootcamps
            ->where('program_type', 'bootcamp')
            ->whereIn('status', ['open_registration', 'published', 'in_progress'])
            ->count();

        $seatSummary = $bootcamps->reduce(function (array $carry, Bootcamp $bootcamp) {
            [$filled, $total] = $this->parseSeatLabel($bootcamp->seats_label);
            $carry['filled'] += $filled;
            $carry['total'] += $total;
            return $carry;
        }, ['filled' => 0, 'total' => 0]);

        $mentorAssignments = BootcampMentor::query()
            ->with('user')
            ->latest('id_bootcamp_mentor')
            ->get();

        $mentorRows = $mentorAssignments
            ->groupBy('id_user')
            ->map(function ($rows) {
                $first = $rows->first();
                $user = $first?->user;

                if (!$user) {
                    return null;
                }

                return [
                    'name' => $user->name,
                    'role' => $first->role_label ?: 'mentor',
                    'load' => $rows->count() . ' batch aktif',
                    'status' => 'Siap',
                ];
            })
            ->filter()
            ->take(5)
            ->values()
            ->all();

        $pendingCount = $bootcamps->whereIn('status', ['draft', 'internal_review'])->count();
        $needMentorCount = $bootcamps->filter(fn (Bootcamp $bootcamp) => blank(trim((string) $bootcamp->mentor_label)) || str_contains(strtolower((string) $bootcamp->mentor_label), '0 mentor'))->count();
        $publishReadyCount = $bootcamps->whereIn('status', ['open_registration', 'published'])->count();
        $opsBoard = [
            ['label' => 'Draft Baru', 'count' => $pendingCount, 'helper' => 'Perlu review admin sebelum publish', 'tone' => 'bg-slate-50 dark:bg-gray-900/40'],
            ['label' => 'Butuh Mentor', 'count' => $needMentorCount, 'helper' => 'Batch baru belum lengkap pengajar', 'tone' => 'bg-amber-50 dark:bg-amber-500/10'],
            ['label' => 'Refund / Reschedule', 'count' => 0, 'helper' => 'Kasus peserta perlu tindak lanjut', 'tone' => 'bg-rose-50 dark:bg-rose-500/10'],
            ['label' => 'Siap Publish', 'count' => $publishReadyCount, 'helper' => 'Konten, jadwal, kuota sudah lengkap', 'tone' => 'bg-emerald-50 dark:bg-emerald-500/10'],
        ];

        $occupancyPercent = $seatSummary['total'] > 0
            ? (int) round(($seatSummary['filled'] / $seatSummary['total']) * 100)
            : 0;

        $ticketFlows = [
            ['name' => 'Landing -> Checkout', 'value' => $occupancyPercent . '%', 'note' => 'Seat occupancy tiket dan bootcamp aktif'],
            ['name' => 'Checkout -> Paid', 'value' => max(0, $occupancyPercent - 12) . '%', 'note' => 'Perlu reminder pembayaran otomatis'],
            ['name' => 'Paid -> Attend', 'value' => max(0, min(100, $occupancyPercent + 8)) . '%', 'note' => 'Konversi attendance estimasi operasional'],
            ['name' => 'Attend -> Certificate', 'value' => max(0, min(100, $occupancyPercent - 5)) . '%', 'note' => 'Menunggu integrasi sertifikat final'],
        ];

        $bootcampStats = [
            ['label' => 'Bootcamp Aktif', 'value' => $activeBootcampCount, 'helper' => $activeBootcampCount . ' batch berjalan', 'tone' => 'from-sky-500 to-blue-600'],
            ['label' => 'Kuota Terisi', 'value' => $seatSummary['filled'] . '/' . $seatSummary['total'], 'helper' => $occupancyPercent . '% seat occupancy', 'tone' => 'from-emerald-500 to-teal-600'],
            ['label' => 'Mentor Aktif', 'value' => $mentorAssignments->pluck('id_user')->unique()->count(), 'helper' => 'dosen dan mentor eksternal', 'tone' => 'from-violet-500 to-fuchsia-600'],
            ['label' => 'Pending Approval', 'value' => $pendingCount, 'helper' => 'draft dan internal review', 'tone' => 'from-amber-500 to-orange-500'],
        ];

        $availableMentors = User::query()
            ->where('role', 'dosen')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('Auth.admin.bootcamp-tiket', [
            'bootcampStats' => $bootcampStats,
            'bootcampPrograms' => $bootcampPrograms,
            'opsBoard' => $opsBoard,
            'ticketFlows' => $ticketFlows,
            'mentorRows' => $mentorRows,
            'availableMentors' => $availableMentors,
        ]);
    }

    public function storeBootcamp(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'program_type' => ['required', Rule::in(['bootcamp', 'ticketed_event'])],
            'batch' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:100'],
            'mentor' => ['required', 'string', 'max:100'],
            'seats' => ['required', 'string', 'max:100'],
            'schedule' => ['required', 'string', 'max:255'],
            'risk' => ['required', 'string', 'max:2000'],
            'mentor_user_id' => ['nullable', 'exists:users,id'],
            'mentor_role' => ['nullable', 'string', 'max:100'],
        ]);

        $bootcamp = Bootcamp::create([
            'program_type' => $validated['program_type'],
            'title' => $this->cleanTextInput($validated['title']),
            'batch_label' => $this->cleanTextInput($validated['batch']),
            'status' => 'draft',
            'mentor_label' => $this->cleanTextInput($validated['mentor']),
            'seats_label' => $this->cleanTextInput($validated['seats']),
            'price_label' => $this->cleanTextInput($validated['price']),
            'schedule_label' => $this->cleanTextInput($validated['schedule']),
            'risk_note' => $this->cleanTextInput($validated['risk']),
            'created_by' => Auth::guard('admin')->id(),
        ]);

        if (!empty($validated['mentor_user_id'])) {
            BootcampMentor::updateOrCreate(
                [
                    'id_bootcamp' => $bootcamp->id_bootcamp,
                    'id_user' => (int) $validated['mentor_user_id'],
                ],
                [
                    'role_label' => $validated['mentor_role'] ?? 'mentor',
                    'assignment_note' => $validated['risk'],
                ]
            );
        }

        return redirect()
            ->route('admin.bootcamp-tiket')
            ->with('success', 'Bootcamp baru berhasil dibuat.');
    }

    public function updateBootcampBatch(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'draft',
                'internal_review',
                'open_registration',
                'published',
                'registration_closed',
                'in_progress',
                'completed',
                'archived',
            ])],
            'seats' => ['required', 'string', 'max:100'],
            'risk' => ['nullable', 'string', 'max:2000'],
        ]);

        $bootcamp = Bootcamp::findOrFail($id);
        $bootcamp->update([
            'status' => $validated['status'],
            'seats_label' => $this->cleanTextInput($validated['seats']),
            'risk_note' => $this->cleanTextInput($validated['risk'] ?? ''),
        ]);

        return redirect()
            ->route('admin.bootcamp-tiket')
            ->with('success', 'Batch bootcamp berhasil diperbarui.');
    }

    public function assignBootcampMentor(Request $request, $id)
    {
        $validated = $request->validate([
            'mentor' => ['required', 'string', 'max:100'],
            'risk' => ['nullable', 'string', 'max:2000'],
            'mentor_user_id' => ['nullable', 'exists:users,id'],
            'mentor_role' => ['nullable', 'string', 'max:100'],
        ]);

        $bootcamp = Bootcamp::findOrFail($id);
        $bootcamp->update([
            'mentor_label' => $this->cleanTextInput($validated['mentor']),
            'risk_note' => $this->cleanTextInput($validated['risk'] ?? ''),
        ]);

        if (!empty($validated['mentor_user_id'])) {
            BootcampMentor::updateOrCreate(
                [
                    'id_bootcamp' => $bootcamp->id_bootcamp,
                    'id_user' => (int) $validated['mentor_user_id'],
                ],
                [
                    'role_label' => $validated['mentor_role'] ?? 'mentor',
                    'assignment_note' => $validated['risk'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('admin.bootcamp-tiket')
            ->with('success', 'Mentor bootcamp berhasil diperbarui.');
    }

    private function bootcampStatusLabel(string $status): string
    {
        return match ($status) {
            'internal_review' => 'Internal Review',
            'open_registration' => 'Open Registration',
            'published' => 'Published',
            'registration_closed' => 'Registration Closed',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'archived' => 'Archived',
            default => 'Draft',
        };
    }

    private function parseSeatLabel(?string $seatLabel): array
    {
        $label = (string) $seatLabel;

        if (preg_match('/(\d+)\s*\/\s*(\d+)/', $label, $matches)) {
            return [(int) $matches[1], (int) $matches[2]];
        }

        return [0, 0];
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
            ->with(['profile.jurusan', 'profile.jurusans']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function($pq) use ($search) {
                      $pq->where('nomor_induk', 'like', "%{$search}%");
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
                $pq->where('id_jurusan', $jurusanId)
                   ->orWhereHas('jurusans', function ($jurusanQuery) use ($jurusanId) {
                       $jurusanQuery->where('jurusans.id_jurusan', $jurusanId);
                   });
            });
        }

        $dosenPaginated = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        // Transform data for view
        $dosenList = $dosenPaginated->map(function($dosen) {
            return [
                'id' => $dosen->id,
                'foto' => $dosen->profile?->foto_profile,
                'nama' => $dosen->name,
                'nomor_induk' => $dosen->profile?->nomor_induk ?? '-',
                'program_studi' => !empty($dosen->profile?->jurusan_names)
                    ? implode(', ', $dosen->profile->jurusan_names)
                    : '-',
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
        if ($response = $this->rejectOversizedPhotoUpload($request, 'foto')) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_induk' => 'required|string|max:50|unique:profiles,nomor_induk',
            'id_jurusan' => 'required|array|min:1',
            'id_jurusan.*' => 'required|distinct|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nomor_induk.unique' => 'Nomor Induk sudah terdaftar di sistem.',
            'id_jurusan.required' => 'Program Studi wajib dipilih.',
            'id_jurusan.array' => 'Program Studi wajib dipilih.',
            'id_jurusan.min' => 'Pilih minimal satu Program Studi.',
            'id_jurusan.*.exists' => 'Program Studi yang dipilih tidak valid.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890 atau +62 812-3456-7890).',
        ]);

        $jurusanIds = $this->normalizeJurusanIds($request->input('id_jurusan', []));

        // Create user (P0 FIX: Generate secure random password instead of hardcoded)
        $defaultPassword = \Illuminate\Support\Str::random(12);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($defaultPassword),
            'role' => 'dosen',
            'status' => 'aktif',
        ]);

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('dosen-photos', 'public');
        }

        // Create profile
        $profile = $user->profile()->create([
            'nomor_induk' => $request->nomor_induk,
            'id_jurusan' => $jurusanIds[0] ?? null,
            'no_hp' => $request->no_hp,
            'foto_profile' => $fotoPath,
        ]);

        $this->syncDosenJurusans($profile, $jurusanIds);

        return redirect()->route('admin.dosen')
            ->with('success', "Dosen berhasil ditambahkan! Password default: {$defaultPassword} (catat sekarang, tidak ditampilkan lagi)");
    }

    /**
     * Get Dosen data for edit
     */
    public function getDosen($id)
    {
        $dosen = User::with(['profile.jurusans'])->find($id);
        
        if (!$dosen || $dosen->role !== 'dosen') {
            return response()->json(['error' => 'Dosen tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $dosen->id,
            'name' => $dosen->name,
            'email' => $dosen->email,
            'status' => $dosen->status,
            'nomor_induk' => $dosen->profile?->nomor_induk,
            'id_jurusan' => $dosen->profile?->jurusan_ids ?? [],
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

        if ($response = $this->rejectOversizedPhotoUpload($request, 'foto')) {
            return $response;
        }

        $profileId = $dosen->profile?->id;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nomor_induk' => 'required|string|max:50|unique:profiles,nomor_induk,' . ($profileId ?? 'NULL') . ',id',
            'id_jurusan' => 'required|array|min:1',
            'id_jurusan.*' => 'required|distinct|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nomor_induk.unique' => 'Nomor Induk sudah terdaftar di sistem.',
            'id_jurusan.required' => 'Program Studi wajib dipilih.',
            'id_jurusan.array' => 'Program Studi wajib dipilih.',
            'id_jurusan.min' => 'Pilih minimal satu Program Studi.',
            'id_jurusan.*.exists' => 'Program Studi yang dipilih tidak valid.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890 atau +62 812-3456-7890).',
        ]);

        $jurusanIds = $this->normalizeJurusanIds($request->input('id_jurusan', []));

        // Update user
        $dosen->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'aktif',
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
        $profile = $dosen->profile()->updateOrCreate(
            ['user_id' => $dosen->id],
            [
                'nomor_induk' => $request->nomor_induk,
                'id_jurusan' => $jurusanIds[0] ?? null,
                'no_hp' => $request->no_hp,
                'foto_profile' => $fotoPath,
            ]
        );

        $this->syncDosenJurusans($profile, $jurusanIds);

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
            $search = trim((string) $request->search);
            $normalizedStatusSearch = match (strtolower(str_replace([' ', '-'], '', $search))) {
                'aktif' => 'aktif',
                'nonaktif' => 'nonaktif',
                default => null,
            };

            $query->where(function($q) use ($search, $normalizedStatusSearch) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function($pq) use ($search) {
                      $pq->where('nomor_induk', 'like', "%{$search}%")
                         ->orWhere('no_hp', 'like', "%{$search}%")
                         ->orWhereHas('jurusan', function ($jurusanQuery) use ($search) {
                             $jurusanQuery->where('nama_jurusan', 'like', "%{$search}%")
                                 ->orWhere('kode_jurusan', 'like', "%{$search}%")
                                 ->orWhere('fakultas', 'like', "%{$search}%")
                                 ->orWhere('jenjang', 'like', "%{$search}%");
                         });
                  });

                if ($normalizedStatusSearch !== null) {
                    $q->orWhere('status', $normalizedStatusSearch);
                }
            });
        }

        $statusFilter = $request->input('status', 'all');
        $prodiFilter = $request->input('prodi', 'all');

        // Status filter
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Prodi filter
        if ($prodiFilter !== 'all') {
            $query->whereHas('profile', function($q) use ($prodiFilter) {
                $q->where('id_jurusan', $prodiFilter);
            });
        }

        $mahasiswaPaginated = $query
            ->latest('id')
            ->orderByDesc('created_at')
            ->paginate($perPage);
        $mahasiswaPaginated->appends($request->only(['search', 'status', 'prodi']));
        $mahasiswaList = $mahasiswaPaginated->map(function($mhs) {
            return [
                'id' => $mhs->id,
                'foto' => $mhs->profile?->foto_profile,
                'nama' => $mhs->name,
                'nomor_induk' => $mhs->profile?->nomor_induk ?? '-',
                'program_studi' => $mhs->profile?->jurusan?->nama_jurusan ?? '-',
                'email' => $mhs->email,
                'no_telepon' => $mhs->profile?->no_hp ?? '-',
                'status' => $mhs->status === 'aktif' ? 'Aktif' : 'Nonaktif',
            ];
        });

        // Get jurusan list for dropdown
        $jurusanList = \App\Models\Jurusan::orderBy('nama_jurusan')->get();
        
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
            'statusFilter' => $statusFilter,
            'prodiFilter' => $prodiFilter,
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
        if ($response = $this->rejectOversizedPhotoUpload($request, 'foto')) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_induk' => 'required|string|max:50|unique:profiles,nomor_induk',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nomor_induk.unique' => 'Nomor Induk sudah terdaftar di sistem.',
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
            'nomor_induk' => $request->nomor_induk,
            'id_jurusan' => $request->id_jurusan,
            'no_hp' => $request->no_hp,
            'foto_profile' => $fotoPath,
        ]);

        // Enroll in selected courses
        if ($request->has('courses') && is_array($request->courses)) {
            $selectedCourses = Course::query()
                ->whereIn('id_course', $request->courses)
                ->get()
                ->keyBy('id_course');

            foreach ($request->courses as $courseId) {
                $course = $selectedCourses->get($courseId);
                if (!$course) {
                    continue;
                }

                \App\Models\Enrollment::create([
                    'id_mahasiswa' => $user->id,
                    'id_course' => $courseId,
                    'tanggal_daftar' => now(),
                    'progress' => 0,
                    'status' => $this->resolveEnrollmentStatusForCourse($course),
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
            'nomor_induk' => $mhs->profile?->nomor_induk,
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

        if ($response = $this->rejectOversizedPhotoUpload($request, 'foto')) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nomor_induk' => 'required|string|max:50|unique:profiles,nomor_induk,' . $id . ',user_id',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{8,20}$/',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nomor_induk.unique' => 'Nomor Induk sudah terdaftar di sistem.',
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
                'nomor_induk' => $request->nomor_induk,
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
            if ($request->status === 'pending') {
                $query->where('approval_status', 'pending');
            } elseif ($request->status === 'ditolak') {
                $query->where('approval_status', 'ditolak');
            } else {
                $query->where('status', $request->status);
            }
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
            'dosen' => $kursus->dosen?->name ?? '-',
            'thumbnail' => $kursus->thumbnail,
            'video_thumbnail' => $videoThumb,
            'module_count' => $moduleCount,
                'tipe' => $kursus->tipe,
                'kategori' => $kursus->kategori,
                'harga' => $kursus->harga,
                'diskon' => $kursus->diskon,
                'status' => $kursus->status,
                'approval_status' => $kursus->approval_status,
                'approval_notes' => $kursus->approval_notes,
                'tanggal_webinar' => optional($kursus->tanggal_webinar)?->format('Y-m-d'),
                'jam_mulai_webinar' => $kursus->jam_mulai_webinar,
                'jam_selesai_webinar' => $kursus->jam_selesai_webinar,
                'kuota_peserta' => $kursus->kuota_peserta,
                'rating' => $kursus->rating,
                'jumlah_ulasan' => $kursus->jumlah_ulasan,
                'enrollments_count' => $kursus->enrollments_count ?? 0,
                'has_youtube' => !empty($kursus->youtube_playlist),
            ];
        });
        
        // Get dosen list for dropdown
        $dosenList = User::where('role', 'dosen')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();
        $webinarSpeakerList = User::whereIn('role', ['dosen', 'admin'])
            ->where('status', 'aktif')
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();
        $jurusanList = \App\Models\Jurusan::orderBy('nama_jurusan')->get();
        
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
            'webinarSpeakerList' => $webinarSpeakerList,
            'jurusanList' => $jurusanList,
            'nextKursusCode' => $this->generateNextCourseCode('KRS'),
            'nextWebinarCode' => $this->generateNextCourseCode('WEB'),
        ]);
    }

    /**
     * Store new Kursus
     */
    public function storeKursus(Request $request)
    {
        $request->validate($this->courseValidationRules());
        $playlistValidationError = $this->validateYoutubePlaylistForCourse($request);
        if ($playlistValidationError) {
            return redirect()->back()->withInput()->with('error', $playlistValidationError);
        }

        // Handle status from button or toggle
        $status = $request->input('add_status_btn', $request->status);

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course = Course::create($this->buildAdminCoursePayload($request, $status, $thumbnailPath));
        $playlistSummary = $this->syncPlaylistForCourse($course, $request->youtube_playlist, $request->kategori);
        $this->notifyMahasiswaAboutPublishedProgram($course, 'Admin');

        $label = $request->kategori === 'webinar' ? 'Webinar' : 'Kursus';
        $message = "{$label} berhasil ditambahkan!";
        if ($playlistSummary) {
            $message .= ' ' . $playlistSummary;
        }

        return redirect()->route('admin.kursus')
            ->with('success', $message);
    }

    /**
     * Get Kursus data for edit
     */
    public function getKursus($id)
    {
        $kursus = \App\Models\Course::with(['dosen', 'jurusan', 'youtubePlaylistVideos'])->find($id);
        
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
            'tanggal_webinar' => optional($kursus->tanggal_webinar)?->format('Y-m-d'),
            'jam_mulai_webinar' => $kursus->jam_mulai_webinar,
            'jam_selesai_webinar' => $kursus->jam_selesai_webinar,
            'kuota_peserta' => $kursus->kuota_peserta,
            'approval_status' => $kursus->approval_status,
            'approval_notes' => $kursus->approval_notes,
            'sertifikat' => (bool) $kursus->sertifikat,
            'akses_publik' => (bool) $kursus->akses_publik,
            'youtube_videos_count' => $kursus->youtubePlaylistVideos->count(),
            'youtube_videos' => $kursus->youtubePlaylistVideos->map(fn ($video) => [
                'id' => $video->id,
                'youtube_id' => $video->youtube_id,
                'title' => $video->title,
                'thumbnail_url' => $video->thumbnail_url,
                'urutan' => $video->urutan,
                'watch_url' => 'https://www.youtube.com/watch?v=' . $video->youtube_id,
            ])->values(),
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

        $request->validate($this->courseValidationRules($id));
        $playlistValidationError = $this->validateYoutubePlaylistForCourse($request);
        if ($playlistValidationError) {
            return redirect()->back()->withInput()->with('error', $playlistValidationError);
        }

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($kursus->thumbnail) {
                Storage::disk('public')->delete($kursus->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $kursus->update($this->buildAdminCoursePayload($request, $request->status, $thumbnailPath, $kursus));
        $playlistSummary = $this->syncPlaylistForCourse($kursus->fresh(), $request->youtube_playlist, $request->kategori);

        $label = $request->kategori === 'webinar' ? 'Webinar' : 'Kursus';
        $message = "{$label} berhasil diperbarui!";
        if ($playlistSummary) {
            $message .= ' ' . $playlistSummary;
        }

        return redirect()->route('admin.kursus')
            ->with('success', $message);
    }

    public function approveWebinar($id)
    {
        $kursus = Course::find($id);

        if (!$kursus || $kursus->kategori !== 'webinar') {
            return redirect()->route('admin.kursus')
                ->with('error', 'Webinar tidak ditemukan.');
        }

        $admin = Auth::guard('admin')->user();

        $kursus->update([
            'status' => 'aktif',
            'approval_status' => 'disetujui',
            'approval_notes' => null,
            'approved_by' => $admin?->id,
            'approved_at' => now(),
        ]);

        if ($kursus->id_dosen) {
            $speaker = User::find($kursus->id_dosen);
            if ($speaker?->role === 'dosen') {
            DosenNotification::notifyDosen(
                $kursus->id_dosen,
                'Webinar Disetujui',
                "Webinar \"{$kursus->nama_course}\" telah disetujui dan dipublikasikan.",
                'success',
                'webinar',
                route('dosen.kursus.edit', $kursus->id_course)
            );
            }
        }

        $this->notifyMahasiswaAboutPublishedProgram($kursus, 'Admin');

        return redirect()->route('admin.kursus')
            ->with('success', 'Webinar berhasil disetujui dan dipublikasikan.');
    }

    public function rejectWebinar(Request $request, $id)
    {
        $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ]);

        $kursus = Course::find($id);

        if (!$kursus || $kursus->kategori !== 'webinar') {
            return redirect()->route('admin.kursus')
                ->with('error', 'Webinar tidak ditemukan.');
        }

        $admin = Auth::guard('admin')->user();
        $notes = trim($request->approval_notes);

        $kursus->update([
            'status' => 'draft',
            'approval_status' => 'ditolak',
            'approval_notes' => $notes,
            'approved_by' => $admin?->id,
            'approved_at' => now(),
        ]);

        if ($kursus->id_dosen) {
            $speaker = User::find($kursus->id_dosen);
            if ($speaker?->role === 'dosen') {
            DosenNotification::notifyDosen(
                $kursus->id_dosen,
                'Pengajuan Webinar Ditolak',
                "Webinar \"{$kursus->nama_course}\" perlu direvisi. Catatan: {$notes}",
                'warning',
                'webinar',
                route('dosen.kursus.edit', $kursus->id_course)
            );
            }
        }

        return redirect()->route('admin.kursus')
            ->with('success', 'Pengajuan webinar berhasil ditolak dan dikembalikan ke draft.');
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

        if ($response = $this->rejectOversizedPhotoUpload($request, 'foto')) {
            return $response;
        }

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

    private function rejectOversizedPhotoUpload(Request $request, string $fieldName): ?RedirectResponse
    {
        $rawUpload = $_FILES[$fieldName] ?? null;
        $uploadError = is_array($rawUpload) ? ($rawUpload['error'] ?? null) : null;

        if (in_array($uploadError, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return back()
                ->withInput()
                ->withErrors([$fieldName => 'Ukuran foto maksimal 2MB.']);
        }

        $file = $request->file($fieldName);
        if (!$file instanceof UploadedFile) {
            return null;
        }

        if (!$file->isValid()) {
            return back()
                ->withInput()
                ->withErrors([$fieldName => 'Upload foto gagal. Silakan pilih ulang file dengan ukuran maksimal 2MB.']);
        }

        if (($file->getSize() ?? 0) > (2 * 1024 * 1024)) {
            return back()
                ->withInput()
                ->withErrors([$fieldName => 'Ukuran foto maksimal 2MB.']);
        }

        return null;
    }

    private function normalizeJurusanIds(array $jurusanIds): array
    {
        return collect($jurusanIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function syncDosenJurusans(Profile $profile, array $jurusanIds): void
    {
        $profile->jurusans()->sync($jurusanIds);

        $primaryJurusanId = $jurusanIds[0] ?? null;
        if ((string) $profile->id_jurusan !== (string) $primaryJurusanId) {
            $profile->forceFill(['id_jurusan' => $primaryJurusanId])->save();
        }
    }

    private function parseDelimitedJurusanCodes(?string $value): array
    {
        return collect(preg_split('/[\s]*[,;|]+[\s]*/', (string) $value) ?: [])
            ->map(fn ($code) => strtoupper(trim((string) $code)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function resolveJurusanIdsFromCodes(?string $value): array
    {
        $codes = $this->parseDelimitedJurusanCodes($value);
        if (empty($codes)) {
            return [];
        }

        $jurusanMap = Jurusan::query()
            ->select(['id_jurusan', 'kode_jurusan', 'nama_jurusan'])
            ->get()
            ->values();

        $jurusanByCode = $jurusanMap
            ->filter(fn ($jurusan) => filled($jurusan->kode_jurusan))
            ->mapWithKeys(fn ($jurusan) => [strtoupper((string) $jurusan->kode_jurusan) => (int) $jurusan->id_jurusan])
            ->toArray();

        return collect($codes)
            ->map(function ($code) use ($jurusanByCode, $jurusanMap) {
                if (isset($jurusanByCode[$code])) {
                    return $jurusanByCode[$code];
                }

                $matchedJurusan = $jurusanMap->first(function ($jurusan) use ($code) {
                    $name = mb_strtolower((string) ($jurusan->nama_jurusan ?? ''));
                    return $name !== '' && (stripos($name, mb_strtolower($code)) !== false || stripos(mb_strtolower($code), $name) !== false);
                });

                return $matchedJurusan?->id_jurusan;
            })
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
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

        // Normalize header so exported files can be imported again without manual rename
        $headerMap = [];
        foreach ($header as $index => $column) {
            $normalized = \App\Services\ExcelImportService::normalizeHeaderName($column);
            if ($normalized === '' || $normalized === 'no') {
                continue;
            }

            $headerMap[$index] = $normalized;
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }

            $data = [];
            foreach ($headerMap as $index => $column) {
                $data[$column] = trim((string) ($row[$index] ?? ''));
            }
            $nama = $data['nama'] ?? '';
            $nomor_induk = $data['nomor_induk'] ?? '';
            $email = $data['email'] ?? '';
            $kodeJurusan = $data['kode_jurusan'] ?? ($data['jurusan'] ?? '');
            $noHp = $data['no_hp'] ?? '';
            $status = strtolower(trim((string) ($data['status'] ?? ''))) ?: 'aktif';

            if (empty($nama) || empty($nomor_induk) || empty($email)) {
                $skipped++;
                continue;
            }

            // Check duplicates
            if (User::where('email', $email)->exists()) {
                $errors[] = "Email {$email} sudah terdaftar.";
                $skipped++;
                continue;
            }

            $idJurusan = $this->resolveJurusanIdsFromCodes($kodeJurusan)[0] ?? null;

            try {
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($nomor_induk),
                    'role' => 'mahasiswa',
                    'status' => in_array($status, ['aktif', 'nonaktif'], true) ? $status : 'aktif',
                    'requires_password_reset' => true,
                ]);

                $user->profile()->create([
                    'nomor_induk' => $nomor_induk,
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

        $headerMap = [];
        foreach ($header as $index => $column) {
            $normalized = \App\Services\ExcelImportService::normalizeHeaderName($column);
            if ($normalized === '' || $normalized === 'no') {
                continue;
            }

            $headerMap[$index] = $normalized;
        }
        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }

            $data = [];
            foreach ($headerMap as $index => $column) {
                $data[$column] = trim((string) ($row[$index] ?? ''));
            }
            $nama = $data['nama'] ?? '';
            $nomor_induk = $data['nomor_induk'] ?? '';
            $email = $data['email'] ?? '';
            $kodeJurusan = $data['kode_jurusan'] ?? ($data['jurusan'] ?? '');
            $noHp = $data['no_hp'] ?? '';
            $status = strtolower(trim((string) ($data['status'] ?? ''))) ?: 'aktif';

            if (empty($nama) || empty($nomor_induk) || empty($email)) {
                $skipped++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Email {$email} sudah terdaftar.";
                $skipped++;
                continue;
            }

            $jurusanIds = $this->resolveJurusanIdsFromCodes($kodeJurusan);
            $idJurusan = $jurusanIds[0] ?? null;

            try {
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($nomor_induk),
                    'role' => 'dosen',
                    'status' => in_array($status, ['aktif', 'nonaktif'], true) ? $status : 'aktif',
                    'requires_password_reset' => true,
                ]);

                $profile = $user->profile()->create([
                    'nomor_induk' => $nomor_induk,
                    'id_jurusan' => $idJurusan,
                    'no_hp' => $noHp ?: null,
                ]);

                $this->syncDosenJurusans($profile, $jurusanIds);

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
     * Get notifications (JSON API) â€” real persistent notifications
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
                    'link' => $this->resolveNotificationLink($n),
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

    public function showSupportTickets(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $status = (string) $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));
        $selectedTicketId = (int) $request->query('ticket', 0);

        $query = SupportTicket::query()
            ->with(['mahasiswa.profile', 'dosen.profile', 'answeredBy'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('subject', 'like', "%{$search}%")
                    ->orWhere('question', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('profile', function ($profileQuery) use ($search) {
                                $profileQuery->where('nomor_induk', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('dosen', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('profile', function ($profileQuery) use ($search) {
                                $profileQuery->where('nomor_induk', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('Auth.admin.support-tickets', [
            'admin' => $admin,
            'tickets' => $tickets,
            'statusFilter' => $status,
            'search' => $search,
            'selectedTicketId' => $selectedTicketId,
            'openCount' => SupportTicket::where('status', 'open')->count(),
            'answeredCount' => SupportTicket::where('status', 'answered')->count(),
        ]);
    }

    public function replySupportTicket(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'admin_reply' => 'required|string|min:3|max:3000',
            'status' => 'nullable|in:open,answered',
        ]);

        $ticket = SupportTicket::with(['mahasiswa', 'dosen'])->findOrFail($id);

        $ticket->update([
            'admin_reply' => trim($validated['admin_reply']),
            'status' => $validated['status'] ?? 'answered',
            'answered_by' => $admin->id,
            'answered_at' => now(),
        ]);

        if ($ticket->mahasiswa) {
            Notification::notifyMahasiswa(
                (int) $ticket->mahasiswa->id,
                'Balasan Support Baru',
                'Admin telah membalas tiket support Anda: ' . $ticket->subject,
                'info',
                'support',
                '#2563EB'
            );
        }

        if ($ticket->dosen) {
            DosenNotification::notifyDosen(
                (int) $ticket->dosen->id,
                'Balasan Support Baru',
                'Admin telah membalas tiket support Anda: ' . $ticket->subject,
                'info',
                'support'
            );
        }

        return redirect()
            ->route('admin.support-tickets', [
                'ticket' => $ticket->id_support_ticket,
                'status' => $request->query('status', 'all'),
                'search' => $request->query('search', ''),
            ])
            ->with('success', 'Balasan support berhasil dikirim.');
    }

    private function notifyMahasiswaAboutPublishedProgram(Course $course, string $sourceLabel): void
    {
        if ($course->status !== 'aktif' || !$course->akses_publik) {
            return;
        }

        $config = match ($course->kategori) {
            'webinar' => [
                'judul' => 'Webinar Baru Tersedia',
                'konten' => sprintf(
                    '%s mempublikasikan webinar "%s". Cek sekarang di katalog pembelajaran.',
                    $sourceLabel,
                    $course->nama_course
                ),
                'icon_color' => '#7C3AED',
            ],
            'tiket' => [
                'judul' => 'Program Tiket Baru Tersedia',
                'konten' => sprintf(
                    '%s menambahkan program "%s". Detailnya sudah tersedia untuk mahasiswa.',
                    $sourceLabel,
                    $course->nama_course
                ),
                'icon_color' => '#059669',
            ],
            default => [
                'judul' => 'Kursus Baru Tersedia',
                'konten' => sprintf(
                    '%s mempublikasikan kursus "%s". Cek sekarang di katalog pembelajaran.',
                    $sourceLabel,
                    $course->nama_course
                ),
                'icon_color' => '#2563EB',
            ],
        };

        Notification::notifyAllMahasiswa(
            $config['judul'],
            $config['konten'],
            'umum',
            'info',
            $config['icon_color']
        );
    }

    private function resolveNotificationLink(AdminNotification $notification): ?string
    {
        $link = $notification->link;

        if (($notification->icon ?? '') === 'support') {
            if (is_string($link) && str_contains($link, '/admin/support-tickets')) {
                return $link;
            }

            return route('admin.support-tickets');
        }

        return $link;
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

        if ($course->kategori !== 'kursus') {
            return response()->json(['error' => 'Sinkronisasi playlist hanya tersedia untuk kategori kursus.'], 422);
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

            $sync = YoutubePlaylistService::syncCourseContent($course->id_course, $result['videos']);

            // Update playlist URL on course if changed
            if ($course->youtube_playlist !== $playlistUrl) {
                $course->update(['youtube_playlist' => $playlistUrl]);
            }

            // Notify admin
            AdminNotification::notifyAllAdmins(
                "YouTube Playlist disinkronkan",
                "Kursus \"{$course->nama_course}\": {$sync['database']['added']} video database ditambah, {$sync['materials']['total']} materi playlist aktif.",
                'success',
                'youtube',
                route('admin.kursus')
            );

            return response()->json([
                'success' => true,
                'message' => $this->buildPlaylistSyncSummary($sync, $result['message'] ?? null),
                'sync' => $sync,
                'videos' => YoutubePlaylistVideo::where('id_course', $course->id_course)
                    ->orderBy('urutan')
                    ->get()
                    ->map(function ($video) {
                        return [
                            'id' => $video->id,
                            'youtube_id' => $video->youtube_id,
                            'title' => $video->title,
                            'thumbnail_url' => $video->thumbnail_url,
                            'urutan' => $video->urutan,
                            'watch_url' => 'https://www.youtube.com/watch?v=' . $video->youtube_id,
                        ];
                    })
                    ->values(),
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
        $course = Course::with('youtubePlaylistVideos')->find($id);

        if (!$course) {
            return response()->json(['error' => 'Kursus tidak ditemukan.'], 404);
        }

        $videos = $course->youtubePlaylistVideos()
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'videos' => $videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'youtube_id' => $video->youtube_id,
                    'title' => $video->title,
                    'thumbnail_url' => $video->thumbnail_url,
                    'urutan' => $video->urutan,
                    'watch_url' => 'https://www.youtube.com/watch?v=' . $video->youtube_id,
                ];
            })->values(),
            'count' => $videos->count(),
            'playlist_url' => $course->youtube_playlist,
            'kategori' => $course->kategori,
        ]);
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

        $query = Jurusan::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function($q) use ($search) {
                $q->where('nama_jurusan', 'like', "%{$search}%")
                  ->orWhere('kode_jurusan', 'like', "%{$search}%")
                  ->orWhere('fakultas', 'like', "%{$search}%")
                  ->orWhere('jenjang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('fakultas')) {
            $query->where('fakultas', $request->fakultas);
        }

        $prodiPaginated = $query
            ->withCount([
                'profiles as mahasiswa_count' => function ($profileQuery) {
                    $profileQuery->whereHas('user', function ($userQuery) {
                        $userQuery->where('role', 'mahasiswa');
                    });
                },
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id_jurusan')
            ->paginate($perPage);
        $prodiPaginated->appends($request->only(['search', 'jenjang', 'fakultas']));

        $totalAll = Jurusan::count();
        $jenjangList = Jurusan::select('jenjang')->distinct()->orderBy('jenjang')->pluck('jenjang');
        $fakultasList = Jurusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');

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
        $validated = $this->validateProdiPayload($request);

        Jurusan::create($validated);

        return redirect()->route('admin.prodi')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function getProdi($id)
    {
        $prodi = Jurusan::findOrFail($id);
        return response()->json($prodi);
    }

    public function updateProdi(Request $request, $id)
    {
        $prodi = Jurusan::findOrFail($id);

        $validated = $this->validateProdiPayload($request, $prodi);

        $prodi->update($validated);

        return redirect()->route('admin.prodi')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function deleteProdi($id)
    {
        $prodi = Jurusan::findOrFail($id);

        $mahasiswaCount = $prodi->profiles()
            ->whereHas('user', function ($query) {
                $query->where('role', 'mahasiswa');
            })
            ->count();

        $dosenCount = $prodi->dosenProfiles()
            ->whereHas('user', function ($query) {
                $query->where('role', 'dosen');
            })
            ->distinct('profiles.id')
            ->count('profiles.id');

        $courseCount = $prodi->courses()->count();

        $blockingRelations = [];
        if ($mahasiswaCount > 0) {
            $blockingRelations[] = "{$mahasiswaCount} mahasiswa";
        }
        if ($dosenCount > 0) {
            $blockingRelations[] = "{$dosenCount} dosen";
        }
        if ($courseCount > 0) {
            $blockingRelations[] = "{$courseCount} kursus";
        }

        if (!empty($blockingRelations)) {
            $relationMessage = implode(', ', $blockingRelations);

            return redirect()->route('admin.prodi')->with(
                'error',
                "Tidak dapat menghapus prodi ini karena masih terhubung dengan {$relationMessage}."
            );
        }

        $prodi->delete();

        return redirect()->route('admin.prodi')->with('success', 'Program Studi berhasil dihapus.');
    }

    private function validateProdiPayload(Request $request, ?Jurusan $prodi = null): array
    {
        $request->merge([
            'kode_jurusan' => Str::upper($this->cleanTextInput($request->input('kode_jurusan'))),
            'nama_jurusan' => $this->cleanTextInput($request->input('nama_jurusan')),
            'fakultas' => $this->cleanTextInput($request->input('fakultas')),
            'jenjang' => Str::upper($this->cleanTextInput($request->input('jenjang'))),
        ]);

        return $request->validate([
            'kode_jurusan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('jurusans', 'kode_jurusan')
                    ->ignore($prodi?->id_jurusan, 'id_jurusan'),
            ],
            'nama_jurusan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jurusans', 'nama_jurusan')
                    ->where(fn ($query) => $query
                        ->where('fakultas', $request->input('fakultas'))
                        ->where('jenjang', $request->input('jenjang')))
                    ->ignore($prodi?->id_jurusan, 'id_jurusan'),
            ],
            'fakultas' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'string', Rule::in(['D3', 'D4', 'S1', 'S2', 'S3'])],
        ], [
            'kode_jurusan.required' => 'Kode prodi wajib diisi.',
            'kode_jurusan.unique' => 'Kode prodi sudah digunakan.',
            'nama_jurusan.required' => 'Nama program studi wajib diisi.',
            'nama_jurusan.unique' => 'Program studi dengan fakultas dan jenjang yang sama sudah ada.',
            'fakultas.required' => 'Fakultas wajib diisi.',
            'jenjang.required' => 'Jenjang wajib dipilih.',
            'jenjang.in' => 'Jenjang yang dipilih tidak valid.',
        ]);
    }

    private function cleanTextInput(mixed $value): string
    {
        return preg_replace('/\s+/u', ' ', trim((string) $value));
    }

    // ========================
    // CATEGORY MANAGEMENT
    // ========================

    public function showKategori(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $perPage = 10;

        $query = Category::query();

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($categoryQuery) use ($search) {
                $categoryQuery->where('kode_kategori', 'like', "%{$search}%")
                    ->orWhere('nama_kategori', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $tipeFilter = $request->get('tipe', 'all');
        if (in_array($tipeFilter, ['kursus', 'pengumuman'], true)) {
            $query->where('tipe', $tipeFilter);
        } else {
            $tipeFilter = 'all';
        }

        $statusFilter = $request->get('status', 'all');
        if (in_array($statusFilter, ['aktif', 'nonaktif'], true)) {
            $query->where('status', $statusFilter);
        } else {
            $statusFilter = 'all';
        }

        $kategoriPaginated = $query
            ->latest('id_category')
            ->paginate($perPage);
        $kategoriPaginated->appends($request->only(['search', 'tipe', 'status']));

        return view('Auth.admin.kategori', [
            'admin' => $admin,
            'kategoriPaginated' => $kategoriPaginated,
            'totalAll' => Category::count(),
            'totalKursus' => Category::where('tipe', 'kursus')->count(),
            'totalPengumuman' => Category::where('tipe', 'pengumuman')->count(),
            'search' => $search,
            'tipeFilter' => $tipeFilter,
            'statusFilter' => $statusFilter,
            'nextCategoryCode' => $this->generateNextCategoryCode(),
        ]);
    }

    public function storeKategori(Request $request)
    {
        $validated = $this->validateKategoriPayload($request);

        Category::create($validated);

        return redirect()->route('admin.kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function getKategori($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Category::findOrFail($id);

        $validated = $this->validateKategoriPayload($request, $kategori);

        $kategori->update($validated);

        return redirect()->route('admin.kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteKategori($id)
    {
        $kategori = Category::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.kategori')->with('success', 'Kategori berhasil dihapus.');
    }

    private function validateKategoriPayload(Request $request, ?Category $kategori = null): array
    {
        $request->merge([
            'kode_kategori' => Str::upper($this->cleanTextInput($request->input('kode_kategori'))),
            'nama_kategori' => $this->cleanTextInput($request->input('nama_kategori')),
            'tipe' => strtolower($this->cleanTextInput($request->input('tipe'))),
            'status' => strtolower($this->cleanTextInput($request->input('status', 'aktif'))),
        ]);

        return $request->validate([
            'kode_kategori' => [
                'required',
                'string',
                'max:20',
                Rule::unique('categories', 'kode_kategori')
                    ->ignore($kategori?->id_category, 'id_category'),
            ],
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'nama_kategori')
                    ->where(fn ($query) => $query->where('tipe', $request->input('tipe')))
                    ->ignore($kategori?->id_category, 'id_category'),
            ],
            'tipe' => ['required', 'string', Rule::in(['kursus', 'pengumuman'])],
            'status' => ['required', 'string', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori sudah digunakan.',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori untuk tipe ini sudah ada.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'tipe.in' => 'Tipe kategori yang dipilih tidak valid.',
            'status.required' => 'Status kategori wajib dipilih.',
            'status.in' => 'Status kategori yang dipilih tidak valid.',
        ]);
    }

    private function generateNextCategoryCode(): string
    {
        $maxNumber = Category::query()
            ->get(['kode_kategori'])
            ->map(function (Category $category): int {
                if (preg_match('/(\d+)$/', $category->kode_kategori, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        return 'KAT-' . str_pad((string) ($maxNumber + 1), 3, '0', STR_PAD_LEFT);
    }

    // ========================
    // AUTOMATIC CERTIFICATE MANAGEMENT
    // ========================

    public function showSertifikasi()
    {
        $this->ensureDefaultCertificateTemplates();

        $templateCollection = CertificateTemplate::query()
            ->latest('id')
            ->get();

        $templates = $templateCollection
            ->filter(fn (CertificateTemplate $template) => $this->isCertificateTemplateAvailable($template))
            ->map(fn (CertificateTemplate $template) => $this->transformCertificateTemplate($template))
            ->values();

        $certificates = AutomaticCertificate::query()
            ->with('template')
            ->latest('id')
            ->get()
            ->map(fn (AutomaticCertificate $certificate) => $this->transformAutomaticCertificate($certificate, $templateCollection))
            ->values();

        return view('Auth.admin.sertifikasi', [
            'initialTemplates' => $templates,
            'initialCertificates' => $certificates,
        ]);
    }

    public function storeCertificateTemplate(Request $request)
    {
        $request->validate([
            'blangko' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'name' => ['nullable', 'string', 'max:255'],
        ], [
            'blangko.required' => 'Blangko wajib diupload.',
            'blangko.image' => 'Blangko harus berupa gambar.',
            'blangko.max' => 'Ukuran blangko maksimal 5MB.',
        ]);

        $file = $request->file('blangko');
        $path = $file->store('certificate-templates', 'public');
        $defaultName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $template = CertificateTemplate::create([
            'name' => $this->cleanTextInput($request->input('name', $defaultName ?: 'Blangko Sertifikat')),
            'background_type' => 'image',
            'background_image_path' => $path,
            'background_gradient' => null,
            ...$this->defaultCertificateTemplateSettings(),
        ]);

        return response()->json([
            'message' => 'Blangko berhasil ditambahkan.',
            'template' => $this->transformCertificateTemplate($template),
        ]);
    }

    public function updateCertificateTemplate(Request $request, $id)
    {
        $template = CertificateTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_x' => ['required', 'numeric', 'between:0,100'],
            'nomor_y' => ['required', 'numeric', 'between:0,100'],
            'nomor_size' => ['required', 'integer', 'between:10,72'],
            'nama_x' => ['required', 'numeric', 'between:0,100'],
            'nama_y' => ['required', 'numeric', 'between:0,100'],
            'nama_size' => ['required', 'integer', 'between:14,96'],
            'program_x' => ['required', 'numeric', 'between:0,100'],
            'program_y' => ['required', 'numeric', 'between:0,100'],
            'program_size' => ['required', 'integer', 'between:10,56'],
            'tanggal_x' => ['required', 'numeric', 'between:0,100'],
            'tanggal_y' => ['required', 'numeric', 'between:0,100'],
            'tanggal_size' => ['required', 'integer', 'between:10,42'],
        ], [
            'name.required' => 'Nama blangko wajib diisi.',
        ]);

        $template->update([
            'name' => $this->cleanTextInput($validated['name']),
            'nomor_x' => $validated['nomor_x'],
            'nomor_y' => $validated['nomor_y'],
            'nomor_size' => $validated['nomor_size'],
            'nama_x' => $validated['nama_x'],
            'nama_y' => $validated['nama_y'],
            'nama_size' => $validated['nama_size'],
            'program_x' => $validated['program_x'],
            'program_y' => $validated['program_y'],
            'program_size' => $validated['program_size'],
            'tanggal_x' => $validated['tanggal_x'],
            'tanggal_y' => $validated['tanggal_y'],
            'tanggal_size' => $validated['tanggal_size'],
        ]);

        return response()->json([
            'message' => 'Pengaturan blangko berhasil diperbarui.',
            'template' => $this->transformCertificateTemplate($template->fresh()),
        ]);
    }

    public function showCertificateTemplateImage($id)
    {
        $template = CertificateTemplate::findOrFail($id);

        abort_unless(
            $template->background_type === 'image' && filled($template->background_image_path),
            404
        );

        $fullPath = Storage::disk('public')->path($template->background_image_path);
        abort_unless(is_file($fullPath), 404);

        return response()->file($fullPath);
    }

    public function deleteCertificateTemplate($id)
    {
        $template = CertificateTemplate::findOrFail($id);

        $usageCount = $template->certificates()->count();
        if ($usageCount > 0) {
            return response()->json([
                'message' => "Blangko tidak bisa dihapus karena masih dipakai oleh {$usageCount} sertifikat.",
            ], 422);
        }

        if ($template->background_type === 'image' && filled($template->background_image_path)) {
            Storage::disk('public')->delete($template->background_image_path);
        }

        $template->delete();

        return response()->json([
            'message' => 'Blangko berhasil dihapus.',
        ]);
    }

    public function storeAutomaticCertificate(Request $request)
    {
        $validated = $this->validateAutomaticCertificatePayload($request);

        $certificate = AutomaticCertificate::create($validated);
        $this->notifyMahasiswaCertificateAvailable($certificate);

        return response()->json([
            'message' => 'Sertifikat otomatis berhasil ditambahkan.',
            'certificate' => $this->transformAutomaticCertificate($certificate->load('template')),
        ]);
    }

    public function getAutomaticCertificate($id)
    {
        $certificate = AutomaticCertificate::with('template')->findOrFail($id);

        return response()->json(
            $this->transformAutomaticCertificate(
                $certificate,
                CertificateTemplate::query()->latest('id')->get()
            )
        );
    }

    public function updateAutomaticCertificate(Request $request, $id)
    {
        $certificate = AutomaticCertificate::findOrFail($id);

        $validated = $this->validateAutomaticCertificatePayload($request, $certificate);
        $certificate->update($validated);
        $this->notifyMahasiswaCertificateAvailable($certificate->fresh());

        return response()->json([
            'message' => 'Sertifikat otomatis berhasil diperbarui.',
            'certificate' => $this->transformAutomaticCertificate($certificate->fresh()->load('template')),
        ]);
    }

    public function deleteAutomaticCertificate($id)
    {
        $certificate = AutomaticCertificate::findOrFail($id);
        $certificate->delete();

        return response()->json([
            'message' => 'Sertifikat otomatis berhasil dihapus.',
        ]);
    }

    private function validateAutomaticCertificatePayload(Request $request, ?AutomaticCertificate $certificate = null): array
    {
        $request->merge([
            'nomor_sertifikat' => Str::upper($this->cleanTextInput(
                $request->input('nomor_sertifikat') ?: $this->generateNextCertificateNumber()
            )),
            'nama_peserta' => $this->cleanTextInput($request->input('nama_peserta')),
            'nama_program' => $this->cleanTextInput($request->input('nama_program')),
        ]);

        $validated = $request->validate([
            'certificate_template_id' => ['required', 'exists:certificate_templates,id'],
            'nomor_sertifikat' => [
                'required',
                'string',
                'max:50',
                Rule::unique('automatic_certificates', 'nomor_sertifikat')
                    ->ignore($certificate?->id),
            ],
            'nama_peserta' => ['required', 'string', 'max:255'],
            'nama_program' => ['required', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
        ], [
            'certificate_template_id.required' => 'Blangko wajib dipilih.',
            'certificate_template_id.exists' => 'Blangko yang dipilih tidak valid.',
            'nomor_sertifikat.required' => 'Nomor sertifikat wajib diisi.',
            'nomor_sertifikat.unique' => 'Nomor sertifikat sudah digunakan.',
            'nama_peserta.required' => 'Nama peserta wajib diisi.',
            'nama_program.required' => 'Program wajib diisi.',
            'tanggal_terbit.required' => 'Tanggal terbit wajib diisi.',
        ]);

        $template = CertificateTemplate::query()->find($validated['certificate_template_id']);
        if (! $this->isCertificateTemplateAvailable($template)) {
            throw ValidationException::withMessages([
                'certificate_template_id' => 'Blangko yang dipilih tidak tersedia atau file gambarnya hilang.',
            ]);
        }

        return $validated;
    }

    private function notifyMahasiswaCertificateAvailable(AutomaticCertificate $certificate): void
    {
        $participantName = Str::lower(trim((string) $certificate->nama_peserta));
        $programName = Str::lower(trim((string) $certificate->nama_program));

        if ($participantName === '' || $programName === '') {
            return;
        }

        $mahasiswas = User::query()
            ->where('role', 'mahasiswa')
            ->whereRaw('LOWER(TRIM(name)) = ?', [$participantName])
            ->get(['id', 'name']);

        foreach ($mahasiswas as $mahasiswa) {
            $enrollment = \App\Models\Enrollment::query()
                ->where('id_mahasiswa', $mahasiswa->id)
                ->whereHas('course', function ($query) use ($programName) {
                    $query->whereRaw('LOWER(TRIM(nama_course)) = ?', [$programName])
                        ->where('sertifikat', true);
                })
                ->with('course:id_course,nama_course,sertifikat')
                ->orderByDesc('updated_at')
                ->first();

            if (! $enrollment) {
                continue;
            }

            $isCompleted = (($enrollment->status ?? null) === 'selesai') || ((float) ($enrollment->progress ?? 0) >= 100);
            if (! $isCompleted) {
                continue;
            }

            $courseName = trim((string) ($enrollment->course?->nama_course ?? $certificate->nama_program));

            $alreadyNotified = Notification::query()
                ->where('id_mahasiswa', $mahasiswa->id)
                ->where('tipe', 'pencapaian')
                ->where('icon', 'certificate')
                ->where('judul', 'Sertifikat Kursus Tersedia')
                ->where('konten', 'like', '%' . $courseName . '%')
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            Notification::notifyMahasiswa(
                $mahasiswa->id,
                'Sertifikat Kursus Tersedia',
                'Sertifikat untuk kursus "' . $courseName . '" sudah tersedia. Buka halaman kursus dan klik Download Sertifikat.',
                'pencapaian',
                'certificate',
                '#10B981'
            );
        }
    }

    private function ensureDefaultCertificateTemplates(): void
    {
        if (CertificateTemplate::query()->exists()) {
            return;
        }

        CertificateTemplate::query()->insert([
            [
                'name' => 'Blangko Biru Classic',
                'background_type' => 'gradient',
                'background_gradient' => 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 40%, #bfdbfe 100%)',
                'background_image_path' => null,
                ...$this->defaultCertificateTemplateSettings(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Blangko Emas Formal',
                'background_type' => 'gradient',
                'background_gradient' => 'linear-gradient(135deg, #fff7ed 0%, #ffedd5 45%, #fed7aa 100%)',
                'background_image_path' => null,
                ...$this->defaultCertificateTemplateSettings(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
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

    private function resolveCertificateTemplateSettings(CertificateTemplate $template): array
    {
        $defaults = $this->defaultCertificateTemplateSettings();

        $legacyTopRightSettings = [
            'nomor_x' => 82.00,
            'nomor_y' => 10.50,
            'nomor_size' => 28,
            'nama_x' => 50.00,
            'nama_y' => 54.00,
            'nama_size' => 52,
            'program_x' => 50.00,
            'program_y' => 71.00,
            'program_size' => 18,
            'tanggal_x' => 16.00,
            'tanggal_y' => 89.00,
            'tanggal_size' => 14,
        ];

        $currentSettings = [
            'nomor_x' => (float) $template->nomor_x,
            'nomor_y' => (float) $template->nomor_y,
            'nomor_size' => (int) $template->nomor_size,
            'nama_x' => (float) $template->nama_x,
            'nama_y' => (float) $template->nama_y,
            'nama_size' => (int) $template->nama_size,
            'program_x' => (float) $template->program_x,
            'program_y' => (float) $template->program_y,
            'program_size' => (int) $template->program_size,
            'tanggal_x' => (float) ($template->tanggal_x ?? 0),
            'tanggal_y' => (float) ($template->tanggal_y ?? 0),
            'tanggal_size' => (int) ($template->tanggal_size ?? 0),
        ];

        $hasZeroCoordinates = collect($currentSettings)
            ->only(['nomor_x', 'nomor_y', 'nama_x', 'nama_y', 'program_x', 'program_y', 'tanggal_x', 'tanggal_y'])
            ->contains(fn ($value) => (float) $value <= 0);

        $isLegacyLayout = $currentSettings === $legacyTopRightSettings;

        if ($hasZeroCoordinates || $isLegacyLayout) {
            return $defaults;
        }

        return $currentSettings;
    }

    private function generateNextCertificateNumber(): string
    {
        $currentYear = now()->year;

        $maxNumber = AutomaticCertificate::query()
            ->where('nomor_sertifikat', 'like', "SRT-{$currentYear}-%")
            ->get(['nomor_sertifikat'])
            ->map(function (AutomaticCertificate $certificate): int {
                if (preg_match('/(\d+)$/', $certificate->nomor_sertifikat, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        return 'SRT-' . $currentYear . '-' . str_pad((string) ($maxNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    private function transformCertificateTemplate(CertificateTemplate $template): array
    {
        $settings = $this->resolveCertificateTemplateSettings($template);

        return [
            'id' => $template->id,
            'name' => $template->name,
            'kind' => $template->background_type,
            'image' => $template->background_image_path
                ? route('admin.sertifikasi.templates.image', ['id' => $template->id], false)
                : null,
            'gradient' => $template->background_gradient,
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

    private function transformAutomaticCertificate(AutomaticCertificate $certificate, $templates = null): array
    {
        $resolvedTemplate = $this->resolveAutomaticCertificateTemplateForDisplay($certificate, $templates);

        return [
            'id' => $certificate->id,
            'nomor' => $certificate->nomor_sertifikat,
            'nama' => $certificate->nama_peserta,
            'program' => $certificate->nama_program,
            'tanggal' => optional($certificate->tanggal_terbit)->format('Y-m-d'),
            'templateId' => $resolvedTemplate?->id ?? $certificate->certificate_template_id,
        ];
    }

    private function isCertificateTemplateAvailable(?CertificateTemplate $template): bool
    {
        if (! $template) {
            return false;
        }

        if ($template->background_type !== 'image') {
            return true;
        }

        return filled($template->background_image_path)
            && Storage::disk('public')->exists($template->background_image_path);
    }

    private function resolvePreferredCertificateTemplate($templates = null): ?CertificateTemplate
    {
        $templateCollection = $templates instanceof \Illuminate\Support\Collection
            ? $templates
            : CertificateTemplate::query()->latest('id')->get();

        return $templateCollection
            ->first(fn (CertificateTemplate $template) => $this->isCertificateTemplateAvailable($template));
    }

    private function resolveAutomaticCertificateTemplateForDisplay(AutomaticCertificate $certificate, $templates = null): ?CertificateTemplate
    {
        $template = $certificate->relationLoaded('template')
            ? $certificate->template
            : $certificate->template()->first();

        if ($this->isCertificateTemplateAvailable($template)) {
            return $template;
        }

        return $this->resolvePreferredCertificateTemplate($templates);
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
        $allowedKategori = self::NEWS_ALLOWED_CATEGORIES;

        // Filter by kategori
        $kategoriFilter = $request->get('kategori', 'all');
        if ($kategoriFilter !== 'all' && in_array($kategoriFilter, $allowedKategori, true)) {
            if ($kategoriFilter === 'keuangan') {
                $query->whereIn('kategori', ['keuangan', 'keungan']);
            } else {
                $query->where('kategori', $kategoriFilter);
            }
        }

        // Filter by status
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter === 'aktif') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'nonaktif') {
            $query->where('is_active', false);
        }

        $targetProdiFilter = $request->get('target_prodi', 'all');
        if ($targetProdiFilter !== 'all') {
            $query->where('target_prodi', $this->normalizeNewsTargetProdi($targetProdiFilter));
        }

        // Search
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        $newsPaginated = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id_news')
            ->paginate(10)
            ->withQueryString();

        return view('Auth.admin.pengumuman', [
            'newsList' => $newsPaginated,
            'totalNews' => \App\Models\News::count(),
            'kategoriFilter' => $kategoriFilter,
            'statusFilter' => $statusFilter,
            'targetProdiFilter' => $targetProdiFilter,
            'search' => $search,
        ]);
    }

    public function showFinanceReport(Request $request)
    {
        [$selectedYear, $selectedMonth] = $this->resolveFinanceFilter($request);
        $financeReportData = $this->buildFinanceReportData($selectedYear, $selectedMonth);

        return view('Auth.admin.finance-report', [
            'financeReportData' => $financeReportData,
            'financeSummary' => $financeReportData['summary'],
            'financeChartPayload' => $financeReportData['chartPayload'],
            'financeTopProducts' => $financeReportData['topProducts'],
            'financeFilter' => $financeReportData['filter'],
            'financeServiceFee' => $financeReportData['serviceFee'],
        ]);
    }

    public function updateFinanceServiceFee(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('platform_settings')) {
            return redirect()
                ->route('admin.finance-report')
                ->with('error', 'Tabel setting platform belum tersedia. Jalankan migration terlebih dahulu.');
        }

        $validated = $request->validate([
            'course_service_fee' => ['required', 'integer', 'min:0', 'max:100000000'],
            'year' => ['nullable', 'integer'],
            'month' => ['nullable', 'integer'],
        ], [
            'course_service_fee.required' => 'Biaya layanan wajib diisi.',
            'course_service_fee.integer' => 'Biaya layanan harus berupa angka bulat.',
            'course_service_fee.min' => 'Biaya layanan tidak boleh negatif.',
        ]);

        PlatformSetting::setValue(
            PlatformSetting::KEY_COURSE_SERVICE_FEE,
            (int) $validated['course_service_fee'],
            'int',
            'Biaya layanan global untuk checkout kursus.'
        );

        $query = array_filter([
            'year' => $validated['year'] ?? null,
            'month' => $validated['month'] ?? null,
        ], fn ($value) => filled($value));

        return redirect()
            ->route('admin.finance-report', $query)
            ->with('success', 'Biaya layanan global berhasil diperbarui.');
    }

    public function exportFinanceReportExcel(Request $request)
    {
        [$selectedYear, $selectedMonth] = $this->resolveFinanceFilter($request);
        $financeReportData = $this->buildFinanceReportData($selectedYear, $selectedMonth);
        $summary = $financeReportData['summary'];
        $topProducts = $financeReportData['topProducts'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Ringkasan');

        $summarySheet->fromArray([
            ['Finance Report', '', '', ''],
            ['Periode', $summary['periodLabel'], '', ''],
            ['Dibuat Pada', $summary['generatedAt'], '', ''],
            ['', '', '', ''],
            ['Metrik', 'Nilai', 'Metrik', 'Nilai'],
            ['Total Revenue', (float) $summary['totalRevenue'], 'Kursus', (float) $summary['channels']['kursus']],
            ['Growth vs Bulan Lalu', $summary['momPercent'] . '%', 'Webinar', (float) $summary['channels']['webinar']],
            ['', '', 'Tiket', (float) $summary['channels']['tiket']],
        ], null, 'A1');

        $summarySheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(13);
        $summarySheet->getStyle('A5:D5')->getFont()->setBold(true);
        $summarySheet->getStyle('B6:B7')->getNumberFormat()->setFormatCode('#,##0');
        $summarySheet->getStyle('D6:D8')->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'D') as $column) {
            $summarySheet->getColumnDimension($column)->setAutoSize(true);
        }

        $trendSheet = $spreadsheet->createSheet();
        $trendSheet->setTitle('Trend Revenue');
        $trendSheet->setCellValue('A1', 'Bulan');
        $trendSheet->setCellValue('B1', 'Revenue');
        $trendSheet->getStyle('A1:B1')->getFont()->setBold(true);

        foreach ($financeReportData['monthlySeries'] as $index => $series) {
            $row = $index + 2;
            $trendSheet->setCellValue("A{$row}", $series['label']);
            $trendSheet->setCellValue("B{$row}", (float) $series['revenue']);
        }
        $trendSheet->getStyle('B2:B1000')->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'B') as $column) {
            $trendSheet->getColumnDimension($column)->setAutoSize(true);
        }

        $productSheet = $spreadsheet->createSheet();
        $productSheet->setTitle('Top Produk');
        $productSheet->fromArray([
            ['Produk', 'Kategori', 'Transaksi', 'Revenue'],
        ], null, 'A1');
        $productSheet->getStyle('A1:D1')->getFont()->setBold(true);

        foreach ($topProducts as $index => $product) {
            $row = $index + 2;
            $productSheet->setCellValue("A{$row}", $product['name']);
            $productSheet->setCellValue("B{$row}", $product['type']);
            $productSheet->setCellValue("C{$row}", (int) $product['tx']);
            $productSheet->setCellValue("D{$row}", (float) $product['revenue']);
        }

        $productSheet->getStyle('C2:C1000')->getNumberFormat()->setFormatCode('#,##0');
        $productSheet->getStyle('D2:D1000')->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'D') as $column) {
            $productSheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Finance_Report_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildFinanceReportData(?int $selectedYear = null, ?int $selectedMonth = null): array
    {
        $now = now();
        $availableYears = $this->getFinanceAvailableYears();
        $mode = 'rolling';

        if ($selectedYear && $selectedMonth) {
            $mode = 'month';
            $startDate = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $previousStartDate = $startDate->copy()->subMonth()->startOfMonth();
            $previousEndDate = $startDate->copy()->subMonth()->endOfMonth();
        } elseif ($selectedYear) {
            $mode = 'year';
            $startDate = \Carbon\Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = $startDate->copy()->endOfYear();
            $previousStartDate = $startDate->copy()->subYear()->startOfYear();
            $previousEndDate = $startDate->copy()->subYear()->endOfYear();
        } else {
            $startDate = $now->copy()->startOfMonth()->subMonths(5);
            $endDate = $now->copy()->endOfMonth();
            $previousStartDate = $now->copy()->subMonth()->startOfMonth();
            $previousEndDate = $now->copy()->subMonth()->endOfMonth();
        }

        $channelRows = $this->baseFinanceItemQuery($startDate, $endDate)
            ->selectRaw("LOWER(COALESCE(NULLIF(c.kategori, ''), 'kursus')) as kategori")
            ->selectRaw('SUM(payment_transaction_items.price) as total_revenue')
            ->groupBy('kategori')
            ->get();

        $channelTotals = [
            'kursus' => 0.0,
            'webinar' => 0.0,
            'tiket' => 0.0,
        ];

        foreach ($channelRows as $row) {
            $kategori = $this->normalizeFinanceCategory($row->kategori ?? null);
            $channelTotals[$kategori] += (float) $row->total_revenue;
        }

        [$monthlyLabels, $monthlyRevenue, $monthlySeries, $chartTitle, $chartSubtitle] = $this->buildFinanceTrendSeries(
            $startDate,
            $endDate,
            $selectedYear,
            $selectedMonth,
            $mode
        );

        $topProducts = $this->baseFinanceItemQuery($startDate, $endDate)
            ->selectRaw('payment_transaction_items.id_course')
            ->selectRaw('MAX(payment_transaction_items.course_name) as course_name')
            ->selectRaw("LOWER(COALESCE(NULLIF(c.kategori, ''), 'kursus')) as kategori")
            ->selectRaw('COUNT(payment_transaction_items.id_payment_transaction_item) as transaksi')
            ->selectRaw('SUM(payment_transaction_items.price) as revenue')
            ->groupBy('payment_transaction_items.id_course', 'kategori')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $kategori = $this->normalizeFinanceCategory($row->kategori ?? null);
                return [
                    'id_course' => (int) $row->id_course,
                    'name' => (string) $row->course_name,
                    'category' => $kategori,
                    'type' => $this->financeCategoryLabel($kategori),
                    'tx' => (int) $row->transaksi,
                    'revenue' => (int) round((float) $row->revenue),
                ];
            })
            ->values()
            ->all();

        $totalRevenue = $channelTotals['kursus'] + $channelTotals['webinar'] + $channelTotals['tiket'];
        $serviceFeeCollected = (float) $this->baseFinanceTransactionQuery($startDate, $endDate)
            ->sum('pt.service_fee');
        $previousPeriodRevenue = (float) $this->baseFinanceItemQuery($previousStartDate, $previousEndDate)
            ->sum('payment_transaction_items.price');
        $comparePercent = $previousPeriodRevenue > 0
            ? (($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100
            : ($totalRevenue > 0 ? 100.0 : 0.0);

        $compareLabel = match ($mode) {
            'month' => 'vs bulan sebelumnya',
            'year' => 'vs tahun sebelumnya',
            default => 'vs bulan lalu',
        };

        $filterLabel = match ($mode) {
            'month' => $this->financeMonthYearLabel($selectedYear, $selectedMonth),
            'year' => 'Tahun ' . $selectedYear,
            default => '6 Bulan Terakhir',
        };

        $periodLabel = match ($mode) {
            'month' => $this->financeMonthYearLabel($selectedYear, $selectedMonth),
            'year' => 'Jan - Des ' . $selectedYear,
            default => $this->financeShortMonthLabel($startDate) . ' - ' . $this->financeShortMonthLabel($endDate),
        };

        return [
            'summary' => [
                'totalRevenue' => (int) round($totalRevenue),
                'channels' => [
                    'kursus' => (int) round($channelTotals['kursus']),
                    'webinar' => (int) round($channelTotals['webinar']),
                    'tiket' => (int) round($channelTotals['tiket']),
                ],
                'momPercent' => round($comparePercent, 1),
                'compareLabel' => $compareLabel,
                'periodLabel' => $periodLabel,
                'generatedAt' => now()->format('d M Y H:i'),
            ],
            'chartPayload' => [
                'monthlyRevenue' => $monthlyRevenue,
                'monthlyLabels' => $monthlyLabels,
                'chartTitle' => $chartTitle,
                'chartSubtitle' => $chartSubtitle,
                'channels' => [
                    'kursus' => (int) round($channelTotals['kursus']),
                    'webinar' => (int) round($channelTotals['webinar']),
                    'tiket' => (int) round($channelTotals['tiket']),
                ],
                'topProducts' => array_map(function (array $product) {
                    return [
                        'name' => $product['name'],
                        'type' => $product['type'],
                        'tx' => $product['tx'],
                        'revenue' => $product['revenue'],
                    ];
                }, $topProducts),
            ],
            'topProducts' => $topProducts,
            'monthlySeries' => $monthlySeries,
            'filter' => [
                'year' => $selectedYear,
                'month' => $selectedMonth,
                'availableYears' => $availableYears,
                'availableMonths' => $this->financeMonthOptions(),
                'label' => $filterLabel,
            ],
            'serviceFee' => [
                'current' => PlatformSetting::getCourseServiceFee(),
                'collected' => (int) round($serviceFeeCollected),
                'settingKey' => PlatformSetting::KEY_COURSE_SERVICE_FEE,
            ],
        ];
    }

    private function resolveFinanceFilter(Request $request): array
    {
        $year = $request->filled('year') ? (int) $request->input('year') : null;
        $month = $request->filled('month') ? (int) $request->input('month') : null;

        if ($year !== null && ($year < 2000 || $year > ((int) now()->format('Y') + 2))) {
            $year = null;
        }

        if ($month !== null && ($month < 1 || $month > 12)) {
            $month = null;
        }

        if ($month && !$year) {
            $year = (int) now()->format('Y');
        }

        return [$year, $month];
    }

    private function getFinanceAvailableYears(): array
    {
        $years = PaymentTransaction::query()
            ->selectRaw('DISTINCT YEAR(COALESCE(paid_at, updated_at, created_at)) as report_year')
            ->whereNotNull(DB::raw('COALESCE(paid_at, updated_at, created_at)'))
            ->orderByDesc('report_year')
            ->pluck('report_year')
            ->filter()
            ->map(fn ($year) => (int) $year)
            ->values()
            ->all();

        if (empty($years)) {
            return [(int) now()->format('Y')];
        }

        return $years;
    }

    private function buildFinanceTrendSeries($startDate, $endDate, ?int $selectedYear, ?int $selectedMonth, string $mode): array
    {
        if ($mode === 'month') {
            $dailyRows = $this->baseFinanceItemQuery($startDate, $endDate)
                ->selectRaw("DATE_FORMAT(COALESCE(pt.paid_at, pt.updated_at, pt.created_at), '%Y-%m-%d') as day_key")
                ->selectRaw('SUM(payment_transaction_items.price) as total_revenue')
                ->groupBy('day_key')
                ->orderBy('day_key')
                ->pluck('total_revenue', 'day_key');

            $labels = [];
            $revenue = [];
            $series = [];
            $cursor = $startDate->copy()->startOfDay();

            while ($cursor->lte($endDate)) {
                $dayKey = $cursor->format('Y-m-d');
                $label = $cursor->format('d');
                $value = (float) ($dailyRows[$dayKey] ?? 0);

                $labels[] = $label;
                $revenue[] = (int) round($value);
                $series[] = [
                    'key' => $dayKey,
                    'label' => $label,
                    'revenue' => (int) round($value),
                ];

                $cursor->addDay();
            }

            return [
                $labels,
                $revenue,
                $series,
                'Trend Revenue Harian',
                'Total pendapatan gabungan per hari pada bulan terpilih.',
            ];
        }

        $monthlyRows = $this->baseFinanceItemQuery($startDate, $endDate)
            ->selectRaw("DATE_FORMAT(COALESCE(pt.paid_at, pt.updated_at, pt.created_at), '%Y-%m') as month_key")
            ->selectRaw('SUM(payment_transaction_items.price) as total_revenue')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total_revenue', 'month_key');

        $labels = [];
        $revenue = [];
        $series = [];

        if ($mode === 'year') {
            for ($month = 1; $month <= 12; $month++) {
                $monthDate = \Carbon\Carbon::create($selectedYear, $month, 1)->startOfMonth();
                $monthKey = $monthDate->format('Y-m');
                $label = $this->financeShortMonthLabel($monthDate);
                $value = (float) ($monthlyRows[$monthKey] ?? 0);

                $labels[] = $label;
                $revenue[] = (int) round($value);
                $series[] = [
                    'key' => $monthKey,
                    'label' => $label,
                    'revenue' => (int) round($value),
                ];
            }

            return [
                $labels,
                $revenue,
                $series,
                'Trend Revenue ' . $selectedYear,
                'Total pendapatan gabungan per bulan pada tahun terpilih.',
            ];
        }

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->copy()->startOfMonth()->subMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $label = $this->financeShortMonthLabel($monthDate);
            $value = (float) ($monthlyRows[$monthKey] ?? 0);

            $labels[] = $label;
            $revenue[] = (int) round($value);
            $series[] = [
                'key' => $monthKey,
                'label' => $label,
                'revenue' => (int) round($value),
            ];
        }

        return [
            $labels,
            $revenue,
            $series,
            'Trend Revenue 6 Bulan',
            'Total pendapatan gabungan per bulan.',
        ];
    }

    private function baseFinanceItemQuery($startDate, $endDate)
    {
        return PaymentTransactionItem::query()
            ->join('payment_transactions as pt', 'payment_transaction_items.id_payment_transaction', '=', 'pt.id_payment_transaction')
            ->join('courses as c', 'payment_transaction_items.id_course', '=', 'c.id_course')
            ->whereIn('pt.transaction_status', ['settlement', 'capture'])
            ->where(function ($query) {
                $query->whereNull('pt.fraud_status')
                    ->orWhere('pt.fraud_status', '!=', 'challenge');
            })
            ->whereBetween(DB::raw('COALESCE(pt.paid_at, pt.updated_at, pt.created_at)'), [$startDate, $endDate]);
    }

    private function baseFinanceTransactionQuery($startDate, $endDate)
    {
        return PaymentTransaction::query()
            ->from('payment_transactions as pt')
            ->whereIn('pt.transaction_status', ['settlement', 'capture'])
            ->where(function ($query) {
                $query->whereNull('pt.fraud_status')
                    ->orWhere('pt.fraud_status', '!=', 'challenge');
            })
            ->whereBetween(DB::raw('COALESCE(pt.paid_at, pt.updated_at, pt.created_at)'), [$startDate, $endDate]);
    }

    private function normalizeFinanceCategory(?string $category): string
    {
        $normalized = strtolower(trim((string) $category));
        return match ($normalized) {
            'webinar' => 'webinar',
            'tiket', 'ticket' => 'tiket',
            default => 'kursus',
        };
    }

    private function financeCategoryLabel(string $category): string
    {
        return match ($category) {
            'webinar' => 'Webinar',
            'tiket' => 'Tiket',
            default => 'Kursus',
        };
    }

    private function financeShortMonthLabel($date): string
    {
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $monthNumber = (int) $date->format('n');
        return $months[$monthNumber] ?? $date->format('M');
    }

    private function financeMonthYearLabel(?int $year, ?int $month): string
    {
        if (!$year || !$month) {
            return '-';
        }

        return $this->financeShortMonthLabel(\Carbon\Carbon::create($year, $month, 1)) . ' ' . $year;
    }

    private function financeMonthOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    /**
     * Store new pengumuman
     */
    public function storePengumuman(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'required|in:' . implode(',', self::NEWS_ALLOWED_CATEGORIES),
            'target_prodi' => 'nullable|string|max:100',
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
            'kategori' => $this->normalizeNewsCategory($request->kategori),
            'target_prodi' => $this->normalizeNewsTargetProdi($request->target_prodi),
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
            'target_prodi' => $news->target_prodi,
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
            'kategori' => 'required|in:' . implode(',', self::NEWS_ALLOWED_CATEGORIES),
            'target_prodi' => 'nullable|string|max:100',
            'tanggal_publish' => 'required|date',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable',
        ]);

        $news->judul = $request->judul;
        $news->konten = $request->konten;
        $news->kategori = $this->normalizeNewsCategory($request->kategori);
        $news->target_prodi = $this->normalizeNewsTargetProdi($request->target_prodi);
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
            ->with($type === 'dosen'
                ? ['profile.jurusan', 'profile.jurusans']
                : ['profile.jurusan'])
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $isMahasiswa = ($type === 'mahasiswa');
        $sheet->setTitle(ucfirst($type));

        // Headers
        $headers = $isMahasiswa
            ? ['No', 'Nama', 'Nomor Induk', 'Email', 'Kode Jurusan', 'No. Telepon', 'Status']
            : ['No', 'Nama', 'Nomor Induk', 'Email', 'Kode Jurusan', 'No. Telepon', 'Status'];

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
            $sheet->setCellValueExplicit('C' . $rowNum, $user->profile?->nomor_induk ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $rowNum, $user->email);
            if ($type === 'dosen') {
                $kodeJurusan = '-';
                if ($user->profile?->relationLoaded('jurusans') && $user->profile->jurusans->isNotEmpty()) {
                    $kodeJurusan = $user->profile->jurusans
                        ->pluck('kode_jurusan')
                        ->filter()
                        ->implode(', ');
                } elseif ($user->profile?->relationLoaded('jurusan') && $user->profile?->jurusan?->kode_jurusan) {
                    $kodeJurusan = $user->profile->jurusan->kode_jurusan;
                }

                $sheet->setCellValueExplicit('E' . $rowNum, $kodeJurusan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            } else {
                $sheet->setCellValueExplicit(
                    'E' . $rowNum,
                    $user->profile?->jurusan?->kode_jurusan ?? '-',
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            }
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

    private function normalizeNewsCategory(?string $category): string
    {
        $normalized = strtolower(trim((string) $category));

        return match ($normalized) {
            'keungan' => 'keuangan',
            default => $normalized !== '' ? $normalized : 'pengumuman',
        };
    }

    private function normalizeNewsTargetProdi(?string $targetProdi): ?string
    {
        $normalized = Str::slug(trim((string) $targetProdi));

        return $normalized !== '' && $normalized !== 'all' ? $normalized : null;
    }

    private function courseValidationRules(?int $courseId = null): array
    {
        $kodeCourseRule = 'required|string|max:50|unique:courses,kode_course';
        if ($courseId !== null) {
            $kodeCourseRule .= ',' . $courseId . ',id_course';
        }

        return [
            'nama_course' => 'required|string|max:255',
            'kode_course' => $kodeCourseRule,
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
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'youtube_playlist' => 'nullable|url|max:500',
            'tanggal_webinar' => 'required_if:kategori,webinar|nullable|date',
            'jam_mulai_webinar' => 'required_if:kategori,webinar|nullable|date_format:H:i',
            'jam_selesai_webinar' => 'required_if:kategori,webinar|nullable|date_format:H:i|after:jam_mulai_webinar',
            'kuota_peserta' => 'nullable|integer|min:1',
        ];
    }

    private function buildAdminCoursePayload(Request $request, string $status, ?string $thumbnailPath = null, ?Course $existingCourse = null): array
    {
        $isWebinar = $request->kategori === 'webinar';
        $generatedCode = $isWebinar
            ? $this->generateNextCourseCode('WEB', $existingCourse?->id_course)
            : $this->generateNextCourseCode('KRS', $existingCourse?->id_course);
        $durasiSatuan = !$isWebinar && $request->filled('estimasi_waktu')
            ? ($request->durasi_satuan ?: 'Jam')
            : null;

        $data = [
            'kode_course' => $isWebinar
                ? ($existingCourse?->kode_course ?: $generatedCode)
                : ($request->kode_course ?: $generatedCode),
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
            'estimasi_waktu' => $isWebinar ? null : $request->estimasi_waktu,
            'durasi_satuan' => $durasiSatuan,
            'youtube_playlist' => $request->youtube_playlist,
            'sertifikat' => $request->boolean('sertifikat'),
            'akses_publik' => $request->boolean('akses_publik'),
            'tanggal_webinar' => $isWebinar ? $request->tanggal_webinar : null,
            'jam_mulai_webinar' => $isWebinar ? $request->jam_mulai_webinar : null,
            'jam_selesai_webinar' => $isWebinar ? $request->jam_selesai_webinar : null,
            'kuota_peserta' => $isWebinar ? $request->kuota_peserta : null,
            'approval_status' => $isWebinar
                ? ($status === 'aktif' ? 'disetujui' : ($existingCourse?->approval_status ?? 'tidak_perlu'))
                : 'tidak_perlu',
            'approval_notes' => $isWebinar && $status !== 'aktif'
                ? ($existingCourse?->approval_notes)
                : null,
            'approved_by' => $isWebinar && $status === 'aktif'
                ? Auth::guard('admin')->id()
                : ($existingCourse?->approved_by),
            'approved_at' => $isWebinar && $status === 'aktif'
                ? now()
                : ($existingCourse?->approved_at),
        ];

        if ($thumbnailPath !== null) {
            $data['thumbnail'] = $thumbnailPath;
        }

        if ($existingCourse === null) {
            $data['thumbnail'] = $thumbnailPath;
            $data['rating'] = 0;
            $data['jumlah_ulasan'] = 0;
        }

        return $data;
    }

    private function validateYoutubePlaylistForCourse(Request $request): ?string
    {
        if ($request->kategori !== 'kursus' || !$request->filled('youtube_playlist')) {
            return null;
        }

        if (!YoutubePlaylistService::extractPlaylistId((string) $request->youtube_playlist)) {
            return 'Playlist YouTube untuk kursus tidak valid. Gunakan URL playlist YouTube yang memiliki parameter list=...';
        }

        return null;
    }

    private function syncPlaylistForCourse(Course $course, ?string $playlistUrl, string $kategori): ?string
    {
        $playlistUrl = trim((string) $playlistUrl);

        if ($kategori !== 'kursus' || $playlistUrl === '') {
            $cleanup = YoutubePlaylistService::clearCourseContent($course->id_course);

            if (($cleanup['videos_deleted'] ?? 0) > 0 || ($cleanup['materials_deleted'] ?? 0) > 0) {
                return 'Sinkronisasi playlist lama dibersihkan karena kursus tidak lagi memakai playlist YouTube.';
            }

            return null;
        }

        try {
            $result = YoutubePlaylistService::fetchPlaylistVideos(
                YoutubePlaylistService::extractPlaylistId($playlistUrl)
            );

            if (empty($result['videos'])) {
                return $result['message'] ?? 'Playlist valid tetapi tidak ada video publik yang berhasil diimpor.';
            }

            $sync = YoutubePlaylistService::syncCourseContent($course->id_course, $result['videos']);
            if ($course->youtube_playlist !== $playlistUrl) {
                $course->update(['youtube_playlist' => $playlistUrl]);
            }

            return $this->buildPlaylistSyncSummary($sync, $result['message'] ?? null);
        } catch (\Throwable $e) {
            Log::warning('Auto sync YouTube playlist gagal', [
                'course' => $course->id_course,
                'playlist' => $playlistUrl,
                'error' => $e->getMessage(),
            ]);

            return 'Kursus tersimpan, tetapi sinkronisasi playlist gagal: ' . $e->getMessage();
        }
    }

    private function buildPlaylistSyncSummary(array $sync, ?string $extraMessage = null): string
    {
        $summary = sprintf(
            'Playlist tersinkron: %d video database, %d materi siap diputar, %d video lama dibersihkan.',
            (int) ($sync['database']['total'] ?? 0),
            (int) ($sync['materials']['total'] ?? 0),
            (int) ($sync['materials']['removed'] ?? 0)
        );

        if ($extraMessage) {
            $summary .= ' ' . $extraMessage;
        }

        return $summary;
    }

    // ==========================================
    // Chat Management (Admin)
    // ==========================================

    public function getChatConversations(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $adminId = (int) (Auth::guard('admin')->id() ?? 0);

        $baseMessages = DB::table('messages')
            ->join('users as sender', 'sender.id', '=', 'messages.id_sender')
            ->join('users as receiver', 'receiver.id', '=', 'messages.id_receiver')
            ->where(function ($query) {
                $query
                    ->where(function ($q) {
                        $q->where('sender.role', 'mahasiswa')
                            ->where('receiver.role', 'dosen');
                    })
                    ->orWhere(function ($q) {
                        $q->where('sender.role', 'dosen')
                            ->where('receiver.role', 'mahasiswa');
                    });
            })
            ->orderByDesc('messages.created_at')
            ->get([
                'messages.id_message',
                'messages.content',
                'messages.created_at',
                'sender.id as sender_id',
                'sender.name as sender_name',
                'sender.role as sender_role',
                'receiver.id as receiver_id',
                'receiver.name as receiver_name',
                'receiver.role as receiver_role',
            ]);

        $grouped = [];
        foreach ($baseMessages as $message) {
            $isSenderStudent = $message->sender_role === 'mahasiswa';
            $studentId = (int) ($isSenderStudent ? $message->sender_id : $message->receiver_id);
            $lecturerId = (int) ($isSenderStudent ? $message->receiver_id : $message->sender_id);
            $studentName = $isSenderStudent ? (string) $message->sender_name : (string) $message->receiver_name;
            $lecturerName = $isSenderStudent ? (string) $message->receiver_name : (string) $message->sender_name;
            $conversationKey = $studentId . '-' . $lecturerId;

            if (!isset($grouped[$conversationKey])) {
                $latestIso = $this->toIsoTimestamp($message->created_at);
                $grouped[$conversationKey] = [
                    'id' => $conversationKey,
                    'student_id' => $studentId,
                    'lecturer_id' => $lecturerId,
                    'student_name' => $studentName,
                    'lecturer_name' => $lecturerName,
                    'last_message' => Str::limit((string) $message->content, 90),
                    'last_message_at' => $latestIso ?? now()->toISOString(),
                    'message_count' => 0,
                ];
            }

            $grouped[$conversationKey]['message_count']++;
        }

        if ($adminId > 0 && !empty($grouped)) {
            $adminMessages = Message::query()
                ->where('id_sender', $adminId)
                ->where('content', 'like', '[ADMCHAT:%')
                ->orderByDesc('created_at')
                ->get(['id_message', 'content', 'created_at']);

            foreach ($adminMessages as $adminMessage) {
                $threadMeta = $this->extractAdminThreadMeta((string) $adminMessage->content);
                if ($threadMeta === null || !isset($grouped[$threadMeta['conversation_id']])) {
                    continue;
                }

                $conversation = &$grouped[$threadMeta['conversation_id']];
                $conversation['message_count']++;

                $adminCreatedAtIso = $adminMessage->created_at?->toISOString();
                if (!$adminCreatedAtIso) {
                    continue;
                }

                if (strtotime($adminCreatedAtIso) >= strtotime((string) $conversation['last_message_at'])) {
                    $conversation['last_message'] = Str::limit((string) $threadMeta['content'], 90);
                    $conversation['last_message_at'] = $adminCreatedAtIso;
                }
            }
        }

        $conversations = collect(array_values($grouped))
            ->map(function (array $item) {
                $item['status'] = $this->resolveChatStatus($item['last_message_at']);
                return $item;
            })
            ->when($search !== '', function ($collection) use ($search) {
                $keyword = Str::lower($search);
                return $collection->filter(function (array $item) use ($keyword) {
                    $haystack = Str::lower(
                        implode(' ', [
                            $item['student_name'] ?? '',
                            $item['lecturer_name'] ?? '',
                            $item['last_message'] ?? '',
                        ])
                    );
                    return Str::contains($haystack, $keyword);
                });
            })
            ->sortByDesc('last_message_at')
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    public function getChatConversationMessages(string $conversationId)
    {
        $parsed = $this->parseConversationId($conversationId);
        if ($parsed === null) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation tidak valid.',
            ], 422);
        }

        [$studentId, $lecturerId] = $parsed;
        $participants = $this->resolveConversationParticipants($studentId, $lecturerId);
        if ($participants === null) {
            return response()->json([
                'success' => false,
                'message' => 'Percakapan tidak ditemukan.',
            ], 404);
        }

        $admin = Auth::guard('admin')->user();
        $adminId = (int) $admin->id;
        $threadPrefix = $this->buildAdminThreadPrefix($studentId, $lecturerId);

        $messages = Message::query()
            ->with(['sender:id,name,role'])
            ->where(function ($query) use ($studentId, $lecturerId, $adminId, $threadPrefix) {
                $query
                    ->where(function ($pairQuery) use ($studentId, $lecturerId) {
                        $pairQuery
                            ->where('id_sender', $studentId)
                            ->where('id_receiver', $lecturerId);
                    })
                    ->orWhere(function ($pairQuery) use ($studentId, $lecturerId) {
                        $pairQuery
                            ->where('id_sender', $lecturerId)
                            ->where('id_receiver', $studentId);
                    })
                    ->orWhere(function ($adminQuery) use ($adminId, $threadPrefix) {
                        $adminQuery
                            ->where('id_sender', $adminId)
                            ->where('content', 'like', $threadPrefix . '%');
                    });
            })
            ->orderBy('created_at')
            ->get();

        $payload = $messages->map(function (Message $message) use ($studentId, $lecturerId, $participants) {
            $senderRole = 'admin';
            $senderName = $message->sender?->name ?? 'Admin';
            $content = (string) $message->content;

            if ((int) $message->id_sender === $studentId) {
                $senderRole = 'student';
                $senderName = $participants['student']->name;
            } elseif ((int) $message->id_sender === $lecturerId) {
                $senderRole = 'lecturer';
                $senderName = $participants['lecturer']->name;
            } else {
                $content = $this->stripAdminThreadPrefix($content);
            }

            return [
                'id' => $message->id_message,
                'sender_name' => $senderName,
                'sender_role' => $senderRole,
                'content' => $content,
                'created_at' => $message->created_at?->toISOString(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }

    public function sendAdminChatMessage(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $validated = $request->validate([
            'conversation_id' => ['required', 'string'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $parsed = $this->parseConversationId((string) $validated['conversation_id']);
        if ($parsed === null) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation tidak valid.',
            ], 422);
        }

        [$studentId, $lecturerId] = $parsed;
        $participants = $this->resolveConversationParticipants($studentId, $lecturerId);
        if ($participants === null) {
            return response()->json([
                'success' => false,
                'message' => 'Percakapan tidak ditemukan.',
            ], 404);
        }

        $threadPrefix = $this->buildAdminThreadPrefix($studentId, $lecturerId);
        $content = trim((string) $validated['content']);

        $message = Message::create([
            'id_sender' => $admin->id,
            'id_receiver' => $studentId,
            'content' => $threadPrefix . $content,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id_message,
                'sender_name' => $admin->name ?? 'Admin',
                'sender_role' => 'admin',
                'content' => $content,
                'created_at' => $message->created_at?->toISOString(),
            ],
        ]);
    }

    public function deleteChatMessage(int $messageId)
    {
        $message = Message::find($messageId);
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan.',
            ], 404);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }

    public function purgeChatByRole(Request $request, string $conversationId)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['student', 'lecturer', 'admin'])],
        ]);

        $parsed = $this->parseConversationId($conversationId);
        if ($parsed === null) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation tidak valid.',
            ], 422);
        }

        [$studentId, $lecturerId] = $parsed;
        $participants = $this->resolveConversationParticipants($studentId, $lecturerId);
        if ($participants === null) {
            return response()->json([
                'success' => false,
                'message' => 'Percakapan tidak ditemukan.',
            ], 404);
        }

        $adminId = (int) (Auth::guard('admin')->id() ?? 0);
        $role = (string) $validated['role'];

        $query = Message::query()->whereRaw('1 = 0');
        if ($role === 'student') {
            $query = Message::query()
                ->where('id_sender', $studentId)
                ->where('id_receiver', $lecturerId);
        } elseif ($role === 'lecturer') {
            $query = Message::query()
                ->where('id_sender', $lecturerId)
                ->where('id_receiver', $studentId);
        } elseif ($role === 'admin' && $adminId > 0) {
            $query = Message::query()
                ->where('id_sender', $adminId)
                ->where('content', 'like', $this->buildAdminThreadPrefix($studentId, $lecturerId) . '%');
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus.',
            'data' => ['deleted' => (int) $deleted],
        ]);
    }

    public function deleteChatConversation(string $conversationId)
    {
        $parsed = $this->parseConversationId($conversationId);
        if ($parsed === null) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation tidak valid.',
            ], 422);
        }

        [$studentId, $lecturerId] = $parsed;
        $participants = $this->resolveConversationParticipants($studentId, $lecturerId);
        if ($participants === null) {
            return response()->json([
                'success' => false,
                'message' => 'Percakapan tidak ditemukan.',
            ], 404);
        }

        $adminId = (int) (Auth::guard('admin')->id() ?? 0);
        $threadPrefix = $this->buildAdminThreadPrefix($studentId, $lecturerId);

        $deletedDirectMessages = Message::query()
            ->where(function ($query) use ($studentId, $lecturerId) {
                $query
                    ->where(function ($q) use ($studentId, $lecturerId) {
                        $q->where('id_sender', $studentId)->where('id_receiver', $lecturerId);
                    })
                    ->orWhere(function ($q) use ($studentId, $lecturerId) {
                        $q->where('id_sender', $lecturerId)->where('id_receiver', $studentId);
                    });
            })
            ->delete();

        $deletedAdminMessages = 0;
        if ($adminId > 0) {
            $deletedAdminMessages = Message::query()
                ->where('id_sender', $adminId)
                ->where('content', 'like', $threadPrefix . '%')
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Percakapan berhasil dihapus.',
            'data' => [
                'deleted' => (int) ($deletedDirectMessages + $deletedAdminMessages),
            ],
        ]);
    }

    private function parseConversationId(string $conversationId): ?array
    {
        if (preg_match('/^(\d+)-(\d+)$/', trim($conversationId), $matches) !== 1) {
            return null;
        }

        $studentId = (int) $matches[1];
        $lecturerId = (int) $matches[2];

        if ($studentId <= 0 || $lecturerId <= 0) {
            return null;
        }

        return [$studentId, $lecturerId];
    }

    private function resolveConversationParticipants(int $studentId, int $lecturerId): ?array
    {
        $student = User::where('id', $studentId)->where('role', 'mahasiswa')->first();
        $lecturer = User::where('id', $lecturerId)->where('role', 'dosen')->first();

        if (!$student || !$lecturer) {
            return null;
        }

        return [
            'student' => $student,
            'lecturer' => $lecturer,
        ];
    }

    private function buildAdminThreadPrefix(int $studentId, int $lecturerId): string
    {
        return '[ADMCHAT:' . $studentId . '-' . $lecturerId . '] ';
    }

    private function stripAdminThreadPrefix(string $content): string
    {
        return preg_replace('/^\[ADMCHAT:\d+\-\d+\]\s*/', '', $content) ?? $content;
    }

    private function extractAdminThreadMeta(string $content): ?array
    {
        if (preg_match('/^\[ADMCHAT:(\d+\-\d+)\]\s*(.*)$/s', $content, $matches) !== 1) {
            return null;
        }

        return [
            'conversation_id' => $matches[1],
            'content' => trim((string) $matches[2]),
        ];
    }

    private function resolveChatStatus(?string $lastMessageAt): string
    {
        if (!$lastMessageAt) {
            return 'idle';
        }

        try {
            $last = \Carbon\Carbon::parse($lastMessageAt);
            $minutes = $last->diffInMinutes(now());
            if ($minutes <= 30) {
                return 'ongoing';
            }

            if ($minutes <= 24 * 60) {
                return 'active24h';
            }
        } catch (\Throwable $exception) {
            return 'idle';
        }

        return 'idle';
    }

    private function toIsoTimestamp(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return \Carbon\Carbon::instance($value)->toISOString();
        }

        if (is_string($value) && trim($value) !== '') {
            try {
                return \Carbon\Carbon::parse($value)->toISOString();
            } catch (\Throwable $exception) {
                return null;
            }
        }

        return null;
    }

    private function resolveEnrollmentStatusForCourse(Course $course): string
    {
        return $course->tipe === 'berbayar' ? 'pending' : 'aktif';
    }

    private function generateNextCourseCode(string $prefix, ?int $ignoreCourseId = null): string
    {
        $latestCode = Course::query()
            ->when($ignoreCourseId !== null, fn ($query) => $query->where('id_course', '!=', $ignoreCourseId))
            ->where('kode_course', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(kode_course) DESC')
            ->orderByDesc('kode_course')
            ->value('kode_course');

        $lastNumber = 0;

        if (is_string($latestCode) && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $latestCode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return $prefix . str_pad((string) ($lastNumber + 1), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Logout
     */
    public function logout(Request $request, DeviceSessionLimitService $deviceSessionLimitService)
    {
        $currentSessionId = $request->session()->getId();
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $deviceSessionLimitService->deleteSessionById($currentSessionId);

        return redirect()->route('admin.login')
            ->with('status', 'Logout berhasil.');
    }
}
