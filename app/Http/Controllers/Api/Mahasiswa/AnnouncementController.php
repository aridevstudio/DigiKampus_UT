<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @tags Mahasiswa Pengumuman
 */
class AnnouncementController extends Controller
{
    /**
     * Get Pengumuman
     *
     * Endpoint untuk mendapatkan pengumuman aktif yang sudah dipublish.
     * Pengumuman umum dan pengumuman sesuai prodi mahasiswa akan ditampilkan.
     *
     * @security BearerToken
     * @queryParam kategori string Optional. Filter kategori pengumuman.
     * @queryParam search string Optional. Cari judul atau konten.
     * @queryParam per_page int Optional. Jumlah data per halaman, maksimal 50.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || $user->role !== 'mahasiswa') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk mahasiswa.',
            ], 403);
        }

        $user->loadMissing('profile.jurusan');

        $perPage = min(max((int) $request->query('per_page', 20), 1), 50);
        $targetKeys = $this->resolveMahasiswaTargetKeys($user);

        $query = News::query()
            ->active()
            ->published()
            ->visibleForProdi($targetKeys)
            ->orderByDesc('tanggal_publish')
            ->orderByDesc('id_news');

        if ($request->filled('kategori')) {
            $query->where('kategori', Str::lower((string) $request->query('kategori')));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman retrieved successfully',
            'data' => $announcements->getCollection()
                ->map(fn (News $news) => $this->mapAnnouncement($news))
                ->values(),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'per_page' => $announcements->perPage(),
                'total' => $announcements->total(),
                'last_page' => $announcements->lastPage(),
            ],
        ]);
    }

    /**
     * Get Pengumuman Detail
     *
     * @security BearerToken
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (!$user || $user->role !== 'mahasiswa') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk mahasiswa.',
            ], 403);
        }

        $user->loadMissing('profile.jurusan');

        $news = News::query()
            ->active()
            ->published()
            ->visibleForProdi($this->resolveMahasiswaTargetKeys($user))
            ->where('id_news', $id)
            ->first();

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman detail retrieved successfully',
            'data' => $this->mapAnnouncement($news),
        ]);
    }

    private function mapAnnouncement(News $news): array
    {
        return [
            'id_news' => $news->id_news,
            'judul' => $news->judul,
            'konten' => $news->konten,
            'kategori' => $news->kategori,
            'target_prodi' => $news->target_prodi,
            'thumbnail' => $news->thumbnail,
            'thumbnail_url' => $news->thumbnail ? asset('storage/' . $news->thumbnail) : null,
            'tanggal_publish' => $news->tanggal_publish?->toDateTimeString(),
            'waktu_relatif' => $news->tanggal_publish?->diffForHumans(),
            'created_at' => $news->created_at?->toDateTimeString(),
            'updated_at' => $news->updated_at?->toDateTimeString(),
        ];
    }

    private function resolveMahasiswaTargetKeys($user): array
    {
        $profile = $user->profile;
        $jurusan = $profile?->jurusan;

        return collect([
            $profile?->id_jurusan,
            $jurusan?->kode_jurusan,
            $jurusan?->nama_jurusan,
            $jurusan ? $jurusan->jenjang . ' ' . $jurusan->nama_jurusan : null,
        ])
            ->filter(fn ($value) => filled($value))
            ->flatMap(fn ($value) => [
                Str::lower(trim((string) $value)),
                Str::slug((string) $value),
            ])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
