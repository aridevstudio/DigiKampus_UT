<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @tags Dosen Courses
 */
class DosenCourseController extends Controller
{
    /**
     * Get All My Courses (Kursus Saya)
     * 
     * Endpoint untuk mendapatkan daftar semua kursus yang dibuat oleh dosen.
     * Mendukung filter status, pencarian, dan paginasi.
     *
     * @security BearerToken
     * @queryParam filter string Filter status: semua, aktif, draft, segera_dibuka. Defaults to semua.
     * @queryParam sort string Urutan: terbaru, terlama, nama. Defaults to terbaru.
     * @queryParam search string Kata kunci pencarian nama atau deskripsi kursus.
     * @queryParam per_page int Jumlah item per halaman. Defaults to 6.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;
        $filter = $request->query('filter', 'semua');
        $sort = $request->query('sort', 'terbaru');
        $search = $request->query('search');
        $perPage = $request->query('per_page', 6);

        $query = Course::where('id_dosen', $dosenId)
            ->with(['enrollments', 'jurusan']);

        // Filter by status
        if ($filter !== 'semua') {
            $query->where('status', $filter);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_course', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Sort
        switch ($sort) {
            case 'terlama':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nama':
                $query->orderBy('nama_course', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $courses = $query->paginate($perPage);

        $coursesData = $courses->getCollection()->map(function ($course) {
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
                'created_at' => $course->created_at->format('Y-m-d')
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
                    'total' => $courses->total()
                ]
            ]
        ], 200);
    }

    /**
     * Get Course Detail
     * 
     * Endpoint untuk mendapatkan detail kursus berdasarkan ID.
     * Menampilkan informasi lengkap termasuk modul-modul yang ada.
     *
     * @security BearerToken
     * @param Request $request
     * @param int $id Course ID
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $dosenId = $request->user()->id;

        $course = Course::where('id_course', $id)
            ->where('id_dosen', $dosenId)
            ->with(['materials', 'enrollments', 'jurusan'])
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.'
            ], 404);
        }

        $enrollmentCount = $course->enrollments->count();
        $avgProgress = $course->enrollments->avg('progress') ?? 0;

        return response()->json([
            'success' => true,
            'message' => 'Detail kursus berhasil diambil.',
            'data' => [
                'id' => $course->id_course,
                'kode' => $course->kode_course,
                'nama' => $course->nama_course,
                'deskripsi' => $course->deskripsi,
                'thumbnail' => $course->thumbnail,
                'status' => $course->status,
                'tipe' => $course->tipe,
                'harga' => $course->harga,
                'jurusan' => $course->jurusan ? [
                    'id' => $course->jurusan->id_jurusan,
                    'nama' => $course->jurusan->nama_jurusan
                ] : null,
                'jumlah_mahasiswa' => $enrollmentCount,
                'progress_rata_rata' => round($avgProgress, 0),
                'rating' => $course->rating,
                'jumlah_ulasan' => $course->jumlah_ulasan,
                'modules' => $course->materials->map(function ($material) {
                    return [
                        'id' => $material->id_material,
                        'judul' => $material->judul_material,
                        'tipe' => $material->tipe,
                        'urutan' => $material->urutan,
                        'durasi' => $material->durasi
                    ];
                }),
                'created_at' => $course->created_at->format('Y-m-d'),
                'updated_at' => $course->updated_at->format('Y-m-d')
            ]
        ], 200);
    }

    /**
     * Create New Course (Buat Kursus Baru)
     * 
     * Endpoint untuk membuat kursus baru.
     * Mendukung upload thumbnail dan pengaturan pricing.
     *
     * @security BearerToken
     * @bodyParam judul_kursus string required Judul kursus. Max 255 karakter.
     * @bodyParam deskripsi string required Deskripsi kursus.
     * @bodyParam kategori int required ID kategori/jurusan.
     * @bodyParam tingkat_kesulitan string Tingkat kesulitan: pemula, menengah, lanjutan.
     * @bodyParam estimasi_waktu int Estimasi waktu belajar dalam jam.
     * @bodyParam thumbnail file Thumbnail kursus (PNG/JPG, max 5MB).
     * @bodyParam status_kursus boolean Status aktif atau draft.
     * @bodyParam akses_publik boolean Dapat dilihat oleh semua mahasiswa.
     * @bodyParam sertifikat boolean Berikan sertifikat setelah selesai.
     * @bodyParam harga numeric Harga kursus dalam Rupiah.
     * @bodyParam diskon numeric Diskon dalam persen (0-100).
     * @bodyParam is_gratis boolean Kursus gratis.
     * @bodyParam tipe string Tipe kursus: kursus, webinar, tiket.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'judul_kursus' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'nama_course')
                    ->where(static fn ($query) => $query->where('id_dosen', $dosenId)),
            ],
            'deskripsi' => 'required|string',
            'kategori' => 'required|integer|exists:jurusans,id_jurusan',
            'tingkat_kesulitan' => 'nullable|in:pemula,menengah,lanjutan',
            'estimasi_waktu' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'status_kursus' => 'nullable|boolean',
            'akses_publik' => 'nullable|boolean',
            'sertifikat' => 'nullable|boolean',
            'harga' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'is_gratis' => 'nullable|boolean',
            'tipe' => 'nullable|in:kursus,webinar,tiket'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Generate course code
        $kodePrefix = 'CRS';
        $lastCourse = Course::orderBy('id_course', 'desc')->first();
        $kodeCourse = $kodePrefix . str_pad(($lastCourse ? $lastCourse->id_course + 1 : 1), 5, '0', STR_PAD_LEFT);

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        // Calculate final price
        $harga = $validated['is_gratis'] ?? false ? 0 : ($validated['harga'] ?? 0);
        if (isset($validated['diskon']) && $validated['diskon'] > 0) {
            $harga = $harga - ($harga * $validated['diskon'] / 100);
        }

        // Determine status
        $status = ($validated['status_kursus'] ?? false) ? 'aktif' : 'draft';

        $course = Course::create([
            'kode_course' => $kodeCourse,
            'nama_course' => $validated['judul_kursus'],
            'deskripsi' => $validated['deskripsi'],
            'id_dosen' => $dosenId,
            'id_jurusan' => $validated['kategori'],
            'thumbnail' => $thumbnailPath,
            'status' => $status,
            'tipe' => $validated['tipe'] ?? 'kursus',
            'harga' => $harga,
            'rating' => 0,
            'jumlah_ulasan' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kursus berhasil dibuat.',
            'data' => [
                'id' => $course->id_course,
                'kode' => $course->kode_course,
                'nama' => $course->nama_course,
                'status' => $course->status
            ]
        ], 201);
    }

    /**
     * Update Course
     * 
     * Endpoint untuk mengupdate informasi kursus yang sudah ada.
     *
     * @security BearerToken
     * @bodyParam judul_kursus string Judul kursus. Max 255 karakter.
     * @bodyParam deskripsi string Deskripsi kursus.
     * @bodyParam kategori int ID kategori/jurusan.
     * @bodyParam thumbnail file Thumbnail kursus (PNG/JPG, max 5MB).
     * @bodyParam status_kursus boolean Status aktif atau draft.
     * @bodyParam harga numeric Harga kursus dalam Rupiah.
     * @bodyParam tipe string Tipe kursus: kursus, webinar, tiket.
     * @param Request $request
     * @param int $id Course ID
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $dosenId = $request->user()->id;

        $course = Course::where('id_course', $id)
            ->where('id_dosen', $dosenId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul_kursus' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('courses', 'nama_course')
                    ->where(static fn ($query) => $query->where('id_dosen', $dosenId))
                    ->ignore($course->id_course, 'id_course'),
            ],
            'deskripsi' => 'sometimes|string',
            'kategori' => 'sometimes|integer|exists:jurusans,id_jurusan',
            'tingkat_kesulitan' => 'nullable|in:pemula,menengah,lanjutan',
            'estimasi_waktu' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'status_kursus' => 'nullable|boolean',
            'harga' => 'nullable|numeric|min:0',
            'tipe' => 'nullable|in:kursus,webinar,tiket'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        // Map fields
        $updateData = [];
        if (isset($validated['judul_kursus']))
            $updateData['nama_course'] = $validated['judul_kursus'];
        if (isset($validated['deskripsi']))
            $updateData['deskripsi'] = $validated['deskripsi'];
        if (isset($validated['kategori']))
            $updateData['id_jurusan'] = $validated['kategori'];
        if (isset($validated['thumbnail']))
            $updateData['thumbnail'] = $validated['thumbnail'];
        if (isset($validated['status_kursus']))
            $updateData['status'] = $validated['status_kursus'] ? 'aktif' : 'draft';
        if (isset($validated['harga']))
            $updateData['harga'] = $validated['harga'];
        if (isset($validated['tipe']))
            $updateData['tipe'] = $validated['tipe'];

        $course->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Kursus berhasil diupdate.',
            'data' => [
                'id' => $course->id_course,
                'nama' => $course->nama_course,
                'status' => $course->status
            ]
        ], 200);
    }

    /**
     * Delete Course
     * 
     * Endpoint untuk menghapus kursus.
     * Tidak dapat menghapus kursus yang sudah memiliki mahasiswa terdaftar.
     *
     * @security BearerToken
     * @param Request $request
     * @param int $id Course ID
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $dosenId = $request->user()->id;

        $course = Course::where('id_course', $id)
            ->where('id_dosen', $dosenId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.'
            ], 404);
        }

        // Check if course has enrollments
        if ($course->enrollments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kursus yang sudah memiliki mahasiswa terdaftar.'
            ], 400);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kursus berhasil dihapus.'
        ], 200);
    }

    /**
     * Add Module to Course
     * 
     * Endpoint untuk menambahkan modul baru ke kursus.
     *
     * @security BearerToken
     * @bodyParam judul_modul string required Judul modul. Max 255 karakter.
     * @bodyParam tipe string Tipe konten: video, text, quiz. Defaults to text.
     * @bodyParam konten string Konten modul dalam format text/html.
     * @bodyParam video_url string URL video jika tipe video.
     * @bodyParam durasi int Durasi dalam menit.
     * @param Request $request
     * @param int $courseId Course ID
     * @return JsonResponse
     */
    public function addModule(Request $request, int $courseId): JsonResponse
    {
        $dosenId = $request->user()->id;

        $course = Course::where('id_course', $courseId)
            ->where('id_dosen', $dosenId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul_modul' => 'required|string|max:255',
            'tipe' => 'nullable|in:video,bacaan,kuis,tugas,text,quiz',
            'konten' => 'nullable|string',
            'video_url' => 'nullable|url',
            'durasi' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Get next order
        $lastModule = CourseMaterial::where('id_course', $courseId)
            ->orderBy('urutan', 'desc')
            ->first();
        $urutan = $lastModule ? $lastModule->urutan + 1 : 1;

        $normalizedType = $this->normalizeModuleType(
            $validated['tipe'] ?? null,
            $validated['konten'] ?? null
        );

        $module = CourseMaterial::create([
            'id_course' => $courseId,
            'judul_material' => $validated['judul_modul'],
            'tipe' => $normalizedType,
            'konten' => $validated['konten'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'urutan' => $urutan,
            'durasi' => $validated['durasi'] ?? 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Modul berhasil ditambahkan.',
            'data' => [
                'id' => $module->id_material,
                'judul' => $module->judul_material,
                'urutan' => $module->urutan
            ]
        ], 201);
    }

    /**
     * Update Module
     * 
     * Endpoint untuk mengupdate modul yang sudah ada.
     *
     * @security BearerToken
     * @bodyParam judul_modul string Judul modul. Max 255 karakter.
     * @bodyParam tipe string Tipe konten: video, text, quiz.
     * @bodyParam konten string Konten modul dalam format text/html.
     * @bodyParam video_url string URL video jika tipe video.
     * @bodyParam durasi int Durasi dalam menit.
     * @bodyParam urutan int Urutan modul.
     * @param Request $request
     * @param int $courseId Course ID
     * @param int $moduleId Module ID
     * @return JsonResponse
     */
    public function updateModule(Request $request, int $courseId, int $moduleId): JsonResponse
    {
        $dosenId = $request->user()->id;

        $course = Course::where('id_course', $courseId)
            ->where('id_dosen', $dosenId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.'
            ], 404);
        }

        $module = CourseMaterial::where('id_material', $moduleId)
            ->where('id_course', $courseId)
            ->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul_modul' => 'sometimes|string|max:255',
            'tipe' => 'nullable|in:video,bacaan,kuis,tugas,text,quiz',
            'konten' => 'nullable|string',
            'video_url' => 'nullable|url',
            'durasi' => 'nullable|integer|min:1',
            'urutan' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $updateData = [];
        if (isset($validated['judul_modul']))
            $updateData['judul_material'] = $validated['judul_modul'];
        if (isset($validated['tipe'])) {
            $updateData['tipe'] = $this->normalizeModuleType(
                $validated['tipe'],
                $validated['konten'] ?? $module->konten
            );
        }
        if (isset($validated['konten']))
            $updateData['konten'] = $validated['konten'];
        if (isset($validated['video_url']))
            $updateData['video_url'] = $validated['video_url'];
        if (isset($validated['durasi']))
            $updateData['durasi'] = $validated['durasi'];
        if (isset($validated['urutan']))
            $updateData['urutan'] = $validated['urutan'];

        $module->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Modul berhasil diupdate.',
            'data' => [
                'id' => $module->id_material,
                'judul' => $module->judul_material
            ]
        ], 200);
    }

    /**
     * Delete Module
     * 
     * Endpoint untuk menghapus modul dari kursus.
     *
     * @security BearerToken
     * @param Request $request
     * @param int $courseId Course ID
     * @param int $moduleId Module ID
     * @return JsonResponse
     */
    public function deleteModule(Request $request, int $courseId, int $moduleId): JsonResponse
    {
        $dosenId = $request->user()->id;

        $course = Course::where('id_course', $courseId)
            ->where('id_dosen', $dosenId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.'
            ], 404);
        }

        $module = CourseMaterial::where('id_material', $moduleId)
            ->where('id_course', $courseId)
            ->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan.'
            ], 404);
        }

        $module->delete();

        return response()->json([
            'success' => true,
            'message' => 'Modul berhasil dihapus.'
        ], 200);
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
