<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        return view('Auth.admin.dashboard', [
            'admin' => $admin
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
                'status' => $dosen->status === 'aktif' ? 'Aktif' : 'Nonaktif',
            ];
        });

        // Get jurusan list for dropdown
        $jurusanList = \App\Models\Jurusan::all();

        return view('Auth.admin.dosen', [
            'admin' => $admin,
            'dosenList' => $dosenList,
            'dosenPaginated' => $dosenPaginated,
            'totalDosen' => $dosenPaginated->total(),
            'currentPage' => $dosenPaginated->currentPage(),
            'perPage' => $perPage,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
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
            'nip' => 'required|string|max:50',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
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
            ->with('success', 'Dosen berhasil ditambahkan! Password default: password123');
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'required|string|max:50',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:aktif,nonaktif',
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

        $mahasiswaPaginated = $query->paginate($perPage);

        // Transform data for view
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

        return view('Auth.admin.mahasiswa', [
            'admin' => $admin,
            'mahasiswaList' => $mahasiswaList,
            'mahasiswaPaginated' => $mahasiswaPaginated,
            'totalMahasiswa' => $mahasiswaPaginated->total(),
            'currentPage' => $mahasiswaPaginated->currentPage(),
            'perPage' => $perPage,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
            'jurusanList' => $jurusanList,
            'courseList' => $courseList,
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
            'nim' => 'required|string|max:50',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
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
            ->with('success', 'Mahasiswa berhasil ditambahkan! Password default: password123');
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
            'nim' => 'required|string|max:50',
            'id_jurusan' => 'required|exists:jurusans,id_jurusan',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:aktif,nonaktif',
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
        
        $query = \App\Models\Course::with(['dosen', 'jurusan']);
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by tipe
        if ($request->tipe && $request->tipe !== 'all') {
            $query->where('tipe', $request->tipe);
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
            return [
                'id' => $kursus->id_course,
                'kode' => $kursus->kode_course,
                'nama' => $kursus->nama_course,
                'thumbnail' => $kursus->thumbnail,
                'dosen' => $kursus->dosen?->name ?? '-',
                'jurusan' => $kursus->jurusan?->nama_jurusan ?? '-',
                'tipe' => $kursus->tipe,
                'harga' => $kursus->harga,
                'status' => $kursus->status,
                'rating' => $kursus->rating,
                'jumlah_ulasan' => $kursus->jumlah_ulasan,
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
            'id_dosen' => 'nullable|exists:users,id',
            'id_jurusan' => 'nullable|exists:jurusans,id_jurusan',
            'tipe' => 'required|in:gratis,berbayar',
            'harga' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,draft,nonaktif',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        \App\Models\Course::create([
            'kode_course' => $request->kode_course,
            'nama_course' => $request->nama_course,
            'deskripsi' => $request->deskripsi,
            'id_dosen' => $request->id_dosen,
            'id_jurusan' => $request->id_jurusan,
            'tipe' => $request->tipe,
            'harga' => $request->tipe === 'berbayar' ? $request->harga : 0,
            'status' => $request->status,
            'thumbnail' => $thumbnailPath,
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
            'id_dosen' => $kursus->id_dosen,
            'id_jurusan' => $kursus->id_jurusan,
            'tipe' => $kursus->tipe,
            'harga' => $kursus->harga,
            'status' => $kursus->status,
            'thumbnail' => $kursus->thumbnail,
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
            'id_dosen' => 'nullable|exists:users,id',
            'id_jurusan' => 'nullable|exists:jurusans,id_jurusan',
            'tipe' => 'required|in:gratis,berbayar',
            'harga' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,draft,nonaktif',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            'id_dosen' => $request->id_dosen,
            'id_jurusan' => $request->id_jurusan,
            'tipe' => $request->tipe,
            'harga' => $request->tipe === 'berbayar' ? $request->harga : 0,
            'status' => $request->status,
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
