<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseModule;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminContentApiController extends Controller
{
    public function resolveVideoDuration(Request $request): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'URL video tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = $validator->validated()['url'];
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        [$seconds, $provider, $source, $errorMessage] = $this->detectDurationFromProvider($url, $host);

        if ($seconds === null || $seconds <= 0) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage ?: 'Durasi video belum bisa dideteksi otomatis.',
                'data' => [
                    'provider' => $provider,
                ],
            ], 422);
        }

        $minutes = max(1, (int) ceil($seconds / 60));

        return response()->json([
            'success' => true,
            'message' => 'Durasi video berhasil dideteksi.',
            'data' => [
                'provider' => $provider,
                'source' => $source,
                'seconds' => $seconds,
                'minutes' => $minutes,
            ],
        ]);
    }

    public function courses(Request $request): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $filter = $request->query('filter', 'semua');
        $sort = $request->query('sort', 'terbaru');
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 100);
        $perPage = max(1, min($perPage, 100));

        $query = Course::where('id_dosen', $dosen->id)
            ->with(['enrollments', 'jurusan']);

        if ($filter !== 'semua') {
            $query->where('status', $filter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_course', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'terlama':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nama':
                $query->orderBy('nama_course', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $courses = $query->paginate($perPage);

        $coursesData = $courses->getCollection()->map(function (Course $course) {
            $enrollmentCount = $course->enrollments->count();
            $avgProgress = $course->enrollments->avg('progress') ?? 0;

            return [
                'id' => $course->id_course,
                'kode' => $course->kode_course,
                'nama' => $course->nama_course,
                'deskripsi' => $course->deskripsi,
                'thumbnail' => $course->thumbnail,
                'status' => $course->status,
                'tipe' => $course->tipe,
                'harga' => $course->harga,
                'jurusan' => $course->jurusan->nama_jurusan ?? null,
                'jumlah_mahasiswa' => $enrollmentCount,
                'progress_rata_rata' => round($avgProgress, 0),
                'rating' => $course->rating,
                'jumlah_ulasan' => $course->jumlah_ulasan,
                'created_at' => $course->created_at?->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar kursus berhasil diambil.',
            'data' => [
                'courses' => $coursesData,
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                ],
            ],
        ]);
    }

    public function courseStudents(int $courseId): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $course = Course::query()
            ->where('id_course', $courseId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan atau bukan milik dosen ini.',
            ], 404);
        }

        $students = Enrollment::query()
            ->where('id_course', $course->id_course)
            ->with(['mahasiswa.profile'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Enrollment $enrollment) {
                return [
                    'id' => $enrollment->id_mahasiswa,
                    'name' => $enrollment->mahasiswa?->name ?? 'Mahasiswa',
                    'email' => $enrollment->mahasiswa?->email,
                    'nim' => $enrollment->mahasiswa?->profile?->nim,
                    'status' => $enrollment->status,
                ];
            })
            ->filter(fn (array $item) => !empty($item['id']))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Daftar mahasiswa kursus berhasil diambil.',
            'data' => [
                'course' => [
                    'id' => $course->id_course,
                    'nama' => $course->nama_course,
                ],
                'items' => $students,
            ],
        ]);
    }

    public function upcomingSchedules(Request $request): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $limit = (int) $request->query('limit', 3);
        $limit = max(1, min($limit, 20));

        $schedules = Agenda::query()
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->with(['course', 'mahasiswa'])
            ->orderBy('tanggal')
            ->orderByRaw('CASE WHEN waktu_mulai IS NULL THEN 1 ELSE 0 END, waktu_mulai ASC')
            ->limit($limit)
            ->get();

        $items = $schedules->map(function (Agenda $schedule) {
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
                'judul' => $schedule->judul,
                'deskripsi' => $schedule->deskripsi,
                'course' => $schedule->course?->nama_course ?? $schedule->judul ?? 'Jadwal Mengajar',
                'tanggal' => $schedule->tanggal?->translatedFormat('l, d F Y') ?? '-',
                'tanggal_raw' => $schedule->tanggal?->format('Y-m-d'),
                'waktu' => $timeText,
                'waktu_mulai' => $schedule->waktu_mulai,
                'waktu_selesai' => $schedule->waktu_selesai,
                'tipe' => $schedule->tipe,
                'id_mahasiswa' => $schedule->id_mahasiswa,
                'mahasiswa' => $schedule->mahasiswa?->name,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal mengajar terdekat berhasil diambil.',
            'data' => [
                'items' => $items,
            ],
        ]);
    }

    public function storeSchedule(Request $request): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validator = Validator::make(
            $request->all(),
            $this->scheduleValidationRules(),
            $this->scheduleValidationMessages()
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $course = Course::where('id_course', $validated['id_course'])
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan atau bukan milik dosen ini.',
            ], 404);
        }

        $selectedMahasiswaId = $this->resolveAndValidateScheduleMahasiswaId($validated, $course->id_course);
        if ($selectedMahasiswaId === false) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa yang dipilih tidak terdaftar di kursus ini.',
                'errors' => [
                    'id_mahasiswa' => ['Mahasiswa yang dipilih tidak terdaftar di kursus ini.'],
                ],
            ], 422);
        }

        $agenda = Agenda::create([
            'id_mahasiswa' => $selectedMahasiswaId,
            'id_dosen' => $dosen->id,
            'id_course' => $course->id_course,
            'judul' => trim($validated['judul']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'tipe' => $validated['tipe'],
            'warna' => isset($validated['warna'])
                ? $this->normalizeHexColor($validated['warna'])
                : Agenda::getColorByType($validated['tipe']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal mengajar berhasil dibuat.',
            'data' => [
                'id_agenda' => $agenda->id_agenda,
                'id_course' => $agenda->id_course,
                'judul' => $agenda->judul,
                'tanggal' => optional($agenda->tanggal)->format('Y-m-d'),
                'waktu_mulai' => $agenda->waktu_mulai,
                'waktu_selesai' => $agenda->waktu_selesai,
                'id_mahasiswa' => $agenda->id_mahasiswa,
            ],
        ], 201);
    }

    public function updateSchedule(Request $request, int $agendaId): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validator = Validator::make(
            $request->all(),
            $this->scheduleValidationRules(),
            $this->scheduleValidationMessages()
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $agenda = Agenda::query()
            ->where('id_agenda', $agendaId)
            ->first();

        if (!$agenda) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan atau bukan milik dosen ini.',
            ], 404);
        }

        $course = Course::where('id_course', $validated['id_course'])
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan atau bukan milik dosen ini.',
            ], 404);
        }

        $selectedMahasiswaId = $this->resolveAndValidateScheduleMahasiswaId($validated, $course->id_course);
        if ($selectedMahasiswaId === false) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa yang dipilih tidak terdaftar di kursus ini.',
                'errors' => [
                    'id_mahasiswa' => ['Mahasiswa yang dipilih tidak terdaftar di kursus ini.'],
                ],
            ], 422);
        }

        $resolvedType = $validated['tipe'];

        $agenda->update([
            'id_course' => $course->id_course,
            'id_mahasiswa' => $selectedMahasiswaId,
            'judul' => trim($validated['judul']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'tipe' => $resolvedType,
            'warna' => isset($validated['warna'])
                ? $this->normalizeHexColor($validated['warna'])
                : Agenda::getColorByType($resolvedType),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal mengajar berhasil diperbarui.',
            'data' => [
                'id_agenda' => $agenda->id_agenda,
                'id_course' => $agenda->id_course,
                'judul' => $agenda->judul,
                'tanggal' => optional($agenda->tanggal)->format('Y-m-d'),
                'waktu_mulai' => $agenda->waktu_mulai,
                'waktu_selesai' => $agenda->waktu_selesai,
                'id_mahasiswa' => $agenda->id_mahasiswa,
            ],
        ]);
    }

    public function deleteSchedule(int $agendaId): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $agenda = Agenda::query()
            ->where('id_agenda', $agendaId)
            ->first();

        if (!$agenda) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan atau bukan milik dosen ini.',
            ], 404);
        }

        $agenda->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal mengajar berhasil dihapus.',
        ]);
    }

    public function addModule(Request $request, int $courseId): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $course = Course::where('id_course', $courseId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul_modul' => 'required|string|max:255',
            'tipe' => 'nullable|in:video,bacaan,kuis,tugas,text,quiz',
            'konten' => 'nullable|string',
            'video_url' => 'nullable|url',
            'durasi' => 'nullable|integer|min:1',
            'lampiran_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,jpeg,png,webp,txt',
            'sumber_referensi' => 'nullable|array',
            'sumber_referensi.*' => 'nullable|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $normalizedType = $this->normalizeModuleType($validated['tipe'] ?? null, $validated['konten'] ?? null);
        $lampiranPath = null;
        $sumberReferensi = [];

        if ($normalizedType === 'bacaan') {
            if ($request->hasFile('lampiran_file')) {
                $lampiranPath = $request->file('lampiran_file')->store('bacaan-lampiran', 'public');
            }

            $sumberReferensi = collect($validated['sumber_referensi'] ?? [])
                ->map(static fn ($url) => trim((string) $url))
                ->filter(static fn ($url) => $url !== '')
                ->values()
                ->all();
        }

        $moduleOrder = (int) (CourseModule::where('id_course', $courseId)->max('urutan') ?? 0) + 1;

        // Each publish from typed content page creates a dedicated module,
        // so course structure and new content stay in sync.
        $courseModule = CourseModule::create([
            'id_course' => $courseId,
            'judul_module' => $validated['judul_modul'],
            'deskripsi' => $this->moduleDescriptionByType($normalizedType),
            'urutan' => $moduleOrder,
        ]);

        $urutan = (int) (CourseMaterial::where('id_module', $courseModule->id_module)->max('urutan') ?? 0) + 1;

        $module = CourseMaterial::create([
            'id_course' => $courseId,
            'id_module' => $courseModule->id_module,
            'judul_material' => $validated['judul_modul'],
            'tipe' => $normalizedType,
            'konten' => $validated['konten'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'lampiran_path' => $lampiranPath,
            'sumber_referensi' => $sumberReferensi,
            'urutan' => $urutan,
            'durasi' => $validated['durasi'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Modul berhasil ditambahkan.',
            'data' => [
                'id' => $module->id_material,
                'id_module' => $courseModule->id_module,
                'judul' => $module->judul_material,
                'urutan' => $module->urutan,
                'lampiran_url' => $module->lampiran_path ? asset('storage/' . $module->lampiran_path) : null,
                'sumber_referensi' => $module->sumber_referensi ?? [],
            ],
        ], 201);
    }

    private function moduleDescriptionByType(string $type): string
    {
        return match ($type) {
            'video' => 'Modul video pembelajaran',
            'kuis' => 'Modul evaluasi kuis',
            'tugas' => 'Modul tugas/penugasan',
            default => 'Modul materi bacaan',
        };
    }

    private function normalizeModuleType(?string $type, ?string $content = null): string
    {
        $normalized = strtolower(trim((string) ($type ?? '')));

        if ($normalized === '') {
            return 'bacaan';
        }

        return match ($normalized) {
            'video' => 'video',
            'kuis', 'quiz' => 'kuis',
            'tugas' => 'tugas',
            'bacaan' => 'bacaan',
            'text' => $this->isAssignmentPayload($content) ? 'tugas' : 'bacaan',
            default => 'bacaan',
        };
    }

    private function isAssignmentPayload(?string $content): bool
    {
        if (!$content) {
            return false;
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) && !empty($decoded['is_tugas']);
    }

    private function formatScheduleTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('H:i');
        } catch (\Throwable $exception) {
            return preg_match('/^\d{2}:\d{2}/', $time) === 1
                ? substr($time, 0, 5)
                : null;
        }
    }

    private function normalizeHexColor(string $color): string
    {
        return str_starts_with($color, '#') ? $color : '#' . $color;
    }

    private function scheduleValidationRules(): array
    {
        return [
            'id_course' => 'required|integer|exists:courses,id_course',
            'id_mahasiswa' => 'nullable|integer|exists:users,id',
            'judul' => ['required', 'string', 'max:255', 'regex:/\S/'],
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'tipe' => 'required|in:webinar,deadline,workshop',
            'warna' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
        ];
    }

    private function scheduleValidationMessages(): array
    {
        return [
            'id_course.required' => 'Kursus wajib dipilih.',
            'id_mahasiswa.exists' => 'Mahasiswa yang dipilih tidak ditemukan.',
            'judul.required' => 'Judul sesi wajib diisi.',
            'judul.regex' => 'Judul sesi wajib diisi.',
            'tanggal.required' => 'Tanggal jadwal wajib diisi.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            'tipe.required' => 'Tipe jadwal wajib dipilih.',
        ];
    }

    private function resolveAndValidateScheduleMahasiswaId(array $validated, int $courseId): int|false|null
    {
        if (!isset($validated['id_mahasiswa']) || $validated['id_mahasiswa'] === null || $validated['id_mahasiswa'] === '') {
            return null;
        }

        $mahasiswaId = (int) $validated['id_mahasiswa'];
        if ($mahasiswaId <= 0) {
            return false;
        }

        $isEnrolled = Enrollment::query()
            ->where('id_course', $courseId)
            ->where('id_mahasiswa', $mahasiswaId)
            ->exists();

        return $isEnrolled ? $mahasiswaId : false;
    }

    private function detectDurationFromProvider(string $url, string $host): array
    {
        if ($this->isYouTubeHost($host)) {
            return $this->resolveYouTubeDuration($url);
        }

        if ($this->isVimeoHost($host)) {
            return $this->resolveVimeoDuration($url);
        }

        if ($this->isDailymotionHost($host)) {
            return $this->resolveDailymotionDuration($url);
        }

        return [null, 'unknown', null, 'Provider video belum didukung untuk deteksi durasi otomatis.'];
    }

    private function isYouTubeHost(string $host): bool
    {
        return Str::contains($host, ['youtube.com', 'youtu.be', 'youtube-nocookie.com']);
    }

    private function isVimeoHost(string $host): bool
    {
        return Str::contains($host, 'vimeo.com');
    }

    private function isDailymotionHost(string $host): bool
    {
        return Str::contains($host, ['dailymotion.com', 'dai.ly']);
    }

    private function resolveYouTubeDuration(string $url): array
    {
        $videoId = $this->extractYouTubeVideoId($url);
        if (!$videoId) {
            return [null, 'youtube', null, 'URL YouTube tidak valid.'];
        }

        // Parse duration dari HTML watch page.
        try {
            $watchResponse = Http::timeout(12)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; DigiKampusBot/1.0)',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])
                ->get('https://www.youtube.com/watch', [
                    'v' => $videoId,
                    'hl' => 'en',
                ]);

            if ($watchResponse->successful()) {
                $body = $watchResponse->body();
                $seconds = $this->extractDurationFromYouTubeWatchHtml($body);
                if ($seconds !== null && $seconds > 0) {
                    return [$seconds, 'youtube', 'watch_html', null];
                }
            }
        } catch (\Throwable $exception) {
            // Keep manual fallback behavior if remote request fails.
        }

        return [null, 'youtube', null, 'Durasi YouTube belum bisa dideteksi otomatis.'];
    }

    private function extractDurationFromYouTubeWatchHtml(string $body): ?int
    {
        $candidates = [$body];
        $unescapedBody = str_replace('\\"', '"', $body);
        if ($unescapedBody !== $body) {
            $candidates[] = $unescapedBody;
        }

        foreach ($candidates as $candidate) {
            if (preg_match('/"lengthSeconds":"(\d+)"/', $candidate, $matches)) {
                return (int) $matches[1];
            }

            if (preg_match('/"lengthSeconds":(\d+)/', $candidate, $matches)) {
                return (int) $matches[1];
            }

            if (preg_match('/"approxDurationMs":"(\d+)"/', $candidate, $matches)) {
                return (int) ceil(((int) $matches[1]) / 1000);
            }

            if (preg_match('/"approxDurationMs":(\d+)/', $candidate, $matches)) {
                return (int) ceil(((int) $matches[1]) / 1000);
            }
        }

        return null;
    }

    private function resolveVimeoDuration(string $url): array
    {
        try {
            $response = Http::timeout(10)->get('https://vimeo.com/api/oembed.json', [
                'url' => $url,
            ]);

            if ($response->successful()) {
                $duration = (int) $response->json('duration');
                if ($duration > 0) {
                    return [$duration, 'vimeo', 'oembed', null];
                }
            }
        } catch (\Throwable $exception) {
            // Ignore and return user-friendly message below.
        }

        return [null, 'vimeo', null, 'Durasi Vimeo belum bisa dideteksi otomatis.'];
    }

    private function resolveDailymotionDuration(string $url): array
    {
        try {
            $response = Http::timeout(10)->get('https://www.dailymotion.com/services/oembed', [
                'url' => $url,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $duration = (int) $response->json('duration');
                if ($duration > 0) {
                    return [$duration, 'dailymotion', 'oembed', null];
                }
            }
        } catch (\Throwable $exception) {
            // Ignore and return user-friendly message below.
        }

        return [null, 'dailymotion', null, 'Durasi Dailymotion belum bisa dideteksi otomatis.'];
    }

    private function extractYouTubeVideoId(string $url): ?string
    {
        $parts = parse_url($url);
        if (!$parts) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');

        if (Str::contains($host, 'youtu.be')) {
            $id = explode('/', $path)[0] ?? '';
            return $id !== '' ? $id : null;
        }

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
            if (!empty($query['v'])) {
                return (string) $query['v'];
            }
        }

        $segments = explode('/', $path);
        $embedIndex = array_search('embed', $segments, true);
        if ($embedIndex !== false && !empty($segments[$embedIndex + 1])) {
            return (string) $segments[$embedIndex + 1];
        }

        $shortsIndex = array_search('shorts', $segments, true);
        if ($shortsIndex !== false && !empty($segments[$shortsIndex + 1])) {
            return (string) $segments[$shortsIndex + 1];
        }

        $liveIndex = array_search('live', $segments, true);
        if ($liveIndex !== false && !empty($segments[$liveIndex + 1])) {
            return (string) $segments[$liveIndex + 1];
        }

        return null;
    }
}
