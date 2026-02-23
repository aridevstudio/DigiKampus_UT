<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DosenContentApiController extends Controller
{
    public function resolveVideoDuration(Request $request): JsonResponse
    {
        $dosen = Auth::guard('dosen')->user();

        if (!$dosen) {
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
        $dosen = Auth::guard('dosen')->user();

        if (!$dosen) {
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

    public function addModule(Request $request, int $courseId): JsonResponse
    {
        $dosen = Auth::guard('dosen')->user();

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $course = Course::where('id_course', $courseId)
            ->where('id_dosen', $dosen->id)
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
