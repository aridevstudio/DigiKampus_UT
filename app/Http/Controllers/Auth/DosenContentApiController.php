<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DosenContentApiController extends Controller
{
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
}
