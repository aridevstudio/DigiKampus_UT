<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\AdminNotification;
use App\Models\BootcampMentor;
use App\Models\Course;
use App\Models\CourseDiscussion;
use App\Models\CourseInstructorNote;
use App\Models\DosenNotification;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Services\DeviceSessionLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class DosenController extends Controller
{
    private const BOOTCAMP_SESSION_META_PREFIX = 'BOOTCAMP_SESSION|';

    /**
     * Show login form
     */
        public function showLoginForm()
    {
        return view('Auth.dosen.login');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'nomor_induk' => ['required', 'string', 'max:50', 'unique:profiles,nomor_induk'],
            'id_jurusan' => ['required', 'exists:jurusans,id_jurusan'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'google_id' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'dosen',
                'status' => 'pending', 
                'google_id' => $request->google_id,
            ]);

            $user->profile()->create([
                'nomor_induk' => $request->nomor_induk,
                'id_jurusan' => $request->id_jurusan,
            ]);

            DB::commit();

            return redirect()->route('dosen.login')
                ->with('status', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('alert', 'Pendaftaran gagal: ' . $e->getMessage());
        }
    }

    /**
     * Handle login
     */
    public function login(Request $request, DeviceSessionLimitService $deviceSessionLimitService)
    {
        $request->validate([
            'nomor_induk' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('role', 'dosen')
            ->whereHas('profile', function ($query) use ($request) {
                $query->where('nomor_induk', $request->nomor_induk);
            })
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('alert', 'Nomor Induk tidak terdaftar sebagai dosen.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->with('alert', 'Password salah. Silakan coba lagi.');
        }

                if ($user->status === 'pending') {
            return back()
                ->withInput()
                ->with('alert', 'Akun Anda sedang menunggu persetujuan admin.');
        } elseif ($user->status !== 'aktif') {
            return back()
                ->withInput()
                ->with('alert', 'Akun Anda sedang tidak aktif. Hubungi admin.');
        }

        if ($deviceSessionLimitService->hasReachedWebLoginLimit($user)) {
            return back()
                ->withInput()
                ->with('alert', $deviceSessionLimitService->limitMessage());
        }

        // Login using Laravel Auth guard
        Auth::guard('dosen')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $deviceSessionLimitService->bindCurrentSessionToUser($request, (int) $user->id);

        return redirect()->route('dosen.dashboard')
            ->with('status', 'Login berhasil. Selamat datang!');
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
        
        // Get upcoming teaching schedules from agendas backend.
        $upcomingSchedules = Agenda::query()
            ->where('id_dosen', $dosen->id)
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->with(['course', 'mahasiswa'])
            ->orderBy('tanggal')
            ->orderByRaw('CASE WHEN waktu_mulai IS NULL THEN 1 ELSE 0 END, waktu_mulai ASC')
            ->take(3)
            ->get()
            ->map(function (Agenda $schedule) {
                $startTime = $this->formatScheduleTime($schedule->waktu_mulai);
                $endTime = $this->formatScheduleTime($schedule->waktu_selesai);
                $timeText = 'Waktu belum ditentukan';

                if ($startTime && $endTime) {
                    $timeText = "{$startTime} - {$endTime} WIB";
                } elseif ($startTime) {
                    $timeText = "{$startTime} WIB";
                }

                return [
                    'id_agenda' => $schedule->id_agenda,
                    'id_course' => $schedule->id_course ?: $schedule->course?->id_course,
                    'course' => $schedule->course?->nama_course ?? $schedule->judul ?? 'Jadwal Mengajar',
                    'judul' => $schedule->judul,
                    'deskripsi' => $schedule->deskripsi,
                    'tanggal' => $schedule->tanggal?->translatedFormat('l, d F Y') ?? '-',
                    'tanggal_raw' => $schedule->tanggal?->format('Y-m-d'),
                    'waktu' => $timeText,
                    'waktu_mulai_raw' => $startTime,
                    'waktu_selesai_raw' => $endTime,
                    'tipe' => $schedule->tipe ?? 'webinar',
                    'id_mahasiswa' => $schedule->id_mahasiswa,
                    'mahasiswa' => $schedule->mahasiswa?->name,
                ];
            })
            ->values()
            ->all();

        // Auto-create notifications for schedules within the next 24 hours
        $upcoming24h = Agenda::query()
            ->where('id_dosen', $dosen->id)
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->whereDate('tanggal', '<=', now()->addDay()->toDateString())
            ->with('course')
            ->get();

        foreach ($upcoming24h as $schedule) {
            $notifKey = 'jadwal_' . $schedule->id_agenda;

            $exists = DosenNotification::where('dosen_id', $dosen->id)
                ->where('konten', $notifKey)
                ->exists();

            if (!$exists) {
                $courseName = $schedule->course?->nama_course ?? $schedule->judul ?? 'Jadwal Mengajar';
                $tanggal = $schedule->tanggal?->translatedFormat('d M Y') ?? '-';
                $waktu = $this->formatScheduleTime($schedule->waktu_mulai);
                $timeLabel = $waktu ? "pukul {$waktu} WIB" : '';

                DosenNotification::notifyDosen(
                    $dosen->id,
                    "Jadwal mengajar terdekat: {$courseName}",
                    $notifKey,
                    'info',
                    'calendar',
                    $schedule->id_course ? route('dosen.kursus.detail', $schedule->id_course) : null
                );
            }
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

    public function showBootcamp()
    {
        $dosen = Auth::guard('dosen')->user();

        $assignments = BootcampMentor::query()
            ->with('bootcamp')
            ->where('id_user', $dosen->id)
            ->latest('id_bootcamp_mentor')
            ->get()
            ->filter(fn (BootcampMentor $assignment) => $assignment->bootcamp !== null)
            ->values();

        $mentorBootcamps = $assignments->map(function (BootcampMentor $assignment, int $index) {
            $bootcamp = $assignment->bootcamp;
            [$filledSeats] = $this->parseBootcampSeatLabel((string) $bootcamp->seats_label);

            $accentPalettes = [
                ['accent' => 'from-sky-500 to-blue-600', 'badge' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:border-sky-500/20'],
                ['accent' => 'from-violet-500 to-fuchsia-600', 'badge' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-500/10 dark:text-violet-300 dark:border-violet-500/20'],
                ['accent' => 'from-emerald-500 to-teal-600', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20'],
            ];
            $palette = $accentPalettes[$index % count($accentPalettes)];

            return [
                'id' => (string) $bootcamp->id_bootcamp,
                'slug' => Str::slug($bootcamp->title) . '-' . $bootcamp->id_bootcamp,
                'title' => $bootcamp->title,
                'type' => $bootcamp->program_type === 'ticketed_event' ? 'Tiket Event' : 'Bootcamp',
                'batch' => $bootcamp->batch_label,
                'role' => Str::headline((string) $assignment->role_label),
                'progress' => $this->bootcampProgressByStatus((string) $bootcamp->status),
                'next_session' => $bootcamp->schedule_label ?: 'Jadwal belum ditentukan',
                'students' => $filledSeats,
                'tasks' => filled($assignment->assignment_note) ? Str::limit($assignment->assignment_note, 40) : 'Belum ada catatan tugas',
                'status' => $this->bootcampStatusLabel((string) $bootcamp->status),
                'risk' => $bootcamp->risk_note ?: 'Belum ada catatan risiko',
                'accent' => $palette['accent'],
                'accentBadge' => $palette['badge'],
            ];
        })->values();

        $mentorBootcampMap = $mentorBootcamps->keyBy('id');

        $bootcampSchedules = Agenda::query()
            ->where('id_dosen', $dosen->id)
            ->whereNull('id_course')
            ->whereNull('id_mahasiswa')
            ->where('deskripsi', 'like', self::BOOTCAMP_SESSION_META_PREFIX . '%')
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->limit(30)
            ->get()
            ->map(function (Agenda $agenda) use ($mentorBootcampMap) {
                $meta = $this->parseBootcampSessionMeta($agenda->deskripsi);
                if ($meta === null) {
                    return null;
                }

                $bootcampId = (string) ($meta['id_bootcamp'] ?? '');
                if (!$mentorBootcampMap->has($bootcampId)) {
                    return null;
                }

                $bootcamp = $mentorBootcampMap->get($bootcampId);
                $startTime = $this->formatScheduleTime($agenda->waktu_mulai) ?? '09:00';
                $formattedDate = $agenda->tanggal?->translatedFormat('d M Y') ?? '-';
                $sessionType = (string) ($meta['session_type'] ?? $this->bootcampSessionTagFromAgendaType($agenda->tipe));

                return [
                    'id' => 'bootcamp-session-' . $agenda->id_agenda,
                    'id_bootcamp' => $bootcampId,
                    'time' => str_replace(':', '.', $startTime),
                    'session' => $agenda->judul ?: ($sessionType . ' ' . $bootcamp['title']),
                    'detail' => $bootcamp['batch'] . ' - ' . $formattedDate,
                    'tag' => $sessionType,
                    'date_sort' => $agenda->tanggal?->format('Y-m-d') ?: '9999-12-31',
                ];
            })
            ->filter()
            ->values();

        $nextSessionByBootcamp = [];
        foreach ($bootcampSchedules as $session) {
            $bootcampId = (string) $session['id_bootcamp'];
            if (!isset($nextSessionByBootcamp[$bootcampId])) {
                $nextSessionByBootcamp[$bootcampId] = $session['date_sort'] . ' - ' . str_replace('.', ':', $session['time']);
            }
        }

        $mentorBootcamps = $mentorBootcamps->map(function (array $item) use ($nextSessionByBootcamp) {
            $bootcampId = (string) $item['id'];
            if (isset($nextSessionByBootcamp[$bootcampId])) {
                $item['next_session'] = $nextSessionByBootcamp[$bootcampId];
            }

            return $item;
        })->values();

        $batchCount = $mentorBootcamps->count();
        $activeParticipants = (int) $mentorBootcamps->sum('students');
        $reviewPending = (int) $mentorBootcamps->filter(fn (array $item) => in_array($item['status'], ['Draft', 'Internal Review', 'Open Registration'], true))->count() * 3;
        $scheduledSessions = (int) $bootcampSchedules->count();

        $mentorStats = [
            ['label' => 'Batch Diampu', 'value' => $batchCount, 'helper' => 'aktif minggu ini', 'tone' => 'from-sky-500 to-blue-600'],
            ['label' => 'Peserta Aktif', 'value' => $activeParticipants, 'helper' => 'gabungan seluruh cohort', 'tone' => 'from-emerald-500 to-teal-600'],
            ['label' => 'Review Tertunda', 'value' => $reviewPending, 'helper' => 'submission dan rubrik', 'tone' => 'from-violet-500 to-fuchsia-600'],
            ['label' => 'Sesi Terjadwal', 'value' => $scheduledSessions, 'helper' => 'agenda hasil jadwal dosen', 'tone' => 'from-amber-500 to-orange-500'],
        ];

        $sessionQueue = $bootcampSchedules
            ->map(fn (array $item) => collect($item)->except(['date_sort'])->all())
            ->values()
            ->all();

        $cohortRows = $mentorBootcamps->take(3)->values()->map(function (array $item, int $index) {
            $attendance = max(70, min(100, 80 + ($item['progress'] - 20)));
            return [
                'name' => 'Cohort ' . chr(65 + $index),
                'students' => $item['students'],
                'attendance' => $attendance . '%',
                'completion' => $item['progress'] . '%',
                'risk' => $item['risk'],
            ];
        })->all();

        $deliveryChecklist = [
            ['label' => 'Materi sesi minggu ini', 'state' => 'Siap', 'note' => 'Materi mengacu ke batch yang sudah diassign admin'],
            ['label' => 'Feedback tugas akhir', 'state' => 'Butuh Review', 'note' => 'Prioritaskan cohort dengan progres rendah'],
            ['label' => 'Attendance recap', 'state' => 'Sinkron', 'note' => 'Data attendance mengikuti status batch operasional'],
            ['label' => 'Sertifikat cohort lulus', 'state' => 'Blocked', 'note' => 'Menunggu logic publish nilai final'],
        ];

        return view('Auth.dosen.bootcamp-saya', [
            'mentorStats' => $mentorStats,
            'mentorBootcamps' => $mentorBootcamps->all(),
            'sessionQueue' => $sessionQueue,
            'cohortRows' => $cohortRows,
            'deliveryChecklist' => $deliveryChecklist,
        ]);
    }

    public function storeBootcampSession(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();

        $validated = $request->validate([
            'id_bootcamp' => ['required', 'integer'],
            'session_type' => ['required', 'string', Rule::in(['Live Review', 'Mentoring', 'Hands-on', 'Office Hour'])],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
        ], [
            'id_bootcamp.required' => 'Bootcamp harus dipilih.',
            'session_type.required' => 'Jenis sesi harus dipilih.',
            'date.required' => 'Tanggal sesi wajib diisi.',
            'time.required' => 'Jam sesi wajib diisi.',
        ]);

        $assignment = BootcampMentor::query()
            ->with('bootcamp')
            ->where('id_user', $dosen->id)
            ->where('id_bootcamp', $validated['id_bootcamp'])
            ->first();

        if (!$assignment || !$assignment->bootcamp) {
            return response()->json([
                'success' => false,
                'message' => 'Bootcamp tidak ditemukan atau belum di-assign ke akun dosen ini.',
            ], 404);
        }

        $bootcamp = $assignment->bootcamp;
        $startAt = Carbon::createFromFormat('H:i', $validated['time']);
        $endAt = $startAt->copy()->addMinutes(90);
        $agendaType = $validated['session_type'] === 'Hands-on' ? 'workshop' : 'webinar';

        $meta = [
            'context' => 'bootcamp_session',
            'id_bootcamp' => (int) $bootcamp->id_bootcamp,
            'session_type' => $validated['session_type'],
            'batch_label' => (string) $bootcamp->batch_label,
        ];

        $agenda = Agenda::create([
            'id_mahasiswa' => null,
            'id_dosen' => $dosen->id,
            'id_course' => null,
            'judul' => $validated['session_type'] . ' ' . $bootcamp->title,
            'deskripsi' => self::BOOTCAMP_SESSION_META_PREFIX . json_encode($meta, JSON_UNESCAPED_UNICODE),
            'tanggal' => $validated['date'],
            'waktu_mulai' => $startAt->format('H:i:s'),
            'waktu_selesai' => $endAt->format('H:i:s'),
            'tipe' => $agendaType,
            'warna' => Agenda::getColorByType($agendaType),
        ]);

        $formattedDate = optional($agenda->tanggal)->translatedFormat('d M Y') ?? $validated['date'];

        return response()->json([
            'success' => true,
            'message' => 'Jadwal sesi bootcamp berhasil disimpan.',
            'data' => [
                'session' => [
                    'id' => 'bootcamp-session-' . $agenda->id_agenda,
                    'id_bootcamp' => (string) $bootcamp->id_bootcamp,
                    'time' => str_replace(':', '.', $startAt->format('H:i')),
                    'session' => $agenda->judul,
                    'detail' => $bootcamp->batch_label . ' - ' . $formattedDate,
                    'tag' => $validated['session_type'],
                ],
            ],
        ], 201);
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
    public function logout(Request $request, DeviceSessionLimitService $deviceSessionLimitService)
    {
        $currentSessionId = $request->session()->getId();
        Auth::guard('dosen')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $deviceSessionLimitService->deleteSessionById($currentSessionId);

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
            ->with(['enrollments', 'jurusan', 'modules' => function ($q) {
                $q->orderBy('urutan');
            }, 'modules.materials' => function ($q) {
                $q->orderBy('urutan');
            }]);

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
            if ($statusFilter === 'pending') {
                $query->where('approval_status', 'pending');
            } elseif ($statusFilter === 'ditolak') {
                $query->where('approval_status', 'ditolak');
            } else {
                $query->where('status', $statusFilter);
            }
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

            // Build per-module video preview data
            $modulePreviews = collect();
            foreach ($course->modules as $module) {
                $firstVideo = $module->materials->where('tipe', 'video')->whereNotNull('video_url')->first();
                $videoThumb = null;
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
                    }
                }
                $modulePreviews->push([
                    'judul' => $module->judul_module,
                    'video_thumbnail' => $videoThumb,
                    'has_video' => $videoThumb !== null,
                ]);
            }

            return [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'kode' => $course->kode_course,
                'deskripsi' => \Str::limit($course->deskripsi, 60),
                'thumbnail' => $course->thumbnail,
                'mahasiswa_count' => $enrollmentCount,
                'progress_avg' => round($avgProgress),
                'status' => $course->status,
                'kategori' => $course->kategori,
                'approval_status' => $course->approval_status,
                'approval_notes' => $course->approval_notes,
                'tanggal_webinar' => optional($course->tanggal_webinar)?->format('Y-m-d'),
                'jam_mulai_webinar' => $course->jam_mulai_webinar,
                'jam_selesai_webinar' => $course->jam_selesai_webinar,
                'kuota_peserta' => $course->kuota_peserta,
                'module_previews' => $modulePreviews,
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
                'instructorNotes' => fn ($query) => $query->latest(),
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
            'approval_status' => $course->approval_status,
            'approval_notes' => $course->approval_notes,
            'tanggal_webinar' => optional($course->tanggal_webinar)?->format('Y-m-d'),
            'jam_mulai_webinar' => $course->jam_mulai_webinar,
            'jam_selesai_webinar' => $course->jam_selesai_webinar,
            'kuota_peserta' => $course->kuota_peserta,
            'youtube_playlist' => $course->youtube_playlist,
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

    public function getCourseDiscussions(Request $request, $id)
    {
        $dosen = Auth::guard('dosen')->user();

        $course = Course::query()
            ->where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        if (!Schema::hasTable('course_discussions')) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Fitur diskusi belum aktif di server ini.',
            ]);
        }

        try {
            $messages = CourseDiscussion::with(['user.profile'])
                ->where('id_course', $course->id_course)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn (CourseDiscussion $comment) => $this->formatCourseDiscussionComment($comment));

            return response()->json([
                'success' => true,
                'data' => $messages,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal memuat diskusi kursus dosen.', [
                'course_id' => (int) $course->id_course,
                'dosen_id' => (int) $dosen->id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Diskusi kursus sedang bermasalah. Silakan coba lagi.',
            ], 500);
        }
    }

    public function sendCourseDiscussion(Request $request, $id)
    {
        $dosen = Auth::guard('dosen')->user();

        $course = Course::query()
            ->where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        if (!Schema::hasTable('course_discussions')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur diskusi belum aktif di server ini.',
            ], 503);
        }

        try {
            $validated = $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $message = trim($validated['message']);

            $discussion = CourseDiscussion::query()
                ->where('id_course', $course->id_course)
                ->where('id_user', $dosen->id)
                ->where('message', $message)
                ->where('created_at', '>=', now()->subSeconds(15))
                ->latest('id')
                ->first();

            if (!$discussion) {
                $discussion = CourseDiscussion::create([
                    'id_course' => $course->id_course,
                    'id_user' => $dosen->id,
                    'message' => $message,
                ]);
            }

            $discussion->load(['user.profile']);

            try {
                Enrollment::query()
                    ->where('id_course', $course->id_course)
                    ->accessible()
                    ->pluck('id_mahasiswa')
                    ->each(function ($mahasiswaId) use ($course, $dosen) {
                        Notification::notifyMahasiswa(
                            (int) $mahasiswaId,
                            'Balasan dosen di diskusi kursus',
                            'Dosen membalas diskusi pada kursus ' . $course->nama_course . '.',
                            'kursus_pembelajaran',
                            'discussion',
                            '#2563EB'
                        );
                    });
            } catch (\Throwable $notificationException) {
                Log::warning('Notifikasi balasan diskusi kursus ke mahasiswa gagal dikirim.', [
                    'course_id' => (int) $course->id_course,
                    'dosen_id' => (int) $dosen->id,
                    'discussion_id' => (int) $discussion->id,
                    'error' => $notificationException->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatCourseDiscussionComment($discussion),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim diskusi kursus dosen.', [
                'course_id' => (int) $course->id_course,
                'dosen_id' => (int) $dosen->id,
                'payload' => [
                    'message_length' => strlen((string) $request->input('message')),
                ],
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Balasan diskusi gagal dikirim. Server sedang bermasalah.',
            ], 500);
        }
    }

    public function storeCourseNote(Request $request, $id)
    {
        $dosen = Auth::guard('dosen')->user();

        $course = Course::where('id_course', $id)
            ->where('id_dosen', $dosen->id)
            ->with('enrollments')
            ->firstOrFail();

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'konten' => 'required|string|max:3000',
        ]);

        $note = CourseInstructorNote::create([
            'id_course' => $course->id_course,
            'id_dosen' => $dosen->id,
            'judul' => trim((string) ($validated['judul'] ?? '')),
            'konten' => trim($validated['konten']),
            'is_active' => true,
        ]);

        foreach ($course->enrollments as $enrollment) {
            Notification::notifyMahasiswa(
                $enrollment->id_mahasiswa,
                'Catatan baru dari dosen',
                'Dosen menambahkan catatan baru pada kursus ' . $course->nama_course . '.',
                'kursus_pembelajaran',
                'note',
                '#F59E0B'
            );
        }

        return back()->with('success', 'Catatan dosen berhasil dikirim.');
    }

    public function deleteCourseNote($id, $noteId)
    {
        $dosen = Auth::guard('dosen')->user();

        $note = CourseInstructorNote::where('id_course_instructor_note', $noteId)
            ->where('id_dosen', $dosen->id)
            ->where('id_course', $id)
            ->firstOrFail();

        $note->delete();

        return back()->with('success', 'Catatan dosen berhasil dihapus.');
    }

    private function formatCourseDiscussionComment(CourseDiscussion $comment): array
    {
        $avatar = $comment->user?->profile?->foto_profile
            ? asset('storage/' . $comment->user->profile->foto_profile)
            : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user?->name ?? 'Pengguna') . '&background=2563EB&color=fff';

        return [
            'id' => $comment->id_course_discussion,
            'name' => $comment->user?->name ?? 'Pengguna',
            'role' => $comment->user?->role === 'dosen' ? 'Pengajar' : 'Mahasiswa',
            'text' => $comment->message,
            'time' => optional($comment->created_at)->diffForHumans(),
            'avatar' => $avatar,
            'created_at' => optional($comment->created_at)->toISOString(),
        ];
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
            $displayMeta = $this->buildMaterialDisplayMeta($material->tipe, $material->konten);

            return [
                'id' => $material->id_material,
                'judul' => $material->judul_material,
                'tipe' => $material->tipe,
                'konten' => $material->konten,
                'konten_display' => $displayMeta['display'],
                'is_structured_content' => $displayMeta['is_structured'],
                'structured_hint' => $displayMeta['hint'],
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
                'kategori' => $course->kategori,
                'approval_status' => $course->approval_status,
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

        $displayMeta = $this->buildMaterialDisplayMeta($material->tipe, $material->konten);

        return response()->json([
            'id_material' => $material->id_material,
            'judul_material' => $material->judul_material,
            'tipe' => $material->tipe,
            'konten' => $material->konten,
            'konten_display' => $displayMeta['display'],
            'is_structured_content' => $displayMeta['is_structured'],
            'structured_hint' => $displayMeta['hint'],
            'video_url' => $material->video_url,
            'urutan' => $material->urutan,
            'durasi' => $material->durasi,
        ]);
    }

    private function buildMaterialDisplayMeta(?string $type, ?string $content): array
    {
        $rawContent = trim((string) $content);
        $normalizedType = $this->normalizeMaterialTypeForDisplay($type, $rawContent);

        if ($rawContent === '') {
            return [
                'display' => '',
                'is_structured' => false,
                'hint' => null,
            ];
        }

        if ($normalizedType === 'tugas') {
            $assignmentPayload = $this->parseAssignmentPayloadForDisplay($rawContent);

            if ($assignmentPayload !== []) {
                $assignmentDescription = trim((string) ($assignmentPayload['deskripsi'] ?? ''));
                $assignmentInstruction = trim((string) ($assignmentPayload['instruksi'] ?? ''));
                $assignmentDisplay = $assignmentDescription !== ''
                    ? $assignmentDescription
                    : ($assignmentInstruction !== '' ? $assignmentInstruction : 'Tugas terstruktur');

                return [
                    'display' => $assignmentDisplay,
                    'is_structured' => true,
                    'hint' => 'Detail tugas tersimpan sebagai data terstruktur. Ubah instruksi lengkap di halaman Kelola Tugas.',
                ];
            }
        }

        if ($normalizedType === 'kuis') {
            $quizQuestionCount = $this->countQuizQuestionsForDisplay($rawContent);

            if ($quizQuestionCount > 0) {
                return [
                    'display' => sprintf('Kuis berisi %d soal. Kelola pertanyaan melalui halaman Input Kuis.', $quizQuestionCount),
                    'is_structured' => true,
                    'hint' => 'Soal kuis tersimpan sebagai data terstruktur. Ubah daftar soal di halaman Input Kuis.',
                ];
            }
        }

        return [
            'display' => $rawContent,
            'is_structured' => false,
            'hint' => null,
        ];
    }

    private function normalizeMaterialTypeForDisplay(?string $type, ?string $content): string
    {
        $normalized = Str::lower((string) $type);

        return match ($normalized) {
            'quiz' => 'kuis',
            'text' => $this->parseAssignmentPayloadForDisplay((string) $content) !== [] ? 'tugas' : 'bacaan',
            'video', 'bacaan', 'kuis', 'tugas' => $normalized,
            default => 'video',
        };
    }

    private function parseAssignmentPayloadForDisplay(string $content): array
    {
        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            return [];
        }

        $hasAssignmentKeys = !empty($decoded['is_tugas'])
            || array_key_exists('deskripsi', $decoded)
            || array_key_exists('instruksi', $decoded)
            || array_key_exists('deadline', $decoded)
            || array_key_exists('format', $decoded)
            || array_key_exists('allowLinks', $decoded);

        return $hasAssignmentKeys ? $decoded : [];
    }

    private function countQuizQuestionsForDisplay(string $content): int
    {
        $decoded = json_decode($content, true);

        if (is_array($decoded)) {
            if ($this->looksLikeQuizQuestion($decoded)) {
                return 1;
            }

            if (isset($decoded['questions']) && is_array($decoded['questions'])) {
                return collect($decoded['questions'])
                    ->filter(fn ($question) => is_array($question) && $this->looksLikeQuizQuestion($question))
                    ->count();
            }

            if ($this->isSequentialArray($decoded)) {
                return collect($decoded)
                    ->filter(fn ($question) => is_array($question) && $this->looksLikeQuizQuestion($question))
                    ->count();
            }
        }

        preg_match_all('/"pertanyaan"\s*:/u', $content, $questionMatches);
        if (!empty($questionMatches[0])) {
            return count($questionMatches[0]);
        }

        preg_match_all('/"correctAnswer"\s*:/u', $content, $correctAnswerMatches);
        if (!empty($correctAnswerMatches[0])) {
            return count($correctAnswerMatches[0]);
        }

        return 0;
    }

    private function looksLikeQuizQuestion(array $question): bool
    {
        return array_key_exists('pertanyaan', $question)
            || array_key_exists('question', $question)
            || array_key_exists('correctAnswer', $question)
            || array_key_exists('jawaban_benar', $question);
    }

    private function isSequentialArray(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        return array_keys($value) === range(0, count($value) - 1);
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

        if ($course->kategori === 'webinar') {
            if (!$course->tanggal_webinar || !$course->jam_mulai_webinar || !$course->jam_selesai_webinar) {
                return back()->with('error', 'Lengkapi tanggal dan jam webinar sebelum mengajukan ke admin.');
            }

            $course->update([
                'status' => 'draft',
                'approval_status' => 'pending',
                'approval_notes' => null,
                'approved_by' => null,
                'approved_at' => null,
            ]);

            AdminNotification::notifyAllAdmins(
                'Pengajuan Webinar Baru',
                "{$dosen->name} mengajukan webinar \"{$course->nama_course}\" untuk ditinjau.",
                'info',
                'webinar',
                route('admin.kursus')
            );

            return back()->with('success', 'Webinar berhasil diajukan dan menunggu persetujuan admin.');
        }

        // Check if course has at least one material
        $materialsCount = \App\Models\CourseMaterial::where('id_course', $id)->count();
        if ($materialsCount === 0) {
            return back()->with('error', 'Tambahkan minimal 1 modul sebelum mempublikasikan kursus');
        }

        $course->status = 'aktif';
        $course->approval_status = 'tidak_perlu';
        $course->approval_notes = null;
        $course->approved_by = null;
        $course->approved_at = null;
        $course->save();

        return back()->with('success', 'Kursus berhasil dipublikasikan!');
    }

    /**
     * Show Buat Kursus page
     */
    public function showBuatKursus(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        $jurusans = \App\Models\Jurusan::all();
        $allowedKategori = ['kursus', 'webinar'];
        $initialKategori = in_array($request->query('kategori'), $allowedKategori, true)
            ? $request->query('kategori')
            : 'kursus';
        
        return view('Auth.dosen.buat-kursus', [
            'dosen' => $dosen,
            'jurusans' => $jurusans,
            'initialKategori' => $initialKategori,
        ]);
    }

    /**
     * Store new course
     */
    public function storeCourse(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();

        $request->validate($this->dosenCourseRules($dosen->id));

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $initialMaterials = $this->normalizeInitialMaterials($request->input('initial_modules', []));

        $course = DB::transaction(function () use ($dosen, $request, $thumbnailPath, $initialMaterials) {
            $course = \App\Models\Course::create(
                $this->buildDosenCoursePayload($request, $dosen->id, $thumbnailPath)
            );

            $hasInitialStructure = !empty($initialMaterials);

            if ($hasInitialStructure) {
                $mainModule = \App\Models\CourseModule::create([
                    'id_course' => $course->id_course,
                    'judul_module' => 'Modul Utama',
                    'deskripsi' => 'Modul default untuk materi awal kursus',
                    'urutan' => 1,
                ]);

                foreach ($initialMaterials as $index => $material) {
                    \App\Models\CourseMaterial::create([
                        'id_course' => $course->id_course,
                        'id_module' => $mainModule->id_module,
                        'judul_material' => $material['judul'],
                        'tipe' => $material['tipe'],
                        'konten' => $material['konten'],
                        'video_url' => $material['tipe'] === 'video' ? $material['video_url'] : null,
                        'durasi' => $material['tipe'] === 'tugas' ? null : $material['durasi'],
                        'urutan' => $index + 1,
                    ]);
                }
            }

            return $course;
        });

        if ($course->kategori === 'webinar' && $course->approval_status === 'pending') {
            AdminNotification::notifyAllAdmins(
                'Pengajuan Webinar Baru',
                "{$dosen->name} mengajukan webinar \"{$course->nama_course}\" untuk ditinjau.",
                'info',
                'webinar',
                route('admin.kursus')
            );
        }

        $this->notifyMahasiswaAboutPublishedProgram($course, $dosen->name);

        $message = $course->kategori === 'webinar'
            ? ($course->approval_status === 'pending'
                ? 'Webinar berhasil diajukan dan menunggu persetujuan admin.'
                : 'Draft webinar berhasil disimpan.')
            : 'Kursus berhasil dibuat dan tampil di Kursus Saya.';

        return redirect()->route('dosen.kursus')
            ->with('success', $message);
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

    private function dosenCourseRules(int $dosenId, ?int $courseId = null): array
    {
        $namaRule = Rule::unique('courses', 'nama_course')
            ->where(static fn ($query) => $query->where('id_dosen', $dosenId));

        if ($courseId !== null) {
            $namaRule = $namaRule->ignore($courseId, 'id_course');
        }

        return [
            'nama_course' => ['required', 'string', 'max:255', $namaRule],
            'kode_course' => 'required|string|max:50|unique:courses,kode_course' . ($courseId !== null ? ',' . $courseId . ',id_course' : ''),
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string|max:4000',
            'id_jurusan' => 'nullable|integer|exists:jurusans,id_jurusan',
            'tipe' => 'required|in:gratis,berbayar',
            'kategori' => 'required|in:webinar,tiket,kursus',
            'harga' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,aktif,nonaktif',
            'thumbnail' => 'nullable|image|max:2048',
            'level' => 'nullable|in:Pemula,Menengah,Mahir',
            'estimasi_waktu' => 'nullable|integer|min:0',
            'durasi_satuan' => 'nullable|in:Jam,Minggu',
            'diskon' => 'nullable|integer|min:0|max:100',
            'youtube_playlist' => 'nullable|url|max:500',
            'tanggal_webinar' => 'required_if:kategori,webinar|nullable|date',
            'jam_mulai_webinar' => 'required_if:kategori,webinar|nullable|date_format:H:i',
            'jam_selesai_webinar' => 'required_if:kategori,webinar|nullable|date_format:H:i|after:jam_mulai_webinar',
            'kuota_peserta' => 'nullable|integer|min:1',
            'initial_modules' => 'nullable|array',
            'initial_modules.*.judul' => 'nullable|string|max:255',
            'initial_modules.*.tipe' => 'nullable|in:video,bacaan,kuis,tugas',
            'initial_modules.*.konten' => 'nullable|string',
            'initial_modules.*.video_url' => 'nullable|url|max:500',
            'initial_modules.*.durasi' => 'nullable|integer|min:0',
        ];
    }

    private function normalizeInitialMaterials(array $modules): array
    {
        return collect($modules)
            ->map(function ($module) {
                $judul = trim((string) data_get($module, 'judul', ''));
                $tipe = data_get($module, 'tipe', 'video');
                $konten = trim((string) data_get($module, 'konten', ''));
                $videoUrl = trim((string) data_get($module, 'video_url', ''));
                $durasiValue = data_get($module, 'durasi');
                $durasi = $durasiValue === '' || $durasiValue === null ? null : (int) $durasiValue;

                if ($judul === '' && $konten === '' && $videoUrl === '' && $durasi === null) {
                    return null;
                }

                if ($judul === '') {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'initial_modules' => 'Setiap modul awal yang diisi wajib memiliki judul modul.',
                    ]);
                }

                $allowedTypes = ['video', 'bacaan', 'kuis', 'tugas'];
                if (!in_array($tipe, $allowedTypes, true)) {
                    $tipe = 'video';
                }

                return [
                    'judul' => $judul,
                    'tipe' => $tipe,
                    'konten' => $konten !== '' ? $konten : null,
                    'video_url' => $tipe === 'video' && $videoUrl !== '' ? $videoUrl : null,
                    'durasi' => $tipe === 'tugas' ? null : $durasi,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function buildDosenCoursePayload(Request $request, int $dosenId, ?string $thumbnailPath = null, ?Course $existingCourse = null): array
    {
        $isWebinar = $request->kategori === 'webinar';
        $requestedStatus = $request->input('status_btn', $request->input('status', 'draft'));
        $durasiSatuan = $request->filled('estimasi_waktu') ? ($request->durasi_satuan ?: 'Jam') : null;

        $approvalStatus = 'tidak_perlu';
        $approvalNotes = null;
        $approvedBy = null;
        $approvedAt = null;
        $status = $requestedStatus;

        if ($isWebinar) {
            if ($requestedStatus === 'aktif') {
                $status = 'draft';
                $approvalStatus = 'pending';
            } elseif ($requestedStatus === 'nonaktif') {
                $status = 'nonaktif';
            } elseif ($existingCourse && $existingCourse->approval_status === 'ditolak') {
                $status = 'draft';
                $approvalStatus = 'ditolak';
                $approvalNotes = $existingCourse->approval_notes;
                $approvedBy = $existingCourse->approved_by;
                $approvedAt = $existingCourse->approved_at;
            } elseif ($existingCourse && $existingCourse->approval_status === 'disetujui' && $existingCourse->status === 'aktif') {
                $status = 'aktif';
                $approvalStatus = 'disetujui';
                $approvedBy = $existingCourse->approved_by;
                $approvedAt = $existingCourse->approved_at;
            } else {
                $status = 'draft';
            }
        }

        $data = [
            'id_dosen' => $dosenId,
            'nama_course' => $request->nama_course,
            'kode_course' => $request->kode_course,
            'deskripsi' => $request->deskripsi,
            'persyaratan' => $request->persyaratan,
            'id_jurusan' => $request->id_jurusan,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'harga' => $request->tipe === 'berbayar' ? ($request->harga ?? 0) : 0,
            'status' => $status,
            'approval_status' => $approvalStatus,
            'approval_notes' => $approvalNotes,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
            'level' => $request->level,
            'estimasi_waktu' => $request->estimasi_waktu,
            'durasi_satuan' => $durasiSatuan,
            'sertifikat' => $request->boolean('sertifikat'),
            'akses_publik' => $request->boolean('akses_publik'),
            'diskon' => $request->diskon ?? 0,
            'youtube_playlist' => $request->youtube_playlist,
            'tanggal_webinar' => $isWebinar ? $request->tanggal_webinar : null,
            'jam_mulai_webinar' => $isWebinar ? $request->jam_mulai_webinar : null,
            'jam_selesai_webinar' => $isWebinar ? $request->jam_selesai_webinar : null,
            'kuota_peserta' => $isWebinar ? $request->kuota_peserta : null,
        ];

        if ($thumbnailPath !== null) {
            $data['thumbnail'] = $thumbnailPath;
        }

        return $data;
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

    private function notifyMahasiswaAboutPublishedProgram(\App\Models\Course $course, string $sourceLabel): void
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

        $request->validate($this->dosenCourseRules($dosen->id, $course->id_course));

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                \Storage::disk('public')->delete($course->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course->update(
            $this->buildDosenCoursePayload($request, $dosen->id, $thumbnailPath, $course)
        );

        if ($course->kategori === 'webinar' && $course->approval_status === 'pending') {
            AdminNotification::notifyAllAdmins(
                'Pengajuan Webinar Diperbarui',
                "{$dosen->name} mengajukan ulang webinar \"{$course->nama_course}\" untuk ditinjau.",
                'info',
                'webinar',
                route('admin.kursus')
            );
        }

        $message = $course->kategori === 'webinar'
            ? ($course->approval_status === 'pending'
                ? 'Perubahan webinar disimpan dan diajukan ke admin untuk persetujuan.'
                : 'Perubahan webinar berhasil disimpan.')
            : 'Kursus berhasil diperbarui!';

        return back()->with('success', $message);
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
            }, 'enrollments.mahasiswa.profile', 'jurusan', 'instructorNotes' => function ($query) {
                $query->latest();
            }])
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
        $jurusanFilter = $request->input('prodi', 'all');
        $statusFilter = $request->input('status', 'all');

        if (!in_array($statusFilter, ['all', 'aktif', 'selesai', 'tidak_aktif'], true)) {
            $statusFilter = 'all';
        }
        
        $query = \App\Models\Enrollment::whereIn('id_course', $courses)
            ->with(['mahasiswa.profile.jurusan', 'course']);
        
        if ($search) {
            $query->whereHas('mahasiswa', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        if ($courseFilter !== 'all') {
            $query->where('id_course', $courseFilter);
        }

        if ($jurusanFilter !== 'all') {
            $query->whereHas('mahasiswa.profile', function ($q) use ($jurusanFilter) {
                $q->where('id_jurusan', $jurusanFilter);
            });
        }

        if ($statusFilter !== 'all') {
            if ($statusFilter === 'selesai') {
                $query->where('progress', '>=', 100);
            } elseif ($statusFilter === 'aktif') {
                $query->where('progress', '>', 0)->where('progress', '<', 100);
            } elseif ($statusFilter === 'tidak_aktif') {
                $query->where('progress', '<=', 0);
            }
        }
        
        $enrollments = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // Calculate accumulated quiz scores per enrollment
        $enrollmentIds = $enrollments->pluck('id_mahasiswa')->unique()->toArray();
        $courseIds = $enrollments->pluck('id_course')->unique()->toArray();

        // Get best quiz attempt per quiz per mahasiswa for relevant courses
        $quizScores = \App\Models\QuizAttempt::query()
            ->whereIn('id_mahasiswa', $enrollmentIds)
            ->where('status', 'selesai')
            ->whereHas('quiz', function ($q) use ($courseIds) {
                $q->whereIn('id_course', $courseIds);
            })
            ->with('quiz:id_quiz,id_course')
            ->get()
            ->groupBy(function ($attempt) {
                return $attempt->id_mahasiswa . '_' . $attempt->quiz->id_course;
            })
            ->map(function ($attempts) {
                // Group by quiz, take best attempt per quiz
                $byQuiz = $attempts->groupBy('id_quiz');
                $totalSkor = 0;
                $totalPoin = 0;
                $quizCount = $byQuiz->count();
                foreach ($byQuiz as $quizAttempts) {
                    $best = $quizAttempts->sortByDesc('persentase')->first();
                    $totalSkor += $best->skor ?? 0;
                    $totalPoin += $best->total_poin ?? 0;
                }
                return [
                    'skor' => $totalSkor,
                    'total_poin' => $totalPoin,
                    'persentase' => $totalPoin > 0 ? round(($totalSkor / $totalPoin) * 100) : 0,
                    'quiz_count' => $quizCount,
                ];
            });

        // Attach scores to enrollments
        $enrollments->getCollection()->transform(function ($enrollment) use ($quizScores) {
            $key = $enrollment->id_mahasiswa . '_' . $enrollment->id_course;
            $enrollment->quiz_score = $quizScores[$key] ?? null;
            return $enrollment;
        });
        
        $coursesForFilter = \App\Models\Course::where('id_dosen', $dosen->id)
            ->select('id_course', 'nama_course')
            ->get();

        // Get jurusan list from enrolled mahasiswa
        $jurusanIds = \App\Models\Enrollment::whereIn('id_course', $courses)
            ->with('mahasiswa.profile')
            ->get()
            ->pluck('mahasiswa.profile.id_jurusan')
            ->filter()
            ->unique();
        $jurusanList = \App\Models\Jurusan::whereIn('id_jurusan', $jurusanIds)->orderBy('nama_jurusan')->get();

        // Stats counts
        $totalEnrollments = \App\Models\Enrollment::whereIn('id_course', $courses)->count();
        $selesaiCount = \App\Models\Enrollment::whereIn('id_course', $courses)->where('progress', '>=', 100)->count();
        $aktifCount = \App\Models\Enrollment::whereIn('id_course', $courses)->where('progress', '>', 0)->where('progress', '<', 100)->count();
        $tidakAktifCount = \App\Models\Enrollment::whereIn('id_course', $courses)->where('progress', '<=', 0)->count();
        $avgProgress = $totalEnrollments > 0
            ? round(\App\Models\Enrollment::whereIn('id_course', $courses)->avg('progress'))
            : 0;

        // Average quiz score across all enrollments
        $allQuizScores = \App\Models\QuizAttempt::query()
            ->where('status', 'selesai')
            ->whereHas('quiz', function ($q) use ($courses) {
                $q->whereIn('id_course', $courses);
            })
            ->avg('persentase');
        $avgNilai = round($allQuizScores ?? 0);

        return view('Auth.dosen.progres-mahasiswa', [
            'dosen' => $dosen,
            'enrollments' => $enrollments,
            'coursesForFilter' => $coursesForFilter,
            'search' => $search,
            'courseFilter' => $courseFilter,
            'jurusanFilter' => $jurusanFilter,
            'statusFilter' => $statusFilter,
            'jurusanList' => $jurusanList,
            'totalEnrollments' => $totalEnrollments,
            'selesaiCount' => $selesaiCount,
            'aktifCount' => $aktifCount,
            'tidakAktifCount' => $tidakAktifCount,
            'avgProgress' => $avgProgress,
            'avgNilai' => $avgNilai,
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
        $search = trim((string) $request->query('search', ''));

        $availableStudentIds = $this->getAvailableStudentIds($dosenId);

        if ($availableStudentIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $studentsQuery = User::whereIn('id', $availableStudentIds)
            ->where('role', 'mahasiswa')
            ->with('profile');

        if ($search !== '') {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($profileQuery) use ($search) {
                        $profileQuery->where('nomor_induk', 'like', "%{$search}%");
                    });
            });
        }

        $students = $studentsQuery->get();

        $conversations = $students->map(function ($student) use ($dosenId) {
            $directLastMessage = Message::conversation($dosenId, $student->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $threadPrefix = $this->buildAdminChatThreadPrefix((int) $student->id, (int) $dosenId);
            $adminLastMessage = Message::query()
                ->where('content', 'like', $threadPrefix . '%')
                ->whereHas('sender', function ($query) {
                    $query->where('role', 'admin');
                })
                ->orderBy('created_at', 'desc')
                ->first();

            $lastMessage = $this->pickLatestMessage($directLastMessage, $adminLastMessage);
            $lastMessageContent = $lastMessage
                ? (
                    $lastMessage === $adminLastMessage
                        ? $this->stripAdminChatThreadPrefix((string) $lastMessage->content)
                        : (string) $lastMessage->content
                )
                : null;

            $unreadCount = Message::where('id_sender', $student->id)
                ->where('id_receiver', $dosenId)
                ->where('is_read', false)
                ->count();

            $adminUnreadCount = Message::query()
                ->where('id_receiver', $dosenId)
                ->where('is_read', false)
                ->where('content', 'like', $threadPrefix . '%')
                ->whereHas('sender', function ($query) {
                    $query->where('role', 'admin');
                })
                ->count();

            $fotoProfile = $student->profile->foto_profile ?? null;
            $avatar = $fotoProfile
                ? asset('storage/' . $fotoProfile)
                : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=random';

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_nomor_induk' => $student->profile->nomor_induk ?? '-',
                'student_email' => $student->email ?? '-',
                'student_avatar' => $avatar,
                'last_message' => $lastMessageContent
                    ? (strlen($lastMessageContent) > 50 ? substr($lastMessageContent, 0, 50) . '...' : $lastMessageContent)
                    : null,
                'last_message_time' => $lastMessage ? $lastMessage->created_at->toISOString() : null,
                'unread_count' => $unreadCount + $adminUnreadCount,
            ];
        })
            ->sort(function (array $left, array $right) {
                $leftUnread = (int) ($left['unread_count'] ?? 0);
                $rightUnread = (int) ($right['unread_count'] ?? 0);

                if (($leftUnread > 0) !== ($rightUnread > 0)) {
                    return $leftUnread > 0 ? -1 : 1;
                }

                $leftTime = $left['last_message_time'] ?? '';
                $rightTime = $right['last_message_time'] ?? '';

                if ($leftTime !== $rightTime) {
                    return $rightTime <=> $leftTime;
                }

                return strcmp($left['student_name'], $right['student_name']);
            })
            ->values();

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
        $studentId = (int) $studentId;

        if (!$this->canAccessStudent($dosenId, $studentId)) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak dapat diakses.',
            ], 403);
        }

        $student = User::where('id', $studentId)->where('role', 'mahasiswa')->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.'], 404);
        }

        // Mark messages from student as read
        Message::where('id_sender', $studentId)
            ->where('id_receiver', $dosenId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $threadPrefix = $this->buildAdminChatThreadPrefix((int) $studentId, (int) $dosenId);
        Message::query()
            ->where('id_receiver', $dosenId)
            ->where('is_read', false)
            ->where('content', 'like', $threadPrefix . '%')
            ->whereHas('sender', function ($query) {
                $query->where('role', 'admin');
            })
            ->update(['is_read' => true]);

        $directMessages = Message::conversation($dosenId, $studentId)
            ->orderBy('created_at', 'asc')
            ->get();

        $adminMessages = Message::query()
            ->where('content', 'like', $threadPrefix . '%')
            ->whereHas('sender', function ($query) {
                $query->where('role', 'admin');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $messages = $directMessages
            ->concat($adminMessages)
            ->sortBy('created_at')
            ->values()
            ->map(function ($msg) use ($dosenId, $studentId) {
                $senderType = 'admin';
                if ((int) $msg->id_sender === (int) $dosenId) {
                    $senderType = 'dosen';
                } elseif ((int) $msg->id_sender === (int) $studentId) {
                    $senderType = 'mahasiswa';
                }

                return [
                    'id' => $msg->id_message,
                    'content' => $senderType === 'admin'
                        ? $this->stripAdminChatThreadPrefix((string) $msg->content)
                        : $msg->content,
                    'sender_type' => $senderType,
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

        if (!$this->canAccessStudent((int) $dosen->id, (int) $request->student_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghubungi mahasiswa ini.',
            ], 403);
        }

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

    private function getAvailableStudentIds(int $dosenId): Collection
    {
        $conversationStudentIds = Message::where(function ($query) use ($dosenId) {
            $query->where('id_sender', $dosenId)->orWhere('id_receiver', $dosenId);
        })
            ->selectRaw('CASE WHEN id_sender = ? THEN id_receiver ELSE id_sender END as student_id', [$dosenId])
            ->pluck('student_id');

        $dosenCourseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');
        $enrolledStudentIds = Enrollment::whereIn('id_course', $dosenCourseIds)
            ->pluck('id_mahasiswa');

        return $conversationStudentIds
            ->merge($enrolledStudentIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    private function canAccessStudent(int $dosenId, int $studentId): bool
    {
        if ($studentId <= 0) {
            return false;
        }

        return $this->getAvailableStudentIds($dosenId)->contains($studentId);
    }

    private function buildAdminChatThreadPrefix(int $studentId, int $dosenId): string
    {
        return '[ADMCHAT:' . $studentId . '-' . $dosenId . '] ';
    }

    private function stripAdminChatThreadPrefix(string $content): string
    {
        return preg_replace('/^\[ADMCHAT:\d+\-\d+\]\s*/', '', $content) ?? $content;
    }

    private function pickLatestMessage(?Message $directMessage, ?Message $adminMessage): ?Message
    {
        if ($directMessage === null) {
            return $adminMessage;
        }

        if ($adminMessage === null) {
            return $directMessage;
        }

        return $adminMessage->created_at->greaterThan($directMessage->created_at)
            ? $adminMessage
            : $directMessage;
    }

    private function parseBootcampSeatLabel(string $seatLabel): array
    {
        if (preg_match('/(\d+)\s*\/\s*(\d+)/', $seatLabel, $matches)) {
            return [(int) $matches[1], (int) $matches[2]];
        }

        return [0, 0];
    }

    private function bootcampStatusLabel(string $status): string
    {
        return match ($status) {
            'internal_review' => 'Internal Review',
            'open_registration' => 'Open Registration',
            'published' => 'Published',
            'registration_closed' => 'Registration Closed',
            'in_progress' => 'Sedang Jalan',
            'completed' => 'Selesai',
            'archived' => 'Arsip',
            default => 'Draft',
        };
    }

    private function bootcampProgressByStatus(string $status): int
    {
        return match ($status) {
            'internal_review' => 25,
            'open_registration' => 45,
            'published' => 55,
            'registration_closed' => 70,
            'in_progress' => 82,
            'completed' => 100,
            'archived' => 100,
            default => 10,
        };
    }

    private function formatScheduleTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i:s', $time)->format('H:i');
        } catch (\Throwable $exception) {
            // Fallback for values that are already formatted.
            return preg_match('/^\d{2}:\d{2}/', $time) === 1
                ? substr($time, 0, 5)
                : null;
        }
    }

    private function parseBootcampSessionMeta(?string $description): ?array
    {
        if (!is_string($description) || !Str::startsWith($description, self::BOOTCAMP_SESSION_META_PREFIX)) {
            return null;
        }

        $json = substr($description, strlen(self::BOOTCAMP_SESSION_META_PREFIX));
        if (!is_string($json) || trim($json) === '') {
            return null;
        }

        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function bootcampSessionTagFromAgendaType(?string $agendaType): string
    {
        return match ($agendaType) {
            'workshop' => 'Hands-on',
            'deadline' => 'Office Hour',
            default => 'Live Review',
        };
    }
}
