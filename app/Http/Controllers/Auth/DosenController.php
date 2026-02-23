<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\DosenNotification;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class DosenController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('Auth.dosen.login');
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
            ->where('role', 'dosen')
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('alert', 'Email tidak terdaftar sebagai dosen.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->with('alert', 'Password salah. Silakan coba lagi.');
        }

        if ($user->status !== 'aktif') {
            return back()
                ->withInput()
                ->with('alert', 'Akun Anda sedang tidak aktif. Hubungi admin.');
        }

        // Login using Laravel Auth guard
        Auth::guard('dosen')->login($user, $request->boolean('remember'));

        return redirect()->route('dosen.dashboard')
            ->with('status', 'Login berhasil. Selamat datang!');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())
                ->where('role', 'dosen')
                ->first();

            if (!$user) {
                return redirect()->route('dosen.login')
                    ->with('alert', 'Email tidak terdaftar sebagai dosen. Silakan hubungi admin.');
            }

            if ($user->status !== 'aktif') {
                return redirect()->route('dosen.login')
                    ->with('alert', 'Akun Anda sedang tidak aktif. Hubungi admin.');
            }

            // Update google_id if not set
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }

            Auth::guard('dosen')->login($user, true);

            return redirect()->route('dosen.dashboard')
                ->with('status', 'Login dengan Google berhasil. Selamat datang!');

        } catch (\Exception $e) {
            return redirect()->route('dosen.login')
                ->with('alert', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }

    /**
     * Show dashboard
     */
    public function showDashboard()
    {
        $dosen = Auth::guard('dosen')->user();

        // Get courses taught by this dosen
        $courses = \App\Models\Course::where('id_dosen', $dosen->id)
            ->with(['enrollments', 'jurusan'])
            ->get();
        
        $totalCourses = $courses->count();
        
        // Calculate total enrolled students
        $totalMahasiswa = 0;
        $totalProgress = 0;
        $progressCount = 0;
        
        $coursesData = [];
        foreach ($courses as $course) {
            $enrollmentCount = $course->enrollments->count();
            $avgProgress = $course->enrollments->avg('progress') ?? 0;
            
            $totalMahasiswa += $enrollmentCount;
            if ($enrollmentCount > 0) {
                $totalProgress += $avgProgress;
                $progressCount++;
            }
            
            $coursesData[] = [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'kode' => $course->kode_course,
                'thumbnail' => $course->thumbnail,
                'mahasiswa_count' => $enrollmentCount,
                'progress_avg' => round($avgProgress),
                'status' => $course->status,
            ];
        }
        
        $avgTotalProgress = $progressCount > 0 ? round($totalProgress / $progressCount) : 0;
        
        // Get recent student progress
        $recentProgress = \App\Models\Enrollment::whereIn('id_course', $courses->pluck('id_course'))
            ->with(['mahasiswa.profile', 'course'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($enrollment) {
                return [
                    'nama' => $enrollment->mahasiswa?->name ?? 'Unknown',
                    'foto' => $enrollment->mahasiswa?->profile?->foto_profile,
                    'course' => $enrollment->course?->nama_course ?? '-',
                    'progress' => round($enrollment->progress ?? 0),
                    'updated' => $enrollment->updated_at?->diffForHumans() ?? '-',
                ];
            });
        
        // Get upcoming schedules (placeholder - using Agenda model if exists)
        $upcomingSchedules = [];
        try {
            $schedules = \App\Models\Agenda::where('id_dosen', $dosen->id)
                ->where('tanggal', '>=', now())
                ->orderBy('tanggal')
                ->take(3)
                ->get();
            
            foreach ($schedules as $schedule) {
                $startTime = $schedule->jam_mulai ?? $schedule->waktu_mulai ?? '09:00';
                $endTime = $schedule->jam_selesai ?? $schedule->waktu_selesai ?? '11:00';
                $courseId = $schedule->id_course
                    ?? $schedule->course?->id_course
                    ?? null;

                $upcomingSchedules[] = [
                    'id' => $courseId,
                    'course' => $schedule->course?->nama_course ?? $schedule->judul ?? 'Kursus',
                    'tanggal' => $schedule->tanggal?->translatedFormat('l, d F Y') ?? '-',
                    'waktu' => $startTime . ' - ' . $endTime . ' WIB',
                ];
            }
        } catch (\Exception $e) {
            // Agenda model might not have these fields
        }

        return view('Auth.dosen.dashboard', [
            'dosen' => $dosen,
            'totalCourses' => $totalCourses,
            'totalMahasiswa' => $totalMahasiswa,
            'avgProgress' => $avgTotalProgress,
            'coursesData' => array_slice($coursesData, 0, 3),
            'recentProgress' => $recentProgress,
            'upcomingSchedules' => $upcomingSchedules,
        ]);
    }

    /**
     * Show dosen profile page
     */
    public function showProfile()
    {
        $dosen = Auth::guard('dosen')->user();

        return view('Auth.dosen.profile', ['dosen' => $dosen]);
    }

    /**
     * Update dosen profile
     */
    public function updateProfile(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->id,
            'no_hp' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ], [
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'foto.image' => 'File harus berupa gambar.',
        ]);

        $dosen->name = $request->name;
        $dosen->email = $request->email;

        $profileData = [
            'no_hp' => $request->no_hp,
            'bio' => $request->bio,
        ];

        // Handle profile photo upload
        if ($request->hasFile('foto')) {
            if ($dosen->profile?->foto_profile && \Storage::disk('public')->exists($dosen->profile->foto_profile)) {
                \Storage::disk('public')->delete($dosen->profile->foto_profile);
            }

            $profileData['foto_profile'] = $request->file('foto')->store('dosen-photos', 'public');
        }

        // Update/create profile record
        if ($dosen->profile) {
            $dosen->profile->update($profileData);
        } else {
            $dosen->profile()->create($profileData);
        }

        // Handle password change
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $dosen->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'Password lama tidak sesuai.']);
            }

            $dosen->password = Hash::make($request->new_password);
        }

        $dosen->save();

        return redirect()
            ->route('dosen.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('dosen')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dosen.login')
            ->with('status', 'Logout berhasil.');
    }

    /**
     * Show Kursus Saya page
     */
    public function showKursusSaya(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'all');
        $sortBy = $request->input('sort', 'terbaru');
        $perPage = 6;

        $query = \App\Models\Course::where('id_dosen', $dosen->id)
            ->with(['enrollments', 'jurusan']);

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_course', 'like', "%{$search}%")
                  ->orWhere('kode_course', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Sorting
        switch ($sortBy) {
            case 'terlama':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nama':
                $query->orderBy('nama_course', 'asc');
                break;
            case 'mahasiswa':
                $query->withCount('enrollments')->orderBy('enrollments_count', 'desc');
                break;
            default: // terbaru
                $query->orderBy('created_at', 'desc');
        }

        $coursesPaginated = $query->paginate($perPage)->withQueryString();

        $coursesData = $coursesPaginated->map(function($course) {
            $enrollmentCount = $course->enrollments->count();
            $avgProgress = $course->enrollments->avg('progress') ?? 0;
            
            return [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'kode' => $course->kode_course,
                'deskripsi' => \Str::limit($course->deskripsi, 60),
                'thumbnail' => $course->thumbnail,
                'mahasiswa_count' => $enrollmentCount,
                'progress_avg' => round($avgProgress),
                'status' => $course->status,
            ];
        });

        return view('Auth.dosen.kursus-saya', [
            'dosen' => $dosen,
            'coursesData' => $coursesData,
            'coursesPaginated' => $coursesPaginated,
            'totalCourses' => \App\Models\Course::where('id_dosen', $dosen->id)->count(),
            'search' => $search,
            'statusFilter' => $statusFilter,
            'sortBy' => $sortBy,
        ]);
    }

    /**
     * Get Kursus detail for API
     */
    public function getKursusDetail(Request $request, $id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->with([
                'enrollments.mahasiswa.profile',
                'materials',
                'modules.materials',
                'jurusan',
            ])
            ->first();

        if (!$course) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Kursus tidak ditemukan'], 404);
            }

            return redirect()
                ->route('dosen.kursus')
                ->with('error', 'Kursus tidak ditemukan');
        }

        $detailData = [
            'id_course' => $course->id_course,
            'nama_course' => $course->nama_course,
            'kode_course' => $course->kode_course,
            'deskripsi' => $course->deskripsi,
            'thumbnail' => $course->thumbnail,
            'status' => $course->status,
            'tipe' => $course->tipe,
            'kategori' => $course->kategori,
            'harga' => $course->harga,
            'jurusan' => $course->jurusan?->nama_jurusan,
            'mahasiswa_count' => $course->enrollments->count(),
            'progress_avg' => round($course->enrollments->avg('progress') ?? 0),
            'materials_count' => $course->materials?->count() ?? 0,
            'modules_count' => $course->modules?->count() ?? 0,
        ];

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json($detailData);
        }

        return view('Auth.dosen.detail-kursus', [
            'course' => $course,
            'detail' => $detailData,
        ]);
    }

    /**
     * Show Kelola Modul page
     */
    public function showKelolaModul($id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->with(['enrollments', 'materials' => function($q) {
                $q->orderBy('urutan', 'asc');
            }])
            ->first();

        if (!$course) {
            return redirect()->route('dosen.kursus')->with('error', 'Kursus tidak ditemukan');
        }

        // Group materials by section (using first digit of urutan as section number)
        $materials = $course->materials->map(function($material) {
            return [
                'id' => $material->id_material,
                'judul' => $material->judul_material,
                'tipe' => $material->tipe,
                'konten' => $material->konten,
                'video_url' => $material->video_url,
                'urutan' => $material->urutan,
                'durasi' => $material->durasi,
            ];
        });

        return view('Auth.dosen.kelola-modul', [
            'dosen' => $dosen,
            'course' => [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'kode' => $course->kode_course,
                'status' => $course->status,
                'mahasiswa_count' => $course->enrollments->count(),
            ],
            'materials' => $materials,
        ]);
    }

    /**
     * Get Modul/Material detail
     */
    public function getMaterialDetail($courseId, $materialId)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $material = \App\Models\CourseMaterial::where('id_material', $materialId)
            ->whereHas('course', function($q) use ($dosen, $courseId) {
                $q->where('id_dosen', $dosen->id)->where('id_course', $courseId);
            })
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
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->first();

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
        $dosen = Auth::guard('dosen')->user();
        
        $module = \App\Models\CourseModule::where('id_module', $moduleId)
            ->where('id_course', $courseId)
            ->whereHas('course', function($q) use ($dosen) {
                $q->where('id_dosen', $dosen->id);
            })
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
        $dosen = Auth::guard('dosen')->user();
        
        $module = \App\Models\CourseModule::where('id_module', $moduleId)
            ->where('id_course', $courseId)
            ->whereHas('course', function($q) use ($dosen) {
                $q->where('id_dosen', $dosen->id);
            })
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
        $dosen = Auth::guard('dosen')->user();
        
        // Verify course belongs to dosen
        $course = \App\Models\Course::where('id_course', $courseId)
            ->where('id_dosen', $dosen->id)
            ->first();

        if (!$course) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->first();

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
        $dosen = Auth::guard('dosen')->user();
        
        $material = \App\Models\CourseMaterial::where('id_material', $materialId)
            ->whereHas('course', function($q) use ($dosen, $courseId) {
                $q->where('id_dosen', $dosen->id)->where('id_course', $courseId);
            })
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
        $dosen = Auth::guard('dosen')->user();
        
        // Verify course belongs to dosen
        $course = \App\Models\Course::where('id_course', $courseId)
            ->where('id_dosen', $dosen->id)
            ->first();

        if (!$course) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
        $dosen = Auth::guard('dosen')->user();
        
        $material = \App\Models\CourseMaterial::where('id_material', $materialId)
            ->whereHas('course', function($q) use ($dosen, $courseId) {
                $q->where('id_dosen', $dosen->id)->where('id_course', $courseId);
            })
            ->first();

        if (!$material) {
            return back()->with('error', 'Material tidak ditemukan');
        }

        $material->delete();

        return back()->with('success', 'Material berhasil dihapus');
    }

    /**
     * Publish course (change status to aktif)
     */
    public function publishCourse($id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->first();

        if (!$course) {
            return back()->with('error', 'Kursus tidak ditemukan');
        }

        // Check if course has at least one material
        $materialsCount = \App\Models\CourseMaterial::where('id_course', $id)->count();
        if ($materialsCount === 0) {
            return back()->with('error', 'Tambahkan minimal 1 modul sebelum mempublikasikan kursus');
        }

        $course->status = 'aktif';
        $course->save();

        return back()->with('success', 'Kursus berhasil dipublikasikan!');
    }

    /**
     * Show Buat Kursus page
     */
    public function showBuatKursus()
    {
        $dosen = Auth::guard('dosen')->user();
        $jurusans = \App\Models\Jurusan::all();
        
        return view('Auth.dosen.buat-kursus', [
            'dosen' => $dosen,
            'jurusans' => $jurusans,
        ]);
    }

    /**
     * Store new course
     */
    public function storeCourse(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();

        $typeRoutes = [
            'video' => 'dosen.kelola-video',
            'bacaan' => 'dosen.kelola-bacaan',
            'kuis' => 'dosen.kelola-quiz',
            'tugas' => 'dosen.kelola-tugas',
        ];

        $redirectToTypedPage = $request->filled('modul_judul') && isset($typeRoutes[$request->modul_tipe]);
        
        $request->validate([
            'nama_course' => 'required|string|max:255',
            'kode_course' => 'required|string|max:50|unique:courses,kode_course',
            'deskripsi' => 'nullable|string',
            'id_jurusan' => 'nullable|integer|exists:jurusans,id_jurusan',
            'tipe' => 'required|in:gratis,berbayar',
            'kategori' => 'required|in:webinar,tiket,kursus',
            'harga' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'level' => 'nullable|in:Pemula,Menengah,Mahir',
            'estimasi_waktu' => 'nullable|integer|min:0',
            'diskon' => 'nullable|integer|min:0|max:100',
            'youtube_playlist' => 'nullable|url|max:500',
            'modul_judul' => 'nullable|string|max:255',
            'modul_tipe' => 'nullable|in:video,bacaan,kuis,tugas',
            'modul_konten' => 'nullable|string',
            'modul_video_url' => 'nullable|url|max:500|exclude_unless:modul_tipe,video',
            'modul_durasi' => 'nullable|integer|min:0|exclude_if:modul_tipe,tugas',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course = DB::transaction(function () use ($dosen, $request, $thumbnailPath, $redirectToTypedPage) {
            $course = \App\Models\Course::create([
                'id_dosen' => $dosen->id,
                'nama_course' => $request->nama_course,
                'kode_course' => $request->kode_course,
                'deskripsi' => $request->deskripsi,
                'id_jurusan' => $request->id_jurusan,
                'tipe' => $request->tipe,
                'kategori' => $request->kategori,
                'harga' => $request->tipe === 'berbayar' ? $request->harga : 0,
                'thumbnail' => $thumbnailPath,
                'status' => $request->status ?? 'draft',
                'level' => $request->level,
                'estimasi_waktu' => $request->estimasi_waktu,
                'sertifikat' => $request->boolean('sertifikat'),
                'akses_publik' => $request->boolean('akses_publik'),
                'diskon' => $request->diskon ?? 0,
                'youtube_playlist' => $request->youtube_playlist,
            ]);

            // Build "Modul Utama" when initial structure fields are filled.
            $hasInitialStructure =
                $request->filled('modul_judul') ||
                $request->filled('modul_konten') ||
                $request->filled('modul_video_url') ||
                $request->filled('modul_durasi');

            if ($hasInitialStructure) {
                $mainModule = \App\Models\CourseModule::create([
                    'id_course' => $course->id_course,
                    'judul_module' => 'Modul Utama',
                    'deskripsi' => 'Modul default untuk materi awal kursus',
                    'urutan' => 1,
                ]);

                // If title provided, create first material inside the main module.
                // Prevent duplicate first material:
                // when user is redirected to typed content page, that page will create the material.
                if ($request->filled('modul_judul') && !$redirectToTypedPage) {
                    $materialType = $request->modul_tipe ?? 'video';

                    \App\Models\CourseMaterial::create([
                        'id_course' => $course->id_course,
                        'id_module' => $mainModule->id_module,
                        'judul_material' => $request->modul_judul,
                        'tipe' => $materialType,
                        'konten' => $request->modul_konten,
                        'video_url' => $materialType === 'video' ? $request->modul_video_url : null,
                        'durasi' => $materialType === 'tugas' ? null : $request->modul_durasi,
                        'urutan' => 1,
                    ]);
                }
            }

            return $course;
        });

        if ($redirectToTypedPage) {
            $redirectParams = array_filter([
                'course_id' => $course->id_course,
                'modul_judul' => $request->modul_judul,
                'modul_konten' => $request->modul_konten,
                'modul_video_url' => $request->modul_video_url,
                'modul_durasi' => $request->modul_durasi,
            ], static function ($value) {
                return $value !== null && $value !== '';
            });

            return redirect()->route($typeRoutes[$request->modul_tipe], $redirectParams)
                ->with('success', 'Kursus berhasil dibuat! Lanjutkan pengelolaan konten sesuai tipe modul.');
        }

        return redirect()->route('dosen.kursus.modul', $course->id_course)
            ->with('success', 'Kursus berhasil dibuat! Silakan tambahkan modul.');
    }

    /**
     * Show Edit Kursus page
     */
    public function showEditKursus($id)
    {
        $dosen = Auth::guard('dosen')->user();
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->with(['modules.materials' => function($q) {
                $q->orderBy('urutan');
            }])
            ->firstOrFail();

        $this->ensureMainModuleForLegacyCourse($course);
        $course->load(['modules.materials' => function($q) {
            $q->orderBy('urutan');
        }]);

        $jurusans = \App\Models\Jurusan::all();
        // $materials = $course->materials()->orderBy('urutan')->get(); // No longer needed as primary source?
        
        return view('Auth.dosen.edit-kursus', compact('course', 'jurusans'));
    }

    /**
     * Ensure legacy courses still have a main module.
     * Old data may contain materials without id_module.
     */
    private function ensureMainModuleForLegacyCourse(\App\Models\Course $course): void
    {
        $hasUnlinkedMaterials = \App\Models\CourseMaterial::where('id_course', $course->id_course)
            ->where(function ($query) {
                $query->whereNull('id_module')
                    ->orWhereDoesntHave('module');
            })
            ->exists();

        if (!$hasUnlinkedMaterials) {
            return;
        }

        DB::transaction(function () use ($course) {
            $targetModule = \App\Models\CourseModule::where('id_course', $course->id_course)
                ->orderBy('urutan')
                ->first();

            if (!$targetModule) {
                $targetModule = \App\Models\CourseModule::create([
                    'id_course' => $course->id_course,
                    'judul_module' => 'Modul Utama',
                    'deskripsi' => 'Modul otomatis untuk data kursus lama',
                    'urutan' => 1,
                ]);
            }

            $materialIds = \App\Models\CourseMaterial::where('id_course', $course->id_course)
                ->where(function ($query) {
                    $query->whereNull('id_module')
                        ->orWhereDoesntHave('module');
                })
                ->pluck('id_material');

            if ($materialIds->isNotEmpty()) {
                \App\Models\CourseMaterial::whereIn('id_material', $materialIds)
                    ->update(['id_module' => $targetModule->id_module]);
            }
        });
    }

    /**
     * Update course
     */
    public function updateCourse(Request $request, $id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->first();

        if (!$course) {
            return back()->with('error', 'Kursus tidak ditemukan');
        }

        $request->validate([
            'nama_course' => 'required|string|max:255',
            'kode_course' => 'required|string|max:50|unique:courses,kode_course,' . $id . ',id_course',
            'deskripsi' => 'nullable|string',
            'id_jurusan' => 'nullable|integer|exists:jurusans,id_jurusan',
            'tipe' => 'required|in:gratis,berbayar',
            'kategori' => 'required|in:webinar,tiket,kursus',
            'harga' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,aktif,nonaktif',
            'thumbnail' => 'nullable|image|max:2048',
            'estimasi_waktu' => 'nullable|integer|min:0',
            'durasi_satuan' => 'nullable|in:Minggu,Jam',
            'level' => 'nullable|in:Pemula,Menengah,Mahir',
            'diskon' => 'nullable|integer|min:0|max:100',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                \Storage::disk('public')->delete($course->thumbnail);
            }
            $course->thumbnail = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course->update([
            'nama_course' => $request->nama_course,
            'kode_course' => $request->kode_course,
            'deskripsi' => $request->deskripsi,
            'id_jurusan' => $request->id_jurusan,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'harga' => $request->tipe === 'berbayar' ? $request->harga : 0,
            'status' => $request->status,
            'estimasi_waktu' => $request->estimasi_waktu,
            'durasi_satuan' => $request->durasi_satuan,
            'level' => $request->level,
            'sertifikat' => $request->boolean('sertifikat'),
            'akses_publik' => $request->boolean('akses_publik'),
            'diskon' => $request->diskon ?? 0,
        ]);

        return back()->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Preview course (public view)
     */
    public function previewKursus($id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->with(['materials' => function($q) {
                $q->orderBy('urutan');
            }, 'enrollments', 'jurusan'])
            ->first();

        if (!$course) {
            return redirect()->route('dosen.kursus')->with('error', 'Kursus tidak ditemukan');
        }

        return view('Auth.dosen.preview-kursus', [
            'dosen' => $dosen,
            'course' => $course,
        ]);
    }

    /**
     * Show progress for a specific course
     */
    public function showProgresKursus($id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $course = \App\Models\Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->first();

        if (!$course) {
            return redirect()->route('dosen.kursus')->with('error', 'Kursus tidak ditemukan');
        }

        $enrollments = \App\Models\Enrollment::where('id_course', $id)
            ->with(['mahasiswa.profile'])
            ->orderBy('progress', 'desc')
            ->get()
            ->map(function($enrollment) {
                return [
                    'id' => $enrollment->id_enroll,
                    'nama' => $enrollment->mahasiswa?->name ?? 'Unknown',
                    'foto' => $enrollment->mahasiswa?->profile?->foto_profile,
                    'progress' => round($enrollment->progress ?? 0),
                    'status' => $enrollment->status,
                    'tanggal_daftar' => $enrollment->tanggal_daftar?->format('d M Y') ?? '-',
                ];
            });

        return view('Auth.dosen.progres-kursus', [
            'dosen' => $dosen,
            'course' => $course,
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * Show all student progress (across all courses)
     */
    public function showProgresMahasiswa(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $courses = \App\Models\Course::where('id_dosen', $dosen->id)->pluck('id_course');
        
        $search = $request->input('search');
        $courseFilter = $request->input('course', 'all');
        
        $query = \App\Models\Enrollment::whereIn('id_course', $courses)
            ->with(['mahasiswa.profile', 'course']);
        
        if ($search) {
            $query->whereHas('mahasiswa', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        if ($courseFilter !== 'all') {
            $query->where('id_course', $courseFilter);
        }
        
        $enrollments = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();
        
        $coursesForFilter = \App\Models\Course::where('id_dosen', $dosen->id)
            ->select('id_course', 'nama_course')
            ->get();

        // Stats counts
        $totalEnrollments = \App\Models\Enrollment::whereIn('id_course', $courses)->count();
        $selesaiCount = \App\Models\Enrollment::whereIn('id_course', $courses)->where('progress', '>=', 100)->count();
        $aktifCount = \App\Models\Enrollment::whereIn('id_course', $courses)->where('progress', '>', 0)->where('progress', '<', 100)->count();
        $tidakAktifCount = \App\Models\Enrollment::whereIn('id_course', $courses)->where('progress', '<=', 0)->count();
        $avgProgress = $totalEnrollments > 0
            ? round(\App\Models\Enrollment::whereIn('id_course', $courses)->avg('progress'))
            : 0;

        return view('Auth.dosen.progres-mahasiswa', [
            'dosen' => $dosen,
            'enrollments' => $enrollments,
            'coursesForFilter' => $coursesForFilter,
            'search' => $search,
            'courseFilter' => $courseFilter,
            'totalEnrollments' => $totalEnrollments,
            'selesaiCount' => $selesaiCount,
            'aktifCount' => $aktifCount,
            'tidakAktifCount' => $tidakAktifCount,
            'avgProgress' => $avgProgress,
        ]);
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('Auth.dosen.forgot-password');
    }

    /**
     * Send OTP for password reset
     */
    public function sendForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)
            ->where('role', 'dosen')
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak ditemukan sebagai dosen.']);
        }

        // Generate 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        // Send OTP via email
        try {
            Mail::to($email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi nanti.']);
        }

        // Store email in session for verification
        session(['dosen_reset_email' => $email]);

        return redirect()->route('dosen.verify-otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtpForm()
    {
        if (!session('dosen_reset_email')) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Silakan masukkan email terlebih dahulu.');
        }

        return view('Auth.dosen.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:4'],
        ]);

        $email = session('dosen_reset_email');
        $otp = $request->otp;

        if (!$email) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid. Silakan coba lagi.']);
        }

        // Check if OTP is expired (5 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.']);
        }

        // Verify OTP
        if (!Hash::check($otp, $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali.']);
        }

        // Generate verification token for reset password
        $verificationToken = Str::random(64);
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($verificationToken),
                'created_at' => now()
            ]);

        session(['dosen_reset_token' => $verificationToken]);

        return redirect()->route('dosen.reset-password')
            ->with('status', 'OTP valid. Silakan masukkan password baru.');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm()
    {
        if (!session('dosen_reset_email') || !session('dosen_reset_token')) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        return view('Auth.dosen.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = session('dosen_reset_email');
        $token = session('dosen_reset_token');

        if (!$email || !$token) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Token tidak valid. Silakan ulangi proses.');
        }

        // Update user password
        $user = User::where('email', $email)
            ->where('role', 'dosen')
            ->first();

        if (!$user) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'User tidak ditemukan.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Clear session
        session()->forget(['dosen_reset_email', 'dosen_reset_token']);

        return redirect()->route('dosen.login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    // ==========================================
    // NOTIFICATIONS
    // ==========================================

    /**
     * Get notifications list (JSON for dropdown)
     */
    public function getNotifications()
    {
        $dosen = Auth::guard('dosen')->user();

        $notifications = DosenNotification::where('dosen_id', $dosen->id)
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

        $unreadCount = DosenNotification::where('dosen_id', $dosen->id)->unread()->count();

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
        $dosen = Auth::guard('dosen')->user();
        $count = DosenNotification::where('dosen_id', $dosen->id)->unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a single notification as read
     */
    public function markNotificationRead($id)
    {
        $dosen = Auth::guard('dosen')->user();
        DosenNotification::where('id', $id)->where('dosen_id', $dosen->id)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $dosen = Auth::guard('dosen')->user();
        DosenNotification::where('dosen_id', $dosen->id)->unread()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // ==========================================
    // MESSAGE UNREAD COUNT (for header badge)
    // ==========================================

    /**
     * Get unread message count (JSON for header badge)
     */
    public function getUnreadMessageCount()
    {
        $dosen = Auth::guard('dosen')->user();
        $count = Message::where('id_receiver', $dosen->id)->where('is_read', false)->count();

        return response()->json(['count' => $count]);
    }

    // ==========================================
    // MESSAGING (for pesan page)
    // ==========================================

    /**
     * Get conversations list (JSON for chat sidebar)
     */
    public function getConversations(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        $dosenId = $dosen->id;
        $search = $request->query('search');

        // Get students who have conversations with this dosen
        $conversationStudents = Message::where('id_sender', $dosenId)
            ->orWhere('id_receiver', $dosenId)
            ->selectRaw('CASE WHEN id_sender = ? THEN id_receiver ELSE id_sender END as student_id', [$dosenId])
            ->distinct()
            ->pluck('student_id');

        // Also include students enrolled in dosen's courses (potential contacts)
        $dosenCourseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');
        $enrolledStudents = Enrollment::whereIn('id_course', $dosenCourseIds)
            ->pluck('id_mahasiswa');

        $allStudentIds = $conversationStudents->merge($enrolledStudents)->unique();

        // Query students
        $studentsQuery = User::whereIn('id', $allStudentIds)
            ->where('role', 'mahasiswa')
            ->with('profile');

        if ($search) {
            $studentsQuery->where('name', 'like', "%{$search}%");
        }

        $students = $studentsQuery->get();

        // Build conversations data
        $conversations = $students->map(function ($student) use ($dosenId) {
            $lastMessage = Message::where(function ($q) use ($dosenId, $student) {
                    $q->where('id_sender', $dosenId)->where('id_receiver', $student->id);
                })->orWhere(function ($q) use ($dosenId, $student) {
                    $q->where('id_sender', $student->id)->where('id_receiver', $dosenId);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            $unreadCount = Message::where('id_sender', $student->id)
                ->where('id_receiver', $dosenId)
                ->where('is_read', false)
                ->count();

            $fotoProfile = $student->profile->foto_profile ?? null;
            $avatar = $fotoProfile
                ? asset('storage/' . $fotoProfile)
                : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=random';

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_nim' => $student->profile->nim ?? '-',
                'student_email' => $student->email ?? '-',
                'student_avatar' => $avatar,
                'last_message' => $lastMessage
                    ? (strlen($lastMessage->content) > 50 ? substr($lastMessage->content, 0, 50) . '...' : $lastMessage->content)
                    : null,
                'last_message_time' => $lastMessage ? $lastMessage->created_at->toISOString() : null,
                'unread_count' => $unreadCount,
            ];
        })->sortByDesc(function ($conv) {
            return $conv['unread_count'] > 0 ? 1 : 0;
        })->values();

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Get chat messages with a specific student
     */
    public function getChatMessages(Request $request, $studentId)
    {
        $dosen = Auth::guard('dosen')->user();
        $dosenId = $dosen->id;

        $student = User::where('id', $studentId)->where('role', 'mahasiswa')->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.'], 404);
        }

        // Mark messages from student as read
        Message::where('id_sender', $studentId)
            ->where('id_receiver', $dosenId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get messages
        $messages = Message::where(function ($q) use ($dosenId, $studentId) {
                $q->where('id_sender', $dosenId)->where('id_receiver', $studentId);
            })->orWhere(function ($q) use ($dosenId, $studentId) {
                $q->where('id_sender', $studentId)->where('id_receiver', $dosenId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($dosenId) {
                return [
                    'id' => $msg->id_message,
                    'content' => $msg->content,
                    'sender_type' => $msg->id_sender === $dosenId ? 'dosen' : 'mahasiswa',
                    'created_at' => $msg->created_at->toISOString(),
                    'is_read' => $msg->is_read,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Send a message to a student
     */
    public function sendChatMessage(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();

        $request->validate([
            'student_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:2000',
        ]);

        $student = User::where('id', $request->student_id)->where('role', 'mahasiswa')->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.'], 404);
        }

        $message = Message::create([
            'id_sender' => $dosen->id,
            'id_receiver' => $request->student_id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id_message,
                'content' => $message->content,
                'sender_type' => 'dosen',
                'created_at' => $message->created_at->toISOString(),
                'is_read' => false,
            ],
        ]);
    }
}
